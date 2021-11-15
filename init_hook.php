<?php

register_activation_hook( __FILE__, function() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/Activation.php';
    Activation::activate();
} );


register_deactivation_hook( __FILE__, function() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/Deactivation.php';
    Deactivation::deactivate();
} );


register_uninstall_hook( __FILE__, function() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/Uninstall.php';
    Uninstall::uninstall();
} );
