<?php
/**
 * plex web viewer
 */

$db       = new MysqliDb('localhost', DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$conn     = mysqli_connect('localhost', DB_USERNAME, DB_PASSWORD, DB_DATABASE);

dbObject::autoload('models');

$settings = new MetaSettings();

$val      = $settings->orderBy('type')->get();

if ($val) {
    foreach ($val as $u) {
        $setting[$u->name] = $u->type.';'.$u->value;

        if ('array' == $u->type) {
            define($u->name, json_decode($u->value, 1));

            if (defined('__DISPLAY_PAGES__') && array_key_exists(__THIS_FILE__, __DISPLAY_PAGES__)) {
                define('__SHOW_PAGES__', __DISPLAY_PAGES__[__THIS_FILE__]['pages']);
                define('__SHOW_SORT__', __DISPLAY_PAGES__[__THIS_FILE__]['sort']);

                if (__SHOW_PAGES__ == 0 && __SHOW_SORT__ == 0) {
                    define('__BOTTOM_NAV__', 0);
                } else {
                    define('__BOTTOM_NAV__', 1);
                }
            }
        } else {
            if (!defined($u->name)) {
                define($u->name, $u->value);
            }
        }
    }// end foreach

    define('__SETTINGS__', $setting);
}// end if

if (!defined('__BOTTOM_NAV__')) {
    define('__BOTTOM_NAV__', 0);
}
