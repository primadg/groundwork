<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Lists Controller class
* @date: 11-June-2012
* @Purpose: This controller handles all the Lists functionalities.
* @filesource: application/controllers/lists.php
* @author:  Vyacheslav Isaev
* @version: 0.0.2 
* @revision: 
**/
class Lists extends MY_Controller {
    
    protected $_LimitPerPage;
    protected $_CurrentPage;
    protected $_TotalPageCount;

    function __construct()
    {
        // Call the parent Protocontroller constructor
        parent::__construct();
        // Устанавливаем лимит для количества отобращенных элементов на одной странице
        $this->_LimitPerPage = 10;
        // Изначально устанавливаем - что мы находимся на первой странице
        $this->_CurrentPage = 1;
        // Для отображения на странице общего количества записей в БД
        // По умолчанию ставим 0
        $this->_pData['totalCountInDB'] = 0;
        // Вызов нашел модели для работы со списком
        $this->load->model('lists_model');
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
    public function index()
    {
        
        // Получаем общее количество записей в таблице
        $this->_TotalPageCount = $this->lists_model->getCountLists();
        // Для отображения на странице общего количества записей в БД в самом первом показе
        $this->_pData['totalCountInDB'] = $this->_TotalPageCount;
        // Получаем список с таблицы
        $this->_pData['lists'] = $this->lists_model->getAllLists($this->_CurrentPage,$this->_LimitPerPage);
        // Строим наш pagination
        $this->_pData['pagination'] = get_paginator($this->_TotalPageCount, $this->_CurrentPage, $this->_LimitPerPage);

        // Получаем с какого по какую запись показываем на странице
        $this->_FirstLastNumber();
        
        // В первый файл передаем все наши данные
        $this->load->view('includes/header',  $this->_pData);
        $this->load->view('list');
        $this->load->view('includes/footer');
    }
    
    /**
    * Method : pagination 
    * @date: 11-June-2012
    * @Purpose: Index Page
    * @filesource: application/controllers/lists.php
    * @Param: 
    * @Return: JSON string
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision: 
    * @access: public
    */
    public function pagination()
    {
        // Получаем количество отображаемых елементов на странице
        $lItemsPerPage = $this->input->post("itemsperpage",TRUE);
        // Если мы получили данные
        if(isset($lItemsPerPage)&&!empty($lItemsPerPage))
        {
            // Если количество больше чем по умолчанию
            if($lItemsPerPage>10)
            {
                // Выставляем новый лимит
                $this->_LimitPerPage = intval($lItemsPerPage);
            }
        }
        
        // Получаем способ сортировки данных
        $lSortBy = $this->input->post("sort",TRUE);
        
        // Делаем дополнительную проверку - чтобы нам не пришли другие данные
        $lSortBy = sort_by_asc_desc($lSortBy);

        // Получаем имя поля, по которому будем производить сортировку
        $lFieldName = $this->input->post("field",TRUE);
        
        // Делаем проверку на существование данных
        if(isset($lSortBy)&&!empty($lSortBy)&&isset($lFieldName)&&!empty($lFieldName))
        {
            // Получаем текущюю страницу
            $this->_CurrentPage = intval($this->input->post("current_page"));
                
            // Поисковая фраза
            $lSearch = $this->input->post("search",TRUE);
            // Если у нас присутствует поисковая фраза
            if(isset($lSearch)&&!empty($lSearch))
            {
                // Получаем общее количество записей в таблице
                $this->_TotalPageCount = $this->lists_model->getCountListsBySearch($lSearch);
                // Получаем новый список с уже измененными данными
                $this->_pData['lists'] = $this->lists_model->getAllLists($this->_CurrentPage, $this->_LimitPerPage, $lSortBy, $lFieldName, $lSearch);

            }
            else
            {
                // Если поисковая фраза пуста - используем обычную паджинацию

                // Получаем общее количество записей в таблице
                $this->_TotalPageCount = $this->lists_model->getCountLists();
                // Получаем список с таблицы
                $this->_pData['lists'] = $this->lists_model->getAllLists($this->_CurrentPage,$this->_LimitPerPage, $lSortBy, $lFieldName);
            }
            // Строим наш pagination
            $this->_pData['pagination'] = get_paginator($this->_TotalPageCount, $this->_CurrentPage, $this->_LimitPerPage);
            
            // Получаем с какого по какую запись показываем на странице
            $this->_FirstLastNumber();
            
            // Отдаем на Callback функцию
            echo json_encode(
                array(
                    "result"=>'complete',
                    "data"=>$this->load->view('lists_table',$this->_pData,TRUE),
                    "pagination" => $this->_pData['pagination'],
                    "totalCountInDB"=>$this->_TotalPageCount,
                    "lastNumber"=>$this->_pData['lastNumber'],
                    "firstNumber"=>$this->_pData['firstNumber']
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
    public function updatefield()
    {
        if($this->input->post())
        {
            // Получаем количество отображаемых елементов на странице
            $lItemsPerPage = $this->input->post("itemsperpage",TRUE);
            // Если мы получили данные
            if(isset($lItemsPerPage)&&!empty($lItemsPerPage))
            {
                // Если количество больше чем по умолчанию
                if($lItemsPerPage>10)
                {
                    // Выставляем новый лимит
                    $this->_LimitPerPage = intval($lItemsPerPage);
                }
            }
            
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
                    $fieldId = intval($this->input->post("fieldId",TRUE));

                    // Обнавляем данные в БД
                    $this->lists_model->updateOneField($fieldId,$updateData);

                    // Получаем текущюю страницу
                    $this->_CurrentPage = intval($this->input->post("current_page"));
                   
                    // Поисковая фраза
                    $lSearch = $this->input->post("search",TRUE);
                    // Если у нас присутствует поисковая фраза
                    if(isset($lSearch)&&!empty($lSearch))
                    {
                        // Получаем общее количество записей в таблице
                        $this->_TotalPageCount = $this->lists_model->getCountListsBySearch($lSearch);
                        // Получаем новый список с уже измененными данными
                        $this->_pData['lists'] = $this->lists_model->getAllLists($this->_CurrentPage, $this->_LimitPerPage, $lSortBy, $lFieldName, $lSearch);

                    }
                    else
                    {
                        // Если поисковая фраза пуста - используем обычную паджинацию

                        // Получаем общее количество записей в таблице
                        $this->_TotalPageCount = $this->lists_model->getCountLists();
                        // Получаем список с таблицы
                        $this->_pData['lists'] = $this->lists_model->getAllLists($this->_CurrentPage,$this->_LimitPerPage, $lSortBy, $lFieldName);
                    }
                    
                    // Строим наш pagination
                    $this->_pData['pagination'] = get_paginator($this->_TotalPageCount, $this->_CurrentPage, $this->_LimitPerPage);

                    // Получаем с какого по какую запись показываем на странице
                    $this->_FirstLastNumber();
            
                    // Отдаем данные на Callback функцию
                    // Третий аргумент во VIEW отвечает за возврат значения - RETURN
                    echo json_encode(
                        array(
                            "result"=>'complete',
                            "data"=>$this->load->view('lists_table',$this->_pData,TRUE),
                            "pagination" => $this->_pData['pagination'],
                            "totalCountInDB"=>$this->_TotalPageCount,
                            "lastNumber"=>$this->_pData['lastNumber'],
                            "firstNumber"=>$this->_pData['firstNumber']
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
    public function deletefield()
    {
        // Получаем количество отображаемых елементов на странице
        $lItemsPerPage = $this->input->post("itemsperpage",TRUE);
        // Если мы получили данные
        if(isset($lItemsPerPage)&&!empty($lItemsPerPage))
        {
            // Если количество больше чем по умолчанию
            if($lItemsPerPage>10)
            {
                // Выставляем новый лимит
                $this->_LimitPerPage = intval($lItemsPerPage);
            }
        }
        
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
            $fieldId = intval($this->input->post("fieldId",TRUE));

            // Получаем текущюю страницу
            $this->_CurrentPage = intval($this->input->post("current_page"));

            // Обновляем данные в БД
            $affectedRows = $this->lists_model->deleteOneField($fieldId);

            // Поисковая фраза
            $lSearch = $this->input->post("search",TRUE);
            // Если у нас присутствует поисковая фраза
            if(isset($lSearch)&&!empty($lSearch))
            {
                // Получаем общее количество записей в таблице
                $this->_TotalPageCount = $this->lists_model->getCountListsBySearch($lSearch);
                // Получаем новый список с уже измененными данными
                $this->_pData['lists'] = $this->lists_model->getAllLists($this->_CurrentPage, $this->_LimitPerPage, $lSortBy, $lFieldName, $lSearch);

            }
            else
            {
                // Если поисковая фраза пуста - используем обычную паджинацию

                // Получаем общее количество записей в таблице
                $this->_TotalPageCount = $this->lists_model->getCountLists();
                // Получаем список с таблицы
                $this->_pData['lists'] = $this->lists_model->getAllLists($this->_CurrentPage,$this->_LimitPerPage, $lSortBy, $lFieldName);
            }
            
            // Строим наш pagination
            $this->_pData['pagination'] = get_paginator($this->_TotalPageCount, $this->_CurrentPage, $this->_LimitPerPage);

            // Если у нас получилось обновить запись
            if($affectedRows>0)
            {
                // Получаем с какого по какую запись показываем на странице
                $this->_FirstLastNumber();
                // Отдаем данные на Callback функцию
                // Третий аргумент во VIEW отвечает за возврат значения - RETURN
                echo json_encode(
                    array(
                        "result"=>'complete',
                        "data"=>$this->load->view('lists_table',$this->_pData,TRUE),
                        "pagination" => $this->_pData['pagination'],
                        "totalCountInDB"=>$this->_TotalPageCount,
                        "lastNumber"=>$this->_pData['lastNumber'],
                        "firstNumber"=>$this->_pData['firstNumber']
                        )
                );
            }
            else // Если мы не обновили строку
            {
                // Отдаем данные на Callback функцию
                echo json_encode(
                    array(
                        "result"=>'error',
                        "data"=>"Error Update"
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
    public function getfield($fieldId)
    {
        // Получение значения одного поля по его ID
        $this->_pData['field'] = $this->lists_model->getOneField(intval($fieldId));
        
        // Отдаем данные на Callback функцию
        echo json_encode(
            array(
                "result"=>'complete',
                "data"=>$this->_pData['field']
                )
        );
    }
    
    /**
    * Method : sorttable 
    * @date: 14-June-2012
    * @Purpose: AJAX method - sorting data in table
    * @filesource: application/controllers/lists.php
    * @Param: 
    * @Return: JSON data
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision: 
    * @access: public
    */
    public function sorttable()
    {
        // Получаем количество отображаемых елементов на странице
        $lItemsPerPage = $this->input->post("itemsperpage",TRUE);
        // Если мы получили данные
        if(isset($lItemsPerPage)&&!empty($lItemsPerPage))
        {
            // Если количество больше чем по умолчанию
            if($lItemsPerPage>10)
            {
                // Выставляем новый лимит
                $this->_LimitPerPage = intval($lItemsPerPage);
            }
        }
        
        // Получаем способ сортировки данных
        $lSortBy = $this->input->post("sort",TRUE);
        
        // Делаем дополнительную проверку - чтобы нам не пришли другие данные
        $lSortBy = sort_by_asc_desc($lSortBy);

        // Получаем имя поля, по которому будем производить сортировку
        $lFieldName = $this->input->post("field",TRUE);
        
        // Делаем проверку на существование данных
        if(isset($lSortBy)&&!empty($lSortBy)&&isset($lFieldName)&&!empty($lFieldName))
        {
            // Получаем текущюю страницу
            $this->_CurrentPage = intval($this->input->post("current_page"));

            // Поисковая фраза
            $lSearch = $this->input->post("search",TRUE);
            // Если у нас присутствует поисковая фраза
            if(isset($lSearch)&&!empty($lSearch))
            {
                // Получаем общее количество записей в таблице
                $this->_TotalPageCount = $this->lists_model->getCountListsBySearch($lSearch);
                // Получаем новый список с уже измененными данными
                $this->_pData['lists'] = $this->lists_model->getAllLists($this->_CurrentPage, $this->_LimitPerPage, $lSortBy, $lFieldName, $lSearch);

            }
            else
            {
                // Если поисковая фраза пуста - используем обычную паджинацию

                // Получаем общее количество записей в таблице
                $this->_TotalPageCount = $this->lists_model->getCountLists();
                // Получаем список с таблицы
                $this->_pData['lists'] = $this->lists_model->getAllLists($this->_CurrentPage,$this->_LimitPerPage, $lSortBy, $lFieldName);
            }
            
            // Строим наш pagination
            $this->_pData['pagination'] = get_paginator($this->_TotalPageCount, $this->_CurrentPage, $this->_LimitPerPage);
            
            // Получаем с какого по какую запись показываем на странице
            $this->_FirstLastNumber();
            
            // Отдаем данные на Callback функцию
            echo json_encode(
                array(
                    "result"=>'complete',
                    "data"=>$this->load->view('lists_table',$this->_pData,TRUE),
                    "pagination" => $this->_pData['pagination'],
                    "totalCountInDB"=>$this->_TotalPageCount,
                    "lastNumber"=>$this->_pData['lastNumber'],
                    "firstNumber"=>$this->_pData['firstNumber']
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
    * @date: 14-June-2012
    * @Purpose: AJAX method - search data in table
    * @filesource: application/controllers/lists.php
    * @Param: 
    * @Return: JSON data
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision: 
    * @access: public
    */
    public function search()
    {
        // Получаем количество отображаемых елементов на странице
        $lItemsPerPage = $this->input->post("itemsperpage",TRUE);
        // Если мы получили данные
        if(isset($lItemsPerPage)&&!empty($lItemsPerPage))
        {
            // Если количество больше чем по умолчанию
            if($lItemsPerPage>10)
            {
                // Выставляем новый лимит
                $this->_LimitPerPage = intval($lItemsPerPage);
            }
        }
        
        // Получаем способ сортировки данных
        $lSortBy = $this->input->post("sort",TRUE);
        
        // Делаем дополнительную проверку - чтобы нам не пришли другие данные
        $lSortBy = sort_by_asc_desc($lSortBy);

        // Получаем имя поля, по которому будем производить сортировку
        $lFieldName = $this->input->post("field",TRUE);
        
        // Делаем проверку на существование данных
        if(isset($lSortBy)&&!empty($lSortBy)&&isset($lFieldName)&&!empty($lFieldName))
        {
            // Получаем текущюю страницу
            $this->_CurrentPage = intval($this->input->post("current_page"));

            // Поисковая фраза
            $lSearch = $this->input->post("search",TRUE);
            // Если у нас присутствует поисковая фраза
            if(isset($lSearch)&&!empty($lSearch))
            {
                // Получаем общее количество записей в таблице
                $this->_TotalPageCount = $this->lists_model->getCountListsBySearch($lSearch);
                // Получаем новый список с уже измененными данными
                $this->_pData['lists'] = $this->lists_model->getAllLists($this->_CurrentPage, $this->_LimitPerPage, $lSortBy, $lFieldName, $lSearch);

            }
            else
            {
                // Если поисковая фраза пуста - используем обычную паджинацию

                // Получаем общее количество записей в таблице
                $this->_TotalPageCount = $this->lists_model->getCountLists();
                // Получаем список с таблицы
                $this->_pData['lists'] = $this->lists_model->getAllLists($this->_CurrentPage,$this->_LimitPerPage, $lSortBy, $lFieldName);
            }
            
            // Строим наш pagination
            $this->_pData['pagination'] = get_paginator($this->_TotalPageCount, $this->_CurrentPage, $this->_LimitPerPage);
            
            // Получаем с какого по какую запись показываем на странице
            $this->_FirstLastNumber();
            
            // Отдаем данные на Callback функцию
            echo json_encode(
                array(
                    "result"=>'complete',
                    "data"=>$this->load->view('lists_table',$this->_pData,TRUE),
                    "pagination" => $this->_pData['pagination'],
                    "totalCountInDB"=>$this->_TotalPageCount,
                    "lastNumber"=>$this->_pData['lastNumber'],
                    "firstNumber"=>$this->_pData['firstNumber']
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
    * Method : itemsperpage 
    * @date: 14-June-2012
    * @Purpose: AJAX method - filter items per page
    * @filesource: application/controllers/lists.php
    * @Param: 
    * @Return: JSON data
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision: 
    * @access: public
    */
    public function itemsperpage()
    {
        // Получаем количество отображаемых елементов на странице
        $lItemsPerPage = $this->input->post("itemsperpage",TRUE);
        // Если мы получили данные
        if(isset($lItemsPerPage)&&!empty($lItemsPerPage))
        {
            // Если количество больше чем по умолчанию
            if($lItemsPerPage>10)
            {
                // Выставляем новый лимит
                $this->_LimitPerPage = intval($lItemsPerPage);
            }
        }
        
        // Получаем способ сортировки данных
        $lSortBy = $this->input->post("sort",TRUE);
        
        // Делаем дополнительную проверку - чтобы нам не пришли другие данные
        $lSortBy = sort_by_asc_desc($lSortBy);

        // Получаем имя поля, по которому будем производить сортировку
        $lFieldName = $this->input->post("field",TRUE);
        
        // Делаем проверку на существование данных
        if(isset($lSortBy)&&!empty($lSortBy)&&isset($lFieldName)&&!empty($lFieldName))
        {
            // Получаем текущюю страницу
            $this->_CurrentPage = intval($this->input->post("current_page"));

            // Поисковая фраза
            $lSearch = $this->input->post("search",TRUE);
            // Если у нас присутствует поисковая фраза
            if(isset($lSearch)&&!empty($lSearch))
            {
                // Получаем общее количество записей в таблице
                $this->_TotalPageCount = $this->lists_model->getCountListsBySearch($lSearch);
                // Получаем новый список с уже измененными данными
                $this->_pData['lists'] = $this->lists_model->getAllLists($this->_CurrentPage, $this->_LimitPerPage, $lSortBy, $lFieldName, $lSearch);

            }
            else
            {
                // Если поисковая фраза пуста - используем обычную паджинацию

                // Получаем общее количество записей в таблице
                $this->_TotalPageCount = $this->lists_model->getCountLists();
                // Получаем список с таблицы
                $this->_pData['lists'] = $this->lists_model->getAllLists($this->_CurrentPage,$this->_LimitPerPage, $lSortBy, $lFieldName);
            }
            
            // Строим наш pagination
            $this->_pData['pagination'] = get_paginator($this->_TotalPageCount, $this->_CurrentPage, $this->_LimitPerPage);
            
            // Получаем с какого по какую запись показываем на странице
            $this->_FirstLastNumber();
            
            // Отдаем данные на Callback функцию
            echo json_encode(
                array(
                    "result"=>'complete',
                    "data"=>$this->load->view('lists_table',$this->_pData,TRUE),
                    "pagination" => $this->_pData['pagination'],
                    "totalCountInDB"=>$this->_TotalPageCount,
                    "lastNumber"=>$this->_pData['lastNumber'],
                    "firstNumber"=>$this->_pData['firstNumber']
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
    * Method : _FirstLastNumber 
    * @date: 14-June-2012
    * @Purpose: calculation of the number of fields displayed
    * @filesource: application/controllers/lists.php
    * @Param: 
    * @Return: void
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision: 
    * @access: private
    */
    private function _FirstLastNumber()
    {
        // Получаем с какого по какую запись показываем на странице
        $this->_pData['lastNumber'] = intval($this->_CurrentPage * $this->_LimitPerPage);
        $this->_pData['firstNumber'] =  ($this->_pData['lastNumber'] - ($this->_LimitPerPage-1));
        // Если последние число больше количества записей в БД
        if($this->_pData['lastNumber']>$this->_TotalPageCount)
        {
            // Выставляем его равным кол-ву записей в БД
            $this->_pData['lastNumber'] = $this->_TotalPageCount;
        }
    }
}