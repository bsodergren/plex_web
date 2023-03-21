<?php
    $sort_html = '';
    $page_html = '';
    $navbar ='';
$js_html = '';

if (__BOTTOM_NAV__ == 1) {


   if (__SHOW_SORT__ == true && isset($pageObj)) {
       $sort_html = template::echo("footer/sort", ['SORT_HTML' => display_sort_options($url_array)]);
   }

    if (__SHOW_PAGES__ == true && isset($pageObj)) {
        $page_html =  $pageObj->toHtml();
    }

    $footer_nav = ['FOOTER_NAV' => $sort_html . $page_html];
    $navbar = template::echo("footer/navbar", $footer_nav);
}

if (isset($json_array)) {
    $json_string = json_encode($json_array);

    $param = ['JSON_ARRAY' => $json_string];
    $js_html = template::echo("footer/javascript", $param); 

}

echo Template::echo("footer/main",[
    'JAVASCRIPT_HTML' => $js_html, 'FOOT_NAVBAR' => $navbar ]);



?>