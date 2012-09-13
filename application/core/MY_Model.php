<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* MY_Model class
* @date: 14-June-2012
* @Purpose: ProtoModel
* @filesource: application/core/MY_Model.php
* @author:  Vyacheslav Isaev
* @version: 0.0.1
* @revision: 
**/
class MY_Model extends CI_Model {

    // Для хранения имени таблицы
    // с которой будет работать модель
    protected $_pTableName = "";
    // Для хранения массива полей
    // 'column name'=>required
    protected $_pFields = array(); 
    
    function __construct()
    {
        parent::__construct();
    }
    
    /**
    * Method : _CheckFieldIsset
    * @date: 15-June-2012
    * @Purpose: Copy array with list fields - filter not isset
    * @filesource: application/core/MY_Model.php
    * @Param: reference to array &$aReturnArray
    * @Return: bool
    * @author: Vyacheslav Isaev
    * @version: 0.0.1
    * @revision: 
    * @access: protected
    */
    protected function _CheckFieldIsset(&$aReturnArray)
    {
        
    }
    
    
    /**
    * Method : _SaveFields
    * @date: 14-June-2012
    * @Purpose: Copy array with list fields - filter not isset
    * @filesource: application/core/MY_Model.php
    * @Param: array $aDataArray
    * @Param: reference to array &$aReturnArray
    * @Param: bool $aCheckReq - default FALSE
    * @Return: bool
    * @author: Paul Shchurov
    * @version: 0.0.1
    * @revision: 
    * @access: protected
    */
    protected function _SaveFields($aDataArray, &$aReturnArray, $aCheckReq = FALSE)
    {
        // Если принимающий парементр не является массивом
        if (!is_array($aReturnArray))
        {
            // Переопределяем
            $aReturnArray = array();
        }
        // Если нужно сделать проверку по обязательным полям
        if ($aCheckReq)
        {
            foreach ($this->_pFields as $field_name => $req_flag)
            {
                // Если обязательное поле отсутствует
                if ($req_flag && !isset($aDataArray[$field_name]))
                {
                    // Выходим из функции
                    return FALSE;
                }
            }
        }
        // Копируем данные - если у нас отсутсвуют поля в $this->_pFields
        // мы отсеим эти поля - чтобы в базу не пошел запрос с несуществующими полями
        foreach ($this->_pFields as $field_name => $req_flag)
        {
            
            if (isset($aDataArray[$field_name]))
            {
                $aReturnArray[$field_name] = $aDataArray[$field_name];
            }
        }

        return TRUE;
    }
    
    
    /**
    * Method : _SaveCustomFields
    * @date: 14-June-2012
    * @Purpose: If we want to update two fields, for example, out of 10
    * @filesource: application/core/MY_Model.php
    * @Param: array $aDataArray
    * @Param: reference to array &$aReturnArray
    * @Param: array of fields $aArrayFields
    * @Param: bool $aCheckReq - default FALSE
    * @Return: bool
    * @author: Paul Shchurov
    * @version: 0.0.1
    * @revision: 
    * @access: protected
    */
    protected function _SaveCustomFields($aDataArray, &$aReturnArray, $aArrayFields, $aCheckReq = FALSE)
    {
        // Если принимающий парементр не является массивом
        if(!is_array($aReturnArray))
        {
            // Переопределяем
            $aReturnArray = array();
        }
        // Если нужно сделать проверку по обязательным полям
        if($aCheckReq)
        {
            foreach ($aArrayFields as $field_name => $req_flag)
            {
                // Если обязательное поле отсутствует
                if ($req_flag && !isset($aDataArray[$field_name]))
                {
                    // Выходим из функции
                    return FALSE;
                }
            }
        }
        // Если мы хотим обновить например два поля из 10
        // Копируем данные - если у нас отсутсвуют поля в первом переданном массиве $aDataArray
        // мы отсеим эти поля - чтобы в базу не пошел запрос с несуществующими полями
        foreach ($aArrayFields as $field_name => $req_flag)
        {
            if (isset($aDataArray[$field_name]))
            {
                $aReturnArray[$field_name] = $aDataArray[$field_name];
            }
        }

        return TRUE;
    }
    
