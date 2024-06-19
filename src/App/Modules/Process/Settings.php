<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Process;

class Settings extends Forms
{
    // public function __construct($postArray)
    // {
    //    parent::__construct($postArray);
    // }

    public function proccess_settings($redirect_url = '')
    {
        global $form;

        // get our form values and assign them to a variable
        foreach ($this->postArray as $key => $value) {
            switch (true) {
                case 'submit' == $key:
                    break;

                case str_contains($key, 'setting_'):
                    $pcs                   = explode('_', $key);
                    $field                 = $pcs[1];
                    $new_settiings[$field] = $value;

                    break;

                case str_contains($key, '-NAME'):
                    break;

                case \array_key_exists($key, __SETTINGS__):
                    $data = ['value' => $value];
                    $this->where('name', $key);
                    $this->update(Db_TABLE_SETTINGS, $data);

                    break;

                case str_contains($key, '-ADD'):
                    if (!\array_key_exists(str_replace('-ADD', '', $key), __SETTINGS__)) {
                        if (!\array_key_exists(str_replace('-NAME', '', $key), __SETTINGS__)) {
                            $key_name = str_replace('-ADD', '-NAME', $key);
                            if (\array_key_exists($key_name, $this->postArray)) {
                                $value                     = $this->postArray[$key_name];
                                $field                     = str_replace('-NAME', '', $key_name);
                                $transfer_settings[$field] = [
                                    'value' => $value,
                                    'type'  => 'text',
                                ];
                            }
                        }
                    }

                    break;
            } // end switch
        } // end foreach

        if (\is_array($transfer_settings)) {
            foreach ($transfer_settings as $name => $arr) {
                $id = $this->insert(Db_TABLE_SETTINGS, ['name' => $name, 'value' => $arr['value'], 'type' => $arr['type']]);
            }
        }

        if (\is_array($new_settiings)) {
            if ('' != $new_settiings['name']) {
                $id = $this->insert(Db_TABLE_SETTINGS, $new_settiings);
            }
        }

        $form->printr($this->getLastError());
        // show a success message if no errors
        if ($form->ok()) {
            return $this->myHeader($redirect_url);
        }
    } // end proccess_settings()
}
