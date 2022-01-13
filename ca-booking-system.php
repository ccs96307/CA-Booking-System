<?php
/*
"CA Booking System" is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
"CA Booking System" is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with "CA Booking System". If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.
*/

/**
 * Plugin Name:       CA Booking System
 * Plugin URI:    
 * Description:       A easy-to-use booking system
 * Version:           0.0.7
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
add_action('admin_menu', 'create_ca_booking_menu');

function create_ca_booking_menu() {
    //create new top-level menu
    add_menu_page(
        'CA Booking System',
        'CA Booking System',
        'administrator',
        'ca-booking-sysetm-id-0',
        'ca_booking_system_setting_page',
        'dashicons-book',
        99
    );

    //call register settings function
    add_action( 'admin_init', 'register_ca_booking_system_settings' );
}


function register_ca_booking_system_settings() {
    // Sample data
    $course_1on1 = array(
        'teacher_name'         => 'Clay',
        'date'                 => '2021-10-20',
        'start_time'           => '8:0',
        'end_time'             => '9:0',
        'price'                => '10',
        'status'               => 'open', // open; processing; close;
        'student_name'         => 'Chen Tung Chi',
        'sudent_email'         => 'skyonsame@gmail.com',
        'student_comment'      => 'I am not sure whether I book it or not'
    );

    $course_group = array(
        'teacher_name'           => 'Clay',
        'date'                   => '2021-12-02',
        'start_time'             => '',
        'end_time'               => '',
        'price'                  => '30',
        'status'                 => 'open', // open; processing; close;
        'max_student'            => '30',
        'current_student_number' => '5',
        'student_names'          => array( '1', '2', '3', '4', '5' ),
        'student_emails'         => array( '1', '2', '3', '4', '5' ),
        'student_comments'       => array( '', '', '', '', '' ),
    );

    $ca_booking_list = array();
    
    // Register our settings ()
    register_setting( 'tnt-settings-group', 'event_name' );
    register_setting( 'tnt-settings-group', 'ca_booking_list_1on1');
    register_setting( 'tnt-settings-group', 'ca_booking_list_group' );

    // Update
    // update_option( 'event_name' , 'Hello' );
    // array_push( $ca_reserve_list, $test_array );
    // update_option( 'ca_booking_list_group', NULL );
}


// Booking System wp-admin setting page
function ca_booking_system_setting_page() {
?>
<div class="wrap">
    <h1>CA Booking System</h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'tnt-settings-group' ); ?>
        <?php do_settings_sections( 'tnt-settings-group' ); ?>
        
        <table class="form-table">
            <p>
                <?php
                    $ipn_data = get_option( 'event_name' );
                    
                    $ca_payment = false;
                    $ca_course_key = "";
                    foreach ( $ipn_data as $_data ) {
                        echo json_encode($_data) . "<br>";
                        // if ( $_data == "payment_status=Completed" ) {
                        //     $ca_payment = true;
                        // }

                        // $item_name = explode( "=", $_data )[0];
                        // $item_value = explode( "=", $_data )[1];

                        // if ( $item_name == "item_name" ) {
                        //     $item_value = str_replace( '+', ' ', $item_value );
                        //     $item_value = str_replace( '%3A', ':', $item_value );
                        //     $item_value = str_replace( '%28', '(', $item_value );
                        //     $item_value = str_replace( '%29', ')', $item_value );

                        //     //echo $item_value . "<br>";
                        //     //echo json_encode( get_option( $item_value ) );
                        //     //echo json_encode( get_option( "ca_booking_list_1on1" ) );
                        // }    
                        // echo $item_name . "=" . $item_value . "<br>";
                        
                    }

                    // $bookings = get_option( 'ca_booking_list_1on1' );
                    // if ( is_null( $bookings) ) echo "It is NULL!<br>";

                    // echo json_encode( $bookings );
                    
                    // foreach( $bookings as $key ) {
                    //     $data = get_option( $key );
                    //     echo json_encode( $key ) . " ";
                    //     echo json_encode( $data ) . "<br>";
                    // }

                    echo "======================================" . "<br>";

                    $bookings = get_option( 'ca_booking_list_group' );
                    
                    if ( empty( $bookings) ) echo "It is NULL!<br>";
                    echo json_encode( $bookings );
                    
                    foreach( $bookings as $key ) {
                        $data = get_option( $key );
                        echo json_encode( $key ) . " ";
                        echo json_encode( $data ) . "<br>";
                    }
                ?>
            </p>
        </table>
        <?php submit_button(); ?>
    </form>
</div>

<?php 
}


// Try to edit the reseve page
add_filter( 'the_content', 'ca_booking_system_page_init' );

function ca_booking_system_page_init( $content ) {
    if ( get_the_title() == 'Booking System' ) {
        // Content
        $content = file_get_contents( __DIR__ . '/html/ca_booking_page.html' );
    }

    return $content;
}



// Allow AJAX post to backend PHP processing file
add_action( 'wp_head', 'ca_booking_system_process_url' );
function ca_booking_system_process_url() {
    echo '<script type="text/javascript">
            var ca_process_url = "' . plugin_dir_url( __FILE__ ) . 'process.php' . '";
          </script>';
}


// Paypal IPN
add_action( 'wp_ajax_ca_paypal_ipn', 'ca_paypal_ipn_callback' );
add_action( 'wp_ajax_nopriv_ca_paypal_ipn', 'ca_paypal_ipn_callback' );

function ca_paypal_ipn_callback() {
    // Define debug mode and use sandbox or not
    define( "DEBUG", 1 );
    define( "USE_SANDBOX", 1 );

    if (USE_SANDBOX == true) {
        $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
    }
    else {
        $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
    }

    // LOG
    define( "LOG_FILE", "./ipn.log" );
    
    // 
    $raw_post_data = file_get_contents("php://input");
    $raw_post_array = explode( "&", $raw_post_data );
    $myPost = array();

    foreach ($raw_post_array as $keyval) {
        $keyval = explode( "=", $keyval );
        if (count($keyval) == 2) {
            $myPost[$keyval[0]] = urldecode($keyval[0]);
        }
    }

    // Read the post from Paypal system and add "cmd"
    $req = "cmd=_notify-validate";

    if (function_exists( "get_magic_quotes_gpc" )) {
        $get_magic_quotes_exists = true;
    }

    foreach ($myPost as $key => $value) {
        if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
            $value = urlencode(stripslashes($value));
        }
        else {
            $value = urlencode($value);
        }

        $req .= "&$key=$value";
    }


    // Use cURL to get connection
    $ch = curl_init($paypal_url);
    if ($ch == FALSE) {
        return FALSE;
    }

    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURLOPT_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "User-Agent: PHP-IPN-Verification-Script",
        "Connection: Close"
    ));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $reg);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

    // DEBUG mode enable
    if (DEBUG == true) {
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
    }

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Connection: Close"));

    $res = curl_exec($ch);
    if (curl_errno($ch) != 0) {
        if (DEBUG == true) {
            error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
        }
        curl_close($ch);
        exit;
    }
    else {
        // Log the entire HTTP response if debug is switched on.
        if (DEBUG == true) {
            error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
		    error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
        }
        curl_close($ch);
    }

    $tokens = explode("\r\n\r\n", trim($res));
    $res = trim(end($tokens));

    if (strcmp ($res, "VERIFIED") == 0) {
        if (DEBUG == true) {
            error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
        }
        else if (strcmp($res, "INVALID") == 0) {
            if (DEBUG == true) {
           		error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);     
            }
        }
    }

    // Save testing data
    $ipn_data = $raw_post_array;
    $new_array = get_option( "event_name" );
    if ( is_array( $new_array ) != 1 ) {
        $new_array = array();
    }

    // Validation
    $ca_payment = false;
    $ca_course_key = "";
    
    foreach ( $ipn_data as $_data ) {
        if ( $_data == "payment_status=Completed" ) {
            $ca_payment = true;
        }

        $item_name = explode( "=", $_data )[0];
        $item_value = explode( "=", $_data )[1];

        // Book username
        if ( $item_name == "custom" ) {
            $ca_book_username = $item_value;
        }

        if ( $item_name == "item_name" ) {
            $item_value = str_replace( '+', ' ', $item_value );
            $item_value = str_replace( '%3A', ':', $item_value );
            $item_value = str_replace( '%28', '(', $item_value );
            $item_value = str_replace( '%29', ')', $item_value );

            $ca_course_data = get_option( $item_value );

            // Change status
            if ( preg_match("/group/", $item_value) == 1 && $ca_payment == true ) {
                // Pass customize variable to get the user name or ID
                $student_book_index = array_search( $ca_book_username, $ca_course_data["student_names"] );
                $ca_course_data["student_payment"][$student_book_index] = true;

                // Save to database
                update_option( $item_value, $ca_course_data );
            }
            else {
                if ( $ca_course_data["status"] == "booking" && $ca_payment == true ) {
                    $ca_course_data["status"] = "booked";

                    // Save to database
                    update_option( $item_value, $ca_course_data );        
                }
            }
        }
    }

    // Record LOG
    // array_push( $new_array, $ipn_data );
    // update_option( "event_name", $new_array );
    update_option( "event_name", $ipn_data );
}


?>
