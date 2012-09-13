<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//==============================================================================
/**
* Method : generate_string
* @date 13-April-2012
* @Purpose: Generate a string
* @filesource: custom_helper.php
* @Param: int $length - length string
* @Return: string
* @author: Vyacheslav Isaev
* @version: 0.0.1
 */
if (!function_exists('generate_string'))
{
    function generate_string($length=1)
    {
        $arr = array('a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l',
            'm', 'n', 'o', 'p', 'r', 's',
            't', 'u', 'v', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F',
            'G', 'H', 'I', 'J', 'K', 'L',
            'M', 'N', 'O', 'P', 'R', 'S',
            'T', 'U', 'V', 'X', 'Y', 'Z');
        $key = "";
        for ($i = 0; $i < $length; $i++)
        {
            $index = rand(0, count($arr) - 1);
            $key .= $arr[$index];
        }
        return $key;
    }
}
//==============================================================================
//******************************************************************************************
/**
* Method : get_paginator
* @date 8-June-2012
* @Purpose: Paginator
* @filesource: custom_helper.php
* @Param: 
* @Return: HTML code - div paginator
* @author:    Vyacheslav Isaev
* @version: 0.0.2
 */
if (!function_exists('get_paginator'))
{
    function get_paginator($total_count,$page,$limit = 20,$adjacents=3, $class = 'ppages')
    {
        
        /* Setup page vars for display. */
        if ($page <= 0) $page = 1;                            //if no page var is given, default to 1.
        $prev = $page - 1;                                    //previous page is page - 1
        $next = $page + 1;                                    //next page is page + 1
        $lastpage = ceil($total_count/$limit);                //lastpage is = total pages / items per page, rounded up.
        $lpm1 = $lastpage - 1;                                //last page minus 1

        /*
        Now we apply our rules and draw the pagination object.
        We're actually saving the code to a variable in case we want to draw it more than once.
        */
        $pagination = "";
        if($lastpage > 1)
        {
            $pagination .= "<div class=\"pagination\"><ul class=\"pages " . $class . "\">";
            //previous button
            if ($page > 1)
            {
                $pagination.= "<li class=\"prev\"><a page=\"$prev\" href=\"javascript:void(0);\">&lt;</a></li>";
            }
            else
            {
                $pagination.= "<li class=\"prev\"><a class=\"ui-state-disabled\" href=\"javascript:void(0);\">&lt;</a></li>";
            }

            //pages
            if ($lastpage < 7 + ($adjacents * 2))        //not enough pages to bother breaking it up
            {
                for ($counter = 1; $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                    {
                        $pagination.= "<li><a href=\"javascript:void(0);\" class=\"active\">$counter</a></li>";
                    }
                    else
                    {
                        $pagination.= "<li><a page=\"$counter\" href=\"javascript:void(0);\">$counter</a></li>";
                    }
                }
            }
            elseif($lastpage > 5 + ($adjacents * 2))        //enough pages to hide some
            {                
                //close to beginning; only hide later pages
                if($page < 1 + ($adjacents * 2))
                {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                    {
                        if ($counter == $page)
                        {
                            $pagination.= "<li><a href=\"javascript:void(0);\" class=\"active\">$counter</a></li>";
                        }
                        else
                        {
                            $pagination.= "<li><a page=\"$counter\" href=\"javascript:void(0);\">$counter</a></li>";
                        }
                    }
                    $pagination.= "<li>...</li>";
                    $pagination.= "<li><a page=\"$lpm1\" href=\"javascript:void(0);\">$lpm1</a></li>";
                    $pagination.= "<li><a page=\"$lastpage\" href=\"javascript:void(0);\">$lastpage</a></li>";
                }//in middle; hide some front and some back            
                elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
                {
                    $pagination.= "<li><a page=\"1\" href=\"javascript:void(0);\">1</a></li>";
                    $pagination.= "<li><a page=\"2\" href=\"javascript:void(0);\">2</a></li>";
                    $pagination.= "<li>...</li>";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                    {
                        if ($counter == $page)
                        {
                            $pagination.= "<li><a href=\"javascript:void(0);\" class=\"active\">$counter</a></li>";
                        }
                        else
                        {
                            $pagination.= "<li><a page=\"$counter\" href=\"javascript:void(0);\">$counter</a></li>";
                        }
                    }
                    $pagination.= "<li>...</li>";
                    $pagination.= "<li><a page=\"$lpm1\" href=\"javascript:void(0);\">$lpm1</a></li>";
                    $pagination.= "<li><a page=\"$lastpage\" href=\"javascript:void(0);\">$lastpage</a></li>";
                    
                }//close to end; only hide early pages            
                else
                {                   
                    $pagination.= "<li><a page=\"1\" href=\"javascript:void(0);\">1</a></li>";
                    $pagination.= "<li><a page=\"2\" href=\"javascript:void(0);\">2</a></li>";
                    
                    $pagination.= "<li>...</li>";
                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                    {
                        if ($counter == $page)
                        {
                            $pagination.= "<li><a href=\"javascript:void(0);\" class=\"active\">$counter</a></li>";
                        }
                        else
                        {
                            $pagination.= "<li><a page=\"$counter\" href=\"javascript:void(0);\">$counter</a></li>";
                        }
                    }
                } 
            }

            //next button
            if ($page < $counter - 1)
            {
                $pagination.= "<li class=\"next\"><a page=\"$next\" href=\"javascript:void(0);\">&gt;</a></li>";
            }
            else
            {
                $pagination.= "<li class=\"next\"><a class=\"ui-state-disabled\" href=\"javascript:void(0);\">&gt;</a></li>";
                
            }
            $pagination.= "</ul></div>\n";
        }
        return $pagination;
    }
}
//==============================================================================
/**
* Method : sort_by_asc_desc
* @date 14-June-2012
* @Purpose: If we did not come to ASC, we assign to any DESC
* @filesource: custom_helper.php
* @Param: string $aSortBy
* @Return: string
* @author: Vyacheslav Isaev
* @version: 0.0.1
 */
if (!function_exists('sort_by_asc_desc'))
{
    function sort_by_asc_desc($aSortBy)
    {
        // Делаем дополнительную проверку - чтобы нам не пришли другие данные
        if ($aSortBy != 'asc')
        {
            $aSortBy = 'desc';
        }

        return $aSortBy;
    }
}
//==============================================================================

				
			
