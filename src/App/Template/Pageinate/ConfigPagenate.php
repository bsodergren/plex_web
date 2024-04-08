<?php

namespace Plex\Template\Pageinate;

use JasonGrimes\Paginator;
use Plex\Modules\Database\PlexSql;

class ConfigPagenate extends Pageinate
{
    public $table   = Db_TABLE_STUDIO;
    public $library = false;
    public $itemsPerPage;
    public $urlPattern;
    public $totalRecords;
    public $limit_array = [];
    public $offset;
    public $results;
    public $paginator;
    public $itemsSelection    = [10, 25, 50, 100, 250, 500, 1500];
    private $maxRecordsToShow = 6;

    public function __construct($query, $currentPage, $urlPattern)
    {
        global $_SESSION;
        $db                 = PlexSql::$DB;
        $this->itemsPerPage = $_SESSION['itemsPerPage'];
        $this->urlPattern   = $urlPattern;

        $this->currentPage = $currentPage;
        if (false != $query) {
            $table        = $this->table.' v ';
            $libraryField = 'v.library';
            // $db->join(Db_TABLE_VIDEO_TAGS.' m', 'm.video_key=v.video_key', 'INNER');
            // $db->join(Db_TABLE_VIDEO_CUSTOM.' c', 'c.video_key=v.video_key', 'LEFT');

            // foreach ($query as $k => $parts) {

            //     $db->where('(m.'.$parts['field'].' like ? or c.'.$parts['field'].' like ?)', ['%'.$parts['search'].'%', '%'.$parts['search'].'%']
            //     );
            // }
        } else {
            $libraryField = 'library';

            $query = urlQuerystring($urlPattern, ['current', 'allfiles', 'sec', 'days'], true);
            $table = $this->table;
            if (\count($query) > 0) {
                $q = trim(str_replace('m.', '', $query['sql']));
                $db->where($q);
            }
        }
        if ('v.added' == $_SESSION['sort']) {
            if (__THIS_FILE__ == 'recent.php') {
                $db->where(PlexSQL::getLastest('added', $_SESSION['days']));
            }
        }

        if (true === $this->library) {
            if ('All' != $_SESSION['library']) {
                $db->where($libraryField, $_SESSION['library']);
            }
        }

        // $this->results =
        utmdump($db->getQuery($table));
        $this->results = $db->withTotalCount()->get($table);

        $this->totalRecords = $db->totalCount;
        $this->limit_array  = [($this->currentPage - 1) * $this->itemsPerPage, $this->itemsPerPage];

        $this->offset = ($this->currentPage - 1) * $this->itemsPerPage;

        $this->paginator = new Paginator(
            $this->totalRecords,
            $this->itemsPerPage,
            $this->currentPage,
            $this->urlPattern
        );

        $this->paginator->setMaxPagesToShow($this->maxRecordsToShow);
    }
}
