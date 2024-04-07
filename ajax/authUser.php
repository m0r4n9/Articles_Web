<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require ("../config/db.php");
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $password = isset($_POST['password']) ? trim($_POST['password']) : null;

    $sql_query = "select * from users where email='$email' and password='$password'";
    $result = mysqli_query($connect, $sql_query);

    header('Content-Type: application/json');
    if ($result->num_rows > 0) {
        session_start();
        $row = $result->fetch_assoc();

        $_SESSION["user_id"] = $row["id"];
        $_SESSION["role"] = $row["role"];
        http_response_code(200);
        echo json_encode(['status' => 'success']);
    } else {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Неверное имя пользователя или пароль.']);
    }
} else {
    http_response_code(405); // Метод не разрешен
    echo json_encode(['status' => 'error', 'message' => 'Неверный запрос.']);
}
