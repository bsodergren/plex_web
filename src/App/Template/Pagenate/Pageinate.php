<?php
namespace Plex\Template\Pagenate;
/**
 * plex web viewer
 */

use Plex\Template\Render;
use JasonGrimes\Paginator;
use Plex\Template\Template;

class Pageinate extends Paginator
{
    public $itemsPerPage;
    public $urlPattern;
    public $totalRecords;
    public $limit_array       = [];
    public $offset;
    public $library           = true;
    public $table             = Db_TABLE_VIDEO_TAGS;
    public $results;
    public $paginator;
    public $itemsSelection    = [10, 25, 50, 100, 250, 500, 1500];
    private $maxRecordsToShow = __MAX_PAGES_TO_SHOW__;

    public function __construct($query, $currentPage, $urlPattern)
    {
        global $db;
        global $_SESSION;

        $this->itemsPerPage = $_SESSION['itemsPerPage'];
        $this->urlPattern   = $urlPattern;

        $this->currentPage  = $currentPage;

        if (false != $query) {
            if (str_contains($query, 'AND')) {
                $findArr = explode('AND', $query);
                foreach ($findArr as $q) {
                    [$field,$value] = explode('=', $q);

                    $field          = trim($field);
                    $value          = trim(str_replace("'", '%', $value));
                    $db->where($field, $value, 'LIKE');
                }
            } else {
                if (str_contains($query, '=')) {
                    [$field,$value] = explode('=', $query);
                    $field          = trim($field);
                    $value          = trim(str_replace("'", '%', $value));
                    // dump([__METHOD__, [$field,$value]]);
                    $db->where($field, $value, 'LIKE');
                } else {
                    [$field,$value] = explode('IS', $query);
                    $value          = str_replace('NULL', '', $value);
                    $db->where($field, null, 'IS '.$value);
                }
            }
        }

        if (true === $this->library) {
            if ('All' != $_SESSION['library']) {
                $db->where('library', $_SESSION['library']);
            }
        }

        $this->results      = $db->withTotalCount()->get($this->table);
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

    public function toHtml()
    {
        global $_SERVER;
        $link_list   = '';
        $hidden_text = '';
        $placeholder = '';

        if ($this->paginator->numPages <= 1) {
            // $placeholder = '<li class="page-item page-link">Show</li>';

            //     return '';
        }

        if ($this->paginator->getPrevUrl()) {
            $params   = [
                'LI_CLASS' => ' class="page-item" ',
                'A_CLASS'  => ' class="page-link" ',
                'A_HREF'   => htmlspecialchars($this->paginator->getPrevUrl()),
                'A_TExT'   => '&laquo; '.$this->paginator->previousText,
            ];
            $previous = Template::return('base/footer/page_item', $params);
        }

        foreach ($this->paginator->getPages() as $page) {
            // $params = [];
            if ($page['url']) {
                $params = [
                    'LI_CLASS' => $page['isCurrent'] ? ' class="page-item  active"' : ' class="page-item" ',
                    'A_CLASS'  => ' class="page-link" ',
                    'A_HREF'   => htmlspecialchars($page['url']),
                    'A_TExT'   => htmlspecialchars($page['num']),
                ];

                if ($page['isCurrent']) {
                    $current_url = htmlspecialchars($page['url']);
                }

                $link_list .= Template::return('base/footer/page_item', $params);
            } else {
                $link_list .= Template::return('base/footer/page_item_disabled', ['A_TEXT' => htmlspecialchars($page['num'])]);
            }
        }

        if ($this->paginator->getNextUrl()) {
            $params = [
                'LI_CLASS' => ' class="page-item"',
                'A_CLASS'  => ' class="page-link"',
                'A_HREF'   => htmlspecialchars($this->paginator->getNextUrl()),
                'A_TExT'   => $this->paginator->nextText.' &raquo;',
            ];
            $next   = Template::return('base/footer/page_item', $params);
        }

        parse_str($_SERVER['QUERY_STRING'], $query_array);

        foreach ($query_array as $name => $value) {
            if ('itemsPerPage' == $name) {
                continue;
            }
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $hidden_text .= $this->hidden_Field($name, $value);
        }

        $option_text = Render::display_SelectOptions($this->itemsSelection, $this->itemsPerPage);
        $params      = [
            'HIDDEN'           => $hidden_text,
            'SHOW_PLACEHOLDER' => $placeholder,
            'PAGE_UPDATE'      => $current_url,
            'OPTIONS'          => $option_text,
            'PREVIOUS_LINK'    => $previous,
            'LINK_LIST'        => $link_list,
            'NEXT_LINK'        => $next];
        $html        = Template::return('base/footer/pages', $params);

        return $html;
    }

    public function hidden_Field($name, $value)
    {
        return '<input type="hidden" name="'.$name.'" value="'.$value.'">'."\n";
    }
}

