<?php
require "../config/config.php";

session_start();
if (empty($_SESSION["user"])) {
    header("location: login.php");
    exit();
}

$user = $_SESSION["user"];

$id = $_GET["id"];

$statement = $db->prepare("UPDATE posts SET image=:image WHERE id=:id");
$statement->execute([
    "id" => $id,
    "image" => NULL,
]);


header("location: edit.php?id=$id");