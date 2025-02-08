<?php
require "../config/config.php";

$id = $_GET["id"];

$statement = $db->prepare("DELETE FROM users WHERE id=:id");
$statement->execute([
    "id" => $id,
]);

header("location: user_list.php");