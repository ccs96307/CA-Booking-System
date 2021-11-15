<?php


if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    wp_die( sprintf(
        __( '%s should only be called when uninstalling the plugin.', 'ReserveCourse' ),
        __FILE__
    ) );
    exit;
}


// In here to execute uninstallation steps
