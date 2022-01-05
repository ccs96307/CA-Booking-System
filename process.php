<?php
// Use the WordPress core function
//$path = preg_replace('/wp-content(?!.*wp-content).*/', '', __DIR__);
//require_once( $path . 'wp-load.php' );
require_once( "../../../wp-load.php" );
//require_once('wp-load.php');

// Return results
$results = array();

// According to different type, we can give different processing
// Init event
if ($_POST["type"] == "init") {
    // Get teacher list
    $users = get_users( array( 'fields' => array( 'ID' ) ) );
    $teachers = array();

    foreach( $users as $user ) {
        $_user = get_userdata( $user->ID );

        if ( in_array("um_teacher", $_user->roles) == true ) {
            array_push( $teachers, $_user->user_login );
        }
    }

    // Get current user info
    $current_user = wp_get_current_user();

    // Get booking data
    $ca_booking_data = array();
    $ca_booking_keys = array();
    $ca_booking_list = get_option( "ca_booking_list_1on1" );

    foreach ($ca_booking_list as $key) {
        array_push( $ca_booking_data, get_option( $key ) );
        array_push( $ca_booking_keys, $key );
    }

    // Return results
    $results["teachers"] = $teachers;
    $results["current_user_name"] = $current_user->user_login;
    $results["current_user_role"] = $current_user->roles;
    $results["current_user_email"] = $current_user->user_email;
    $results["booking_data"] = $ca_booking_data;
    $results["booking_keys"] = $ca_booking_keys;

    echo json_encode($results);
}


// Before user actually booking, we need to check the dataset booking data again
else if ($_POST["type"] == "before_booking_check") {
    // Init
    $book_user_name = $_POST["book_user_name"];
    $book_user_email = $_POST["book_user_email"];
    $book_course_key = $_POST["book_course_key"];

    $course_data = get_option( $book_course_key );
    if ( $course_data["status"] == "open") {
        $course_data["status"] = "booking";
        $course_data["student_name"] = $book_user_name;
        $course_data["student_email"] = $book_user_email;

        update_option( $book_course_key, $course_data );

        $results["results"] = "ok";
    }
    else if ( $course_data["status"] == "booking" ) {
        if ( $book_user_name == $course_data["student_name"] ) {
            $results["results"] = "wait for payment";
        }
        else {
            $results["results"] = "not you";
        }
    }
    else {
        $results["results"] = "late...";
    }

    echo json_encode($results);
}


// Add New Course
else if ($_POST["type"] == "add_new_course") {
    // Init
    $teacher_name = $_POST["teacher_name"];
    $date = $_POST["date"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];
    $price = $_POST["price"];

    // Course information
    $course_key = $teacher_name . "-" . $date . " " . $start_time . "-" . $end_time;
    $course_value = array(
        "teacher_name"    => $teacher_name,
        "date"            => $date,
        "start_time"      => $start_time,
        "end_time"        => $end_time,
        "price"           => $price,
        "status"          => "open",        // open; booking; close
        "student_name"    => "",
        "student_email"   => "",
        "student_comment" => ""
    );

    // Write to database (key table)
    $ca_booking_list = get_option( "ca_booking_list_1on1" );
    $_count = 1;
    $c_key = "";
    
    foreach ( $ca_booking_list as $key ) {
        $cbl = explode(" ", $key);
        $c_key = "";

        for ($i=0; $i<count($cbl)-1; ++$i) {
            if ($i == 0) $c_key .= $cbl[$i];
            else $c_key .= " " . $cbl[$i];
        }

        if ($course_key == $c_key) {
            ++$_count;
        }
    }

    $course_key .= " (course-id-$_count)";

    array_push( $ca_booking_list, $course_key );
    update_option( "ca_booking_list_1on1", $ca_booking_list);

    // Write to database (Key-Value table)
    update_option( $course_key, $course_value );

    // Return
    $results["message"] = "OK!";
    echo json_encode($results);
}
 
?>
