<?php

trait ConfigSave
{
    public function saveStudioConfig($data_array, $redirect, $timeout = 0)
    {
        global $db;

        $__output = '';

        foreach ($data_array as $key => $val) {
            if (true == str_contains($key, '_')) {
                $value = trim($val);

                if ('' != $value) {
                    $pcs = explode('_', $key);

                    $id = $pcs[1];
                    $field = $pcs[0];
                    $set = '`'.$field.'` = "'.$value.'"';

                    if ('null' == $value) {
                        $set = '`'.$field.'`= NULL ';
                    }

                    $sql = 'UPDATE '.Db_TABLE_STUDIO.'  SET '.$set.' WHERE id = '.$id;
                    $db->query($sql);
                }
            }
        }

        if (false != $redirect) {
            return JavaRefresh($redirect, $timeout);
        }
    }
}
