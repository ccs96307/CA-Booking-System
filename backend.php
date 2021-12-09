<?php
$type = $_POST["type"];
$id = $_POST["id"];
$result = array();

if ($type == "search") {
    if ($id == 1) {
        $name = "Clay";
    } elseif ($id == 2) {
        $name = "a dog";
    }
}

$result = array("name" => $name);

echo json_encode($result);

?>
