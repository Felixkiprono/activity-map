<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Am_Hook_Users
{



    public function hooks_to_user_register($user_id)
    {
        $user = get_user_by('id', $user_id);
    }
    public function hooks_to_delete_user($user_id)
    {
        $user = get_user_by('id', $user_id);
    }

    public function hooks_to_wp_login($user_login, $user)
    {
    }

    public function hooks_to_clear_auth_cookie()
    {
        $user = wp_get_current_user();

        if (empty($user) || !$user->exists()) {
            return;
        }
    }

    public function hooks_to_profile_update($user_id)
    {
        $user = get_user_by('id', $user_id);
    }

    public function hooks_to_wrong_password($username)
    {
        if ('no' === AAL_Main::instance()->settings->get_option('logs_failed_login')) {
            return;
        }
    }

    public function __construct()
    {
        add_action('wp_login', array(&$this, 'hooks_to_wp_login'), 10, 2);
        add_action('clear_auth_cookie', array(&$this, 'hooks_to_clear_auth_cookie'));
        add_action('delete_user', array(&$this, 'hooks_to_delete_user'));
        add_action('user_register', array(&$this, 'hooks_to_user_register'));
        add_action('profile_update', array(&$this, 'hooks_to_profile_update'));
        add_filter('wp_login_failed', array(&$this, 'hooks_to_wrong_password'));
    }
}
