<?php
define('PERMISSION_STATUS_ALLOW', 1);
define('PERMISSION_STATUS_DENY', 2);
class Aclmanager extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('acl_model');
        $this->javascript('aclmanager.js');
    }

    public function index()
    {
        return $this->roles();
    }
    
    public function roles()
    {
        $this->_pData['roles'] = $this->acl_model->getRoles();
        $this->_pData['isajax'] = $this->isAjaxRequest();
        
        return $this->view(__CLASS__, __FUNCTION__);
    }

    public function saverole($aRoleId = NULL)
    {
        $Result = array();
        $RoleData = array();
        $Result['errors'] = array();
        $Result['error'] = false;
        $RoleData['id'] = $this->input->post('id');
        $RoleData['name'] = $this->input->post('name');
        $RoleData['description'] = $this->input->post('description');
        if(!$RoleData['name'])
        {
            $Result['error'] = true;
            $Result['errors'][] = array('field'=>'name', 'message'=>'Enter name') ;
        }
        
        if(!$Result['error'])
        {
            $Success = $this->acl_model->saveRole($RoleData);
            $Result['error'] = false;
        }
        $this->AjaxResponse($Result, true);
    }

    public function deleterole()
    {
        $Result = array(
            'error'=>false
        );
        $RoleId = $this->input->post('role_id');
        if(!$RoleId)
        {
            $Result['error'] = true;
            $Result['message'] = 'Role not found';
        }
        else
        {
            $this->acl_model->deleteRole($RoleId);
        }
        
        $this->AjaxResponse($Result, true);
    }

    public function deletepermission()
    {
        $Result = array(
            'error'=>false
        );
        $PermissionId = (int)$this->input->post('permission_id');
        if(!$PermissionId)
        {
            $Result['error'] = true;
            $Result['message'] = 'Permission not found';
        }
        else
        {
            $this->acl_model->deletePermission($PermissionId);
        }

        $this->AjaxResponse($Result, true);
    }
    
    public function rolepermissions($aRoleId = NULL)
    {
        if(!$aRoleId)
        {
            return false;
        }
        $this->_pData['AllPermissions'] = $this->acl_model->getPermissions();
        $RolePermissions = $this->acl_model->getRolePermissions($aRoleId);
        foreach($this->_pData['AllPermissions'] as &$v)
        {
            foreach($RolePermissions as $permission)
            {
                if($v['id'] == $permission['permission_id'])
                {
                    $v['present'] = true;
                    $v['status'] = $permission['status'];
                }
            }
            if(!isset($v['present']))
            {
                $v['present'] = false;
                $v['status'] = 0;
            }
        }
        return $this->view();
    }

    public function permissions()
    {
        $this->_pData['isajax'] = $this->isAjaxRequest();

        $this->_pData['permissions'] = $this->acl_model->getPermissions();
        
        return $this->view();
    }

    public function permissionform()
    {
        $PermissionId = $this->input->post('permission_id', true);
        if($PermissionId)
        {
            $this->_pData['permission'] = $this->acl_model->findPermission($PermissionId);
        }
        else
        {
            $this->_pData['permission'] = false;
        }
        $this->_pData['permissions'] = $this->acl_model->getPermissions();
        return $this->view();
    }
    
    public function savepermission()
    {
        $PermissionData = array();
        $Result = array('error'=>false, 'errors'=>array());
        
        $PermissionData['id'] = $this->input->post('id', true);
        $PermissionData['parent'] = $this->input->post('parent', true);
        $PermissionData['name'] = $this->input->post('name', true);
        $PermissionData['description'] = $this->input->post('description', true);
        if(!$PermissionData['name'])
        {
            $Result['error'] = true;
            $Result['errors'][] = array('field'=>'name', 'message'=>'Enter name');
        }
        if(!$Result['error'])
        {
            $this->acl_model->savePermission($PermissionData);
        }
        $this->AjaxResponse($Result, true);
    }

    public function roleform()
    {
        $Result = array();
        $RoleId = $this->input->post('role_id', true);
        if(!$RoleId)
        {
            $Result['error'] = true;
            $Result['massage'] = 'Role not found';
        }
        else
        {
            $Result['role'] = $this->acl_model->findRole($RoleId);
            if(!$Result['role'])
            {
                $Result['error'] = true;
                $Result['massage'] = 'Role not found';
            }
        }
        $this->AjaxResponse($Result, true);
    }

    public function saverolepermissions($aRoleId = false)
    {
        if(!$aRoleId)
        {
            return false;
        }

        $Permissions = $this->input->post('permission', true);
        $PermissionsData = array();
        foreach($Permissions as $k=>$v)
        {
            $PermissionsData[] = array(
                'role_id'=>$aRoleId,
                'permission_id'=>$k,
                'status'=>$v
            );
        }
        $this->acl_model->saveRolePermissions($aRoleId, $PermissionsData);
    }

    public function permissionmarix()
    {
        $this->restrict('asdf');
        if($this->input->post())
        {
            $FullPermissions = $this->input->post('permissions', true);
            foreach($FullPermissions as $role=>$permissions)
            {
                $PermissionsData = array();
                foreach($permissions as $k=>$v)
                {
                    $PermissionsData[] = array(
                        'role_id'=>$role,
                        'permission_id'=>$k,
                        'status'=>$v
                    );
                }
                $this->acl_model->saveRolePermissions($role, $PermissionsData);
            }
            redirect('aclmanager/permissionmarix');
        }
        $Roles = $this->acl_model->getRoles();
        $AllPermissions = $this->acl_model->getPermissions();
        $AllRolePermissions = array();
        foreach($Roles as $key=>$role)
        {
            $RolePermissions = $this->acl_model->getRolePermissions($role['id']);
            foreach($AllPermissions as $v)
            {
                foreach($RolePermissions as $permission)
                {
                    if($v['id'] == $permission['permission_id'])
                    {
                        $v['present'] = true;
                        $v['status'] = $permission['status'];
                    }
                }
                if(!isset($v['present']))
                {
                    $v['present'] = false;
                    $v['status'] = 0;
                }
                $AllRolePermissions[$v['name']][$role['id']] = $v;
            }
        }
        $this->_pData['AllRolePermissions'] = $AllRolePermissions;
        $this->_pData['roles'] = $Roles;
        return $this->view();
    }
}
?>

