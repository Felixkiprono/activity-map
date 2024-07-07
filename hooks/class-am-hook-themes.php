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
            $theme_details = json_encode([
                'title' => $new_theme->get('Name'),
                'link' => '',

            ]);

            log_activity(array(
                'action' => 'Switched',
                'action_type' => 'Theme',
                'action_title' => $new_name,
                'message' =>   'Switched to ' . $new_theme->get('Name') . ' from ' . $old_theme->get('Name'),
                'action_id' => 0,
                'action_details' =>  $theme_details,
                'action_changes' => json_encode([
                    'object' => 'theme',
                    'action' => 'switched',
                    'old_value' => $old_theme->get('Name'),
                    'new_value' => $new_theme->get('Name')
                ]),
            ));
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
