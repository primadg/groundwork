<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* MY_Controller class
* @date: 11-June-2012
* @Purpose: ProtoController
* @filesource: application/core/MY_Controller.php
* @author:  Vyacheslav Isaev
* @version: 0.0.1
* @revision: 
**/
class MY_Controller extends CI_Controller {

    // Для хранения данных
    protected $_pData;
    
    function __construct()
    {
        parent::__construct();
        // Открываем сессию
        session_start();
        // Сразу указываем что наша внутренняя переменная для хранения данных 
        // является массивом
        $this->_pData = array();

        //подключаем смарти и библиотеку ошибок
        $this->load->library('smarty');
        $this->load->library('errors');

        //Подключаем обязательные скрипты
        $this->javascript('jquery-1.7.2.min.js');
        $this->javascript('plugins/spinner/ui.spinner.js');
        $this->javascript('plugins/spinner/jquery.mousewheel.js');
        $this->javascript('jquery-ui-1.8.21.custom.min.js');
        $this->javascript('plugins/charts/excanvas.min.js');
        $this->javascript('plugins/charts/jquery.flot.js');
        $this->javascript('plugins/charts/jquery.flot.orderBars.js');
        $this->javascript('plugins/charts/jquery.flot.pie.js');
        $this->javascript('plugins/charts/jquery.flot.resize.js');
        $this->javascript('plugins/charts/jquery.sparkline.min.js');
        $this->javascript('plugins/forms/uniform.js');
        $this->javascript('plugins/forms/jquery.cleditor.js');
        $this->javascript('plugins/forms/jquery.validationEngine-en.js');
        $this->javascript('plugins/forms/jquery.validationEngine.js');
        $this->javascript('plugins/forms/jquery.tagsinput.min.js');
        $this->javascript('plugins/forms/autogrowtextarea.js');
        $this->javascript('plugins/forms/jquery.maskedinput.min.js');
        $this->javascript('plugins/forms/jquery.dualListBox.js');
        $this->javascript('plugins/forms/jquery.inputlimiter.min.js');
        $this->javascript('plugins/forms/chosen.jquery.min.js');
        $this->javascript('plugins/wizard/jquery.form.js');
        $this->javascript('plugins/wizard/jquery.validate.min.js');
        $this->javascript('plugins/wizard/jquery.form.wizard.js');
        $this->javascript('plugins/uploader/plupload.js');
        $this->javascript('plugins/uploader/plupload.html5.js');
        $this->javascript('plugins/uploader/plupload.html4.js');
        $this->javascript('plugins/uploader/jquery.plupload.queue.js');
        $this->javascript('plugins/tables/datatable.js');
        $this->javascript('plugins/tables/tablesort.min.js');
        $this->javascript('plugins/tables/resizable.min.js');
        $this->javascript('plugins/ui/jquery.tipsy.js');
        $this->javascript('plugins/ui/jquery.collapsible.min.js');
        $this->javascript('plugins/ui/jquery.prettyPhoto.js');
        $this->javascript('plugins/ui/jquery.progress.js');
        $this->javascript('plugins/ui/jquery.timeentry.min.js');
        $this->javascript('plugins/ui/jquery.colorpicker.js');
        $this->javascript('plugins/ui/jquery.jgrowl.js');
        $this->javascript('plugins/ui/jquery.breadcrumbs.js');
        $this->javascript('plugins/ui/jquery.sourcerer.js');
        $this->javascript('plugins/calendar.min.js');
        $this->javascript('plugins/elfinder.min.js');
        $this->javascript('leftmenu.js');

        //подключаем обязательные стили
        $this->stylesheet('main.css');
        $this->stylesheet('style.css');

        //Назначаем глобальные js переменные
        $this->template_var(array(
            'gBaseUrl'=> base_url(),
            'gCurrentPage'=> 1,
            'gSortBy'=> "asc",
            'gSortField'=> "id",
            'gSearch'=> '',
            'gItemsPerPage'=> 10
            ));
        //Назначаем заголовок по умолчанию
        $this->title('Groundwork');
        // Чтобы не вызывать функцию много раз
        // мы используем переменную, что ускоряет работу сайта
        $this->_pData['baseUrl'] = base_url();
        // Вызываем наш хелпер
        $this->load->helper('custom');
        $this->load->model('user_model');
        $this->load->library('auth');        
    }

    protected function display($sTemplate)
    {
        $this->load->view('includes/header',  $this->_pData);
        $this->load->view('includes/left_side');
        $this->load->view($sTemplate);
        $this->load->view('includes/footer');
    }

