<?php
/**
 * plex web viewer
 */

/**
 * plex web viewer.
 */
function chk_file($value, $command = 'delete', $options = '')
{
    switch ($command) {
        case 'rename':
            if (is_file($value)) {
                if (is_file($options)) {
                    chk_file($options, 'delete');
                }

                logger("Renaming {$value} to {$options}");
                rename($value, $options);
            }

            break;

        case 'delete':
            if (is_file($value)) {
                logger("deleting {$value}");
                unlink($value);
            }

            break;
    }
}// end chk_file()
