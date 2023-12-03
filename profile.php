<?php

session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: articles.php");
    exit();
} else {
    $user_id = $_SESSION["user_id"];
}

include_once("./config/db.php");

$slq_query_user = "select users.id, username, email, COUNT(a.id) as count_articles from users left join web.articles a on users.id = a.user_id where users.id = $user_id";
$user_data = mysqli_query($connect, $slq_query_user)->fetch_assoc();

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./static/css/profile.css">
    <title>Профиль</title>
</head>
<body>
<div class="app">
    <div class="container">

        <?php include_once("./components/navbar.php") ?>

        <div class="card">
            <div class="card__header">
                <h1>Карточка профиля</h1>
            </div>

            <div class="card__content">
                <h2 class="card__subtitle">Личные данные</h2>
                <ul class="card__list">
                    <li>
                        <div class="card__name">
                            <p>Имя: <?= $user_data["username"] ?></p>
                        </div>
                    </li>
                    <li>
                        <div class="card__email">
                            <p>Электронная почта: <?= $user_data["email"] ?></p>
                        </div>
                    </li>
                    <li>
                        <div class="card__email">
                            <p>Количество написанных статей: <?= $user_data["count_articles"] ?></p>
                        </div>
                    </li>
                </ul>

                <div class="card__changeData">
                    <a href="#">Изменить данные?</a>
                </div>
            </div>
        </div>

    </div>
</div>
</body>
</html>