    /**
     *
     * Генерация html для паджинатора и получение лимитов для запроса
     *
     * @param int $aPage текущая страница
     * @param int $aTotalCount количество элементов
     * @param int $aItemsPerPage количество элементов на страницу
     * @param string $aClass дополнительный класс для контейнера
     * @param string $aStaticLink ссылка на страницу
     * @param string $aTemplate имя шаблона
     * @param int $aVisiblePagesCount количество отображаемых страниц
     * 
     * @return mixed HTML для паджинатора и лимит для запроса
     */
    protected function generatePagination($aPage = 1, $aTotalCount = 0, $aItemsPerPage = 10, $aClass = '', $aStaticLink = false, $aTemplate = 'general', $aVisiblePagesCount = 10)
    {
        if($aPage<0)
        {
            $aPage = 1;
        }
        $LastPage = ceil($aTotalCount/$aItemsPerPage);
        if($LastPage == 1)
        {
            return array('html'=>'','limit'=>$aItemsPerPage, 'offset'=>false);
        }
        if($aPage>$LastPage)
        {
            $aPage = $LastPage;
        }
        $PaginationData = array();
        $PaginationData['last_page'] = $LastPage;
        $PaginationData['current_page'] = $aPage;
        $PaginationData['link'] = $aStaticLink;
        $PaginationData['class'] = $aClass;
        
        if(!file_exists(FCPATH . APPPATH . 'views/pagination/' . $aTemplate .'.php'))
        {
            throw new Exception('Unknown template for pagination', E_USER_WARNING);
        }
        $CollectedData = array();
        $CollectedData['html'] = $this->load->view('pagination/' . $aTemplate, $PaginationData, true);

        if($aPage == 1)
        {
            $CollectedData['limit'] = $aItemsPerPage;
            $CollectedData['offset'] = false;
        }
        else
        {
            $CollectedData['offset'] = $aPage*$aItemsPerPage-$aItemsPerPage;
            $CollectedData['limit'] = $aItemsPerPage;
        }
        
        return $CollectedData;
    }

    /**
         *
         * javascript
         *
         * Sets or assigns javascript includes
         *
         * @staticvar string $sIncludeScripts string that contain all added scripts in html format
         * @staticvar array $aAddedScripts used for storing all scripts to reduce duplicates
         * @param type $sPath path to script
         *
         * @return void | false false if file doesn't exists
         */
        protected function javascript($sPath = NULL)
        {
                static $sIncludeScripts, $aAddedScripts;
                if (!$sPath)
                {
                        $this->smarty->sysassign('sScripts', $sIncludeScripts);
                        return true;
                }
                if (!$aAddedScripts)
                        $aAddedScripts = array();

                if (!in_array($sPath, $aAddedScripts))
                {
                        $aAddedScripts[] = $sPath;
                        if (strpos($sPath, 'http://') === false)
                        {
                                $sPath = base_url() . 'js/' . $sPath;
                        }


                        if (!$sIncludeScripts)
                                $sIncludeScripts = '<script type="text/javascript" src="' . $sPath . '"></script>
';
                        else
                                $sIncludeScripts .= '<script type="text/javascript" src="' . $sPath . '"></script>
';
                }
        }

        /**
         *
         * stylesheet
         *
         * Sets or assigns css files includes
         *
         * @staticvar string $sIncludeScripts string that contain all added scripts in html format
         * @staticvar array $aAddedScripts used for storing all scripts to reduce duplicates
         * @param type $sPath path to file
         *
         * @return void | false false if file doesn't exists
         */
        protected function stylesheet($sPath = NULL)
        {
                static $sIncludeScripts, $aAddedScripts;
                if (!$sPath)
                {
                        $this->smarty->sysassign('sStyles', $sIncludeScripts);
                        return true;
                }
                if (!$aAddedScripts)
                        $aAddedScripts = array();

                if (!in_array($sPath, $aAddedScripts))
                {
                        $aAddedScripts[] = $sPath;
                        if (strpos($sPath, 'http://') === false)
                        {
                                $sPath = base_url() . 'css/' . $sPath;
                        }



                        if (!$sIncludeScripts)
                                $sIncludeScripts = '<link rel="stylesheet" href="' . $sPath . '" type="text/css" />
';
                        else
                                $sIncludeScripts .= '<link rel="stylesheet" href="' . $sPath . '" type="text/css" />
';
                }
        }

        /**
         *
         * title
         *
         * Sets or assigns a site title
         *
         * @staticvar string $title current title
         * @param string $sTitle title to set
         *
         * @return current site title
         */
        protected function title($sTitle = NULL)
        {
                static $title;
                if (!$sTitle)
                        $this->smarty->sysassign('sSiteTitle', $title);
                else
                        $title = $sTitle;

                return $title;
        }

