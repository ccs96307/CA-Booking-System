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

    $results = array("name" => $_mon);
}

echo json_encode($results);


?>
