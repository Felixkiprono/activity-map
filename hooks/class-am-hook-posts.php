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

    public function hooks_to_transition_post_status($new_status, $old_status, $post)
    {
        if ('auto-draft' === $old_status && ('auto-draft' !== $new_status && 'inherit' !== $new_status)) {
            // when page created
            $action = 'created';
        } elseif ('auto-draft' === $new_status || ('new' === $old_status && 'inherit' === $new_status)) {
            return;
        } elseif ('trash' === $new_status) {
            // if page was deleted.
            $action = 'trashed';
        } elseif ('trash' === $old_status) {
            $action = 'restored';
        } else {
            // finaly page updated.
            $action = 'updated';
        }

        if (wp_is_post_revision($post->ID))
            return;

        if ('nav_menu_item' === get_post_type($post->ID))
            return;

        am_add_activity(
            array(
                'action' => $action,
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
