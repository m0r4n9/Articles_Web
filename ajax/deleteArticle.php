<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: auth.php");
    exit();
}

require_once("../config/db.php");
$response = [];
header('Content-Type: application/json');

$article_id = $_POST['article_id'];
$sql_delete_article = "DELETE FROM articles WHERE id = $article_id";
if (mysqli_query($connect, $sql_delete_article)) {
    $response = ['status' => 'success'];
} else {
     $response = ['status' => 'error', 'message' => mysqli_error($connect)];
    echo json_encode(['status' => 'error', 'message' => mysqli_error($connect)]);
}

echo json_encode($response);