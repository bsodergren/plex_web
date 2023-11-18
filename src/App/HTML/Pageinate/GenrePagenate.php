<?php
namespace Plexweb\HTML\Pageinate;

use Plexweb\HTML\Pageinate;


class GenrePagenate extends Pageinate
{
    public $itemsPerPage;
    private $maxRecordsToShow = __MAX_PAGES_TO_SHOW__;
    public $urlPattern;
    public $totalRecords;
    public $limit_array       = [];
    public $offset;
    private $library_query;
    public $results;
    public $paginator;

    public function __construct($currentPage, $urlPattern)
    {
        global $db;
        global $_SESSION;

        $this->itemsPerPage = $_SESSION['itemsPerPage'];
        $this->urlPattern   = $urlPattern;

        $this->currentPage  = $currentPage;
        //        $db->where($query);

        $this->results      = $db->withTotalCount()->get(Db_TABLE_GENRE);
        $this->totalRecords = $db->totalCount;

        $this->limit_array  = [($this->currentPage - 1) * $this->itemsPerPage, $this->itemsPerPage];

        $this->offset       = ($this->currentPage - 1) * $this->itemsPerPage;

        $this->paginator    = new Paginator(
            $this->totalRecords,
            $this->itemsPerPage,
            $this->currentPage,
            $this->urlPattern
        );

        $this->paginator->setMaxPagesToShow($this->maxRecordsToShow);
    }

}

