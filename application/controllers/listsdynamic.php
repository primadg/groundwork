<?php

if (!defined('BASEPATH'))
   exit('No direct script access allowed');

/**
 * Lists Controller class
 * @date: 11-June-2012
 * @Purpose: This controller handles all the Lists functionalities.
 * @filesource: application/controllers/lists.php
 * @author:  Vyacheslav Isaev
 * @version: 0.0.2 
 * @revision: 
 * */
class Listsdynamic extends MY_Controller {

    protected $_LimitPerPage;
    protected $_CurrentPage;
    protected $_TotalPageCount;
    protected $_Offset;

   function __construct() {
      // Call the parent Protocontroller constructor
      parent::__construct();

        // Устанавливаем лимит для количества отобращенных элементов на одной странице
        $this->_LimitPerPage = 20;
        // Изначально устанавливаем - что мы находимся на первой странице
        $this->_CurrentPage = 1;
        //Устанавливаем смещение
        $this->_Offset = 0;
        // Вызов нашел модели для работы со списком
        $this->load->model('lists_dynamic_model');
        // Вызываем хелпер для проверки email
        $this->load->helper('email');
   }

   /**
    * Method : index 
    * @date: 11-June-2012
    * @Purpose: Index Page
    * @filesource: application/controllers/lists.php
    * @Param: 
    * @Return: 
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision: 
    * @access: public
    */
    public function index() {
        // Получаем полный список с таблицы
        $this->_pData['lists'] = $this->lists_dynamic_model->getAllLists();
        // Если у нас отсутствуют данные в БД
        if(count($this->_pData['lists'])==0)
        {
            $this->_pData['emptyDatabase'] = "Empty Dadabase";
        }
        // В первый файл передаем все наши данные
        $this->load->view('includes/header', $this->_pData);
        $this->load->view('includes/left_side');
        $this->load->view('list_dynamic');
        $this->load->view('includes/footer');
    }

   /**
    * Method : updatefield 
    * @date: 11-June-2012
    * @Purpose: AJAX method - Update one field in DB
    * @filesource: application/controllers/lists.php
    * @Param: 
    * @Return: JSON data
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision: 
    * @access: public
    */
   public function updatefield() {
        if($this->input->post())
        {
            // Подключаем библиотеку валидации
            $this->load->library('form_validation');
            // Массив для сбора ошибок валидации
            $errorData = array();

            // Данные для замены значений
            $updateData = array();
            $updateData['name'] = $this->input->post("name",TRUE);
            $updateData['date_of_birth'] = $this->input->post("date_of_birth",TRUE);
            $updateData['phone'] = $this->input->post("phone",TRUE);
            $updateData['email'] = $this->input->post("email",TRUE);

            if(!$this->form_validation->is_natural($updateData['phone']))
            {
                // Добавляем ошибку что phone не валиден
                $errorData['phone'] = "* Numbers only";
            }
            if(!isset($updateData['date_of_birth']) || empty($updateData['date_of_birth']))
            {
                // Добавляем ошибку что date_of_birth не валиден
                $errorData['date_of_birth'] = "* This field is required";
            }

            if(!isset($updateData['name']) || empty($updateData['name']))
            {
                // Добавляем ошибку что name не валиден
                $errorData['name'] = "* This field is required";
            }
            if(!valid_email($updateData['email']))
            {
                // Добавляем ошибку что email не валиден
                $errorData['email'] = "* Invalid email address";
            }

            // Если мы прошли валидацию
            if(count($errorData)==0)
            {
                // Получаем способ сортировки данных
                $lSortBy = $this->input->post("sort",TRUE);

                // Делаем дополнительную проверку - чтобы нам не пришли другие данные
                $lSortBy = sort_by_asc_desc($lSortBy);

                // Получаем имя поля, по которому будем производить сортировку
                $lFieldName = $this->input->post("field",TRUE);

                // Делаем проверку на существование данных
                if(isset($lSortBy)&&!empty($lSortBy)&&isset($lFieldName)&&!empty($lFieldName))
                {
                    // Получаем ID редактируемого поля
                    $fieldId = $this->input->post("fieldId",TRUE);

                    // Обнавляем данные в БД
                    $affectedRows = $this->lists_dynamic_model->updateOneField($fieldId,$updateData);

                    // Поисковая фраза
                    $lSearch = $this->input->post("search",TRUE);
                    // Получаем новый список с уже измененными данными
                    $this->_pData['lists'] = $this->lists_dynamic_model->getAllLists($this->_Offset, $this->_LimitPerPage, $lSortBy, $lFieldName, $lSearch);

                    // Отдаем данные на Callback функцию
                    // Третий аргумент во VIEW отвечает за возврат значения - RETURN
                    echo json_encode(
                        array(
                            "result"=>'complete',
                            "data"=>$this->load->view('block_list_table',$this->_pData,TRUE)
                            )
                    );
                }
                else
                {
                    // Иначе выдаем ошибку отсутствия пришедших данных
                    // Отдаем данные на Callback функцию
                    echo json_encode(
                        array(
                            "result"=>'error',
                            "data"=>"Empty Data"
                            )
                    );
                }
            }
            else
            {
                // Если мы не прошли валидацию
                // Отдаем данные на Callback функцию
                echo json_encode(
                    array(
                        "result"=>'error_valid',
                        "data"=> $errorData
                        )
                );
            }
        }
        else
        {
            // Отдаем данные на Callback функцию
            echo json_encode(
                array(
                    "result"=>'error',
                    "data"=> "Error"
                    )
            );
        }
   }

