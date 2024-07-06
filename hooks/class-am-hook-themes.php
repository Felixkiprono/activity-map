<?php
if (!defined('ABSPATH')) exit;

class Am_Hook_Themes
{


    /**
     * hooks_to_switch_theme
     *
     * @param  mixed $new_name
     * @param  mixed $new_theme
     * @param  mixed $old_theme
     * @return void
     */
    public function hooks_to_switch_theme($new_name, $new_theme, $old_theme)
    {
        if ($new_name && $new_theme) {
            $meta = json_encode(array("new"=>$new_theme->get('Name'), "old"=>$old_theme->get('Name')));
            am_add_activity(
                array(
                    'action' => 'switched',
                    'event_type' => 'Themes',
                    'event_subtype' => $new_theme->get_stylesheet(),
                    'event_name' => $new_name,
                    'event_id' => 0,
                    'metadata' => $meta
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
        add_action('switch_theme', array(&$this, 'hooks_to_switch_theme'), 10, 3);
    }
}
