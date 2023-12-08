<?php
// If uninstall is not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Function to run on uninstall
function currency_rate_uninstall() {
    // Add your uninstall logic here
    // For example, you may want to delete options or database tables created by the plugin
}

// Register the uninstall function
register_uninstall_hook( __FILE__, 'currency_rate_uninstall' );
?>