        /**
         *
         * metakeywords
         *
         * Sets or assigns a site meta keywords
         *
         * @staticvar string $title current keywords
         * @param string $sTitle keywords to set
         *
         * @return current keywords
         */
        protected function metakeywords($sKeys = NULL)
        {
                static $keys;
                if (!$sKeys)
                        $this->smarty->sysassign('sMetaKeywords', $keys);
                else
                        $keys = $sKeys;

                return $keys;
        }

        /**
         *
         * metadescription
         *
         * Sets or assigns a site meta description
         *
         * @staticvar string $title current description
         * @param string $sTitle description to set
         *
         * @return current description
         */
        protected function metadescription($sText = NULL)
        {
                static $text;
                if (!$sText)
                        $this->smarty->sysassign('sMetaDescription', $text);
                else
                        $text = $sText;

                return $text;
        }        

        /**
         * access_denied
         *
         * shows 403 page
         */
        protected function access_denied()
        {
                $this->title('Access denied');
                //http_send_status(403);
                if ($this->isAjaxRequest())
                {
                        $aResult = array();
                        $aResult['error'] = 1;
                        $aResult['mesage'] = t('You have no permissions for it');
                        $this->AjaxResponse($aResult, 1);
                } else
                {
                        $this->view('pages', 'forbidden');
                }
                die;
        }

        /**
         *
         *
         * shows 404 page
         */
        protected function not_found()
        {
                $this->title('404');
                //http_send_status(404);
                if($this->isAjaxRequest())
                {
                        $this->AjaxResponse(array('error'=>1, 'message'=>'Page was not found'), 1);
                }
                else
                {
                        $this->view('pages', 'notfound');
                }
        }

        /**
         *
         * isAjaxRequest
         *
         * Checks is page requested via ajax
         *
         * @param string $mMethod expected method
         *
         * @return bool
         */
        static function isAjaxRequest($mMethod = false)
        {
                return
                (isset($_SERVER['HTTP_X_REQUESTED_WITH']) ?
                        $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' && ( ($mMethod) ? $_SERVER['REQUEST_METHOD'] == $mMethod : true ) : false );
        }

        /**
         *
         * AjaxResponse
         *
         * response data to ajax request
         *
         * @param mixed $oData data to response
         * @param bool $bIsJSON flag to conevert to json
         */
        static function AjaxResponse($oData, $bIsJSON)
        {
                echo ($bIsJSON ? json_encode($oData) : $oData);
                die;
        }                                                

        /**
         *
         * template_var
         *
         * Assign a variable to template
         *
         * @param array $aVariable name=>value variable to assign
         */
        protected function template_var($aVariable = NULL)
        {
                static $aVariables;
                if (!$aVariables)
                        $aVariables = array();
                if (is_array($aVariable))
                {
                        foreach ($aVariable as $k => $v)
                        {
                                if (is_array($v) || is_object($v))
                                        $aVariables[$k] = json_encode($v);
                                else if (is_string($v))
                                        $aVariables[$k] = '"' . $v . '"';
                                else if (is_bool($v))
                                        $aVariables[$k] = intval($v);
                                else if (!$v)
                                        $aVariables[$k] = '""';
                                else
                                        $aVariables[$k] = $v;
                        }
                }
                else
                {
                        $this->smarty->sysassign('aTemplateVar', $aVariables);
                }
        }

        protected function set_future_message($msg, $type='success')
        {
                $this->session->set_userdata('message-' . $type, $msg);
        }

        protected function get_future_message($type='success')
        {
                $msg = $this->session->userdata('message-' . $type);
                $this->session->unset_userdata('message-' . $type);
                return $msg;
        }

        protected function clear_future_messages()
        {
                $this->session->unset_userdata('message-error');
                $this->session->unset_userdata('message-success');
        }        

        /**
         *
         * Displays a template
         *
         * @param <type> $sClassName folder name
         * @param <type> $sViewName file name
         * @param <type> $params additional params
         * @param <type> $templateName wrap template name
         */
        function view($sClassName = NULL, $sViewName = NULL, $params = array(), $templateName='main')
        {
            foreach($this->_pData as $k=>$v)
            {
                $this->smarty->assign($k, $v);
            }
                
            $this->javascript();
            $this->stylesheet();
            $this->title();
            $this->metakeywords();
            $this->metadescription();
            $this->template_var();
            $this->smarty->view($sClassName, $sViewName, $params, $templateName);
        }

        protected function restrict($aPermission)
        {
            if(!$this->checkAccess($aPermission))
            {
                return $this->access_denied();
            }
        }

        protected function checkAccess($aPermission)
        {
            $this->load->model('acl_model');
            $UserData = $this->auth->getUserData();

            $aPresent = $this->acl_model->getRolePermissionTree($UserData['role_id']);
            return $this->acl_model->checkAccess($aPermission, $aPresent);
        }
}