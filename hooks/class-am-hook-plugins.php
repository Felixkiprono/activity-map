<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Am_Hook_Plugins
{


    /**
     * hooks_to_activate_plugin
     *
     * @param  mixed $plugin_name
     * @return void
     */
    public function hooks_to_activate_plugin($plugin_name)
    {
        if (is_null($plugin_name)) {
            return;
        }
        $this->insert_plugin_log($plugin_name, 'activated');
    }


    /**
     * hooks_to_deactivate_plugin
     *
     * @param  mixed $plugin_name
     * @return void
     */
    public function hooks_to_deactivate_plugin($plugin_name)
    {
        if (is_null($plugin_name)) {
            return;
        }
        $this->insert_plugin_log($plugin_name, 'deactivated');
    }

    /**
     * hooks_to_delete_plugin
     *
     * @param  mixed $plugin_name
     * @return void
     */
    public function hooks_to_delete_plugin($plugin_name)
    {
        if (is_null($plugin_name)) {
            return;
        }
        $this->insert_plugin_log($plugin_name, 'deleted');
    }

    /**
     * @param Plugin_Upgrader $upgrader
     * @param array $extra
     */
    public function hook_to_upgrader_process_complete($upgrader_object, $options)
    {
        $path = $upgrader_object->plugin_info();

        if ($options['action'] == 'install' && $options['type'] == 'plugin') {
            $path = $upgrader_object->plugin_info();
            if (!$path) {
                return;
            }
            $data = get_plugin_data($upgrader_object->skin->result['local_destination'] . '/' . $path, true, false);
            $this->insert_plugin_log($data['Name'], 'installed');
        }

        if ($options['action'] == 'update' && $options['type'] == 'plugin') {
            if (isset($extra['bulk']) && true == $extra['bulk']) {
                $plugins = $extra['plugins'];
            } else {
                // $slug =basename($path,".php");
                if (empty($path)) {
                    return;
                }
                //still set the plugin to array
                $plugins = array($path);
            }

            foreach ($plugins as $plugin) {
                $this->insert_plugin_log($plugin, 'updated');
            }
        }
    }

    /**
     * insert_plugin_log
     *
     * @param  mixed $name
     * @param  mixed $event
     * @return void
     */
    protected function insert_plugin_log($name, $event)
    {

        if (empty($name)) {
            return;
        }
        $plugins = get_plugins();

        if ($plugins[$name]) {
            $plugin_details = $plugins[$name];

            $meta = json_encode($plugin_details);

            if ($plugins[$name]) {

                am_add_activity(array(
                    'action' => $event,
                    'event_type' => 'Plugins',
                    'event_subtype' => "plugin_$event",
                    'event_name' =>  $plugin_details['Name'],
                    'event_id' => 0,
                    'metadata' => $meta
                ));
            }
        }
    }
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        add_action('activated_plugin', array(&$this, 'hooks_to_activate_plugin'));
        add_action('deactivated_plugin', array(&$this, 'hooks_to_deactivate_plugin'));
        add_action('delete_plugin', array(&$this, 'hooks_to_delete_plugin'));
        add_action('upgrader_process_complete',  array($this, 'hook_to_upgrader_process_complete'), 10, 2);
    }
}
