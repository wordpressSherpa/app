<?php

require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);

    if ($name !== '') {

        $stmt = $conn->prepare("
            INSERT INTO journals (name)
            VALUES (?)
        ");

        $stmt->bind_param("s", $name);
        $stmt->execute();
    }

    header('Location: index.php');
    exit;
}