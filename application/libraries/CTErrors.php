<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define('LANG_DEFAULT', 'en');
class CTErrors
{
        private $aErrorStack;
        private $bSuccess;
        private $aLanguage;

        function  __construct($aLandDefinition, $cLanguage)
        {
                $this->aLanguage = isset($aLandDefinition[$cLanguage])?$aLandDefinition[$cLanguage]:$aLandDefinition[$cLanguage];
                $this->bSuccess = true;
                $this->aErrorStack = array();
        }

        function set($sKey)
        {
                if(isset($this->aLanguage[$sKey]))
                        $this->aErrorStack[] = $this->aLanguage[$sKey];
                else
                        $this->aErrorStack[] = $sKey;
                $this->bSuccess = false;
        }

        function no()
        {
                return $this->bSuccess;
        }

        function get()
        {
                return empty($this->aErrorStack)?false:$this->aErrorStack;
        }
}

?>
