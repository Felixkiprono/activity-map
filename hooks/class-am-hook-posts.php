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

        $event_name = esc_html(get_the_title($post));
        $meta = json_encode($post);

        if (empty($title))
            $event_name = __('(no title)');

        am_add_activity(
            array(
                'action' => 'deleted',
                'event_type' => 'Posts',
                'event_subtype' => $post->post_type,
                'event_id' => $post->ID,
                'event_name' =>   $event_name,
                'metadata' => $meta
            )
        );
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
        $type = 'Posts';
        if ('page' === $post->post_type) {
            $type = 'Pages';
        }

        $meta = json_encode($post);

        $event_name = esc_html(get_the_title($post));

        if (empty($title))
            $event_name = __('(no title)');

        am_add_activity(
            array(
                'action' => $event,
                'event_type' =>  $type,
                'event_subtype' => $post->post_type,
                'event_id' => $post->ID,
                'event_name' =>  $event_name,
                'metadata' => $meta
            )
        );
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
