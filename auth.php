<?php
require_once("./config/db.php");

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
        $_SESSION["role"] = $row["role"];
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
    <div class="back_toMain">
        <a href="./index.php">Вернутся на главную</a>
    </div>
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
                <input id="create-account" type="submit" value="Войти">
                <div>
                    <a href="./reg.php">Создать аккаунт?</a>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="./static/js/jquery-3.7.1.js"></script>
<script>
    $(document).ready(function () {
        const $buttonReg = $("#create-account");

        $("#email").change(function () {
            const email = $(this).val();
            const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (regex.test(email)) {
                $(this).removeClass('invalid');
                $buttonReg.prop('disabled', false);
            } else {
                $(this).addClass('invalid');
                $buttonReg.prop('disabled', true);
            }
        });

        $("#password").change(function () {
            if ($(this).val()) {
                $(this).removeClass('invalid');
                $buttonReg.prop('disabled', false);
            } else {
                $(this).addClass('invalid');
                $buttonReg.prop('disabled', true);
            }
        });
    });
</script>
</body>
</html>