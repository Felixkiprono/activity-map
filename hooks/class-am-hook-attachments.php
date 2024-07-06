<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Am_Hook_Attachments
{

    /**
     * hooks_to_add_attachment
     *
     * @param  mixed $id
     * @return void
     */
    public function hooks_to_add_attachment($id)
    {
        if (is_null($id)) {
            return;
        }
        $attachment = get_post($id);
        $meta = json_encode($attachment);
        am_add_activity(array(
            'action' => 'uploaded',
            'event_type' => 'Attachments',
            'event_subtype' => $attachment->post_type,
            'event_name' =>  $attachment->post_type.'_uploaded_'.esc_html(get_the_title($attachment->ID)),
            'event_id' => $id,
            'metadata' => $meta
        ));
    }
    
    /**
     * hooks_to_edit_attachment
     *
     * @param  mixed $id
     * @return void
     */
    public function hooks_to_edit_attachment($id)
    {
        if (is_null($id)) {
            return;
        }
        $attachment = get_post($id);
        $meta = json_encode($attachment);
        am_add_activity(array(
            'action' => 'edited',
            'event_type' => 'Attachments',
            'event_subtype' => $attachment->post_type,
            'event_name' =>  $attachment->post_type.'_edited_'.esc_html(get_the_title($attachment->ID)),
            'event_id' => $id,
            'metadata' => $meta
        ));
    }
    
    /**
     * hooks_to_delete_attachment
     *
     * @param  mixed $id
     * @return void
     */
    public function hooks_to_delete_attachment($id)
    {
        if (is_null($id)) {
            return;
        }
        $attachment = get_post($id);
        $meta = json_encode($attachment);
        am_add_activity(array(
            'action' => 'deleted',
            'event_type' => 'Attachments',
            'event_subtype' => $attachment->post_type,
            'event_name' =>  $attachment->post_type.'_deleted_'.esc_html(get_the_title($attachment->ID)),
            'event_id' => $id,
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
        add_action('add_attachment', array(&$this, 'hooks_to_add_attachment'));
        add_action('edit_attachment', array(&$this, 'hooks_to_edit_attachment'));
        add_action('delete_attachment', array(&$this, 'hooks_to_delete_attachment'));
    }
}
