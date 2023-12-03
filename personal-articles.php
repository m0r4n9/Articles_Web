<?php

if (isset($_GET["id"])) {
    $user_id = $_GET["id"];
}

include_once("./config/db.php");
$fetch_articles = "select articles.id, title, category, image, user_id, date, u.id, username from articles join web.users u on u.id = articles.user_id where user_id = $user_id";
$articles = mysqli_query($connect, $fetch_articles);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./static/css/personal-articles.css">
    <title>Созданные статьи</title>
</head>
<body>
<div class="app">
    <div class="container">

        <?php
        include_once("./components/navbar.php");
        ?>

        <div class="articles">
            <?php
            require_once("./components/render-article-card.php");
            while ($data = $articles->fetch_assoc()) {
                renderArticleCard($data);
            }
            ?>
        </div>

    </div>
</div>
</body>
</html>