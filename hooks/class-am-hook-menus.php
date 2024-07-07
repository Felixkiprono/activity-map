<?php
if (!defined('ABSPATH')) exit;

class Am_Hook_Menus
{

    /**
     * hooks_to_menu_created
     *
     * @param  mixed $id
     * @return void
     */
    public function hooks_to_menu_created($id)
    {
        // Get the menu object using the selected nav menu ID
        $menu_object = wp_get_nav_menu_object($id);
        $meta = json_encode($menu_object);

        if ($menu_object) {
            am_add_activity(array(
                'action' => 'created',
                'event_type' => 'Menus',
                'event_subtype' => '-',
                'event_name' =>  $menu_object->name,
                'event_id' => $id,
                'metadata' => $meta
            ));
        }
    }

    /**
     * hooks_to_menu_updated
     *
     * @param  mixed $id
     * @return void
     */
    public function hooks_to_menu_updated($id)
    {
        // Get the menu object using the selected nav menu ID
        $menu_object = wp_get_nav_menu_object($id);
        $meta = json_encode($menu_object);

        if ($menu_object) {
            am_add_activity(array(
                'action' => 'updated',
                'event_type' => 'Menus',
                'event_subtype' => '-',
                'event_name' =>  $menu_object->name,
                'event_id' => $id,
                'metadata' => $meta
            ));
        }
    }

    /**
     * hooks_to_menu_deleted
     *
     * @param  mixed $id
     * @return void
     */
    public function hooks_to_menu_deleted($orig, $id, $deleted)
    {
        // Get the menu object using the term ID
        $menu_object = wp_get_nav_menu_object($term_id);

        if ($menu_object) {
            //build metadata
            $meta = json_encode($attachment);

            am_add_activity(array(
                'action' => 'deleted',
                'event_type' => 'Menus',
                'event_subtype' => '-',
                'event_name' => $menu_object->name,
                'event_id' => $id,
                'metadata' => $meta
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
        // add_action('wp_create_nav_menu', array(&$this, 'hooks_to_menu_created'), 10, 1);
        // add_action('wp_update_nav_menu', array(&$this, 'hooks_to_menu_updated'), 10, 1);
        // add_action('delete_nav_menu', array(&$this, 'hooks_to_menu_deleted'), 10, 3);
    }
}
