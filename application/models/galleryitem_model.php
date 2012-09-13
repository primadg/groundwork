<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Galleries Model class
* @date: 25-July-2012
* @Purpose: This model contains all the functionalities for galleries.
* @filesource: application/models/galleryitem_model.php
* @author:    Mike Vodolazkin
* @version: 0.0.1
* @revision:
**/


class Galleryitem_model extends MY_Model
{
    private $_pInfoTable;
    private $_pInfoFields;
    private $_pFfmpegPath;
    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        // Присваиваем значение таблицы с которой будет работать данная модель
        $this->_pTableName = 'gallery_item';
        $this->_pInfoTable = 'fileinfo';
        // Инициализируем список полей нашей таблицы
        $this->_pFields = array(
            "id"=>TRUE,
            "node_id"=>TRUE,
            "type"=>TRUE,
            "file"=>TRUE,
            "title"=>TRUE,
            "description"=>TRUE,
            'created'=>true,
            'modified'=>false
        );
        $this->_pInfoFields = array(
            'id',
            'size',
            'format'
        );
        $this->_pFfmpegPath = '';
    }

    public function getItems($aWhere = '', $aOrder = false, $aLimit = false)
    {        
        $this->db->from($this->_pTableName);

        if($aWhere)
        {
            $this->db->where($aWhere);
        }
        
        if(isset($aOrder['field']) && in_array($aOrder['field'], array_keys($this->_pFields)) )
        {
            if(isset($aOrder['dirrection']) && in_array(strtolower($aOrder['dirrection']), array('asc', 'desc')))
            {
                $this->db->order_by($aOrder['field'], $aOrder['dirrection']);
            }
            else
            {
                $this->db->order_by($aOrder['field']);
            }
        }

        if($aLimit)
        {
            if(is_array($aLimit))
            {
                $this->db->limit((int)$aLimit[0], (int)$aLimit[1]);
            }
            else
            {
                $this->db->limit((int)$aLimit);
            }
        }

        $Result = $this->db->get();
        return $Result->result_array();
    }

    public function getItem($aWhere = '')
    {
        $this->db->from($this->_pTableName);
        $this->db->where($aWhere);
        $Result = $this->db->get();
        return $Result->row_array();
    }

    /**
     * 
     * Получить все дочерние элементы ноды
     *
     * @param int $aParentId - id родительской ноды
     * @param bool $aWithPepth - на какную глубину выбирать дочерние элементы. true - на всю глубину
     * @param mixed $aType - какие типы элементов выбирать. false - все
     * @param mixed $aLimit - настройки лимита
     * @param mixed $aOrder - настройки сортировки
     * @return array - список элементов
     * 
     * @author Mike Vodolazkin
     */
    public function getChildItems($aParentId = 0, $aWithPepth = false, $aType = false, $aLimit = false , $aOrder = array('field'=>'numleft', 'dirrection'=>'asc'))
    {
        $aParentId = (int)$aParentId;
        if(!$aParentId)
        {
            return false;
        }
        if(!in_array($aOrder['field'], $this->_pFields))
        {
            $aOrder['field'] = 'numleft';
        }
        if(strtolower($aOrder['dirrection']) != 'desc')
        {
            $aOrder['dirrection'] = 'ASC';
        }
        

        if($aType && $aType == GALERYITEM_TYPE_CATEGORY)
        {
            $Query = 'SELECT T.pid, T.numlevel, T.numleft, I.*, IC.file AS cover, IC.id AS coverid
                        FROM gallery_tree TP, ' . $this->_pTableName . ' I,gallery_tree T
                            LEFT JOIN  gallery_tree TC ON  TC.pid = T.id AND TC.id = (
                                            SELECT node_id
                                            FROM gallery_item
                                            WHERE node_id = TC.id
                                            AND `type`
                                            IN (\''.GALERYITEM_TYPE_IMAGE.'\', \''.GALERYITEM_TYPE_VIDEO.'\')
                                            LIMIT 1 )
                            LEFT JOIN  ' . $this->_pTableName . ' IC ON  IC.node_id =  TC.id AND IC.type IN(\''.GALERYITEM_TYPE_IMAGE.'\', \''.GALERYITEM_TYPE_VIDEO.'\')
                        WHERE
                            T.id = I.node_id
                            AND TP.id = ' . $aParentId . ' ';
        }
        else
        {
            $Query = 'SELECT T.pid, T.numlevel, T.numleft, F.*, I.*
                        FROM gallery_tree T, gallery_tree TP, ' . $this->_pTableName . ' I
                            LEFT JOIN fileinfo F ON F.id = I.id
                        WHERE
                            T.id = I.node_id
                            AND TP.id = ' . $aParentId . ' ';
        }
        
        if($aWithPepth)
        {
            $Query .= ' AND T.numleft>TP.numleft AND T.numleft < TP.numright ';
            if($aWithPepth !== true)
            {
                $Query .= ' AND T.numlevel <= TP.numlevel+' . (int)$aWithPepth . ' ';
            }
        }
        else
        {
            $Query .= ' AND T.pid = TP.id ';
        }
        
        if($aType)
        {
            if(!is_array($aType))
            {
                if(in_array($aType, array(GALERYITEM_TYPE_CATEGORY,GALERYITEM_TYPE_IMAGE,GALERYITEM_TYPE_VIDEO)))
                {
                    $Query .= ' AND I.type = \'' . $aType . '\' ';
                }
            }
            else
            {
                $aClearTypes = array();
                foreach($aType as $v)
                {
                    if(in_array($v, array(GALERYITEM_TYPE_CATEGORY,GALERYITEM_TYPE_IMAGE,GALERYITEM_TYPE_VIDEO)))
                    {
                        $aClearTypes[] = '\'' . $v .'\'';
                    }
                }
                $Query .= ' AND I.type IN (' . implode(',', $aClearTypes) . ') ';
            }
        }
        $Query .= ' GROUP BY I.id ';
        if(!is_array($aOrder))
        {
            $Query .= ' ORDER BY numleft ASC ';
        }
        else
        {
            if(isset($aOrder['field']) && in_array(strtolower($aOrder['field']), $this->_pFields))
            {
                if(!isset($aOrder['dirrection']))
                {
                    $aOrder['dirrection'] = 'asc';
                }
                else if(!in_array(strtolower($aOrder['dirrection']), array('asc', 'desc')))
                {
                    $aOrder['dirrection'] = 'asc';
                }
                $Query .= ' ORDER BY ' . $aOrder['field'] . ' ' . $aOrder['dirrection'] . ' ';
            }
            else
            {
                $Query .= ' ORDER BY numleft ASC ';
            }
        }
        
        if($aLimit)
        {
            if(is_array($aLimit))
            {
                $Query .= ' LIMIT ' . $aLimit[0] . ', ' . $aLimit[1];
            }
            else
            {
                $Query .= ' LIMIT ' . $aLimit;
            }
        }
        
        $Result = $this->db->query($Query);

        return $Result->result_array();
    }

    function getChildItemsCount($aParentId = 0, $aWithPepth = false, $aType = false)
    {
        $aParentId = (int)$aParentId;
        if(!$aParentId)
        {
            return false;
        }
        $Query = 'SELECT count(*) AS cnt
                    FROM gallery_tree T, ' . $this->_pTableName . ' I, gallery_tree TP
                    WHERE
                        T.id = I.node_id
                        AND TP.id = ' . $aParentId . ' ';

        if($aWithPepth)
        {
            $Query .= ' AND T.numleft>TP.numleft AND T.numleft < TP.numright ';
            if($aWithPepth !== true)
            {
                $Query .= ' AND T.numlevel <= TP.numlevel+' . (int)$aWithPepth . ' ';
            }
        }
        else
        {
            $Query .= ' AND T.pid = TP.id ';
        }

        if($aType)
        {
            if(!is_array($aType))
            {
                if(in_array($aType, array('category','image','video')))
                {
                    $Query .= ' AND I.type = \'' . $aType . '\' ';
                }
            }
            else
            {
                $aClearTypes = array();
                foreach($aType as $v)
                {
                    if(in_array($v, array('category','image','video')))
                    {
                        $aClearTypes[] = '\'' . $v .'\'';
                    }
                }
                $Query .= ' AND I.type IN (' . implode(',', $aClearTypes) . ') ';
            }
        }
        $Result = $this->db->query($Query);
        $Result = $Result->row_array();
        return $Result['cnt'];
    }
    
    public function saveItem($aData, $aFileInfo = false)
    {
        $ClearData = array();
        $this->_SaveFields($aData, $ClearData);
        
        $ClearData['modified'] = date('Y-m-d h:i:s', time());
        if(isset($ClearData['id']) && $ClearData['id'])
        {
            $InsertedId = $ClearData['id'];
            $aOldInfo = $this->getItem(array('id'=>$ClearData['id']));
            
            $this->db->where(array('id'=>$ClearData['id']));
            $this->db->update($this->_pTableName, $ClearData);
            if($aFileInfo)
            {
                $aFileInfo['id'] = $ClearData['id'];
                $ClearData = array();
                foreach($this->_pInfoFields as $v)
                {
                    $ClearData[$v] = $aFileInfo[$v];
                }
                $this->db->where(array('id'=>$ClearData['id']));
                $this->db->update($this->_pInfoTable, $ClearData);
            }
        }
        else
        {
            $ClearData['created'] = date('Y-m-d h:i:s', time());
            $aOldInfo = $ClearData;
            $this->db->insert($this->_pTableName, $ClearData);
            
            $InsertedId = $this->db->insert_id();

            $this->db->from($this->_pTableName);
            $this->db->select('MAX(orderfield) as max');
            $Res = $this->db->get();
            $Res = $Res->row_array();
            $this->db->query('UPDATE ' . $this->_pTableName .' SET orderfield = ' . ($Res['max']+1).' WHERE id=' . $InsertedId);
            if($aFileInfo)
            {
                $aFileInfo['id'] = $InsertedId;
                $ClearData = array();
                
                foreach($this->_pInfoFields as $v)
                {
                    $ClearData[$v] = $aFileInfo[$v];
                }
                $this->db->insert($this->_pInfoTable, $ClearData);
            }
        }
        if(!empty($_FILES['file']))
        {
            $this->saveFile($_FILES['file'], $InsertedId, $aData['type'], $aOldInfo['type']);
        }
        return $InsertedId;
    }

    public function deleteItem($aItemId = 0)
    {
        $FileData = $this->getItem('id = ' . (int)$aItemId);
        if($FileData)
        {
            $this->load->model('tree_model');
            if($FileData['node_id'])
            {
                $aNodeIds = $this->tree_model->deleteNode($FileData['node_id']);
                if($aNodeIds)
                {
                    foreach($aNodeIds as $v)
                    {
                        $ChildData = $this->getItem('node_id = ' . (int)$v);
                        $this->deleteItem($ChildData['id']);
                    }
                }
            }
            $this->db->delete($this->_pTableName, array('id'=>$aItemId));
            if(in_array($FileData['type'], array(GALERYITEM_TYPE_IMAGE, GALERYITEM_TYPE_VIDEO)))
            {
                $this->db->delete('fileinfo', array('id'=>$aItemId));
            }
            $this->unlinkFile($FileData);
        }
    }

    private function unlinkFile($aItemInfo)
    {    
        $UploadPath = realpath(BASEPATH.'../uploads/');
        
        switch ($aItemInfo['type'])
        {
            case GALERYITEM_TYPE_IMAGE:
            {
                @unlink($UploadPath . '/' . $aItemInfo['id'] . 'th_' . $aItemInfo['file']);
                @unlink($UploadPath . '/' . $aItemInfo['id'] . 'sm_' . $aItemInfo['file']);
                @unlink($UploadPath . '/' . $aItemInfo['id'] . 'md_' . $aItemInfo['file']);
                @unlink($UploadPath . '/' . $aItemInfo['id'] . 'or_' . $aItemInfo['file']);
            }break;
            case GALERYITEM_TYPE_VIDEO:
            {
                @unlink($UploadPath . '/' . $aItemInfo['id'] . 'or_' . $aItemInfo['file']);
                $Ext = pathinfo($UploadPath . '/' . $aItemInfo['id'] . 'or_' . $aItemInfo['file'], PATHINFO_EXTENSION);
                $aFileName = str_replace($Ext, 'jpg', $aItemInfo['file']);
                @unlink($UploadPath . '/' . $aItemInfo['id'] . 'th_' .  $aFileName);
                @unlink($UploadPath . '/' . $aItemInfo['id'] . 'or_' .  $aFileName);
                @unlink($UploadPath . '/' . $aItemInfo['id'] . 'sm_' . $aFileName);
                @unlink($UploadPath . '/' . $aItemInfo['id'] . 'md_' . $aFileName);
            }break;
        }

    }

    private function saveFile($aFileData, $aItemId, $aType, $aOldType)
    {
        $Extension = pathinfo($aFileData['name'], PATHINFO_EXTENSION);
        $UploadPath = realpath(BASEPATH.'../uploads/');
        
        $Name = generate_string(5);
        $Filename = $Name . '.' . $Extension;
        $aOldInfo = $this->getItem('id = '. $aItemId);

        if($aOldInfo['file'])
        {
            $aOldInfo['type'] = $aOldType;
            $this->unlinkFile($aOldInfo);
        }
        switch($aType)
        {
            case GALERYITEM_TYPE_IMAGE:
            {
                $this->load->library('cthumbnail');
                $this->load->helper('custom');
                
                $Size = getimagesize($_FILES['file']['tmp_name']);
                $OrigSize = array('width'=>$Size[0],'height'=> $Size[1]);

                $this->cthumbnail->init(false, false, 'file');
                $FilesOptions = array(
                    array(
                        'filename'=>$UploadPath . '/' . $aItemId . 'th_' . $Filename,
                        'width' => 37,
                        'height' => 36,
                        'quality'=>80,
                        'autofit'=>true,
                    ),
                    array(
                        'filename'=>$UploadPath . '/' . $aItemId . 'sm_' . $Filename,
                        'width' => 102,
                        'height' => 102,
                        'quality'=>80,
                        'autofit'=>true,
                    ),
                    array(
                        'filename'=>$UploadPath . '/' . $aItemId . 'md_' . $Filename,
                        'width' => 600,
                        'height' => 400,
                        'quality'=>80,
                        'autofit'=>true,
                    ),
                    array(
                        'filename'=>$UploadPath . '/' . $aItemId . 'or_' . $Filename,
                        'width' => $OrigSize['width'],
                        'height' => $OrigSize['height'],
                        'quality'=>100,
                        'autofit'=>true,
                    ),
                );
                $this->cthumbnail->save($FilesOptions);
            }break;
            case GALERYITEM_TYPE_VIDEO:
            {
                @move_uploaded_file($_FILES['file']['tmp_name'], $UploadPath . '/' . $aItemId . 'or_' . $Filename);
                $this->_generateVideoPreviews($Name, $aItemId, $UploadPath . '/' . $aItemId . 'or_' . $Filename, $UploadPath);
                $Filename = $Name.'.flv';
            }break;
        }
        $this->db->query('UPDATE ' . $this->_pTableName . ' SET file = \'' . $Filename .'\' WHERE id= ' . $aItemId);
    }

    private function _generateVideoPreviews($aShortName, $aItemId, $aSrc, $aPath)
    {
        exec($this->_pFfmpegPath.'ffmpeg -i '.$aSrc.' -an -ss 00:00:02 -r 1 -vframes 1 -s 37x36 -y -f mjpeg '.$aPath.'/'.$aItemId.'th_'.$aShortName.'.jpg');        
        exec($this->_pFfmpegPath.'ffmpeg -i '.$aSrc.' -an -ss 00:00:02 -r 1 -vframes 1 -s 102x102 -y -f mjpeg '.$aPath.'/'.$aItemId.'sm_'.$aShortName.'.jpg');
        exec($this->_pFfmpegPath.'ffmpeg -i '.$aSrc.' -an -ss 00:00:02 -r 1 -vframes 1 -s 600x400 -y -f mjpeg '.$aPath.'/'.$aItemId.'md_'.$aShortName.'.jpg');
        
        if($aSrc!= $aPath.'/'.$aItemId.'or_'.$aShortName.'.flv')
        {
            exec($this->_pFfmpegPath.'ffmpeg -i '.$aSrc.' -ab 96k -ar 44100 -b 128k -r 15 -y -s 640x480 -f flv '.$aPath.'/'.$aItemId.'or_'.$aShortName.'.flv');
            unlink($aSrc);
        }
    }
    public function getNearItems($aFileInfo, $aDirect, $aParent)
    {
        $Result = array();
        if($aFileInfo['node_id'])
        {
            $Query = 'SELECT NI.id FROM ' . $this->_pTableName.' NI, gallery_tree TP, gallery_tree C, gallery_tree N
                            WHERE 
                                TP.id='.$aParent.'
                                AND C.id = ' . $aFileInfo['node_id'].'
                                AND NI.node_id=N.id
                                AND N.numleft>TP.numleft
                                AND N.numleft<TP.numright
                                AND NI.type IN(\''.GALERYITEM_TYPE_IMAGE.'\',\''.GALERYITEM_TYPE_VIDEO.'\')
                                AND C.numleft<N.numleft';
            if($aDirect)
            {
                $Query .= ' AND N.pid=C.pid';
            }
            $Query .= ' ORDER BY N.numleft ASC LIMIT 1';
            
            $Res = $this->db->query($Query);
            $Res = $Res->row_array();
            if($Res)
            {
                $Result['next'] = $Res['id'];
            }
            else
            {
                $Query = 'SELECT NI.id FROM ' . $this->_pTableName.' NI, gallery_tree TP, gallery_tree C, gallery_tree N
                            WHERE
                                TP.id='.$aParent.'
                                AND C.id = ' . $aFileInfo['node_id'].'
                                AND NI.node_id=N.id
                                AND NI.type IN(\''.GALERYITEM_TYPE_IMAGE.'\',\''.GALERYITEM_TYPE_VIDEO.'\')
                                AND N.numleft>TP.numleft
                                AND N.numleft<TP.numright';
                if($aDirect)
                {
                    $Query .= ' AND N.pid=C.pid';
                }
                $Query .= ' ORDER BY N.numleft ASC LIMIT 1';
                $Res = $this->db->query($Query);
                $Res = $Res->row_array();
                $Result['next'] = $Res['id'];
            }
            $Query = 'SELECT NI.id FROM ' . $this->_pTableName.' NI, gallery_tree TP, gallery_tree C, gallery_tree N
                            WHERE
                                TP.id='.$aParent.'
                                AND C.id = ' . $aFileInfo['node_id'].'
                                AND NI.node_id=N.id
                                AND N.numleft>TP.numleft
                                AND N.numleft<TP.numright
                                AND NI.type IN(\''.GALERYITEM_TYPE_IMAGE.'\',\''.GALERYITEM_TYPE_VIDEO.'\')
                                AND C.numleft>N.numleft';
            if($aDirect)
            {
                $Query .= ' AND N.pid=C.pid';
            }
            $Query .= ' ORDER BY N.numleft DESC LIMIT 1';

            $Res = $this->db->query($Query);
            $Res = $Res->row_array();
            if($Res)
            {
                $Result['prev'] = $Res['id'];
            }
            else
            {
                $Query = 'SELECT NI.id FROM ' . $this->_pTableName.' NI, gallery_tree TP, gallery_tree C, gallery_tree N
                            WHERE
                                TP.id='.$aParent.'
                                AND C.id = ' . $aFileInfo['node_id'].'
                                AND NI.node_id=N.id
                                AND NI.type IN(\''.GALERYITEM_TYPE_IMAGE.'\',\''.GALERYITEM_TYPE_VIDEO.'\')
                                AND N.numleft>TP.numleft
                                AND N.numleft<TP.numright';
                if($aDirect)
                {
                    $Query .= ' AND N.pid=C.pid';
                }
                $Query .= ' ORDER BY N.numleft DESC LIMIT 1';
                $Res = $this->db->query($Query);
                $Res = $Res->row_array();
                $Result['prev'] = $Res['id'];
            }
        }
        else
        {
            $Query = 'SELECT id
                FROM ' . $this->_pTableName.'
                    WHERE
                        orderfield>' . $aFileInfo['orderfield'] .'
                        AND type IN(\''.GALERYITEM_TYPE_IMAGE.'\',\''.GALERYITEM_TYPE_VIDEO.'\')
                        AND node_id=0 ORDER BY orderfield ASC LIMIT 1';
            $Res = $this->db->query($Query);
            $Res = $Res->row_array();
            if($Res)
            {
                $Result['next'] = $Res['id'];
            }
            else
            {
                $Query = 'SELECT id
                FROM ' . $this->_pTableName.'
                    WHERE
                        type IN(\''.GALERYITEM_TYPE_IMAGE.'\',\''.GALERYITEM_TYPE_VIDEO.'\')
                        AND node_id=0 ORDER BY orderfield ASC LIMIT 1';
                $Res = $this->db->query($Query);
                $Res = $Res->row_array();
                $Result['next'] = $Res['id'];
            }
            $Query = 'SELECT id
                FROM ' . $this->_pTableName.'
                    WHERE
                        orderfield<' . $aFileInfo['orderfield'] .'
                        AND type IN(\''.GALERYITEM_TYPE_IMAGE.'\',\''.GALERYITEM_TYPE_VIDEO.'\')
                        AND node_id=0 ORDER BY orderfield DESC LIMIT 1';
            
            $Res = $this->db->query($Query);
            $Res = $Res->row_array();
            
            if($Res)
            {
                $Result['prev'] = $Res['id'];
            }
            else
            {
                $Query = 'SELECT id
                FROM ' . $this->_pTableName.'
                    WHERE
                        type IN(\''.GALERYITEM_TYPE_IMAGE.'\',\''.GALERYITEM_TYPE_VIDEO.'\')
                        AND node_id=0 ORDER BY orderfield DESC LIMIT 1';
                $Res = $this->db->query($Query);
                $Res = $Res->row_array();
                $Result['prev'] = $Res['id'];
            }

        }
        return $Result;
    }

    public function getSingleFileCount()
    {
        $this->db->select('count(*) AS cnt');
        $this->db->from($this->_pTableName);
        $this->db->where(array('node_id'=>'0'));
        $Res = $this->db->get();
        $Res = $Res->row_array();
        return $Res['cnt'];
    }

    public function getSingleFiles($aLimit = false , $aOrder = array('field'=>'id', 'dirrection'=>'asc'))
    {
        $this->db->from($this->_pTableName);
        if($aOrder['field'] == 'numleft')
        {
            $aOrder['field'] = 'orderfield';
        }
        $this->db->join('fileinfo', 'fileinfo.id = ' . $this->_pTableName.'.id' , 'left');
        $this->db->where(array(
            'node_id'=>'0'
        ));
        $this->db->where('type IN(\''.GALERYITEM_TYPE_IMAGE.'\',\''.GALERYITEM_TYPE_VIDEO.'\')');
        if($aLimit)
        {
            if(is_array($aLimit))
            {
                $this->db->limit($aLimit[1],$aLimit[0]);
            }
            else
            {
                $this->db->limit($aLimit);
            }
        }
        if(!is_array($aOrder))
        {
            $this->db->order_by($this->_pTableName.'.orderfield', 'ASC');
        }
        else
        {
            if(isset($aOrder['field']) && in_array(strtolower($aOrder['field']), $this->_pFields))
            {
                if(!isset($aOrder['dirrection']))
                {
                    $aOrder['dirrection'] = 'asc';
                }
                else if(!in_array(strtolower($aOrder['dirrection']), array('asc', 'desc')))
                {
                    $aOrder['dirrection'] = 'asc';
                }
                $this->db->order_by($this->_pTableName.'.'.$aOrder['field'], $aOrder['dirrection']);
            }
            else
            {
                $this->db->order_by($this->_pTableName.'.orderfield', 'ASC');
            }
        }
        $Res = $this->db->get();
        return $Res->result_array();
    }

    public function rearrangeItems($aMovedItem, $aTargetItem)
    {
        $this->db->query('UPDATE ' . $this->_pTableName.' SET orderfield = ' . $aTargetItem['orderfield'] . ' WHERE id = ' . $aMovedItem['id']);
        $this->db->query('UPDATE ' . $this->_pTableName.' SET orderfield = ' . $aMovedItem['orderfield'] . ' WHERE id = ' . $aTargetItem['id']);
    }
}