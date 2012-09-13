<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define('IMAGE_PREFIX_THUMP', 'th');
define('IMAGE_PREFIX_SMALL', 'sm');
define('IMAGE_PREFIX_ORIGINAL', 'or');
define('IMAGE_PREFIX_MEDIUM', 'md');
define('GALERYITEM_TYPE_CATEGORY', 'category');
define('GALERYITEM_TYPE_IMAGE', 'image');
define('GALERYITEM_TYPE_VIDEO', 'video');
define('GALERYITEM_FILES_PER_PAGE', 16);
/**
* Gallery Controller class
* @date: 25-July-2012
* @Purpose: This controller handles all the Gallery functionalities.
* @filesource: application/controllers/gallery.php
* @author:  Mike Vodolazkin
* @version: 0.0.2
* @revision:
**/
class Gallery extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('tree_model');
        $this->load->model('galleryitem_model');
    }

    /**
     * Вывод основной страницы галереи
     * 
     * @param int $aParent - id родительской категории
     * @return void
     */
    public function index($aParent = 0)
    {
        $aParent = (int)$aParent;
        //Проверка на существование коренного элемента дерева
        if(!$aParent)
        {
            $aParent = $this->tree_model->getRootNodeID();
        }
        
        $this->_pData['parent'] = $aParent;
        return $this->display('gallery_index');
    }

    /**
     *
     * Вывод списка файлов
     *
     * @param int $aParentId - id родительской категории
     *
     * @return void
     *
     */
    public function filelist($aParentId = 0)
    {
        $aParentId = (int)$aParentId;

        $Direct = $this->input->post('direct', true);
        $OrderField = $this->input->post('order');
        $OrderDir = $this->input->post('dirrection');
        if($Direct)
        {
            $Depth = false;
        }
        else
        {
            $Depth = true;
        }

        $Page = $this->input->post('page', TRUE);
        if($aParentId)
        {
            $Count = $this->galleryitem_model->getChildItemsCount($aParentId, $Depth, array(GALERYITEM_TYPE_IMAGE, GALERYITEM_TYPE_VIDEO));
            if($Page>ceil($Count/GALERYITEM_FILES_PER_PAGE))
            {
                $Page = ceil($Count/GALERYITEM_FILES_PER_PAGE)>0?ceil($Count/GALERYITEM_FILES_PER_PAGE):1;
            }
            if($Page == 1)
            {
                $Limit = GALERYITEM_FILES_PER_PAGE;
            }
            else
            {
                $Limit = array(
                    $Page*GALERYITEM_FILES_PER_PAGE-GALERYITEM_FILES_PER_PAGE,
                    GALERYITEM_FILES_PER_PAGE
                );
            }
            $this->_pData['paginator'] = get_paginator($Count, $Page, GALERYITEM_FILES_PER_PAGE, 3, 'ppages');
            
            $this->_pData['files'] = $this->galleryitem_model->getChildItems($aParentId, $Depth, array(GALERYITEM_TYPE_IMAGE, GALERYITEM_TYPE_VIDEO), $Limit, array('field'=>$OrderField, 'dirrection'=>$OrderDir));
        }
        else
        {
            $Count = $this->galleryitem_model->getSingleFileCount();
            if($Page>ceil($Count/GALERYITEM_FILES_PER_PAGE))
            {
                $Page = ceil($Count/GALERYITEM_FILES_PER_PAGE)>0?ceil($Count/GALERYITEM_FILES_PER_PAGE):1;
            }
            if($Page == 1)
            {
                $Limit = GALERYITEM_FILES_PER_PAGE;
            }
            else
            {
                $Limit = array(
                    $Page*GALERYITEM_FILES_PER_PAGE-GALERYITEM_FILES_PER_PAGE,
                    GALERYITEM_FILES_PER_PAGE
                );
            }
            $this->_pData['paginator'] = get_paginator($Count, $Page, GALERYITEM_FILES_PER_PAGE, 3, 'epages');

            $this->_pData['files'] = $this->galleryitem_model->getSingleFiles($Limit, array('field'=>$OrderField, 'dirrection'=>$OrderDir));
        }
        $this->_pData['parent'] = $aParentId;
        $this->_pData['direct'] = $Direct;
        $this->_pData['field'] = $OrderField;
        $this->_pData['dir'] = $OrderDir;
        $this->load->view('gallery_filelist',$this->_pData);
    }
    
    public function categorylist($aParentId = 0)
    {
        $aParentId = (int)$aParentId;
        if(!$aParentId)
        {
            return false;
        }
        
        $NodePath = array();
        $this->tree_model->getNodePath($aParentId, $NodePath, false);
        $this->_pData['breadcrumbs'] = array();
        if(!empty ($NodePath))
        {
            $this->_pData['breadcrumbs'] = $this->galleryitem_model->getItems('node_id IN(' . implode(',', $NodePath) . ')', array('field'=>'node_id'));
        }

        $this->_pData['current'] = $this->galleryitem_model->getItems('node_id = ' . $aParentId . '');
        $this->_pData['categories'] = $this->galleryitem_model->getChildItems($aParentId, false, GALERYITEM_TYPE_CATEGORY);
        
        $this->load->view('gallery_categorylist', $this->_pData);
    }
    
    public function savefile()
    {
        if(!$this->input->post('id', true))
        {
            $ItemData = array();
            $Pid = $this->input->post('pid');
            if($Pid)
            {
                $ItemData['node_id'] = $this->tree_model->insertNodeByParentId($Pid);
            }
            else
            {
                $ItemData['node_id'] = 0;
            }
            $ItemData['title'] = $this->input->post('title');
            $ItemData['description'] = $this->input->post('description');
            
            $FileData['format'] = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            if(preg_match('/image.*/', $_FILES['file']['type']))
            {
                $ItemData['type'] = GALERYITEM_TYPE_IMAGE;
            }
            else
            {
                $ItemData['type'] = GALERYITEM_TYPE_VIDEO;
            }
            
            $FileData['size'] = round($_FILES['file']['size']/1000);
            $nItemId = $this->galleryitem_model->saveItem($ItemData, $FileData);
        }
        else
        {
            $ItemData = array();
            $Pid = $this->input->post('pid');
            
            $ItemData['id'] = $this->input->post('id', true);
            $OldInfo = $this->galleryitem_model->getItem(array('id'=>$ItemData['id']));
            
            if($Pid)
            {
                $NodeInfo = $this->tree_model->getNodeInfo($OldInfo['node_id']);
                
                if(!$OldInfo['node_id'])
                {
                    $ItemData['node_id'] = $this->tree_model->insertNodeByParentId($Pid);
                }
                else if($NodeInfo['pid'] != $Pid)
                {
                    $this->tree_model->moveAll($NodeInfo['id'], $Pid);
                }
            }
            else
            {
                $ItemData['node_id'] = 0;
            }
            
            $ItemData['title'] = $this->input->post('title');
            $ItemData['description'] = $this->input->post('description');
            if(!empty ($_FILES['file']))
            {
                $FileData['format'] = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                if(preg_match('/image.*/', $_FILES['file']['type']))
                {
                    $ItemData['type'] = GALERYITEM_TYPE_IMAGE;
                }
                else
                {
                    $ItemData['type'] = GALERYITEM_TYPE_VIDEO;
                }
                $FileData['size'] = round($_FILES['file']['size']/1000);
            }
            else
            {
                $FileData = false;
            }
            
            $nItemId = $this->galleryitem_model->saveItem($ItemData, $FileData);
        }
        
        die();
    }
    
    public function deleteitem()
    {
        $FileId = (int)$this->input->post('item_id', true);
        $this->galleryitem_model->deleteItem($FileId);
    }
    
    public function savecategory()
    {
        $CategoryData = array();
        $CategoryData['id'] = $this->input->post('id', true);
        $CategoryData['pid'] = $this->input->post('pid', true);
        $CategoryData['title'] = $this->input->post('title', true);
        $CategoryData['description'] = $this->input->post('description', true);
        $Resposne = array();
        $Errors = array();
        if(!$CategoryData['title'])
        {
            $Resposne['error'] = 1;
            $Resposne['errors'][] = array('field'=>'title', 'message'=>'Enter title');
        }
        if(empty($Resposne['errors']))
        {
            if(!$CategoryData['pid'])
            {
                $CategoryData['pid'] = $this->tree_model->getRootNodeId();
            }
            if(!$CategoryData['id'])
            {
                $CategoryData['node_id'] = $this->tree_model->insertNodeByParentId($CategoryData['pid']);
            }
            else
            {
                $OldCategoryData = $this->galleryitem_model->getItem(array('id'=>$CategoryData['id']));
                $NodeData = $this->tree_model->getNodeInfo($OldCategoryData['node_id']);
                if($NodeData['pid'] != $CategoryData['pid'])
                {
                    $this->tree_model->moveAll($OldCategoryData['node_id'], $CategoryData['pid']);
                }
            }
            
            $CategoryData['type'] = GALERYITEM_TYPE_CATEGORY;
            $this->galleryitem_model->saveItem($CategoryData);
            $Resposne['error'] = 0;
        }
        echo json_encode($Resposne);
        die;
    }
        
    public function categoryform($aCategoryId = false)
    {
        $aCategoryId = (int)$aCategoryId;
        if($aCategoryId)
        {
            $CategoryData = $this->galleryitem_model->getItem('id = ' . $aCategoryId);
            if(!$CategoryData)
            {
                $this->_pData['error'] = 'Category not found';
            }
            else
            {
                if($CategoryData['type'] == GALERYITEM_TYPE_CATEGORY)
                {
                    $Node = $this->tree_model->getNodeInfo($CategoryData['node_id'], 'pid');
                    $CategoryData['pid'] = $Node['pid'];
                    $this->_pData['categoryinfo'] = $CategoryData;
                }
                else
                {
                    $this->_pData['error'] = 'Category not found';
                }
            }
        }
        $RootId = $this->tree_model->getRootNodeID();
        $this->_pData['categories'] = $this->galleryitem_model->getChildItems($RootId, true, GALERYITEM_TYPE_CATEGORY);
        $this->load->view('gallery_categoryform', $this->_pData);
    }

    public function fileform($nFileId = false)
    {
        $nFileId = (int)$nFileId;
        if($nFileId)
        {
            $FileData = $this->galleryitem_model->getItem('id = ' . $nFileId);
            if(!$FileData)
            {
                $this->_pData['error'] = 'File not found';
            }
            else
            {
                if(in_array($FileData['type'], array(GALERYITEM_TYPE_VIDEO, GALERYITEM_TYPE_IMAGE)))
                {
                    if($FileData['node_id'])
                    {
                        $Node = $this->tree_model->getNodeInfo($FileData['node_id'], 'pid');
                        $FileData['pid'] = $Node['pid'];
                    }
                    else
                    {
                        $FileData['pid'] = 0;
                    }
                    $this->_pData['fileinfo'] = $FileData;
                }
                else
                {
                    $this->_pData['error'] = 'File not found';
                }
            }
        }
        $RootId = $this->tree_model->getRootNodeID();
        $this->_pData['categories'] = $this->galleryitem_model->getChildItems($RootId, true, GALERYITEM_TYPE_CATEGORY);        
        $this->load->view('gallery_fileform', $this->_pData);
    }
    public function deleteitems()
    {
        $Items = $this->input->post('items', true);
        foreach($Items as $v)
        {
            $this->galleryitem_model->deleteItem((int)$v);
        }
    }
    public function moveitems()
    {
        $Items = $this->input->post('items', true);
        $Parent = $this->input->post('pid', true);        
        foreach($Items as $v)
        {
            $ItemData = $this->galleryitem_model->getItem(array('id'=>$v));
            if($Parent)
            {
                if(!$ItemData['node_id'])
                {
                    $ItemData['node_id'] = $this->tree_model->insertNodeByParentId($Parent);
                    
                }
                $this->tree_model->moveAll((int)$ItemData['node_id'], (int)$Parent);
            }
            else
            {
                if($ItemData['node_id'] && in_array($ItemData['type'], array(GALERYITEM_TYPE_IMAGE, GALERYITEM_TYPE_VIDEO)))
                {
                    $this->tree_model->deleteNode($ItemData['node_id']);
                    $ItemData['node_id'] = 0;
                }
            }
            $this->galleryitem_model->saveItem($ItemData);
        }
    }

    public function setparentform()
    {
        $RootId = $this->tree_model->getRootNodeID();
        $this->_pData['categories'] = $this->galleryitem_model->getChildItems($RootId, true, GALERYITEM_TYPE_CATEGORY);

        $this->load->view('gallery_parentform', $this->_pData);
    }

    public function viewitem($aItemId = 0)
    {
        $aItemId = (int)$aItemId;
        if(!$aItemId)
        {
            return false;
        }
        $Direct = $this->input->post('direct', true);
        $Parent = $this->input->post('parent', true);
        $this->_pData['filedata'] = $this->galleryitem_model->getItem(array('id'=>$aItemId));
        $Nears = $this->galleryitem_model->getNearItems($this->_pData['filedata'], $Direct, $Parent);
        $data = array();
        $data['title'] = $this->_pData['filedata']['title'];
        $data['previd'] = $Nears['prev'];
        $data['nextid'] = $Nears['next'];
        switch($this->_pData['filedata']['type'])
        {
            case GALERYITEM_TYPE_IMAGE:
            {
                $data['html'] =  $this->load->view('gallery_viewimage', $this->_pData, true);
                echo json_encode($data);
            }break;
            case GALERYITEM_TYPE_VIDEO:
            {
                $data['html'] =  $this->load->view('gallery_viewvideo', $this->_pData, true);
                echo json_encode($data);
            }break;
            default:
            {
                return false;
            }
        }
    }

    public function rearrangeitems()
    {
        $MovedNodeId = (int)$this->input->post('currentid');
        $TargetNodeId = (int)$this->input->post('targettid');
        $MovedItem = $this->galleryitem_model->getItem(array('id'=>$MovedNodeId));
        $TargetItem = $this->galleryitem_model->getItem(array('id'=>$TargetNodeId));
        
        $CurrentNode = $this->tree_model->getNodeInfo($MovedItem['node_id']);
        $TargetNode = $this->tree_model->getNodeInfo($TargetItem['node_id']);
        $Result = array();
        if($CurrentNode && $TargetNode)
        {
            if($CurrentNode['pid'] != $TargetNode['pid'])
            {
                $Result['error'] = 1;
            }
            else
            {
                $Position = $CurrentNode['numleft']<$TargetNode['numleft']?'after':'before';
                if(!$this->tree_model->ChangePosiotionAll($CurrentNode, $TargetNode,$Position))
                {
                    $Result['error'] = 1;
                }
                else
                {
                    $Result['error'] = 0;
                }
            }
        }
        else
        {
            $this->galleryitem_model->rearrangeItems($MovedItem, $TargetItem);
            $Result['error'] = 0;
        }
        echo json_encode($Result);
    }
}