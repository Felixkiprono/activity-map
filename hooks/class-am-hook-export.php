<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Am_Hook_Export
{

    /**
     * hooks_to_wp_export
     *
     * @param  mixed $arguments
     * @return void
     */
    public function hooks_to_wp_export($arguments)
    {
        if (empty($arguments)) {
            return;
        }
        $name = isset($arguments['content']) ? $arguments['content'] : 'all';

        $meta = json_encode($arguments);

        am_add_activity(array(
            'action' => 'exported_and_downloaded',
            'event_type' => 'Export',
            'event_subtype' => "Exported",
            'event_name' =>  $name,
            'event_id' => 0,
            'metadata' => $meta
        ));
    }

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        add_action('export_wp', array(&$this, 'hooks_to_wp_export'));
    }
}
