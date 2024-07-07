<?php
if (!defined('ABSPATH')) exit;

class Am_Hook_Comments
{

    /**
     * hooks_to_comment
     *
     * @param  mixed $comment_ID
     * @param  mixed $comment
     * @return void
     */
    public function hooks_to_comment($comment_ID, $comment = null)
    {
        if (!$comment_ID) {
            return;
        }
        if (is_null($comment)) {
            $comment = get_comment($comment_ID);
        }
        $meta = json_encode($comment);
        switch (current_filter()) {
            case 'wp_insert_comment':
                $comment_action = 'Created';
                break;

            case 'edit_comment':
                $comment_action = 'Updated';
                break;

            case 'delete_comment':
                $comment_action = 'Deleted';
                break;

            case 'trash_comment':
                $comment_action = 'Trashed';
                break;

            case 'untrash_comment':
                $comment_action = 'Untrashed';
                break;

            case 'spam_comment':
                $comment_action = 'Spammed';
                break;

            case 'unspam_comment':
                $comment_action = 'Unspammed';
                break;
        }

        $log =  array(
            'action' => ucfirst($comment_action),
            'action_id' => $comment_ID,
            'action_type' => 'Comment',
            'action_title' => "$comment->comment_author Comment",
            'message' =>  $comment->comment_author . ' Commented ' . $comment->comment_content . ' at ' . $comment->comment_date,
            'action_details' => $meta,
            'action_changes' => ''
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
                'action' => ucfirst($current_status),
                'action_type' => 'Comment Transition',
                'action_title' => "$comment->comment_author Comment",
                'action_id' => $comment->comment_ID,
                'message' => 'Comment by ' . $comment->comment_author . ' ' . ucfirst($current_status),
                'action_details' =>  $meta,
                'action_changes' => json_encode([
                    'object' => 'comment',
                    'action' => '',
                    'old_value' => ucfirst($previous_status),
                    'new_value' => ucfirst($current_status)
                ]),
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

            log_activity(array(
                'action' => $comment_log['action'],
                'action_type' => $comment_log['action_type'],
                'action_title' => $comment_log['action_title'],
                'message' =>    $comment_log['message'],
                'action_id' => $comment_log['action_id'],
                'action_details' => $comment_log['action_details'],
                'action_changes' => $comment_log['action_changes'],
            ));
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
