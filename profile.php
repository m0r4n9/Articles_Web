<?php

session_start();
$user_id = isset($_GET["id"]) ? intval($_GET["id"]) : -1;

if (isset($_SESSION["user_id"]) && intval($_SESSION["user_id"]) === $user_id) {
    $can_edit = true;
}

include_once("./config/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];


    $updateQuery = "UPDATE users SET username='$username', email='$email' WHERE id=$user_id";
    $updated = mysqli_query($connect, $updateQuery);

    if (!$updated) {
        echo mysqli_error($connect);
    } else {
        header("Location: profile.php");
        exit();
    }
}

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

            <div class="card__content" id="userDataContainer">
                <h2 class="card__subtitle">Личные данные</h2>
                <ul class="card__list">
                    <li>
                        <div class="card__name">
                            <p id="username">Имя: <?= $user_data["username"] ?></p>
                        </div>
                    </li>
                    <li>
                        <div class="card__email">
                            <p id="email">Электронная почта: <?= $user_data["email"] ?></p>
                        </div>
                    </li>
                    <li>
                        <div class="card__email">
                            <p id="countArticles">Количество написанных статей: <?= $user_data["count_articles"] ?></p>
                            <?php
                            $link_personal_articles = "./personal-articles.php?id=" . $user_data["id"];
                            echo "<a href='$link_personal_articles'>Посмотреть:</a>";
                            ?>

                        </div>
                    </li>
                </ul>

                <?php
                if ($can_edit) {
                    echo "<div class='card__changeData' id='changeDataContainer'>";
                    echo "<button onclick='showForm()'>Изменить данные?</button>";
                    echo "</div>";
                }
                ?>
            </div>


            <?php
            if ($can_edit) {
                include_once("./components/changeFormProfile.php");
            }
            ?>

        </div>
    </div>

    <script>
        function showForm() {
            document.getElementById('userDataContainer').style.display = 'none';
            document.getElementById('changeDataContainer').style.display = 'none';
            document.getElementById('editForm').style.display = 'block';

            document.getElementById('newUsername').value = '<?= $user_data["username"] ?>';
            document.getElementById('newEmail').value = '<?= $user_data["email"] ?>';
            document.getElementById('newCountArticles').value = '<?= $user_data["count_articles"] ?>';
        }

        function saveChanges() {
            let newUsername = document.getElementById('newUsername').value;
            let newEmail = document.getElementById('newEmail').value;
            let newCountArticles = document.getElementById('newCountArticles').value;

            document.getElementById('username').innerText = 'Имя: ' + newUsername;
            document.getElementById('email').innerText = 'Электронная почта: ' + newEmail;
            document.getElementById('countArticles').innerText = 'Количество написанных статей: ' + newCountArticles;

            document.getElementById('userDataContainer').style.display = 'block';
            document.getElementById('changeDataContainer').style.display = 'block';
            document.getElementById('editForm').style.display = 'none';
        }
    </script>

</body>
</html>