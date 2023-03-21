<?php

use JasonGrimes\Paginator;

class pageinate extends Paginator
{
    public $itemsPerPage;
    private $maxRecordsToShow = __MAX_PAGES_TO_SHOW__;
    public $urlPattern;
    public $totalRecords;
    public $limit_array = [];
    public $offset;
    private $library_query;
    public $results;

    public function __construct($query, $currentPage, $urlPattern)
    {
        global $db;
        global $_SESSION;

        $this->itemsPerPage = $_SESSION['itemsPerPage'] ;
        $this->library_query = " library = '" . $_SESSION['library'] . "' ";
        $this->urlPattern = $urlPattern;

        $this->currentPage = $currentPage;

        if ($query == false) {
            $query = $this->library_query;
        } else {
            $query = $query .  " and " . $this->library_query;
        }
        $db->where($query);

        $this->results = $db->withTotalCount()->get(Db_TABLE_FILEDB);
        $this->totalRecords = $db->totalCount;

        $this->limit_array = [($this->currentPage - 1) * $this->itemsPerPage, $this->itemsPerPage];

        $this->offset = ($this->currentPage - 1) * $this->itemsPerPage;

        $this->paginator = new Paginator(
            $this->totalRecords,
            $this->itemsPerPage,
            $this->currentPage,
            $this->urlPattern
        );

        $this->paginator->setMaxPagesToShow($this->maxRecordsToShow);
    }

    public function toHtml()
    {
        global $_SERVER;

        if ($this->paginator->numPages <= 1) {
            return '';
        }
        if ($this->paginator->getPrevUrl()) {
            $params = [
                'LI_CLASS' => ' class="page-item" ',
                'A_CLASS' => ' class="page-link" ',
                'A_HREF' => htmlspecialchars($this->paginator->getPrevUrl()),
                'A_TExT' => '&laquo; ' . $this->paginator->previousText,
            ];
            $previous =    template::echo("footer/page_item", $params);
        }

        foreach ($this->paginator->getPages() as $page) {
            $params = [];
            if ($page['url']) {
                $params = [
                    'LI_CLASS' => $page['isCurrent'] ? ' class="page-item  active"' : ' class="page-item" ',
                    'A_CLASS' => ' class="page-link" ',
                    'A_HREF' => htmlspecialchars($page['url']),
                    'A_TExT' => htmlspecialchars($page['num']),
                ];

                if($page['isCurrent']){ $current_url = htmlspecialchars($page['url']); }

                $link_list .=    template::echo("footer/page_item", $params);
            } else {
                $link_list .=    template::echo("footer/page_item_disabled", ['A_TEXT' => htmlspecialchars($page['num'])]);
            }
        }

        if ($this->paginator->getNextUrl()) {

            $params = [
                'LI_CLASS' => ' class="page-item"',
                'A_CLASS' => ' class="page-link"',
                'A_HREF' =>  htmlspecialchars($this->paginator->getNextUrl()),
                'A_TExT' => $this->paginator->nextText . ' &raquo;',
            ];
            $next =    template::echo("footer/page_item", $params);
        }

        parse_str( $_SERVER['QUERY_STRING'], $query_array);

        foreach($query_array as $name => $value)
        {
            if ($name == 'itemsPerPage') {
                continue;
            }
            $hidden_text .= hidden_Field($name, $value);

        }
        
        $option_text = display_SelectOptions([10,25,30,40,50,100,250,500],$this->itemsPerPage);
        $params = [
            'HIDDEN' => $hidden_text,
            'PAGE_UPDATE' => $current_url ,
        'OPTIONS' => $option_text,
        'PREVIOUS_LINK' => $previous,
        'LINK_LIST' => $link_list ,
        'NEXT_LINK' => $next ];
        $html = template::echo("footer/pages",$params);
        return $html;
    }
}



class ConfigPagenate extends Paginator
{
    public $itemsPerPage;
    private $maxRecordsToShow = __MAX_PAGES_TO_SHOW__;
    public $urlPattern;
    public $totalRecords;
    public $limit_array = [];
    public $offset;
    private $library_query;
    public $results;

