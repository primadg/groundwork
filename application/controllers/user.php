<?php

if (!defined('BASEPATH'))
        exit('No direct script access allowed');


class User extends MY_Controller
{

        function __construct()
        {
                parent::__construct();
                $this->load->model('user_model');
        }
        function index()
        {
                            
        }
        
        function login()
        {
                $this->title('Login | ' . $this->config->item('email_site_name'));
                $aData = array();
                if($this->input->post())
                {
                        if(!$aData['login'] = $this->POST('login', false, true))
                        {
                                $this->errors->set(UM_ERRORTYPE_ERROR, 'Login field is required','login');
                        }
                        if(!$aData['password'] = $this->POST('password', false, true))
                        {
                                $this->errors->set(UM_ERRORTYPE_ERROR, 'Password field is required','password');
                        }
                        if(!$this->errors->check())
                        {
                                if(!$aUserData = $this->user_model->login($aData['login'], $aData['password']))
                                {
                                        $this->errors->set(UM_ERRORTYPE_MESSAGE, 'Login or password are incorrect');
                                }
                                else
                                {
                                        $this->session->set_userdata('user_info', $aUserData);
                                        redirect();
                                }                                
                        }
                }

                $this->_pData['aData'] = $aData;
                return $this->view();
        }

        function logout()
        {
                $this->session->set_userdata('user_info', NULL);
                redirect('');
        }
        function forgot()
        {
                if(!$this->isPostMethod())
                {
                        return $this->not_found();
                }
                $sEmail = $this->POST('email', false, true);

                $this->load->library('form_validation');
                $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
                if(!$sEmail)
                {
                        $this->errors->set(UM_ERRORTYPE_ERROR, 'Enter email please', 'email');
                }
                else if(!$this->form_validation->run())
                {
                        $this->errors->set(UM_ERRORTYPE_ERROR, 'Enter valid email please', 'email');
                }
                else if(!$aUserInfo = $this->user_model->get(array('email'=>$sEmail), true))
                {
                        $this->errors->set(UM_ERRORTYPE_ERROR, 'There is no such email in system', 'email');
                }
                else
                {
                        $sNewPass = $this->generate_key(PASSWORD_GENERATE_LENGHT);
                        $aUpdateData = array('password'=>md5($sNewPass));
                        if($this->user_model->save($aUpdateData, $aUserInfo['user_id']))
                        {
                                $this->load->library('email');

                                $this->email->from('noreply@eventcheck.dev');
                                $this->email->to($sEmail);

                                $this->email->subject($this->config->item('email_forgot'));
                                $this->email->message('New password ' . $sNewPass);

                                if ($this->email->send())
                                {
                                        $this->errors->set(UM_ERRORTYPE_MESSAGE, 'Your new password was sent to your email');
                                        redirect('');
                                }
                                else
                                {
                                        $this->errors->set(UM_ERRORTYPE_ERROR, 'An error occured while sending email. Please, try again.');
                                        redirect('');
                                }
                        }
                        else
                        {
                                $this->errors->set(UM_ERRORTYPE_ERROR, 'Unable to restore password');
                                redirect('');
                        }
                }
                $aData = array();
                $aData['email'] = $sEmail;
                $aData['forgot'] = 1;
                $this->_pData['aData'] = $aData;
                $this->view(__CLASS__, 'login');
        }

