<?php

/**
 * Global helper functions.
 *
 */


/**
 * time_ago
 *
 * @param  mixed $datetime
 * @return void
 */
function time_ago($datetime)
{
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0) {
        return $diff->y . " year" . ($diff->y > 1 ? "s" : "") . " ago";
    } elseif ($diff->m > 0) {
        return $diff->m . " month" . ($diff->m > 1 ? "s" : "") . " ago";
    } elseif ($diff->d > 0) {
        return $diff->d . " day" . ($diff->d > 1 ? "s" : "") . " ago";
    } elseif ($diff->h > 0) {
        return $diff->h . " hour" . ($diff->h > 1 ? "s" : "") . " ago";
    } elseif ($diff->i > 0) {
        return $diff->i . " min" . ($diff->i > 1 ? "s" : "") . " ago";
    } else {
        return "just now";
    }
}

function current_wp_user_id()
{
    $user_id = get_current_user_id();
    return $user_id;
}

function profile_link($user_id, $user_name)
{
    $username = $user_name;
    if ($user_id == current_wp_user_id()) {
        $username = "You";
    }
    $specific_public_profile_link = get_author_posts_url($user_id);
    if ($specific_public_profile_link) {
        return "<a href='" . esc_url($specific_public_profile_link) . "'>$username</a>";
    }
    return null;
}

function current_user()
{
}
