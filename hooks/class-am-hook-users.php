<?php
if (!defined('ABSPATH')) exit;

class Am_Hook_Users
{

    /**
     * hooks_to_user_register
     *
     * @param  mixed $user_id
     * @return void
     */
    public function hooks_to_user_register($user_id)
    {
        if (!$user_id) {
            return;
        }
        $user = get_user_by('id', $user_id);


        if ($user) {
            $meta = json_encode($user);
            log_activity(array(
                'action' => 'Registered',
                'action_type' => 'User',
                'action_title' =>  $user->user_nicename . ' Registration',
                'message' =>    'New account by the username ' . $user->user_nicename . ' account has been registered ',
                'action_id' => $user->ID,
                'action_details' => $meta,
                'action_changes' => ''
            ));
        }
    }

    /**
     * hooks_to_delete_user
     *
     * @param  mixed $user_id
     * @return void
     */
    public function hooks_to_delete_user($user_id)
    {
        if (!$user_id) {
            return;
        }
        $user = get_user_by('id', $user_id);
        if ($user) {
            $meta = json_encode($user);
            log_activity(array(
                'action' => 'Deleted',
                'action_type' => 'User',
                'action_title' =>  $user->user_nicename . ' Deleted',
                'message' =>    'Account by usernmae ' . $user->user_nicename . ' has been deleted ',
                'action_id' => $user->ID,
                'action_details' => $meta,
                'action_changes' => ''
            ));
        }
    }

    /**
     * hooks_to_wp_login
     *
     * @param  mixed $user
     * @return void
     */
    public function hooks_to_wp_login($user)
    {
        if ($user) {
            $logged_user = get_user_by('login',  $user);
            $meta = json_encode($logged_user);
            if ($logged_user) {
                log_activity(array(
                    'action' => 'Login',
                    'action_type' => 'User',
                    'action_title' => $logged_user->user_nicename . ' Login',
                    'message' =>    'User ' . $logged_user->user_nicename . ' has logged in',
                    'action_id' => $logged_user->ID,
                    'action_details' => $meta,
                    'action_changes' => ''
                ));
            }
        }
    }


    /**
     * hooks_to_profile_update
     *
     * @param  mixed $user_id
     * @return void
     */
    public function hooks_to_profile_update($user_id)
    {
        if (!$user_id) {
            return;
        }

        $user = get_user_by('id', $user_id);
        if ($user) {
            $meta = json_encode($user);
            log_activity(array(
                'action' => 'Updated',
                'action_type' => 'User',
                'action_title' => $user->user_nicename . ' Profile Update',
                'message' =>   'Account by the username ' . $user->user_nicename . ' has updated their profile',
                'action_id' => $user->ID,
                'action_details' => $meta,
                'action_changes' => ''
            ));
        }
    }

    /**
     * hooks_to_wrong_password
     *
     * @param  mixed $username
     * @return void
     */
    public function hooks_to_wrong_password($username)
    {
        if ($username) {
            log_activity(array(
                'action' => 'FailedLogin',
                'action_type' => 'User',
                'action_title' => $username . ' Login',
                'message' =>   'User going by name ' . $username . ' failed to login due to wrong password',
                'action_id' => 0,
                'action_details' => json_encode($username),
                'action_changes' => ''
            ));
        }
    }
    /**
     * hooks_to_logout
     *
     * @return void
     */
    public function hooks_to_logout()
    {
        $current_user = wp_get_current_user();

        if ($current_user->ID != 0) {

            log_activity(array(
                'action' => 'Logout',
                'action_type' => 'User',
                'action_title' => $current_user->user_nicename . ' Logout',
                'message' =>    'User ' . $current_user->user_nicename . ' has logged out',
                'action_id' => $current_user->ID,
                'action_details' => json_encode($current_user),
                'action_changes' => ''
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
        add_action('wp_login', array(&$this, 'hooks_to_wp_login'), 10, 2);
        add_action('delete_user', array(&$this, 'hooks_to_delete_user'));
        add_action('user_register', array(&$this, 'hooks_to_user_register'));
        add_action('profile_update', array(&$this, 'hooks_to_profile_update'));
        add_filter('wp_login_failed', array(&$this, 'hooks_to_wrong_password'));
        add_filter('wp_logout', array(&$this, 'hooks_to_logout'));
    }
}
