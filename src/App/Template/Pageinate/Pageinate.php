<?php

namespace Plex\Template\Pageinate;

/*
 * plex web viewer
 */

use JasonGrimes\Paginator;
use Plex\Modules\Database\PlexSql;
use Plex\Template\Render;
use UTMTemplate\HTML\Elements;

class Pageinate extends Paginator
{
    public $itemsPerPage;
    public $urlPattern;
    public $totalRecords;
    public $limit_array = [];
    public $offset;
    public $library = true;
    public $table   = Db_TABLE_VIDEO_FILE;
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
            $db->join(Db_TABLE_VIDEO_TAGS.' m', 'm.video_key=v.video_key', 'INNER');
            $db->join(Db_TABLE_VIDEO_CUSTOM.' c', 'c.video_key=v.video_key', 'LEFT');

            foreach ($query as $k => $parts) {
                $db->where('(m.'.$parts['field'].' like ? or c.'.$parts['field'].' like ?)', ['%'.$parts['search'].'%', '%'.$parts['search'].'%']
                );
            }
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

    public function toHtml()
    {
        global $_SERVER;
        $link_list   = '';
        $hidden_text = '';
        $placeholder = '';
        $next = '';
        $current_url= '';
        $previous= '';
        if ($this->paginator->numPages <= 1) {
            // $placeholder = '<li class="page-item page-link">Show</li>';

            //     return '';
        }
        $pill_start_class = '';
        $pill_end_class   = '';
        if ($this->paginator->getPrevUrl()) {
            $params = [
                'LI_CLASS' => ' class="page-item " ',
                'A_CLASS'  => ' class="page-link  rounded-start-pill" ',
                'A_HREF'   => htmlspecialchars($this->paginator->getPrevUrl()),
                'A_TEXT'   => $this->paginator->previousText,
                // 'A_TEXT'   => '&laquo; '.$this->paginator->previousText,
            ];
            $previous = Render::return('base/footer/page_item', $params);
        } else {
            $pill_start_class = 'rounded-start-pill';
        }
        if ($this->paginator->getNextUrl()) {
            $next_page = true;
            $params    = [
                'LI_CLASS' => ' class="page-item rounded-end-pill"',
                'A_CLASS'  => ' class="page-link rounded-end-pill"',
                'A_HREF'   => htmlspecialchars($this->paginator->getNextUrl()),
                'A_TEXT'   => $this->paginator->nextText,

                //                'A_TEXT'   => $this->paginator->nextText.' &raquo;',
            ];
            $next = Render::return('base/footer/page_item', $params);
        } else {
            $next_page      = false;
            $pill_end_class = 'rounded-end-pill';
        }
        $pages = $this->paginator->getPages();
        if (0 == \count($pages)) {
            $pill_start_class = 'rounded-pill';
        }
        foreach ($pages as $page) {
            // $params = [];

            if ($page['isCurrent'] && false === $next_page) {
                $pill_end_class = 'rounded-end-pill';
            } else {
                $pill_end_class = '';
            }
            if ($page['url']) {
                $params = [
                    'LI_CLASS' => $page['isCurrent'] ? ' class="page-item  active '.$pill_end_class.'"' : ' class="page-item '.$pill_end_class.'" ',
                    'A_CLASS'  => ' class="page-link '.$pill_end_class.'" ',
                    'A_HREF'   => htmlspecialchars($page['url']),
                    'A_TEXT'   => htmlspecialchars($page['num']),
                ];

                $link_list .= Render::return('base/footer/page_item', $params);
            } else {
                $link_list .= Render::return('base/footer/page_item_disabled', ['A_TEXT' => htmlspecialchars($page['num'])]);
            }
        }

        parse_str($_SERVER['QUERY_STRING'], $query_array);

        foreach ($query_array as $name => $value) {
            if ('itemsPerPage' == $name) {
                continue;
            }
            if (\is_array($value)) {
                $value = implode(',', $value);
            }
            $hidden_text .= $this->hidden_Field($name, $value);
        }

        $option_text = Elements::SelectOptions($this->itemsSelection, $this->itemsPerPage);
        $params      = [
            'HIDDEN'           => $hidden_text,
            'SHOW_PLACEHOLDER' => $placeholder,
            'PAGE_UPDATE'      => $current_url,
            'OPTIONS'          => $option_text,
            'PREVIOUS_LINK'    => $previous,
            'PILL_CLASS'       => $pill_start_class,
            'PILL_NEXT_CLASS'  => $pill_end_class,
            'LINK_LIST'        => $link_list,
            'NEXT_LINK'        => $next];
        $html = Render::return('base/footer/pages', $params);

        return $html;
    }

    public function hidden_Field($name, $value)
    {
        return '<input type="hidden" name="'.$name.'" value="'.$value.'">'."\n";
    }
}
