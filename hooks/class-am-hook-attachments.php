<?php
if (!defined('ABSPATH')) exit;

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

        $original_metadata = json_encode(wp_get_attachment_metadata($id));

        $link =  get_permalink($attachment->ID);

        $metadata = json_encode(['link'=>$link]);

        log_activity(array(
            'action' => 'Uploaded',
            'action_type' => 'Attachment',
            'action_title' =>  esc_html(get_the_title($attachment->ID)),
            'message' =>    'Uploaded Attachment ' . esc_html(get_the_title($attachment->ID)),
            'action_id' => $id,
            'action_details' => $original_metadata,
            'action_changes' => '',
            'metadata'=> $metadata 
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
        // Get the original metadata before update
        $original_metadata = wp_get_attachment_metadata($id);
        $meta = json_encode($attachment);

        log_activity(array(
            'action' => 'Edited',
            'action_type' => 'Attachment',
            'action_title' =>  esc_html(get_the_title($attachment->ID)),
            'message' =>    'Edited Attachment ' . esc_html(get_the_title($attachment->ID)),
            'action_id' => $id,
            'action_details' => json_encode($original_metadata),
            'action_changes' =>  $meta,
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
        $original_metadata = json_encode(wp_get_attachment_metadata($id));
        log_activity(array(
            'action' => 'Deleted',
            'action_type' => 'Attachment',
            'action_title' =>  esc_html(get_the_title($attachment->ID)),
            'message' =>   ' Deleted Attachment ' . esc_html(get_the_title($attachment->ID)),
            'action_id' => $id,
            'action_details' => $original_metadata,
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
        add_action('add_attachment', array(&$this, 'hooks_to_add_attachment'));
        add_action('edit_attachment', array(&$this, 'hooks_to_edit_attachment'));
        add_action('delete_attachment', array(&$this, 'hooks_to_delete_attachment'));
    }
}
