<?php
require_once('wp-load.php');


// Init
$type = $_POST["type"];
$id = $_POST["id"];
$_mon = $_POST["_mon"];
$_tue = $_POST["_tue"];


// Return results
$results = array();


// Search event
if ($type == "search") {
    if ( $id != NULL) {
        update_option( 'event_name', $id );
        $name = $id; 
    }

    $results = array("name" => $_tue);

    echo json_encode($results);
}


// Init event
else if ($type == "init") {
    $users = get_users( array( 'fields' => array( 'ID' ) ) );
    $teachers = array();

    foreach( $users as $user ) {
        $capabilities = get_user_meta( $user->ID )['wp_capabilities'];
        if (preg_match( '/um_teacher/', implode(',', $capabilities) )) {
            array_push( $teachers, $user->ID );
        }
    }

    $results = array("teachers" => $teachers[0]);

    echo json_encode($results)
}




?>
