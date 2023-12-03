<?php
include_once("./config/db.php");

$slq_query = "SELECT
    articles.id,
    articles.title,
    articles.rating,
    articles.date,
    articles.image,
    articles.user_id,
    web.users.username,
    web.blocks.id AS block_id,
    web.blocks.type,
    web.blocks.content,
    web.blocks.title AS block_title
FROM
    articles
        JOIN
    web.users ON articles.user_id = web.users.id
        JOIN
    (
        SELECT
            article_id,
            MIN(id) AS block_id
        FROM
            web.blocks
        WHERE
                type = 'text'
        GROUP BY
            article_id
    ) AS filtered_blocks ON articles.id = filtered_blocks.article_id
        JOIN
    web.blocks ON filtered_blocks.block_id = web.blocks.id limit 5;";
$articles = mysqli_query($connect, $slq_query);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./static/css/index.css">
    <link rel="stylesheet" href="./static/css/navbar.css">
    <title>Главная страница</title>
</head>
<body>
<div id="app" class="app" style="background-color: #F0F0F0">
    <div class="container">

        <?php
        require_once("./components/navbar.php")
        ?>


        <div class="content">
            <div class="content__header">
                <h1>Новые статьи</h1>
            </div>
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