<?php
require_once('../config/db.php');
session_start();

if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
    header("Location: auth.php");
    exit;
} else {
    $user_id = $_SESSION["user_id"];
}
header('Content-Type: application/json');

$response = [];

$name = $_POST["name"];
$email = $_POST["email"];
//$phone = $_POST["phone"];

$sql_update_user = "UPDATE users SET USERNAME = '$name', email = '$email' WHERE id = $user_id";
if (mysqli_query($connect, $sql_update_user)) {
    $response = ['status' => 'success', 'name' => $name];
} else {
    $response = ['status' => 'error', 'message' => mysqli_error($connect)];
};

echo json_encode($response);