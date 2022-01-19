<?php
// Use the WordPress core function
require_once( "../../../wp-load.php" );

// Return results
$results = array();

// According to different type, we can process different requirements
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
    $ca_booking_data_1on1 = array();
    $ca_booking_data_group = array();
    $ca_booking_keys_1on1 = get_option( "ca_booking_list_1on1" );
    $ca_booking_keys_group = get_option( "ca_booking_list_group" );

    foreach ($ca_booking_keys_1on1 as $key) {
        array_push( $ca_booking_data_1on1, get_option( $key ) );
    }
    foreach ($ca_booking_keys_group as $key) {
        array_push( $ca_booking_data_group, get_option( $key ) );
    }

    // Return results
    $results["teachers"] = $teachers;
    $results["current_user_name"] = $current_user->user_login;
    $results["current_user_role"] = $current_user->roles;
    $results["current_user_email"] = $current_user->user_email;
    $results["booking_data_1on1"] = $ca_booking_data_1on1;
    $results["booking_keys_1on1"] = $ca_booking_keys_1on1;
    $results["booking_data_group"] = $ca_booking_data_group;
    $results["booking_keys_group"] = $ca_booking_keys_group;    

    echo json_encode($results);
}

// Before user actually booking, we need to check the dataset booking data again
else if ($_POST["type"] == "before_booking_check_1on1") {
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

        $results["results"] = "1on1 book ok";
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


// Add New Course 1on1
else if ($_POST["type"] == "add_new_course_1on1") {
    // Init
    $teacher_name = $_POST["teacher_name"];
    $date = $_POST["date"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];
    $price = $_POST["price"];

    // Course information
    $course_key = "1on1-" . $teacher_name . "-" . $date . " " . $start_time . "-" . $end_time;
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
    if ( empty($ca_booking_list) ) {
        $ca_booking_list = array();
    }

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
    $results["message"] = "1on1 course create OK!";
    echo json_encode($results);
}


// Add New Course Group
else if ($_POST["type"] == "add_new_course_group") {
    // Init
    $teacher_name = $_POST["teacher_name"];
    $date = $_POST["date"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];
    $price = $_POST["price"];
    $student_max_num = $_POST["student_max_num"];

    // Course information
    $course_key = "group-" . $teacher_name . "-" . $date . " " . $start_time . "-" . $end_time;
    $course_value = array(
        "teacher_name"       => $teacher_name,
        "date"               => $date,
        "start_time"         => $start_time,
        "end_time"           => $end_time,
        "price"              => $price,
        "student_max_num"    => $student_max_num,
        "student_payment"    => array(), // true or false
        "student_pay_info"   => array(), // time
        "student_names"      => array(),
        "student_emails"     => array(),
        "student_comments"   => array(),
    );

    // Write to database (key table)
    $ca_booking_list = get_option( "ca_booking_list_group" );
    if ( empty($ca_booking_list) ) {
        $ca_booking_list = array();
    }

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
    update_option( "ca_booking_list_group", $ca_booking_list);

    // Write to database (Key-Value table)
    update_option( $course_key, $course_value );

    // Return
    $results["message"] = "Group course create OK!";
    echo json_encode($results);
}


// (Group)Before user actually booking, we need to check the dataset booking data again
else if ($_POST["type"] == "before_booking_check_group") {
    // Init
    $book_user_name = $_POST["book_user_name"];
    $book_user_email = $_POST["book_user_email"];
    $book_course_key = $_POST["book_course_key"];

    $course_data = get_option( $book_course_key );
    
    // If the student is booked
    if ( in_array($book_user_name, $course_data["student_names"]) ) {
        $student_book_index = array_search( $book_user_name, $course_data["student_names"]);

        // Payment or not
        if ( $course_data["student_payment"][$student_book_index] == true) {
            $results["results"] = "booked.";
        }
        else {
            $results["results"] = "you need to pay!";
        }
    }

    // If the student not book and the course can be booked
    else if ( count($course_data["names"]) < $course_data["student_max_num"]) {
        array_push( $course_data["student_names"], $book_user_name );
        array_push( $course_data["student_emails"], $book_user_email );
        array_push( $course_data["student_payment"], false );

        update_option( $book_course_key, $course_data );

        $results["results"] = "group course can book";
    }

    // The student not book but the course could not be booked
    else if ( count($course_data["names"]) >= $course_data["student_max_num"]) {
        $results["results"] = "課程學生報名人數達到上限。";
    }

    echo json_encode($results);
}


// Delete the course
else if ( $_POST["type"] == "delete_course" ) {
    // Init
    $delete_key = $_POST["delete_course_key"];

    // Get
    $ca_booking_keys_1on1 = get_option( "ca_booking_list_1on1" );
    $ca_booking_keys_group = get_option( "ca_booking_list_group" );

    // Update
    if ( in_array( $delete_key, $ca_booking_keys_1on1 ) ) {
        $key_index = array_search( $delete_key, $ca_booking_keys_1on1);
        array_splice( $ca_booking_keys_1on1, $key_index, 1 );
        update_option( "ca_booking_list_1on1", $ca_booking_keys_1on1 );
    }
    else if ( in_array( $delete_key, $ca_booking_keys_group ) ) {
        $key_index = array_search( $delete_key, $ca_booking_keys_group);
        array_splice( $ca_booking_keys_group, $key_index, 1 );
        update_option( "ca_booking_list_group", $ca_booking_keys_group );
    }

    // Delete
    delete_option( $delete_key );

    $results["results"] = "Delete finished.";

    echo json_encode($results);
}

 
?>
