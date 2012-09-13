<?php

if (!defined('BASEPATH'))
   exit('No direct script access allowed');

/**
 * Form Validation Controller class
 * @date: 18-June-2012
 * @Purpose: This controller handles all the Lists functionalities.
 * @filesource: application/controllers/formvalidation.php
 * @author:  Ivaschenko Ludmila
 * @version: 0.0.2
 * @revision:
 * */
class Formvalidation extends MY_Controller {

    //protected $_Form;

   function __construct() {
      // Call the parent Protocontroller constructor
      parent::__construct();

   }

   /**
    * Method : index
    * @date: 18-June-2012
    * @Purpose: Index Page
    * @filesource: application/controllers/formvalidation.php
    * @Param:
    * @Return:
    * @author: Ivaschenko Ludmila
    * @version: 0.0.1
    * @revision:
    * @access: public
    */
    public function index() {

        // Подключаем библиотеку валидации
        $this->load->library('form_validation');

        // В первый файл передаем все наши данные
        $this->load->view('includes/header', $this->_pData);
        $this->load->view('includes/left_side');

        if($this->input->post())
        {
            //Устанавливаем правила для валидации
            $this->form_validation->set_rules('emailValid', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('minValid', 'Minimum lenght', 'trim|required|min_length[6]');
            $this->form_validation->set_rules('maxValid', 'Maximum lenght', 'trim|required|max_length[6]');
            $this->form_validation->set_rules('min', 'Minimum value', 'trim|required|greater_than[4]');
            $this->form_validation->set_rules('max', 'Maximum value', 'trim|required|less_than[11]');
            $this->form_validation->set_rules('numsValid', 'Only numbers', 'trim|required|numeric');
            $this->form_validation->set_rules('dateValid', 'Date', 'callback_valid_date');
            $this->form_validation->set_rules('datetimeValid', 'Date and Time', 'callback_valid_datetime');
            $this->form_validation->set_rules('timeValid', 'Time', 'callback_valid_time');
            $this->form_validation->set_rules('xssfilterValid', 'XSS filter', 'required|xss_clean');
            $this->form_validation->set_error_delimiters('<label class="error">', '</label>');

            //Запускаем валидацию
            if ($this->form_validation->run() == FALSE)
            {


                $this->load->view('form_validation');
            }
            else
            {
                $this->load->view('form_validation_success');
            }

        }
        else
        {
            $this->load->view('form_validation');

        }
        $this->load->view('includes/footer');
    }


   /**
    * Method : valid_date
    * @date: 19-June-2012
    * @Purpose: PHP method - Validation for "Date" field
    * @filesource: application/controllers/formvalidation.php
    * @Param: $lDate
    * @Return: string
    * @author: Ivaschenko Ludmila
    * @version: 0.0.1
    * @revision:
    * @access: public
    */
    public function valid_date($lDate)
    {
        if ( preg_match("/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])$/", $lDate) )
        {
            return $lDate;
        }
        else
        {
            $this->form_validation->set_message('valid_date', 'Invalid date, must be in YYYY-MM-DD format');
            return FALSE;
        }
    }


   /**
    * Method : valid_datetime
    * @date: 19-June-2012
    * @Purpose: PHP method - Validation for "Date and Time" field
    * @filesource: application/controllers/formvalidation.php
    * @Param: $lDateTime
    * @Return: string
    * @author: Ivaschenko Ludmila
    * @version: 0.0.1
    * @revision:
    * @access: public
    */
    public function valid_datetime($lDateTime)
    {
        if ( preg_match("/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])\s(([0-9])|([0-1][0-9])|([2][0-3])):(([0-9])|([0-5][0-9])):(([0-9])|([0-5][0-9]))$/", $lDateTime) )
        {
            return $lDateTime;
        }
        else
        {
            $this->form_validation->set_message('valid_datetime', 'Invalid date and time, must be in YYYY-MM-DD HH:mm:ss format');
            return FALSE;
        }
    }


   /**
    * Method : valid_time
    * @date: 19-June-2012
    * @Purpose: PHP method - Validation for "Time" field
    * @filesource: application/controllers/formvalidation.php
    * @Param:
    * @Return: string
    * @author: Ivaschenko Ludmila
    * @version: 0.0.1
    * @revision:
    * @access: public
    */
    public function valid_time($lTime) {
        if ( preg_match("/^(([0-9])|([0-1][0-9])|([2][0-3])):(([0-9])|([0-5][0-9])):(([0-9])|([0-5][0-9]))$/", $lTime) )
        {
            return $lTime;
        }
        else
        {
            $this->form_validation->set_message('valid_time', 'Invalid time, must be in HH:mm:ss format');
            return FALSE;
        }
    }
}
?>
