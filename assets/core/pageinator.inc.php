<?php 


if(!isset($_REQUEST['current']))$_REQUEST['current']="1"; 

if (isset($_REQUEST['current'])) {
    $uri['current'] = $_REQUEST['current'];
}

$currentPage = $_REQUEST['current'];
$uri['current'] = $currentPage;

$query_string_no_current = urlQuerystring($_SERVER['QUERY_STRING'],"current");

use JasonGrimes\Paginator;

class pageinate extends Paginator
{
	public $itemsPerPage = __RECORDS_PER_PAGE__;
	public $urlPattern;
	public $totalRecords;
	public $limit_array = [];
	public $offset;
    private $library_query;

	public function __construct($query,$currentPage,$urlPattern)
	{
		global $db;
        global $_SESSION;

        $this->library_query = " library = '". $_SESSION['library']."' ";
		$this->urlPattern = $urlPattern;

		$this->currentPage = $currentPage;
        
        if ($query == false ){
            $query = $this->library_query;
        } else {
            $query = $query .  " and " . $this->library_query;
        }
        
        $db->where( $query);
		$db->withTotalCount()->get(Db_TABLE_FILEDB);
		$this->totalRecords = $db->totalCount;

		$this->limit_array = [($this->currentPage - 1) * $this->itemsPerPage, $this->itemsPerPage];

		$this->offset = ($this->currentPage - 1 ) * $this->itemsPerPage;

		$this->paginator = new Paginator(
			$this->totalRecords, 
			$this->itemsPerPage,
			 $this->currentPage,
			 $this->urlPattern);
	}

	public function toHtml()
    {
        if ($this->paginator->numPages <= 1) {
            return '';
        }

        $html = '<ul class="pagination">';
        if ($this->paginator->getPrevUrl()) {
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($this->paginator->getPrevUrl()) . '">&laquo; '. $this->paginator->previousText .'</a></li>';
        }

        foreach ($this->paginator->getPages() as $page) {
            if ($page['url']) {
                $html .= '<li' . ($page['isCurrent'] ? ' class="page-item active"' : '') . '><a class="page-link" href="' . htmlspecialchars($page['url']) . '">' . htmlspecialchars($page['num']) . '</a></li>';
            } else {
                $html .= '<li class="page-item disabled"><span>' . htmlspecialchars($page['num']) . '</span></li>';
            }
        }

        if ($this->paginator->getNextUrl()) {
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($this->paginator->getNextUrl()) . '">'. $this->paginator->nextText .' &raquo;</a></li>';
        }


        
        $html .= '</ul>';

        return $html;
    }
}

$urlPattern= $_SERVER['SCRIPT_NAME'].'?current=(:num)&'.$query_string_no_current;

$total_pages='';
?>