<?php
if (!defined('ABSPATH')) exit;

class Am_Hook_Comments
{

    /**
     * hooks_to_comment
     *
     * @param  mixed $comment_ID
     * @param  mixed $comment_object
     * @return void
     */
    public function hooks_to_comment($comment_ID, $comment_object = null)
    {
        if (!$comment_ID) {
            return;
        }
        if (is_null($comment_object)) {
            $comment_object = get_comment($comment_ID);
        }
        $meta = json_encode($comment_object);
        switch (current_filter()) {
            case 'wp_insert_comment':
                $comment_action = 'created';
                break;

            case 'edit_comment':
                $comment_action = 'updated';
                break;

            case 'delete_comment':
                $comment_action = 'deleted';
                break;

            case 'trash_comment':
                $comment_action = 'trashed';
                break;

            case 'untrash_comment':
                $comment_action = 'untrashed';
                break;

            case 'spam_comment':
                $comment_action = 'spammed';
                break;

            case 'unspam_comment':
                $comment_action = 'unspammed';
                break;
        }

        $log =  array(
            'action' => $comment_action,
            'event_type' => 'Comments',
            'event_subtype' => get_post_type($comment_ID),
            'event_name' => esc_html(get_the_title($comment_ID)),
            'event_id' => $comment_ID,
            'metadata' => $meta
        );
        $this->insert_comment_log($log);
    }
    /**
     * hooks_to_transition_comment_status
     *
     * @param  mixed $current_status
     * @param  mixed $previous_status
     * @param  mixed $comment
     * @return void
     */
    public function hooks_to_transition_comment_status($current_status, $previous_status, $comment)
    {
        if (empty($comment)) {
            return;
        }
        if (!is_null($comment->comment_ID)) {
            $meta = json_encode($comment);
            $log =  array(
                'action' => $current_status,
                'event_type' => 'Comments',
                'event_subtype' => get_post_type($comment->comment_ID),
                'event_name' => esc_html(get_the_title($comment->comment_ID)),
                'event_id' => $comment->comment_ID,
                'metadata' =>  $meta
            );

            $this->insert_comment_log($log);
        }
    }

    /**
     * insert_comment_log
     *
     * @param  mixed $comment_log
     * @return void
     */
    protected function insert_comment_log($comment_log)
    {
        if (!empty($comment_log)) {
            am_add_activity(
                array(
                    'action' => $comment_log['action'],
                    'event_type' => $comment_log['event_type'],
                    'event_subtype' => $comment_log['event_subtype'],
                    'event_name' => $comment_log['event_name'],
                    'event_id' => $comment_log['event_id'],
                    'metadata' => $comment_log['metadata']
                )
            );
        }
    }

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        add_action('transition_comment_status', array(&$this, 'hooks_to_transition_comment_status'), 10, 3);
        add_action('wp_insert_comment', array(&$this, 'hooks_to_comment'), 10, 2);
        add_action('edit_comment', array(&$this, 'hooks_to_comment'));
        add_action('trash_comment', array(&$this, 'hooks_to_comment'));
        add_action('untrash_comment', array(&$this, 'hooks_to_comment'));
        add_action('spam_comment', array(&$this, 'hooks_to_comment'));
        add_action('unspam_comment', array(&$this, 'hooks_to_comment'));
        add_action('delete_comment', array(&$this, 'hooks_to_comment'));
    }
}
