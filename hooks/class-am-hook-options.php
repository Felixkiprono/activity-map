<?php
if (!defined('ABSPATH')) exit;

class Am_Hook_Options
{

    /**
     * hooks_to_update_option
     *
     * @param  mixed $old_value
     * @param  mixed $value
     * @param  mixed $option
     * @return void
     */
    public function hooks_to_update_option($key, $value, $option)
    {
        if ($option) {
            am_add_activity(
                array(
                    'action' => 'updated',
                    'event_type' => 'Options',
                    'event_subtype' => '-',
                    'event_name' => $key,
                    'event_id' => 0,
                    'metadata' => ""
                )
            );
        }
    }


    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
    }
}
