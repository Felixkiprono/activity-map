<?php
if (!defined('ABSPATH')) exit;

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

        log_activity(array(
            'action' => 'Exported',
            'action_type' => 'Export',
            'action_title' =>  $name,
            'message' =>    'Exported  ' . $name,
            'action_id' => 0,
            'action_details' => $meta,
            'action_changes' => '',
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
