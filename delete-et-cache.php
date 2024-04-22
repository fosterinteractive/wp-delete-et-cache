<?php
/*
Plugin Name: Delete ET Cache on Cron
Plugin URI: 
Description: Deletes /et-cache folder to work around issues in Pantheon's caching system and Divi creating thousands of files.
Version: 1.0
Author: Aidan Foster - Foster Interactive
Author URI: http://fosterinteractive.com    
*/

// Activation hook to set an option that the plugin has been activated.
register_activation_hook(__FILE__, function() {
    add_option('delete_et_cache_plugin_activated', true);
});

function delete_et_cache_admin_notice() {
    if (get_option('delete_et_cache_plugin_activated')) {
        echo '<div class="notice notice-warning"><p><strong>Delete ET Cache Plugin:</strong> Please define ENABLE_ET_CACHE_DELETION and ET_CACHE_DELETION_FREQUENCY in your wp-config.php to run the plugin.</p></div>';
        delete_option('delete_et_cache_plugin_activated'); // Delete the option so the notice won't show again
    }
}
add_action('admin_notices', 'delete_et_cache_admin_notice');

function check_required_constants() {
    if (!defined('ENABLE_ET_CACHE_DELETION') || !defined('ET_CACHE_DELETION_FREQUENCY')) {
        return false;
    }
    return true;
}

function schedule_deletion() {
    if (check_required_constants()) {
        $frequency = ET_CACHE_DELETION_FREQUENCY;
        switch ($frequency) {
            case 'hourly':
                $interval = 'hourly';
                $target_time = time();
                break;
            case 'daily':
                $interval = 'daily';
                $target_time = strtotime('tomorrow 3:00 am');
                break;
            case 'weekly':
                $interval = 'weekly';
                $target_time = strtotime('next Sunday 3:00 am');
                break;
            default:
                $interval = 'weekly';
                $target_time = strtotime('next Sunday 3:00 am');
                break;
        }

        wp_clear_scheduled_hook('delete_et_cache_hook');
        wp_schedule_event($target_time, $interval, 'delete_et_cache_hook');
    } else {
        wp_clear_scheduled_hook('delete_et_cache_hook');
    }
}

add_action('init', 'schedule_deletion');

function delete_et_cache() {
    if (check_required_constants() && ENABLE_ET_CACHE_DELETION) {
        $cache_directory = WP_CONTENT_DIR . '/et-cache';

        if (file_exists($cache_directory) && is_dir($cache_directory)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($cache_directory, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }

            // Do not attempt to remove the /et-cache directory itself
            // rmdir($cache_directory); <-- This line is removed to avoid permission
        } else {
            if (!file_exists($cache_directory)) {
                error_log("Cache directory does not exist: " . $cache_directory);
            } else if (!is_dir($cache_directory)) {
                error_log("Specified path is not a directory: " . $cache_directory);
            }
        }
    } else {
        error_log("Required constants not set or deletion is disabled.");
    }
}

add_action('delete_et_cache_hook', 'delete_et_cache');
?>
