<?php
require_once('wp-load.php');


$type = $_POST["type"];
$id = $_POST["id"];
$_mon = $_POST["_mon"];
$_tue = $_POST["_tue"];

$result = array();
$results = array();

if ($type == "search") {
    if ( $id != NULL) {
        update_option( 'event_name', $id );
        $name = $id; 
    }
}


$result = array("name" => $_mon);
$results = array(
    "mon" => $_mon,
    "tue" => $_tue
);

echo json_encode($result);
// echo json_encode($results);

?>