    /**
    * Method : _PrepareFields
    * @date: 14-June-2012
    * @Purpose: 
    * @filesource: application/core/MY_Model.php
    * @Param: reference to array &$aReturnArray
    * @Param: array $aPrepareTerms - default NULL
    * @Return: void
    * @author: Paul Shchurov
    * @version: 0.0.1
    * @revision: 
    * @access: protected
    */
    protected function _PrepareFields(&$aReturnArray, $aPrepareTerms = NULL)
    {
        if (is_array($aReturnArray) && is_array($aPrepareTerms))
        {
            foreach ($this->fields as $field_name => $need_flag)
            {

                if (isset($aPrepareTerms[$field_name]) && isset($aReturnArray[$field_name]))
                {
                    $terms = $aPrepareTerms[$field_name];
                    if (is_array($terms) && (count($terms) > 1))
                    {
                        $fnc_name = $aPrepareTerms[$field_name][0];
                        $fnc_args = $aPrepareTerms[$field_name][1];
                        if (is_array($fnc_args))
                        {
                            foreach ($fnc_args as $key => $item)
                            {
                                if ($item == '?')
                                {
                                    $fnc_args[$key] = $aReturnArray[$field_name];
                                }
                            }
                        } //if (is_array($fnc_args))
                        else
                        {
                            $fnc_args = array($aReturnArray[$field_name]);
                        }

                        $rez = call_user_func_array($fnc_name, $fnc_args);
                        if ($rez !== FALSE)
                        {
                            $aReturnArray[$field_name] = $rez;
                        }
                    } //if (is_array($terms) && (count($terms) > 1))
                } //if (isset($prepare_terms[$field_name]) && is_array($prepare_terms[$field_name]))
            } //foreach ($this->fields as $field_name)
        } //if (is_array($data) && is_array($prepare_terms))
    }
    
    /**
    * Method : _PrepareCustomFields
    * @date: 14-June-2012
    * @Purpose: 
    * @filesource: application/core/MY_Model.php
    * @Param: reference to array &$aReturnArray
    * @Param: array $aFieldsList
    * @Param: array $aPrepareTerms - default NULL
    * @Return: void
    * @author: Paul Shchurov
    * @version: 0.0.1
    * @revision: 
    * @access: protected
    */
    protected function _PrepareCustomFields(&$aReturnArray, $aFieldsList = NULL, $aPrepareTerms = NULL)
    {
        if (is_array($aReturnArray) && is_array($aPrepareTerms) && is_array($aFieldsList))
        {
            foreach ($aFieldsList as $field_name => $need_flag)
            { 

                if (isset($aPrepareTerms[$field_name]) && isset($aReturnArray[$field_name]))
                {
                    $terms = $aPrepareTerms[$field_name];
                    if (is_array($terms) && (count($terms) > 1))
                    {
                        $fnc_name = $aPrepareTerms[$field_name][0];
                        $fnc_args = $aPrepareTerms[$field_name][1];
                        if (is_array($fnc_args))
                        {
                            foreach ($fnc_args as $key => $item)
                            {
                                if ($item == '?')
                                {
                                    $fnc_args[$key] = $aReturnArray[$field_name];
                                }
                            }
                        } //if (is_array($fnc_args))
                        else
                        {
                            $fnc_args = array($aReturnArray[$field_name]);
                        }

                        $rez = call_user_func_array($fnc_name, $fnc_args);
                        if ($rez !== FALSE)
                        {
                            $aReturnArray[$field_name] = $rez;
                        }
                    } //if (is_array($terms) && (count($terms) > 1))
                } //if (isset($prepare_terms[$field_name]) && is_array($prepare_terms[$field_name]))
            } //foreach ($this->fields as $field_name)
        } //if (is_array($data) && is_array($prepare_terms))
    }
}