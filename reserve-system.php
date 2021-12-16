<?php
/*
"Reserve System" is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
"Reserve Course" is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with "Reserve Course". If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.
*/

/**
 * Plugin Name:       Reserve Course
 * Plugin URI:    
 * Description:       Handle the basics with this plugin
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Clay Atlas
 * Author URI:        https://clay-atlas.com/us/
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain:
 * Text Path:
 */


// ABSPATH
defined( 'ABSPATH' ) || exit;


// Create menu
add_action('admin_menu', 'create_reserve_menu');

function create_reserve_menu() {
    //create new top-level menu
    add_menu_page(
        'Reserve System',
        'Reserve System',
        'administrator',
        'reserve-sysetm-id-0',
        'reserve_system_setting_page',
        'dashicons-book',
        99
    );

    //call register settings function
    add_action( 'admin_init', 'register_reserve_options_settings' );
}


function register_reserve_options_settings() {
    // Sample data
    $test_array = array(
        'teacher'         => 'Clay',
        'year'            => '2021',
        'Mon'             => '12',
        'Day'             => '20',
        'start_time'      => '0800',
        'end_time'        => '0900',
        'price'           => '29',
        'status'          => '1',
        'student_name'    => 'Chen Tung Chi',
        'sudent_email'    => 'skyonsame@gmail.com',
        'student_comment' => 'I am not sure whether I reserve it or not'
    );

    $ca_reserve_list = array(
        $test_array,
    );
    
    // Register our settings
    register_setting( 'tnt-settings-group', 'event_name' );
    register_setting( 'tnt-settings-group', 'ca_reserve_list', $ca_reserve_list );

    // Update
    // update_option( 'event_name' , 'Hello' );
    // update_option( 'ca_reserve_list', $ca_reserve_list );
    // array_push( $ca_reserve_list, $test_array );
    // update_option( 'ca_reserve_list', $ca_reserve_list );
}


// Reserve System wp-admin setting page
function reserve_system_setting_page() {
?>
<div class="wrap">
    <h1>CA Reserve System</h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'tnt-settings-group' ); ?>
        <?php do_settings_sections( 'tnt-settings-group' ); ?>
        
        <table class="form-table">
            <p>
                <?php 
                    $users = get_users( array( 'fields' => array( 'ID' ) ) );

                    foreach( $users as $user ) {
                        $capabilities = get_user_meta( $user->ID )['wp_capabilities'];
                        if (preg_match( '/um_teacher/', implode(',', $capabilities) )) {
                            //echo json_encode(get_user_meta( $user->ID )['wp_capabilities']);
                        }
                    }

                    //echo json_encode(get_option( 'event_name' ));
                    //echo json_encode(get_option( 'ca_reserve_list' ));
                    //global $new_whitelist_options;
                    //$option_names = $new_whitelist_options[ 'tnt-settings-group' ];
                    //echo json_encode( $option_names );
                    
                    //echo file_get_contents('./html/reserve_page.html');
                    echo dirname( __DIR__ . '/html/reserve_page.html' );
                ?>
            </p>
        </table>
        <?php submit_button(); ?>
    </form>
</div>

<?php 
}





// Try to edit the reseve page
add_filter( 'the_content', 'reserve_system_page_init' );

function reserve_system_page_init( $content ) {
    if ( get_the_title() == 'Reserve System Test' ) {
        // Variable
        $current_user = wp_get_current_user();
        $all_users = get_users( array( 'role__in' => array( 'author', '' )) );

        // Content
        $content = file_get_contents( __DIR__ . '/html/reserve_page.html' );
    }

    return $content;
}

?>
