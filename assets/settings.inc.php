<?php
require_once __PHP_INC_CORE_DIR__.'/MysqliDb.inc.php';
require_once __PHP_INC_CORE_DIR__.'/dbObject.inc.php';


$db = new MysqliDb('localhost', __SQL_USER__, __SQL_PASSWD__, __SQL_DB__);
dbObject::autoload('models');

class MetaSettings extends dbObject
{

    protected $dbTable = Db_TABLE_SETTINGS;
}//end class

function constantType($type)
{

}//end constantType()


function createConstants()
{

}//end createConstants()


$settings = new MetaSettings();


$val = $settings->orderBy('type')->get();

if ($val) {
    foreach ($val as $u) {
        $setting[$u->name] = $u->type.';'.$u->value;

        if ($u->type == 'array') {
            define($u->name, json_decode($u->value, 1));


            if (defined('__DISPLAY_PAGES__') && key_exists(__THIS_FILE__, __DISPLAY_PAGES__)) {
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
    }//end foreach

    define('__SETTINGS__', $setting);
}//end if

if (!defined('__BOTTOM_NAV__')) {
    define('__BOTTOM_NAV__', 0);
}


function proccess_settings($redirect_url='')
{
    global $form;
    global $_POST;
    global $db;

    // get our form values and assign them to a variable
    foreach ($_POST as $key => $value) {
        switch (true) {
            case $key == 'submit':
            break;

            case str_contains($key, 'setting_'):
                $pcs                   = explode('_', $key);
                $field                 = $pcs[1];
                $new_settiings[$field] = $value;
            break;

            case str_contains($key, '-NAME'):
            break;

            case key_exists($key, __SETTINGS__):
                $data = ['value' => $value];
                $db->where('name', $key);
                $db->update(Db_TABLE_SETTINGS, $data);
            break;

            case str_contains($key, '-ADD'):
                if (!key_exists(str_replace('-ADD', '', $key), __SETTINGS__)) {
                    if (!key_exists(str_replace('-NAME', '', $key), __SETTINGS__)) {
                        $key_name = str_replace('-ADD', '-NAME', $key);
                        if (key_exists($key_name, $_POST)) {
                            $value = $_POST[$key_name];
                            $field = str_replace('-NAME', '', $key_name);
                            $transfer_settings[$field] = [
                                'value' => $value,
                                'type'  => 'text',
                            ];
                        }
                    }
                }
            break;
        }//end switch
    }//end foreach

    if (is_array($transfer_settings)) {
        foreach ($transfer_settings as $name => $arr) {
            $id = $db->insert(Db_TABLE_SETTINGS, ['name' => $name, 'value' => $arr['value'], 'type' => $arr['type']]);
        }
    }

    if (is_array($new_settiings)) {
        if ($new_settiings['name'] != '') {
            $id = $db->insert(Db_TABLE_SETTINGS, $new_settiings);
        }
    }

    $form->printr($db->getLastError());
    // show a success message if no errors
    if ($form->ok()) {
        return $form->redirect($redirect_url);
    }

}//end proccess_settings()
