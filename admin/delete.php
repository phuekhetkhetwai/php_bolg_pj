<?php
require "../config/config.php";

$id = $_GET["id"];

$statement = $db->prepare("DELETE FROM posts WHERE id=:id");
$statement->execute([
    "id" => $id,
]);

header("location: index.php");