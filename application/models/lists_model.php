<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Lists Model class
* @date: 11-June-2012
* @Purpose: This model all the functionalities for Lists.
* @filesource: application/models/lists_model.php
* @author:    Vyacheslav Isaev
* @version: 0.0.1
* @revision: 
**/


class Lists_model extends MY_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        // Присваиваем значение таблицы с которой будет работать данная модель
        $this->_pTableName = "list";
        // Инициализируем список полей нашей таблицы
        $this->_pFields = array(
            "id"=>TRUE,
            "name"=>TRUE, 
            "date_of_birth"=>TRUE,
            "phone"=>TRUE,
            "email"=>TRUE
        );
        
    }
    
    /**
    * Method : getCountLists
    * @Date: 11-June-2012
    * @Purpose: Get count data in table
    * @filesource application/models/lists_model.php
    * @Param:
    * @Return: int
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision:
    **/
    function getCountLists()
    {
        return $this->db->count_all($this->_pTableName);
    }
    
    /**
    * Method : getCountListsBySearch
    * @Date: 14-June-2012
    * @Purpose: Get count data in table by search
    * @filesource application/models/lists_model.php
    * @Param: string $aSearch
    * @Return: int
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision:
    **/
    function getCountListsBySearch($aSearch=NULL)
    {
        if($aSearch)
        {
            // Делаем поиск по всем полям
            $this->db->like('name', $aSearch);
            $this->db->or_like('date_of_birth', $aSearch);
            $this->db->or_like('phone', $aSearch);
            $this->db->or_like('email', $aSearch);

            $this->db->from($this->_pTableName);
            // Возвращаем количество строк в БД
            // с использование поисковой фразы
            return $this->db->count_all_results();
        }
    }
    
    
    /**
    * Method : getAllLists
    * @Date: 11-June-2012
    * @Purpose: Get all data on table for pagination
    * @filesource application/models/lists_model.php
    * @Param: int $aCurrentPage
    * @Param: int $aLimitPerPage
    * @Param: string $aSort ( asc OR desc )
    * @Param: string $aSortField
    * @Param: string $aSearch
    * @Return: array
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision:
    **/
    function getAllLists($aCurrentPage=1,$aLimitPerPage=10, $aSort = NULL, $aSortField = NULL, $aSearch = NULL)
    {
        // Сразу приводим пришедшие данные к интовому значению
        $aCurrentPage = intval($aCurrentPage);
        $aLimitPerPage = intval($aLimitPerPage);
        
        // Делаем проверку - если текущая страница меньше 1
        // выставляем значение по умолчанию
        if($aCurrentPage<1)
        {
            $aCurrentPage = 1;
        }
        
        // Если лимит показа на странице меньше 1 
        if($aLimitPerPage < 1)
        {
            $aLimitPerPage = 1;
        }
        // Вычесляем с какой записи делать запрос в БД
        $start = (($aCurrentPage-1)*$aLimitPerPage);
        // Делаем выборку по нашим полям
        $this->db->select('id, name, date_of_birth, phone, email');
                
        // Если мы подключили поиск по таблице
        if($aSearch)
        {
            // Делаем поиск по всем полям
            $this->db->like('name', $aSearch);
            $this->db->or_like('date_of_birth', $aSearch);
            $this->db->or_like('phone', $aSearch);
            $this->db->or_like('email', $aSearch);
        }
        
        // Если мы указали как сортируем и по какому полю
        if($aSort && $aSortField)
        {
            $this->db->order_by($aSortField, $aSort);
        }
        else
        {
            // Иначе применяем сортировку по умолчанию
            $this->db->order_by("id", "asc");
        }
        
        $this->db->from($this->_pTableName);
        $this->db->limit($aLimitPerPage, $start);
        
        $query = $this->db->get();

        $rez = array();

        if($query->num_rows() > 0)
        {
            $rez = $query->result_array();
        }
        // Освобождаем память 
        $query->free_result();
        
        return $rez;
    }
    
    /**
    * Method : deleteOneField
    * @Date: 11-June-2012
    * @Purpose: Delete one field in table
    * @filesource application/models/lists_model.php
    * @Param: int $aFieldId
    * @Return: int affected_rows
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision:
    **/
    function deleteOneField($aFieldId)
    {
        // Сразу приводим пришедшие данные к интовому значению
        $aFieldId = intval($aFieldId);
        // Удаляем запись в таблице по ее ID
        $this->db->delete($this->_pTableName, array('id' => $aFieldId));
        // Возвращаем кол-во затронутых строк
        return $this->db->affected_rows();
    }
    
    /**
    * Method : deleteOneField
    * @Date: 15-June-2012
    * @Purpose: Delete some fields in table
    * @filesource application/models/lists_model.php
    * @Param: array $aSomeArray
    * @Return: int affected_rows
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision:
    **/
    function deleteSomeFields($aSomeArray)
    {
        // Делаем проверку, что пришедшие данные являются массивом
        if(is_array($aSomeArray) && !empty($aSomeArray))
        {
            //приведение типа элементов массива к int
            $aSomeArray = array_map('intval', $aSomeArray);
            //собираем запрос
            $this->db->where_in('id', $aSomeArray);
            // Удаляем записи в таблице передав массив ID
            $this->db->delete($this->_pTableName);
            // Возвращаем кол-во затронутых строк
            return $this->db->affected_rows();
        }
    }
    
    /**
    * Method : updateOneField
    * @Date: 11-June-2012
    * @Purpose: Update one field in table
    * @filesource application/models/lists_model.php
    * @Param: int $aFieldId
    * @Param: array $data
    * @Return: int - count affected rows
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision:
    **/
    function updateOneField($aFieldId,$aData)
    {
        // Сразу приводим пришедшие данные к интовому значению
        $aFieldId = intval($aFieldId);
        // Делаем проверку на существование, пустату и 
        // что пришедшие данные являются массивом
        if(isset($aData)&&!empty($aData)&&is_array($aData))
        {
            $this->db->where('id', $aFieldId);
            $saveData = array();
            if($this->_SaveFields($aData, $saveData))
            {
                $this->db->update($this->_pTableName, $saveData);
            }
            // Возвращаем кол-во затронутых строк
            return $this->db->affected_rows();
        }
    }
    
     /**
    * Method : getOneField
    * @Date: 11-June-2012
    * @Purpose: Get on field
    * @filesource application/models/lists_model.php
    * @Param: int $aFieldId
    * @Return: array
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision:
    **/
    function getOneField($aFieldId)
    {
        // Сразу приводим пришедшие данные к интовому значению
        $aFieldId = intval($aFieldId);
        
        $this->db->select('id, name, date_of_birth, phone, email');
        $this->db->from($this->_pTableName);
        $this->db->where('id',$aFieldId);
        
        $query = $this->db->get();

        $rez = array();

        if($query->num_rows() > 0)
        {
            $res = $query->result_array();
            // Так как мы получаем только одно поле
            // берем только первый массив
            $rez = $res[0];
            // Освобождаем память 
            unset($res);
        }
        // Освобождаем память 
        $query->free_result();
        
        return $rez;
    }
    
}