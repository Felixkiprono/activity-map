<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Am_Hook_Posts
{

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

        am_add_activity(
            array(
                'action' => 'deleted',
                'event_type' => 'Posts',
                'event_subtype' => $post->post_type,
                'event_id' => $post->ID,
                'event_name' =>  $this->_draft_or_post_title($post->ID),
            )
        );
    }

    protected function _draft_or_post_title($post = 0)
    {
        $title = esc_html(get_the_title($post));

        if (empty($title))
            $title = __('(no title)', 'am-activity-map');

        return $title;
    }

    public function hooks_to_transition_post_status($current_status, $previous_status, $post)
    {
        if ('auto-draft' === $previous_status && ('auto-draft' !== $current_status && 'inherit' !== $current_status)) {
            // When the page is created
            $event = 'created';
        } elseif ('auto-draft' === $current_status || ('new' === $previous_status && 'inherit' === $current_status)) {
            return;
        } elseif ('trash' === $current_status) {
            // If the page was deleted
            $event = 'trashed';
        } elseif ('trash' === $previous_status) {
            // If the page was restored from trash
            $event = 'restored';
        } else {
            // Finally, the page was updated
            $event = 'updated';
        }
        if (wp_is_post_revision($post->ID))
            return;

        if ('nav_menu_item' === get_post_type($post->ID))
            return;

        am_add_activity(
            array(
                'action' => $event,
                'event_type' => 'Posts',
                'event_subtype' => $post->post_type,
                'event_id' => $post->ID,
                'event_name' =>  $this->_draft_or_post_title($post->ID),
            )
        );
    }


    public function __construct()
    {
        add_action('delete_post', array(&$this, 'hooks_to_delete_post'));
        add_action('transition_post_status', array(&$this, 'hooks_to_transition_post_status'), 10, 3);
    }
}