   /**
    * Method : getfield 
    * @date: 11-June-2012
    * @Purpose: AJAX method - Get one field on DB
    * @filesource: application/controllers/lists.php
    * @Param: int $field_id
    * @Return: JSON data
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision: 
    * @access: public
    */
   public function getfield($fieldId) {
      // Получение значения одного поля по его ID
      $this->_pData['field'] = $this->lists_dynamic_model->getOneField($fieldId);

      // Отдаем данные на Callback функцию
      echo json_encode(
	      array(
		  "result" => 'complete',
		  "data" => $this->_pData['field']
	      )
      );
   }

   public function getmore() {
        // Получаем способ сортировки данных
        $lSortBy = $this->input->post("sort",TRUE);

        // Делаем дополнительную проверку - чтобы нам не пришли другие данные
        $lSortBy = sort_by_asc_desc($lSortBy);

        // Получаем имя поля, по которому будем производить сортировку
        $lFieldName = $this->input->post("field",TRUE);

        // Делаем проверку на существование данных
        if(isset($lSortBy)&&!empty($lSortBy)&&isset($lFieldName)&&!empty($lFieldName))
        {
            $lPageToLoad = $this->input->post("page_to_load", TRUE);
            $lPageToLoad = intval($lPageToLoad);
            if ($lPageToLoad <= 0) {
             echo json_encode(
                     array(
                         "result" => 'error',
                         "data" => "Invalid page"
                     )
             );
            }
            $this->_Offset = $lPageToLoad * $this->_LimitPerPage;
            // Поисковая фраза
            $lSearch = $this->input->post("search",TRUE);
            $this->_pData['lists'] = $this->lists_dynamic_model->getAllLists($this->_Offset, $this->_LimitPerPage, $lSortBy, $lFieldName,$lSearch);

            echo json_encode(
                  array(
                      "result" => 'complete',
                      "data" => $this->load->view('block_list_table', $this->_pData, TRUE)
                  )
            );
        }
        else
        {
            // Иначе выдаем ошибку отсутствия пришедших данных
            // Отдаем данные на Callback функцию
            echo json_encode(
                array(
                    "result"=>'error',
                    "data"=>"Empty Data"
                    )
            );
        }
   }

