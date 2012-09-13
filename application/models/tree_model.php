<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** table 
 DROP TABLE `<entity>_tree`;
 CREATE TABLE IF NOT EXISTS `<entity>_tree` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `GROUP_ID` int(10) unsigned NOT NULL DEFAULT '0',
  `numleft` int(11) DEFAULT NULL,
  `numright` int(11) DEFAULT NULL,
  `numlevel` int(11) DEFAULT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`numleft`,`numright`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=502 ;
 */

/**
* Tree Model class
* @date: 25-July-2012
* @Purpose: This model contains all the functionalities for Tree.
* @filesource: application/models/tree_model.php
* @author:    Mike Vodolazkin
* @version: 0.0.1
* @revision:
**/


class Tree_model extends MY_Model
{
    private $_pGroupProperty = 'group_id';
    private $_pGroupID = 0;
    private $_pUseGroupProperty = false;
    private $_pRootJustCreated = true;
    
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        // Присваиваем значение таблицы с которой будет работать данная модель
        $this->_pTableName = 'gallery_tree';
        // Инициализируем список полей нашей таблицы
        $this->_pFields = array(
            "id"=>TRUE,
            "pid"=>TRUE,
            "GROUP_ID"=>TRUE,
            "numleft"=>TRUE,
            "numright"=>TRUE,
            "numlevel"=>TRUE,
            "enabled"=>TRUE,
        );
    }

    public function setTable($sTableName)
    {
        $this->_pTableName = $sTableName;
    }

    function setGroupParams($n_pGroupID=0, $s_pGroupPropertyName = 'group_id')
    {
       $this->_pGroupID = (int)$n_pGroupID;
       $this->_pGroupProperty = $s_pGroupPropertyName;
       $this->_pUseGroupProperty = true;
    }

    function set_pGroupID($n_pGroupID=0)
    {
       $this->_pGroupID = (int)$n_pGroupID;
    }

    //------------------------------------------------------------------------------------------------------------
    // _tree functions

    function getNodePath($nNodeID, &$aResult, $sAddWhere = ' AND numlevel>1 ')
    {
        $oRes = $this->db->query('SELECT pid FROM '.$this->_pTableName.' WHERE id='.$nNodeID.' '.$sAddWhere);
        $aRow = $oRes->row_array();
        $nParentNodeID = (int)isset($aRow['pid'])?$aRow['pid']:'';
        if(!$nParentNodeID) return;
        $aResult[] = $nParentNodeID;

        $this->getNodePath($nParentNodeID, $aResult, $sAddWhere);
    }

	function getNodeInfo($nNodeID, $sSelectFields = '*', $sAddQ = '')
	{

        if(empty($sSelectFields))
            $sSelectFields = '*';

        $sQuery = 'SELECT '.$sSelectFields.', FLOOR((numright-numleft) / 2) as child_cnt
                                FROM ' . $this->_pTableName . '
                                WHERE id='.((integer)$nNodeID) . $sAddQ . '
                                LIMIT 1';
        $oRes = $this->db->query($sQuery);

		return $oRes->row_array();
	}

    function getRootNodeID($n_pGroupID = 0)
    {
        if($this->_pUseGroupProperty)
        {
            $n_pGroupID = (int)$n_pGroupID;
            if($n_pGroupID==0) $n_pGroupID = $this->_pGroupID;
        }

        //Check and create root
        $oRes = $this->db->query('SELECT id
                       FROM '.$this->_pTableName.'
                       WHERE '.($this->_pUseGroupProperty?$this->_pGroupProperty.'="'.$n_pGroupID.'" AND':'').' pid=0');
        
        $aRes = $oRes->row_array();
        $nInsertID = isset($aRes['id'])?$aRes['id']:false;
        if( !$nInsertID )
        {
            $this->db->query('INSERT INTO '.$this->_pTableName.'
                          ('.($this->_pUseGroupProperty?$this->_pGroupProperty.',':'').' pid, numlevel, numleft, numright)
                           VALUES('.($this->_pUseGroupProperty?'"'.$n_pGroupID.'",':'').' 0, 0, 1, 2)');
            
            $nInsertID = $this->db->insert_id();
            $this->_pRootJustCreated = true;
        }
        else
        {
            $this->_pRootJustCreated = false;
        }

        return $nInsertID;
    }

    function insertNode($nParentNodeID = 0, $n_pGroupID = 0)
    {
        $nParentNodeID = (int)$nParentNodeID;
        if(!$nParentNodeID)
            $nParentNodeID = $this->getRootNodeID($n_pGroupID);

        if((int)$n_pGroupID==0)
            $n_pGroupID = $this->_pGroupID;

        return $this->insertNodeByParentId($nParentNodeID);
    }

	function insertNodeByParentId($nParentNodeID)
	{
		$aParentInfo = $this->getNodeInfo($nParentNodeID);
		if(!$aParentInfo)
		    return false;

        $sQuery = 'UPDATE '.$this->_pTableName.'
                    SET numright=numright+2, numleft=IF(numleft>'.$aParentInfo['numright'].', numleft+2, numleft)
                    WHERE numright>='.$aParentInfo['numright'].($this->_pUseGroupProperty?' AND '.$this->_pGroupProperty.'="'.$aParentInfo[$this->_pGroupProperty].'"':'');
        $this->db->query($sQuery);

		$sQuery = 'INSERT INTO '.$this->_pTableName.' (
                    '.($this->_pUseGroupProperty?$this->_pGroupProperty.',':'').' pid, numleft, numright, numlevel)
                  VALUES(' .($this->_pUseGroupProperty?'"'.$aParentInfo[$this->_pGroupProperty].'",':'').'
                    '.$nParentNodeID.','.($aParentInfo['numright']).', '.($aParentInfo['numright']+1).', '.($aParentInfo['numlevel']+1).')';
		$this->db->query($sQuery);
		return $this->db->insert_id();
	}

    function deleteNode($nNodeID)
    {
        $aNodeInfo = $this->getNodeInfo($nNodeID);
        if(!$aNodeInfo)
            return false;

        $oRes = $this->db->query('SELECT id FROM '.$this->_pTableName.'
                                   WHERE numleft>='.$aNodeInfo['numleft'].' AND numright<='.$aNodeInfo['numright'].
                                   ($this->_pUseGroupProperty?' AND '.$this->_pGroupProperty.'="'.$aNodeInfo[$this->_pGroupProperty].'"':'') );
        $aRes = $oRes->result_array();
        $aDeleteNodeIDs = array();
        foreach($aRes as $v)
        {
            $aDeleteNodeIDs[] = $v['id'];
        }

        $this->db->query('DELETE FROM '.$this->_pTableName.' WHERE numleft>='.$aNodeInfo['numleft'].' AND numright<='.$aNodeInfo['numright'].
                      ($this->_pUseGroupProperty?' AND '.$this->_pGroupProperty.'="'.$aNodeInfo[$this->_pGroupProperty].'"':'') );

        $this->db->query('UPDATE '.$this->_pTableName.'
                  SET numright=(numright-'.$aNodeInfo['numright'].'+'.$aNodeInfo['numleft'].'-1)
                  WHERE numright > '.$aNodeInfo['numright'].($this->_pUseGroupProperty?' AND '.$this->_pGroupProperty.'="'.$aNodeInfo[$this->_pGroupProperty].'"':'') );

        $this->db->query('UPDATE '.$this->_pTableName.'
                  SET numleft=(numleft-'.$aNodeInfo['numright'].'+'.$aNodeInfo['numleft'].'-1)
                  WHERE numleft > '.$aNodeInfo['numleft'].($this->_pUseGroupProperty?' AND '.$this->_pGroupProperty.'="'.$aNodeInfo[$this->_pGroupProperty].'"':'') );

        return $aDeleteNodeIDs;
    }

    function moveNodeUp($nNodeID)
    {
        //get node info
        $aNodeInfo = $this->getNodeInfo($nNodeID);
        if(!$aNodeInfo)
            return false;

        //get upper node info
        $sQuery = 'SELECT * FROM '.$this->_pTableName.'
                   WHERE numleft<'.$aNodeInfo['numleft'].' AND numlevel='.$aNodeInfo['numlevel'].' AND pid='.$aNodeInfo['pid'].' '.
                         ($this->_pUseGroupProperty?' AND '.$this->_pGroupProperty.'="'.$aNodeInfo[$this->_pGroupProperty].'"':'').'
                         ORDER BY numleft DESC LIMIT 1';
        $oRes = $this->db->query($sQuery);
        $aUpperNodeInfo = $oRes->row_array();
        
        if(!$aUpperNodeInfo)
            return false;

        if(($aUpperNodeInfo['numright'] - $aUpperNodeInfo['numleft']) == 1 &&
           ($aNodeInfo['numright'] - $aNodeInfo['numleft']) == 1 )
            $this->ChangePosition($aNodeInfo, $aUpperNodeInfo);
        else
           $this->ChangePosiotionAll($aNodeInfo, $aUpperNodeInfo, 'before');

        return true;
    }

    function moveNodeDown($nNodeID)
    {

        //get node info
        $aNodeInfo = $this->getNodeInfo($nNodeID);
        if(!$aNodeInfo)
            return false;

        //get lower node info
        $sQuery = 'SELECT * FROM '.$this->_pTableName.'
                   WHERE numleft>'.$aNodeInfo['numleft'].' AND numlevel='.$aNodeInfo['numlevel'].' AND pid='.$aNodeInfo['pid'].' '.
                         ($this->_pUseGroupProperty?' AND '.$this->_pGroupProperty.'="'.$aNodeInfo[$this->_pGroupProperty].'"':'').'
                   ORDER BY numleft ASC LIMIT 1';
        $oRes = $this->db->query($sQuery);
        $aLowerNodeInfo = $oRes->row_array();
        if(!$aLowerNodeInfo)
            return false;

        if(($aLowerNodeInfo['numright'] - $aLowerNodeInfo['numleft']) == 1 &&
           ($aNodeInfo['numright'] - $aNodeInfo['numleft']) == 1)
            $this->ChangePosition($aNodeInfo, $aLowerNodeInfo);
        else
           $this->ChangePosiotionAll($aNodeInfo, $aLowerNodeInfo, 'after');

        return true;
    }

    function moveAll($nNodeID, $nNewParentID)
    {

        $aNodeinfo = $this->getNodeInfo($nNodeID, 'numleft, numright, numlevel');
        if (!$aNodeinfo) {
            return FALSE;
        }
        $nLeftID  = $aNodeinfo['numleft'];
        $nRightID = $aNodeinfo['numright'];
        $nLevel   = $aNodeinfo['numlevel'];

        $aNodeinfo = $this->getNodeInfo($nNewParentID, 'numleft, numright, numlevel');
        if (!$aNodeinfo) {
            return FALSE;
        }
        $nLeftIDParent  = $aNodeinfo['numleft'];
        $nRightIDParent = $aNodeinfo['numright'];
        $nLevelParent   = $aNodeinfo['numlevel'];

        if ($nNodeID == $nNewParentID || $nLeftID == $nLeftIDParent || ($nLeftIDParent >= $nLeftID && $nLeftIDParent <= $nRightID) || ($nLevel == $nLevelParent+1 && $nLeftID > $nLeftIDParent && $nRightID < $nRightIDParent)) {
            return FALSE;
        }

        $sQuery = '';

        if ($nLeftIDParent < $nLeftID && $nRightIDParent > $nRightID && $nLevelParent < $nLevel - 1) {
            $sQuery = 'UPDATE ' . $this->_pTableName . ' SET '
            . 'numlevel = CASE WHEN numleft  BETWEEN ' . $nLeftID . ' AND ' . $nRightID . ' THEN ' . 'numlevel'.sprintf('%+d', -($nLevel-1)+$nLevelParent) . ' ELSE numlevel END, '
            . 'numright = CASE WHEN numright BETWEEN ' . ($nRightID+1) . ' AND ' . ($nRightIDParent-1) . ' THEN numright-' . ($nRightID-$nLeftID+1) . ' '
            . 'WHEN numleft BETWEEN ' . $nLeftID . ' AND ' . $nRightID . ' THEN numright+' . ((($nRightIDParent-$nRightID-$nLevel+$nLevelParent)/2)*2+$nLevel-$nLevelParent-1) . ' ELSE numright END, '
            . 'numleft = CASE WHEN numleft BETWEEN ' . ($nRightID+1) . ' AND ' . ($nRightIDParent-1) . ' THEN numleft-' . ($nRightID-$nLeftID+1) . ' '
            . 'WHEN numleft BETWEEN ' . $nLeftID . ' AND ' . $nRightID . ' THEN numleft+' . ((($nRightIDParent-$nRightID-$nLevel+$nLevelParent)/2)*2+$nLevel-$nLevelParent-1) . ' ELSE numleft END '
            . 'WHERE numleft BETWEEN ' . ($nLeftIDParent+1) . ' AND ' . ($nRightIDParent-1);
        } elseif ($nLeftIDParent < $nLeftID) {
            $sQuery = 'UPDATE ' . $this->_pTableName . ' SET '
            . 'numlevel = CASE WHEN numleft BETWEEN ' . $nLeftID . ' AND ' . $nRightID . ' THEN numlevel '.sprintf('%+d', -($nLevel-1)+$nLevelParent) . ' ELSE numlevel END, '
            . 'numleft = CASE WHEN numleft BETWEEN ' . $nRightIDParent . ' AND ' . ($nLeftID-1) . ' THEN numleft+' . ($nRightID-$nLeftID+1) . ' '
            . 'WHEN numleft BETWEEN ' . $nLeftID . ' AND ' . $nRightID . ' THEN numleft-' . ($nLeftID-$nRightIDParent) . ' ELSE numleft END, '
            . 'numright = CASE WHEN numright BETWEEN ' . $nRightIDParent . ' AND ' . $nLeftID . ' THEN numright+' . ($nRightID-$nLeftID+1) . ' '
            . 'WHEN numright BETWEEN ' . $nLeftID . ' AND ' . $nRightID . ' THEN numright-' . ($nLeftID-$nRightIDParent) . ' ELSE numright END '
            . 'WHERE (numleft BETWEEN ' . $nLeftIDParent . ' AND ' . $nRightID. ' '
            . 'OR numright BETWEEN ' . $nLeftIDParent . ' AND ' . $nRightID . ')';
        } else {
            $sQuery = 'UPDATE ' . $this->_pTableName . ' SET '
            . 'numlevel = CASE WHEN numleft BETWEEN ' . $nLeftID . ' AND ' . $nRightID . ' THEN numlevel '.sprintf('%+d', -($nLevel-1)+$nLevelParent) . ' ELSE numlevel END, '
            . 'numleft = CASE WHEN numleft BETWEEN ' . $nRightID . ' AND ' . $nRightIDParent . ' THEN numleft-' . ($nRightID-$nLeftID+1) . ' '
            . 'WHEN numleft BETWEEN ' . $nLeftID . ' AND ' . $nRightID . ' THEN numleft+' . ($nRightIDParent-1-$nRightID) . ' ELSE numleft END, '
            . 'numright = CASE WHEN numright BETWEEN ' . ($nRightID+1) . ' AND ' . ($nRightIDParent-1) . ' THEN numright-' . ($nRightID-$nLeftID+1) . ' '
            . 'WHEN numright BETWEEN ' . $nLeftID . ' AND ' . $nRightID . ' THEN numright+' . ($nRightIDParent-1-$nRightID) . ' ELSE numright END '
            . 'WHERE (numleft BETWEEN ' . $nLeftID . ' AND ' . $nRightIDParent . ' '
            . 'OR numright BETWEEN ' . $nLeftID . ' AND ' . $nRightIDParent . ')';
        }
        $this->db->query($sQuery);
        if($this->db->affected_rows())
        {
            $this->db->query("UPDATE $this->_pTableName SET pid=$nNewParentID WHERE id=$nNodeID LIMIT 1");
        }

        return TRUE;
    }

    function ChangePosition($aFirstNode, $aSecondNode)
    {
        $sQuery = 'UPDATE '.$this->_pTableName.'
                    SET
                        numleft='.$aFirstNode['numleft'].',
                        numright='.$aFirstNode['numright'].',
                        numlevel='.$aFirstNode['numlevel'].',
                        pid='.$aFirstNode['pid'].'
                    WHERE id='.$aSecondNode['id'];
        $this->db->query($sQuery);

        $sQuery = 'UPDATE '.$this->_pTableName.'
                    SET
                        numleft='.$aSecondNode['numleft'].',
                        numright='.$aSecondNode['numright'].',
                        numlevel='.$aSecondNode['numlevel'].',
                        pid = '.$aSecondNode['pid'].'
                    WHERE id='.$aFirstNode['id'];
        $this->db->query($sQuery);
    }

    function ChangePosiotionAll($aFirstNode, $aSecondNode, $sPosition='after')
    {
        $leftId1 = $aFirstNode['numleft'];
        $rightId1 = $aFirstNode['numright'];
        $level1 = $aFirstNode['numlevel'];

        $leftId2 = $aSecondNode['numleft'];
        $rightId2 = $aSecondNode['numright'];
        $level2 = $aSecondNode['numlevel'];

        if ('before' == $sPosition) {
            if ($leftId1 > $leftId2) {
                $sQuery = 'UPDATE '.$this->_pTableName.' SET
                numright = CASE WHEN numleft BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN numright - ' . ($leftId1 - $leftId2).'
                WHEN numleft BETWEEN ' . $leftId2 . ' AND ' . ($leftId1 - 1) . ' THEN numright +  ' . ($rightId1 - $leftId1 + 1) . ' ELSE numright END,
                numleft = CASE WHEN numleft BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN numleft - ' . ($leftId1 - $leftId2).'
                WHEN numleft BETWEEN ' . $leftId2 . ' AND ' . ($leftId1 - 1) . ' THEN numleft + ' . ($rightId1 - $leftId1 + 1) . ' ELSE numleft END
                WHERE numleft BETWEEN ' . $leftId2 . ' AND ' . $rightId1;
            } else {
                $sQuery = 'UPDATE '.$this->_pTableName.' SET
                  numright = CASE WHEN numleft BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN numright + ' . (($leftId2 - $leftId1) - ($rightId1 - $leftId1 + 1)).'
                  WHEN numleft BETWEEN ' . ($rightId1 + 1) . ' AND ' . ($leftId2 - 1) . ' THEN numright - ' . (($rightId1 - $leftId1 + 1)) . ' ELSE numright END,
                  numleft = CASE WHEN numleft BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN numleft + ' . (($leftId2 - $leftId1) - ($rightId1 - $leftId1 + 1)).'
                  WHEN numleft BETWEEN ' . ($rightId1 + 1) . ' AND ' . ($leftId2 - 1) . ' THEN numleft - ' . ($rightId1 - $leftId1 + 1) . ' ELSE numleft END
                  WHERE numleft BETWEEN ' . $leftId1 . ' AND ' . ($leftId2 - 1);
            }
        }
        if ('after' == $sPosition) {
            if ($leftId1 > $leftId2) {
                $sQuery = 'UPDATE '.$this->_pTableName.' SET
                  numright = CASE WHEN numleft BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN numright - ' . ($leftId1 - $leftId2 - ($rightId2 - $leftId2 + 1)).'
                  WHEN numleft BETWEEN ' . ($rightId2 + 1) . ' AND ' . ($leftId1 - 1) . ' THEN numright +  ' . ($rightId1 - $leftId1 + 1) . ' ELSE numright END,
                  numleft = CASE WHEN numleft BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN numleft - ' . ($leftId1 - $leftId2 - ($rightId2 - $leftId2 + 1)).'
                  WHEN numleft BETWEEN ' . ($rightId2 + 1) . ' AND ' . ($leftId1 - 1) . ' THEN numleft + ' . ($rightId1 - $leftId1 + 1) . ' ELSE numleft END
                  WHERE numleft BETWEEN ' . ($rightId2 + 1) . ' AND ' . $rightId1;
            } else {
                $sQuery = 'UPDATE '.$this->_pTableName.' SET
                   numright = CASE WHEN numleft BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN numright + ' . ($rightId2 - $rightId1).'
                   WHEN numleft BETWEEN ' . ($rightId1 + 1) . ' AND ' . $rightId2 . ' THEN numright - ' . (($rightId1 - $leftId1 + 1)).' ELSE numright END,
                   numleft = CASE WHEN numleft BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN numleft + ' . ($rightId2 - $rightId1).'
                   WHEN numleft BETWEEN ' . ($rightId1 + 1) . ' AND ' . $rightId2 . ' THEN numleft - ' . ($rightId1 - $leftId1 + 1) . ' ELSE numleft END
                   WHERE numleft BETWEEN ' . $leftId1 . ' AND ' . $rightId2;
            }
        }

        if(isset($sQuery))
        {
            $this->db->query($sQuery);
            return true;
        }
        return false;
    }

    function toggleNodeEnabled($nNodeID, $bToggleChildNodes = false, $bReturnToggledChildNodes = false, &$aToggledInfo = array())
    {
        $aNodeInfo = $this->getNodeInfo((int)$nNodeID);

        if(!$aNodeInfo)
            return false;

        if($bToggleChildNodes)
        {
            $this->db->query('UPDATE '.$this->_pTableName.' SET enabled='.($aNodeInfo['enabled']=='Y'?'"N"':'"Y"').'
                           WHERE numleft>='.$aNodeInfo['numleft'].' AND numright<='.$aNodeInfo['numright'].
                                 ($this->_pUseGroupProperty?' AND '.$this->_pGroupProperty.'="'.$aNodeInfo[$this->_pGroupProperty].'"':'') );

            if($bReturnToggledChildNodes)
            {
                $aToggledInfo['toggled'] = array();
                $aToggledInfo['status']  = ($aNodeInfo['enabled']=='Y'?0:1);
                $oRes = $this->db->query('SELECT id FROM '.$this->_pTableName.'
                           WHERE numleft>='.$aNodeInfo['numleft'].' AND numright<='.$aNodeInfo['numright'].
                                 ($this->_pUseGroupProperty?' AND '.$this->_pGroupProperty.'="'.$aNodeInfo[$this->_pGroupProperty].'"':'') );
                $aRes = $oRes->result_array();
                foreach($aRes as $v)
                {
                    $aToggledInfo['toggled'][] = $v['id'];
                }
            }
        }
        else
        {
            $this->db->query('UPDATE '.$this->_pTableName.' SET enabled = IF(enabled="Y","N","Y")
                           WHERE id='.((int)$nNodeID).'
                           '.($this->_pUseGroupProperty?' AND '.$this->_pGroupProperty.'="'.$aNodeInfo[$this->_pGroupProperty].'"':'').' LIMIT 1');
        }

        return true;
    }
}