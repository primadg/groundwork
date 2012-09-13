<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Auth
{
    private $_pUserData = false;
    function  __construct()
    {
        $this->_pUserData = get_instance()->session->userdata('user');
    }

    public function login($aLogin, $aPassword)
    {
        $this->_pSetUserData(array('login'=>'admin','role_id'=>11));
    }

    public function logout($aLogin, $aPassword)
    {
        get_instance()->session->set_userdata('user', false);
    }
    private function _pSetUserData($aData)
    {
        get_instance()->session->set_userdata('user', $aData);
    }
    public function getUserData()
    {
        return $this->_pUserData;
    }
}

?>