   public function generate() {
      for ($i = 0; $i < 50; $i++) {
	 $name = generate_string(5);
	 $email = generate_string(5) . '@' . generate_string(5) . '.com';
	 $timestamp = rand(1324509766, 1339509766);
	 $date = date('Y-m-d');
	 $phone = rand(1111111, 9999999);
	 //echo $name . "\n" . $email . "\n". $date . "\n" . $phone;
	 $arr = array('name' => $name,
	     'email' => $email,
	     'date_of_birth' => $date,
	     'phone' => $phone);
	 $this->lists_dynamic_model->addField($arr);
      }
   }

   /**
    * Method : deletefield 
    * @date: 11-June-2012
    * @Purpose: AJAX method - Delete one field in DB
    * @filesource: application/controllers/lists.php
    * @Param: 
    * @Return: JSON data
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision: 
    * @access: public
    */
   public function deletefield() {
      // Получаем ID редактируемого поля
      $fieldId = intval($this->input->post("fieldId", TRUE));

      // Получаем текущюю страницу
      $this->_CurrentPage = intval($this->input->post("current_page"));

      // Обновляем данные в БД
      if ($this->lists_dynamic_model->deleteOneField($fieldId)) {
	 echo json_encode(
		 array(
		     "result" => 'complete'
		 )
	 );
      } else {
	 echo json_encode(
		 array(
		     "result" => 'error'
		 )
	 );
      }
   }
    /**
    * Method : sorttable
    * @date: 14-June-2012
    * @Purpose: AJAX method - sorting data in table
    * @filesource: application/controllers/listsdynamic.php
    * @Param:
    * @Return: JSON data
    * @author: Ivaschenko Ludmila
    * @version: 0.0.1
    * @revision:
    * @access: public
    */
    public function sorttable()
    {       
        // Получаем способ сортировки данных
        $lSortBy = $this->input->post("sort",TRUE);

        // Делаем дополнительную проверку - чтобы нам не пришли другие данные
        $lSortBy = sort_by_asc_desc($lSortBy);
                       
        // Получаем имя поля, по которому будем производить сортировку
        $lFieldName = $this->input->post("field",TRUE);

        // Делаем проверку на существование данных
        if(isset($lSortBy)&&!empty($lSortBy)&&isset($lFieldName)&&!empty($lFieldName))
        {

            $lPageToLoad = $this->input->post("page_to_load", TRUE);
            $lPageToLoad = intval($lPageToLoad);
            if ($lPageToLoad <= 0)
            {
                echo json_encode(
                     array(
                         "result" => 'error',
                         "data" => "Invalid page"
                     )
                );
            }
            $lLimit = $lPageToLoad * $this->_LimitPerPage;

            // Поисковая фраза
            $lSearch = $this->input->post("search",TRUE);
            // Получаем новый список с уже измененными данными
            $this->_pData['lists'] = $this->lists_dynamic_model->getAllLists($this->_Offset, $lLimit, $lSortBy, $lFieldName, $lSearch);
                // Отдаем данные на Callback функцию
                echo json_encode(
                    array(
                        "result"=>'complete',
                        "data"=>$this->load->view('block_list_table',$this->_pData,TRUE)
                        )
                );
        }
        else
        {
            // Иначе выдаем ошибку отсутствия пришедших данных
            // Отдаем данные на Callback функцию
            echo json_encode(
                array(
                    "result"=>'error',
                    "data"=>"Empty Data"
                    )
            );
        }
    }


