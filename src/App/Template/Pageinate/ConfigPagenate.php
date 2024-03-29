<?php
namespace Plex\Template\Pageinate;

use Plex\Modules\Database\PlexSql;
use Plex\Template\Pageinate\Pageinate;


use Plex\Template\Render;
use JasonGrimes\Paginator;
use UTMTemplate\HTML\Elements;
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
    public $itemsSelection = [10, 25, 50, 100, 250, 500, 1500];
    private $maxRecordsToShow = 6;

    public function __construct($query, $currentPage, $urlPattern)
    {
        global $_SESSION;
        $db = PlexSql::$DB;
        $this->itemsPerPage = $_SESSION['itemsPerPage'];
        $this->urlPattern = $urlPattern;

        $this->currentPage = $currentPage;
         if (false != $query) {
            $table = $this->table.' f ';
            $libraryField = 'f.library';
            // $db->join(Db_TABLE_VIDEO_TAGS.' m', 'm.video_key=f.video_key', 'INNER');
            // $db->join(Db_TABLE_VIDEO_CUSTOM.' c', 'c.video_key=f.video_key', 'LEFT');

            // foreach ($query as $k => $parts) {
                
            //     $db->where('(m.'.$parts['field'].' like ? or c.'.$parts['field'].' like ?)', ['%'.$parts['search'].'%', '%'.$parts['search'].'%']
            //     );
            // }
        } else {
            $libraryField = 'library';
            
            $query = urlQuerystring($urlPattern, ['current', 'allfiles', 'sec','days'], true);
            $table = $this->table;
            if (\count($query) > 0) {
                $q = trim(str_replace('m.', '', $query['sql']));
                $db->where($q);
            }
        }
        if($_SESSION['sort'] == 'f.added')
        {
            if (__THIS_FILE__ == 'recent.php') {
           
                $db->where(PlexSQL::getLastest('added',$_SESSION['days']));
            }
        }

        if (true === $this->library) {
            if ('All' != $_SESSION['library']) {
                $db->where($libraryField, $_SESSION['library']);
            }
        }

        //$this->results = 
        utmdump($db->getQuery($table));
        $this->results = $db->withTotalCount()->get($table);

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

}
