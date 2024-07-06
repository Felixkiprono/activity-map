<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

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
        $user = get_user_by('id', $user_id);
        am_add_activity(
            array(
                'action' => 'registered',
                'event_type' => 'Users',
                'event_subtype' => 'Profile',
                'event_id' => $user->ID,
                'event_name' =>   $user->user_nicename,
            )
        );
    }
    
    /**
     * hooks_to_delete_user
     *
     * @param  mixed $user_id
     * @return void
     */
    public function hooks_to_delete_user($user_id)
    {
        $user = get_user_by('id', $user_id);
        am_add_activity(
            array(
                'action' => 'deleted',
                'event_type' => 'Users',
                'event_subtype' => 'Profile',
                'event_id' => $user->ID,
                'event_name' =>   $user->user_nicename,
            )
        );
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
            if ($logged_user) {
                am_add_activity(
                    array(
                        'action' => 'logged_in',
                        'event_type' => 'Users',
                        'event_subtype' => 'Login',
                        'event_id' => $logged_user->ID,
                        'event_name' =>  $logged_user->user_nicename,
                    )
                );
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
        $user = get_user_by('id', $user_id);
        am_add_activity(
            array(
                'action' => 'updated',
                'event_type' => 'Users',
                'event_subtype' => 'Profile',
                'event_id' => $user->ID,
                'event_name' =>  $user->user_nicename,
            )
        );
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
            am_add_activity(
                array(
                    'action' => 'login_fail',
                    'event_type' => 'Users',
                    'event_subtype' => 'login',
                    'event_id' => 0,
                    'event_name' =>  $username,
                )
            );
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
            am_add_activity(
                array(
                    'action' => 'logged_out',
                    'event_type' => 'Users',
                    'event_subtype' => 'logout',
                    'event_id' => $current_user->ID,
                    'event_name' =>  $current_user->user_nicename,
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
        add_action('wp_login', array(&$this, 'hooks_to_wp_login'), 10, 2);
        add_action('delete_user', array(&$this, 'hooks_to_delete_user'));
        add_action('user_register', array(&$this, 'hooks_to_user_register'));
        add_action('profile_update', array(&$this, 'hooks_to_profile_update'));
        add_filter('wp_login_failed', array(&$this, 'hooks_to_wrong_password'));
        add_filter('wp_logout', array(&$this, 'hooks_to_logout'));
    }
}