    /**
    * Method : search
    * @date: 15-June-2012
    * @Purpose: AJAX method - search data in table
    * @filesource: application/controllers/listsdynamic.php
    * @Param:
    * @Return: JSON data
    * @author: Ivaschenko Ludmila
    * @version: 0.0.1
    * @revision:
    * @access: public
    */
    public function search()
    {
        // Получаем способ сортировки данных
        $lSortBy = $this->input->post("sort",TRUE);

        // Делаем дополнительную проверку - чтобы нам не пришли другие данные
        $lSortBy = sort_by_asc_desc($lSortBy);

        // Получаем имя поля, по которому будем производить сортировку
        $lFieldName = $this->input->post("field",TRUE);

        // Делаем проверку на существование данных
        if(isset($lSortBy)&&!empty($lSortBy)&&isset($lFieldName)&&!empty($lFieldName))
        {
            // Поисковая фраза
            $lSearch = $this->input->post("search",TRUE);
            // Получаем новый список с уже измененными данными
            $this->_pData['lists'] = $this->lists_dynamic_model->getAllLists($this->_Offset, $this->_LimitPerPage, $lSortBy, $lFieldName, $lSearch);

            // Если у нас в БД не осталось записей
            if(count($this->_pData['lists'])==0)
            {
                $this->_pData['emptyDatabase'] = "According to your request records not found";
                echo json_encode(
                    array(
                        "result"=>'empty_database',
                        "data"=>$this->_pData['emptyDatabase']
                        )
                );
            }
            else
            {
                // Отдаем данные на Callback функцию
                echo json_encode(
                    array(
                        "result"=>'complete',
                        "data"=>$this->load->view('block_list_table',$this->_pData,TRUE),
                        )
                );
            }
        }
        else
        {
            // Иначе выдаем ошибку отсутствия пришедших данных
            // Отдаем данные на Callback функцию
            echo json_encode(
                array(
                    "result"=>'error',
                    "data"=>"Empty Data"
                    )
            );
        }
    }


    /**
    * Method : deletesomefields
    * @date: 15-June-2012
    * @Purpose: AJAX method - Delete some fields in DB
    * @filesource: application/controllers/listsdynamic.php
    * @Param:
    * @Return: JSON data
    * @author: Ivaschenko Ludmila
    * @version: 0.0.1
    * @revision:
    * @access: public
    */
    public function deletesomefields()
    {
        // Получаем способ сортировки данных
        $lSortBy = $this->input->post("sort",TRUE);

        // Делаем дополнительную проверку - чтобы нам не пришли другие данные
        $lSortBy = sort_by_asc_desc($lSortBy);

        // Получаем имя поля, по которому будем производить сортировку
        $lFieldName = $this->input->post("field",TRUE);

        // Делаем проверку на существование данных
        if(isset($lSortBy)&&!empty($lSortBy)&&isset($lFieldName)&&!empty($lFieldName))
        {
            // Получаем ID удаляемых полей поля
            $lSomeArray = $this->input->post("somefields",TRUE);
            // Удаляем данные в БД
            $affectedRows = $this->lists_dynamic_model->deleteSomeFields($lSomeArray);

            // Поисковая фраза
            $lSearch = $this->input->post("search",TRUE);
            // Получаем новый список с уже измененными данными
            $this->_pData['lists'] = $this->lists_dynamic_model->getAllLists($this->_Offset, $this->_LimitPerPage, $lSortBy, $lFieldName, $lSearch);

            // Если у нас в БД не осталось записей
            if(count($this->_pData['lists'])==0)
            {
                if(isset($lSearch)&&!empty($lSearch))
                {
                    $lMessage = "Data removed";
                }
                else
                {
                    $lMessage = "Empty Dadabase";
                }

                $this->_pData['emptyDatabase'] = $lMessage;
                echo json_encode(
                    array(
                        "result"=>'empty_database',
                        "data"=>$this->_pData['emptyDatabase']
                        )
                );
            }
            else
            {
 
                // Отдаем данные на Callback функцию
                // Третий аргумент во VIEW отвечает за возврат значения - RETURN
                echo json_encode(
                    array(
                        "result"=>'complete',
                        "data"=>$this->load->view('block_list_table',$this->_pData,TRUE),
                        )
                );
            }
        }
        else
        {
            // Иначе выдаем ошибку отсутствия пришедших данных
            // Отдаем данные на Callback функцию
            echo json_encode(
                array(
                    "result"=>'error',
                    "data"=>"Empty Data"
                    )
            );
        }
    }

}