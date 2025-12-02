<?php
/**
 * Helper Functions for API
 */

// Create excerpt from content
function create_excerpt($content, $length = 150) {
    $content = strip_tags($content);
    if (strlen($content) <= $length) {
        return $content;
    }
    return substr($content, 0, $length) . '...';
}

// Format date
function format_date($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}
?>