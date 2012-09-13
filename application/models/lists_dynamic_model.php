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


class Lists_dynamic_model extends MY_Model
{

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        // Присваиваем значение таблицы с которой будет работать данная модель
        $this->_pTableName = "list";
    }
    
    /**
    * Method : getAllLists
    * @Date: 11-June-2012
    * @Purpose: Get all data on table
    * @filesource application/models/lists_model.php
    * @Param: 
    * @Return: 
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision:
    **/
    function getAllLists($aOffset = 0, $aLimit = 20, $aSort = NULL, $aSortField = NULL, $aSearch = NULL)
    {
        //Приводим данные к интовому значению
        $aOffset = intval($aOffset);
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
        
	if ($aLimit != 'all')
        {
            //Приводим данные к интовому значению
            $aLimit = intval($aLimit);
            /*if ($aLimit <= 0)
            {
                $aLimit = 10;
            }*/

            if ($aOffset > 0)
            {
                $this->db->limit($aLimit, $aOffset);
            }
	    else
            {
                $this->db->limit($aLimit);
            }
        }
	
        $query = $this->db->get();

        $rez = array();

        if($query->num_rows() > 0)
        {
            $rez = $query->result_array();
        }
        $query->free_result();
        
        return $rez;
    }
    
    /**
    * Method : deleteOneField
    * @Date: 11-June-2012
    * @Purpose: Delete one field in table
    * @filesource application/models/lists_model.php
    * @Param: int $field_id
    * @Return: void
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision:
    **/
    function deleteOneField($aFieldId)
    {
        //Приводим пришедшие данные к интовому значению
        $aFieldId = intval($aFieldId);
        if ($this->db->delete($this->_pTableName, array('id' => $aFieldId)))
	{
	   return true;
	}
	return false;
    }
    
    /**
    * Method : updateOneField
    * @Date: 11-June-2012
    * @Purpose: Update one field in table
    * @filesource application/models/lists_model.php
    * @Param: int $field_id
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
        // Делаем проверку на существование, пустоту и
        // что пришедшие данные являются массивом
        if(isset($aData)&&!empty($aData)&&is_array($aData))
        {
            $this->db->where('id', $aFieldId);
            $this->db->update($this->_pTableName, $aData);
            // Возвращаем кол-во затронутых строк
            return $this->db->affected_rows();
        }
    }

    /**
    * Method : deleteOneField
    * @Date: 15-June-2012
    * @Purpose: Delete some fields in table
    * @filesource application/models/lists_dynamic_model.php
    * @Param: array $aSomeArray
    * @Return: int affected_rows
    * @author: Ivaschenko Ludmila
    * @version: 0.0.1
    * @revision:
    **/
    function deleteSomeFields($aSomeArray)
    {
        // Делаем проверку, что пришедшие данные являются массивом
        if(is_array($aSomeArray) && !empty($aSomeArray))
        {
            $this->db->where_in('id', $aSomeArray);
            // Удаляем записи в таблице передав массив ID
            $this->db->delete($this->_pTableName);
            // Возвращаем кол-во затронутых строк
            return $this->db->affected_rows();
        }
    }

     /**
    * Method : getOneField
    * @Date: 11-June-2012
    * @Purpose: Get on field
    * @filesource application/models/lists_model.php
    * @Param: int $field_id
    * @Return: string
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
        $query->free_result();
        // Освобождаем память 
        return $rez;
    }
    
    function addField($aData)
    {
       $this->db->insert($this->_pTableName, $aData);
    }
}