<?php
include_once("./config/db.php");

session_start();
if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql_query = "select * from users where email='$email' and password='$password'";
    $result = mysqli_query($connect, $sql_query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $_SESSION["user_id"] = $row["id"];
        header("Location: index.php");
        exit();
    } else {
        $error = "Неверная почта или пароль";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./static/css/auth.css">
    <title>Авторизация</title>
</head>
<body>
<div class="container">
    <div class="auth">
        <div class="auth__header">
            <h1>Вход</h1>
            <?php
            if (isset($error)) {
                echo "<h2 style='color: red; font-size: 18px;'>$error</h2>";
            }
            ?>
        </div>

        <form method="post" class="auth__form">
            <div class="auth__form-wrapper">
                <label for="email">Электронная почта</label>
                <input id="email" name="email" type="email" placeholder="Введите электронную почту"
                       class="auth__input">
            </div>
            <div class="auth__form-wrapper">
                <label for="password">Пароль</label>
                <input id="password" name="password" type="password" placeholder="Введите пароль"
                       class="auth__input">
            </div>

            <div class="auth__form-footer">
                <input type="submit" value="Войти">
            </div>
        </form>
    </div>
</div>
</body>
</html>