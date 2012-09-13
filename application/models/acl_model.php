<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* ACL Model class
* @date: 16-August-2012
* @Purpose: This model contains all the functionalities for ACL
* @filesource: application/models/acl_model.php
* @author:    Mike Vodolazkin
* @version: 0.0.1
* @revision:
**/

class Acl_model extends MY_Model
{
    private $_pRolesTable;
    private $_pUserTable;
    private $_pRolePermissionTable;

    private $_pRolesFields;
    private $_pUserFields;
    private $_pRolePermissionFields;

    private $_pAllPermissios;
    
    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        // Присваиваем значение таблицы с которой будет работать данная модель
        $this->_pTableName = 'permissions';
        $this->_pRolesTable = 'roles';
        $this->_pUserTable = 'users';


        $this->_pRolePermissionTable = 'role_permissions';
        // Инициализируем список полей нашей таблицы
        $this->_pFields = array(
            "id"=>TRUE,
            "parent"=>TRUE,
            "name"=>TRUE,
            "description"=>TRUE,
        );
        $this->_pRolesFields = array(
            'id'=>true,
            'name'=>false,
            'description'=>false
        );

        $this->_pUserFields = array(
            'id'=>true,
            'role_id'=>false,
            'first_name'=>false,
            'last_name'=>false,
            'email'=>false,
            'login'=>false,
            'password'=>false,
            'created_on'=>false,
        );

        $this->_pRolePermissionFields = array(
            'id'=>true,
            'role_id'=>true,
            'permission_id'=>true,
            "status"=>TRUE,
        );
        