        function add($nUserId = NULL)
        {
                $aData = array();
                $aUserInfo = $this->session->userdata('user_info');
                

                if($aUserInfo['user_id']==$nUserId)
                {
                        $aData['self'] = true;
                }
                
                if(!$this->isPostMethod())
                {
                        if(intval($nUserId))
                        {
                                $aData['user_info'] = $this->user_model->get(array('user_id'=>intval($nUserId)),true);
                                $this->title(($aData['self']?'':'Edit user ') . mb_strimwidth($aData['user_info']['first_name'].' ' . $aData['user_info']['last_name'], 0,40,'...') . '|' . $this->config->item('email_site_name'));
                                unset($aData['user_info']['password']);
                                if(empty ($aData))
                                {
                                        $this->errors->set(UM_ERRORTYPE_ERROR, 'User not found');
                                        redirect($_SERVER['HTTP_REFERER']);
                                }
                        }
                        else
                        {
                                $this->title('Add user | ' . $this->config->item('email_site_name'));
                        }
                }
                else
                {
                        if(!$aData['user_info']['login'] = $this->POST('login', false, true))
                        {
                                $this->errors->set(UM_ERRORTYPE_ERROR, 'Login is required', 'login');
                        }
                        else if($this->user_model->login_exists($aData['user_info']['login'], $nUserId))
                        {
                                $this->errors->set(UM_ERRORTYPE_ERROR, 'This login is already used', 'login');
                        }
                        if(!$nUserId)
                        {
                                if(!$aData['user_info']['password'] = $this->POST('password', false, true))
                                {
                                        $this->errors->set(UM_ERRORTYPE_ERROR, 'Password is required', 'password');
                                }
                                else if($aData['user_info']['password'] != $this->POST('confirm', false, true))
                                {
                                        $this->errors->set(UM_ERRORTYPE_ERROR, 'Passwords not match', 'password');
                                }
                        }
                        else
                        {
                                if($this->POST('password', false, true))
                                {
                                        $aData['user_info']['password'] = $this->POST('password', false, true);
                                        if($aData['user_info']['password'] != $this->POST('confirm', false, true))
                                        {
                                                $this->errors->set(UM_ERRORTYPE_ERROR, 'Passwords not match', 'password');
                                        }
                                }
                        }
                        
                        if(!$aData['user_info']['email'] = $this->POST('email', false, true))
                        {
                                $this->errors->set(UM_ERRORTYPE_ERROR, 'Email is required', 'email');
                        }
                        else if(!$this->isValidEmail($aData['user_info']['email']))
                        {
                                $this->errors->set(UM_ERRORTYPE_ERROR, 'Enter valid email please', 'email');
                        }
                        else if($this->user_model->email_exists($aData['user_info']['email'], $nUserId))
                        {
                                $this->errors->set(UM_ERRORTYPE_ERROR, 'This email is already used', 'email');
                        }

                        if(!$aData['user_info']['first_name'] = $this->POST('first_name', false, true))
                        {
                                $this->errors->set(UM_ERRORTYPE_ERROR, 'Name is required', 'first_name');
                        }

                        $aData['user_info']['last_name'] = $this->POST('last_name', false, true);
                        if($aUserInfo['user_id']!=$nUserId)
                        {
                                $aData['user_info']['status'] = $this->POST('status', true)?USER_STATUS_ADMIN:0;
                        }

                        if(!$this->errors->check())
                        {
                                $sPassword = $aData['user_info']['password'];
                                $aData['user_info']['password'] = md5($aData['user_info']['password']);
                                        
                                if($this->user_model->save($aData['user_info'], $nUserId) &&!$nUserId)
                                {
                                        $this->load->library('email');

                                        $this->email->from('noreply@eventcheck.dev');
                                        $this->email->to($aData['user_info']['email']);

                                        $this->email->subject($this->config->item('account_created'));
                                        $this->email->message('Your accaount at ' . site_url() .
                                                ' was created. You can login here - <a href="' . site_url() . 'user/login">'.site_url().
                                                'user/login</a><br />Your login is ' .
                                                $aData['user_info']['login'] . '<br/>' .
                                                'Your password is ' . $sPassword);

                                        $this->email->send();
                                }

                                $this->errors->set(UM_ERRORTYPE_MESSAGE, 'User successfuly saved');
                                redirect('user/listing', 'refresh');
                        }
                }

                $this->_pData['aData'] = $aData;
                return $this->view(__CLASS__, __FUNCTION__);
        }

        function edit($nUserId = NULL)
        {
                $nUserId = intval($nUserId);
                if($nUserId)
                {
                        return $this->add($nUserId);
                }
        }
        function profile()
        {
                $aUserInfo = $this->session->userdata('user_info');
                return $this->add($aUserInfo['user_id']);
        }

        function delete($nUserId = NULL)
        {
                $nUserId = intval($nUserId);
                if(!$nUserId)
                {
                        return $this->not_found();
                }
                if($this->user_model->delete(array('user_id'=>$nUserId)))
                {
                        $this->errors->set(UM_ERRORTYPE_MESSAGE, 'User deleted successfuly');
                }
                else
                {
                        $this->errors->set(UM_ERRORTYPE_ERROR, 'User not found');
                }
                redirect('user/listing');
        }

        function listing($nPageNumber = NULL, $sOrder = 'login', $sDirrection = 'ASC')
        {
                $this->title('Users | ' . $this->config->item('email_site_name'));
                $aData = array();
                $nCount = $this->user_model->get_count();
                $aData['usercount'] = $nCount;
                $aData['pagecount'] = ceil($nCount/RECORD_COUNT_PER_PAGE);
                $nPageNumber = intval($nPageNumber);
                if($nPageNumber)
                {
                        
                        if($nPageNumber>$aData['pagecount'])
                        {
                                $nPageNumber = $aData['pagecount'];
                        }
                        if($nPageNumber<1)
                        {
                                $nPageNumber = 1;
                        }
                        $aLimit = array(
                            RECORD_COUNT_PER_PAGE,
                            $nPageNumber * RECORD_COUNT_PER_PAGE - RECORD_COUNT_PER_PAGE
                            
                        );
                }
                else
                {
                        $nPageNumber = 1;
                        $aLimit = array(RECORD_COUNT_PER_PAGE, 0);
                }

                $aData['page'] = $nPageNumber;
                $aData['order'] = $sOrder;
                $aData['dirrection'] = $sDirrection;
                
                $aData['users'] = $this->user_model->get(array(), false, $aLimit, array($sOrder=>$sDirrection));
                $this->_pData['aData'] = $aData;
                
                return $this->view();
        }

}
?>