    public function __construct($query, $currentPage, $urlPattern)
    {
        global $db;
        global $_SESSION;

        $this->itemsPerPage = $_SESSION['itemsPerPage'] ;
        $this->urlPattern = $urlPattern;

        $this->currentPage = $currentPage;
        $db->where($query);

        $this->results = $db->withTotalCount()->get(Db_TABLE_STUDIO);
        $this->totalRecords = $db->totalCount;

        $this->limit_array = [($this->currentPage - 1) * $this->itemsPerPage, $this->itemsPerPage];

        $this->offset = ($this->currentPage - 1) * $this->itemsPerPage;

        $this->paginator = new Paginator(
            $this->totalRecords,
            $this->itemsPerPage,
            $this->currentPage,
            $this->urlPattern
        );

        $this->paginator->setMaxPagesToShow($this->maxRecordsToShow);
    }

    
    public function toHtml()
    {
        global $_SERVER;

        if ($this->paginator->numPages <= 1) {
            return '';
        }
        if ($this->paginator->getPrevUrl()) {
            $params = [
                'LI_CLASS' => ' class="page-item" ',
                'A_CLASS' => ' class="page-link" ',
                'A_HREF' => htmlspecialchars($this->paginator->getPrevUrl()),
                'A_TExT' => '&laquo; ' . $this->paginator->previousText,
            ];
            $previous =    template::echo("footer/page_item", $params);
        }

        foreach ($this->paginator->getPages() as $page) {
            $params = [];
            if ($page['url']) {
                $params = [
                    'LI_CLASS' => $page['isCurrent'] ? ' class="page-item  active"' : ' class="page-item" ',
                    'A_CLASS' => ' class="page-link" ',
                    'A_HREF' => htmlspecialchars($page['url']),
                    'A_TExT' => htmlspecialchars($page['num']),
                ];

                if($page['isCurrent']){ $current_url = htmlspecialchars($page['url']); }

                $link_list .=    template::echo("footer/page_item", $params);
            } else {
                $link_list .=    template::echo("footer/page_item_disabled", ['A_TEXT' => htmlspecialchars($page['num'])]);
            }
        }

        if ($this->paginator->getNextUrl()) {

            $params = [
                'LI_CLASS' => ' class="page-item"',
                'A_CLASS' => ' class="page-link"',
                'A_HREF' =>  htmlspecialchars($this->paginator->getNextUrl()),
                'A_TExT' => $this->paginator->nextText . ' &raquo;',
            ];
            $next =    template::echo("footer/page_item", $params);
        }

        parse_str( $_SERVER['QUERY_STRING'], $query_array);

        foreach($query_array as $name => $value)
        {
            if ($name == 'itemsPerPage') {
                continue;
            }
            $hidden_text .= hidden_Field($name, $value);

        }
        
        $option_text = display_SelectOptions([10,25,30,40,50,100,250,500],$this->itemsPerPage);
        $params = [
            'HIDDEN' => $hidden_text,
            'PAGE_UPDATE' => $current_url ,
        'OPTIONS' => $option_text,
        'PREVIOUS_LINK' => $previous,
        'LINK_LIST' => $link_list ,
        'NEXT_LINK' => $next ];
        $html = template::echo("footer/pages",$params);
        return $html;
    }

}

class GenrePagenate extends Paginator
{
    public $itemsPerPage;
    private $maxRecordsToShow = __MAX_PAGES_TO_SHOW__;
    public $urlPattern;
    public $totalRecords;
    public $limit_array = [];
    public $offset;
    private $library_query;
    public $results;

    public function __construct($currentPage, $urlPattern)
    {
        global $db;
        global $_SESSION;

        $this->itemsPerPage = $_SESSION['itemsPerPage'] ;
        $this->urlPattern = $urlPattern;

        $this->currentPage = $currentPage;
//        $db->where($query);

        $this->results = $db->withTotalCount()->get(Db_TABLE_GENRE);
        $this->totalRecords = $db->totalCount;

        $this->limit_array = [($this->currentPage - 1) * $this->itemsPerPage, $this->itemsPerPage];

        $this->offset = ($this->currentPage - 1) * $this->itemsPerPage;

        $this->paginator = new Paginator(
            $this->totalRecords,
            $this->itemsPerPage,
            $this->currentPage,
            $this->urlPattern
        );

        $this->paginator->setMaxPagesToShow($this->maxRecordsToShow);
    }

    public function toHtml()
    {
        global $_SERVER;

        if ($this->paginator->numPages <= 1) {
            return '';
        }
        if ($this->paginator->getPrevUrl()) {
            $params = [
                'LI_CLASS' => ' class="page-item" ',
                'A_CLASS' => ' class="page-link" ',
                'A_HREF' => htmlspecialchars($this->paginator->getPrevUrl()),
                'A_TExT' => '&laquo; ' . $this->paginator->previousText,
            ];
            $previous =    template::echo("footer/page_item", $params);
        }

        foreach ($this->paginator->getPages() as $page) {
            $params = [];
            if ($page['url']) {
                $params = [
                    'LI_CLASS' => $page['isCurrent'] ? ' class="page-item  active"' : ' class="page-item" ',
                    'A_CLASS' => ' class="page-link" ',
                    'A_HREF' => htmlspecialchars($page['url']),
                    'A_TExT' => htmlspecialchars($page['num']),
                ];

                if($page['isCurrent']){ $current_url = htmlspecialchars($page['url']); }

                $link_list .=    template::echo("footer/page_item", $params);
            } else {
                $link_list .=    template::echo("footer/page_item_disabled", ['A_TEXT' => htmlspecialchars($page['num'])]);
            }
        }

        if ($this->paginator->getNextUrl()) {

            $params = [
                'LI_CLASS' => ' class="page-item"',
                'A_CLASS' => ' class="page-link"',
                'A_HREF' =>  htmlspecialchars($this->paginator->getNextUrl()),
                'A_TExT' => $this->paginator->nextText . ' &raquo;',
            ];
            $next =    template::echo("footer/page_item", $params);
        }

        parse_str( $_SERVER['QUERY_STRING'], $query_array);

        foreach($query_array as $name => $value)
        {
            if ($name == 'itemsPerPage') {
                continue;
            }
            $hidden_text .= hidden_Field($name, $value);

        }
        
        $option_text = display_SelectOptions([10,25,30,40,50,100,250,500],$this->itemsPerPage);
        $params = [
            'HIDDEN' => $hidden_text,
            'PAGE_UPDATE' => $current_url ,
        'OPTIONS' => $option_text,
        'PREVIOUS_LINK' => $previous,
        'LINK_LIST' => $link_list ,
        'NEXT_LINK' => $next ];
        $html = template::echo("footer/pages",$params);
        return $html;
    }
}