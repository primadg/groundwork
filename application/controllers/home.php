<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Home Controller class
* @date: 15-June-2012
* @Purpose: This controller handles all the Home Page functionalities.
* @filesource: application/controllers/home.php
* @author:  Vyacheslav Isaev
* @version: 0.0.1
* @revision: 
**/
class Home extends MY_Controller {

    function __construct()
    {
        // Call the parent Protocontroller constructor
        parent::__construct();
    }
    
    /**
    * Method : index 
    * @date: 15-June-2012
    * @Purpose: Index Page
    * @filesource: application/controllers/home.php
    * @Param: 
    * @Return: 
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision: 
    * @access: public
    */
    public function index()
    {
        // В первый файл передаем все наши данные
        $this->load->view('includes/header',  $this->_pData);
        $this->load->view('includes/left_side');
        $this->load->view('home');
        $this->load->view('includes/footer');
    }
}