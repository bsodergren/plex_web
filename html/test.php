<?php
/**
 * plex web viewer
 */

use Plex\Template\Rain;

define('__SHOW_SORT__', true);

$_REQUEST['itemsPerPage'] = 25;
$_REQUEST['current']      = '3';
require_once '_config.inc.php';
define('TITLE', 'Test Page');

$t                        = new Rain();
$tpl                      = $t->init();
$tpl->assign('jobArray', htmlspecialchars($output));

$tpl->draw('body');

exit;
