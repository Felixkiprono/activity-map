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

