<?php
if (!defined('ABSPATH')) exit;

class Am_Hook_Posts
{

    /**
     * hooks_to_delete_post
     *
     * @param  mixed $post_id
     * @return void
     */
    public function hooks_to_delete_post($post_id)
    {
        if (wp_is_post_revision($post_id))
            return;

        $post = get_post($post_id);

        if (!$post) {
            return;
        }

        if (in_array($post->post_status, array('auto-draft', 'inherit')))
            return;

        // Skip for menu items.
        if ('nav_menu_item' === get_post_type($post->ID))
            return;
        if ('wp_navigation' === get_post_type($post->ID))
            return;

        $post_details = json_encode([
            'title' => $post->post_title,
            'link' => get_permalink($post->ID),

        ]);

        log_activity(array(
            'action' => 'Deleted',
            'action_type' =>  'Post',
            'action_title' =>  $post->post_title,
            'message' =>   'Deleted' . ' Post ' . $post->post_title,
            'action_id' => $post->ID,
            'action_details' => $post_details,
            'action_changes' => '',
        ));
    }


    /**
     * hooks_to_transition_post_status
     *
     * @param  mixed $current_status
     * @param  mixed $previous_status
     * @param  mixed $post
     * @return void
     */
    public function hooks_to_transition_post_status($current_status, $previous_status, $post)
    {
        if (wp_is_post_revision($post->ID))
            return;

        if ('nav_menu_item' === get_post_type($post->ID))
            return;

        if ('wp_navigation' === get_post_type($post->ID))
            return;
        if (!$post) {
            return;
        }
        if ('auto-draft' === $previous_status && ('auto-draft' !== $current_status && 'inherit' !== $current_status)) {
            // When the page is created
            $event = 'Created';
        } elseif ('auto-draft' === $current_status || ('new' === $previous_status && 'inherit' === $current_status)) {
            return;
        } elseif ('trash' === $current_status) {
            // If the page was deleted
            $event = 'Trashed';
        } elseif ('trash' === $previous_status) {
            // If the page was restored from trash
            $event = 'Restored';
        } else {
            // Finally, the page was updated
            $event = 'Updated';
        }
        $type = 'Posts';
        if ('page' === $post->post_type) {
            $type = 'Pages';
        }

        $post_details = json_encode([
            'title' => $post->post_title,
            'link' => get_permalink($post->ID),
        ]);

        log_activity(array(
            'action' => $event,
            'action_type' =>  'Post',
            'action_title' =>  $post->post_title,
            'message' =>   $event . ' Post ' .  $post->post_title,
            'action_id' => $post->ID,
            'action_details' =>  $post_details,
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
        add_action('delete_post', array(&$this, 'hooks_to_delete_post'));
        add_action('transition_post_status', array(&$this, 'hooks_to_transition_post_status'), 10, 3);
    }
}