        $this->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
        $this->_pAllPermissios = $this->getPermissions();
    }

    public function getRolePermissions($aRoleId = false)
    {
        if(!$aRoleId)
        {
            return false;
        }
        
        $this->db->from($this->_pTableName);
        $this->db->join($this->_pRolePermissionTable, $this->_pTableName . '.id = ' . $this->_pRolePermissionTable.'.permission_id');
        $this->db->where(array($this->_pRolePermissionTable.'.role_id'=>$aRoleId));
        $this->db->order_by('name', 'ASC');
        $Res = $this->db->get();
        
        return $Res->result_array();
    }

    public function savePermission($aPermissionData = NULL)
    {
        $this->_PrepareCustomFields($aPermissionData, $this->_pFields, $aPermissionData);
        if((int)$aPermissionData['id'])
        {
            $this->db->where(array('id'=>$aPermissionData['id']));
            $this->db->update($this->_pTableName, $aPermissionData);
            return $this->db->affected_rows();
        }
        else
        {
            unset($aPermissionData['id']);
            $this->db->insert($this->_pTableName, $aPermissionData);
            return $this->db->insert_id();
        }
        @$this->cache->delete('permissions_all');
    }

    public function deletePermission($aId = false)
    {
        $aId = (int)$aId;
        if(!$aId)
        {
            return false;
        }
        $this->db->from($this->_pTableName);
        $this->db->where(array('parent'=>$aId));
        $Res = $this->db->get();
        $aResult = $Res->result_array();
        foreach($aResult as $v)
        {
            $this->deletePermission($v['id']);
        }
        $this->db->from($this->_pTableName);
        $this->db->where(array('id'=>$aId));
        $this->db->delete();

        $this->db->from($this->_pRolePermissionTable);
        $this->db->where(array('permission_id'=>$aId));
        $this->db->delete();
        @$this->cache->delete('permissions_all');
        $Roles = $this->getRoles();
        foreach($Roles as $v)
        {
            @$this->cache->delete('permissions_'.$v['id']);
        }
    }

    public function getPermissions()
    {
        if(!$Permissions = $this->cache->get('permissions_all'))
        {
            $this->db->from($this->_pTableName);
            $Res = $this->db->get();
            $Permissions = $Res->result_array();
            $this->cache->save('permissions_all', $Permissions, false);
        }
        return $Permissions;
    }

    public function deleteRolePermission($aRoleId = false, $aPermissionId = false)
    {
        $Args = get_defined_vars();
        foreach($Args as $k=>$v)
        {
            $$k = (int)$$k;
        }
        
        if(!$aRoleId || !$aPermissionId)
        {
            return false;
        }
        $this->db->from($this->_pRolePermissionTable);
        $this->db->where(array('role_id'=>$aRoleId, 'permission_id'=>$aPermissionId));
        @$this->cache->delete('permissions_'.$aRoleId);
        return $this->db->delete();
    }

    public function getRoles($aFilters = false,$aLimit = array(), $aOrder = array('field'=>'id','dirrection'=>'ASC'))
    {
        $this->db->from($this->_pRolesTable);
        if($aFilters)
        {
            $this->db->where($aFilters);
        }
        $this->db->order_by($aOrder['field'], $aOrder['dirrection']);
        if($aLimit)
        {
            if(is_array($aLimit))
            {
                $this->db->limit($aLimit[0], $aLimit[1]);
            }
            else
            {
                $this->db->limit($aLimit);
            }
        }
        $Res = $this->db->get();
        return $Res->result_array();
    }

    public function findRole($aRoleId =false)
    {
        $aRoleId = (int)$aRoleId;
        if(!$aRoleId)
        {
            return false;
        }
        $this->db->from($this->_pRolesTable);
        $this->db->where(array('id'=>$aRoleId));
        $this->db->limit(1);
        $Result = $this->db->get();
        return $Result->row_array();
    }

    public function saveRole($aRoleData = NULL)
    {
        $this->_PrepareCustomFields($aRoleData, $this->_pRolesFields, $aRoleData);
        
        if((int)$aRoleData['id'])
        {
            $this->db->where(array('id'=>$aRoleData['id']));
            $this->db->update($this->_pRolesTable, $aRoleData);
            return $this->db->affected_rows();
        }
        else
        {
            unset($aRoleData['id']);
            $this->db->insert($this->_pRolesTable, $aRoleData);
            return $this->db->insert_id();
        }
    }

    public function deleteRole($aRoleId)
    {
        $aRoleId = (int)$aRoleId;
        if(!$aRoleId)
        {
            return false;
        }
        $this->db->from($this->_pRolesTable);
        $this->db->where(array('id'=>$aRoleId));
        $this->db->delete();
        
        $this->db->from($this->_pRolePermissionTable);
        $this->db->where(array('role_id'=>$aRoleId));
        $this->db->delete();
        @$this->cache->delete('permissions_'.$aRoleId);
        return true;
    }

    public function findPermission($aPermissionId = false)
    {
        $aPermissionId = (int)$aPermissionId;
        if(!$aPermissionId)
        {
            return false;
        }
        $this->db->from($this->_pTableName);
        $this->db->where(array('id'=>$aPermissionId));
        $this->db->limit(1);
        $Result = $this->db->get();
        return $Result->row_array();
    }

    public function saveRolePermissions($aRoleId = false, $aPermissions = false)
    {
        if(!$aRoleId || !$aPermissions)
        {
            return false;
        }
        
        $this->db->from($this->_pRolePermissionTable);
        $this->db->where(array('role_id'=>$aRoleId));
        $this->db->delete();

        foreach($aPermissions as $k=>$v)
        {
            $this->_PrepareCustomFields($v, $this->_pRolePermissionFields, $v);
            $this->db->insert($this->_pRolePermissionTable, $v);
        }
        @$this->cache->delete('permissions_'.$aRoleId);
        return true;
    }

    public function getRolePermissionTree($aRoleId = false)
    {
        if(!$aRoleId)
        {
            return false;
        }
        if(!$aResultPermissions = $this->cache->get('permissions_'.$aRoleId))
        {
            $aPermissions = $this->getRolePermissions($aRoleId);
            $aResultPermissions = array();
            foreach($aPermissions as $v)
            {
                $aResultPermissions[$v['name']] = $v;
            }
            $this->cache->save('permissions_'.$aRoleId, $aResultPermissions, false);
        }
        return $aResultPermissions;
    }

    public function checkAccess($aPermission = false, $aPresentPermissions = false)
    {
        if(!$aPermission||!$aPresentPermissions)
        {
            return false;
        }
        
        if(isset($aPresentPermissions[$aPermission]))
        {
            if($aPresentPermissions[$aPermission]['status'] == PERMISSION_STATUS_ALLOW)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return $this->_checkPermissionParent($aPermission, $aPresentPermissions);
        }
    }
    private function _checkPermissionParent($aPermission, $aPresentPermissions)
    {
        foreach($this->_pAllPermissios as $v)
        {
            if($v['name'] == $aPermission)
            {
                if($v['parent'])
                {
                    $CurrentPermission = $this->_getPermissionById($v['parent']);
                    return $this->checkAccess($CurrentPermission['name'], $aPresentPermissions);
                }
                else
                {
                    return false;
                }
            }
        }
    }    

    private function _getPermissionById($aId)
    {
        foreach($this->_pAllPermissios as $v)
        {
            if($v['id'] == $aId)
            {
                return $v;
            }
        }
    }
}
?>