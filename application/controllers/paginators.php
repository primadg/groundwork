<?php
class Paginators extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index($aPage = 1)
    {
        $this->load->model('acl_model');
        
        $aTypes = array('beginend', 'beginendpages', 'hider', 'nextprev', 'ordered', 'orderedpages', 'scroll');
        $this->_pData['pgn'] = array();
        foreach($aTypes as $v)
        {
            $Pager = $this->generatePagination($aPage, 476, 10, $v, $this->_pData['baseUrl'] . 'paginators/index/%s', $v);
            $this->_pData['pgn'][] = $Pager['html'];
        }
        return $this->view();
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
}

?>
