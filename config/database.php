<?php

$host = "localhost";
$user = "root";
$password = "root";
$dbname = "scorecard";

$conn = new mysqli(
    $host,
    $user,
    $password,
    $dbname
);

if ($conn->connect_error) {
    die("Connection failed");
}
?>