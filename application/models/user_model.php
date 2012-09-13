<?php
class user_model extends MY_Model
{

        private $table = 'users';

        function __construct()
        {
                parent::__construct();
                $this->load->database();
        }

        function login($sLogin, $sPassword)
        {
                $this->db->from($this->table);
                $this->db->select('*, status &' . USER_STATUS_ADMIN . ' AS admin, status & ' . USER_STATUS_BLOCKED .' AS blocked');
                $this->db->where(array('login'=>$sLogin,'password'=>md5($sPassword)));
                $this->db->where('status & ' . USER_STATUS_BLOCKED .' = 0');
                $this->db->limit(1);
                $oResult = $this->db->get();
                return $oResult->row_array();
        }

        function get($aFilters, $bSingle=FALSE, $aLimit=NULL, $aOrder=NULL)
        {
                $this->db->select('*, status &' . USER_STATUS_ADMIN . ' AS admin');
                $this->db->from($this->table);
                if($aFilters)
                {
                        $this->db->where($aFilters);
                }                
                if ($aLimit)
                {
                        if (is_array($aLimit))
                        {
                                $this->db->limit($aLimit['0'], $aLimit['1']);
                        } else if (is_numeric($aLimit))
                        {
                                $this->db->limit($aLimit);
                        }
                }
                if ($aOrder)
                {
                        foreach ($aOrder as $key => $value)
                        {
                                $this->db->order_by($key, $value); // $value = "desc"/"asc"
                        }
                }
                $oRes = $this->db->get();
                return $bSingle?$oRes->row_array():$oRes->result_array();
        }

        function save($aData, $nUserId = NULL)
        {
                if($nUserId)
                {
                        $this->db->where(array('id'=>$nUserId));
                        $this->db->update($this->table, $aData);
                        return $this->db->affected_rows();
                }
                else
                {
                        $this->db->insert($this->table, $aData);
                        return $this->db->insert_id();
                }
        }

        function email_exists($sEmail, $nCurrentUserId = NULL)
        {
                $this->db->select('id');
                $this->db->from($this->table);
                $this->db->where(array('email'=>$sEmail));
                if($nCurrentUserId)
                {
                        $this->db->where('id != ' . $nCurrentUserId);
                }
                $oRes = $this->db->get();
                return $oRes->num_rows();
        }
        function login_exists($sLogin, $nCurrentUserId = NULL)
        {
                $this->db->select('id');
                $this->db->from($this->table);
                $this->db->where(array('login'=>$sLogin));
                if($nCurrentUserId)
                {
                        $this->db->where('id != ' . $nCurrentUserId);
                }
                $oRes = $this->db->get();
                return $oRes->num_rows();
        }

        function delete($aFilters)
        {
                $this->db->where($aFilters);
                $this->db->delete($this->table);
                return $this->db->affected_rows();
        }

        function get_count($aFilters = NULL)
        {
                $this->db->select('count( id ) AS count');
                $this->db->from($this->table);
                if($aFilters)
                {
                        $this->db->where($aFilters);
                }
                
                $oResult = $this->db->get();
                $aResult = $oResult->row_array();
                return $aResult['count'];
        }
}
?>