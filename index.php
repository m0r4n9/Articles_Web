<?php
require_once("./config/db.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$slq_query = "SELECT
    articles.id,
    articles.title,
    articles.rating,
    articles.date,
    articles.image_url,
    articles.user_id,
    users.username,
    blocks.id AS block_id,
    blocks.type,
    blocks.content,
    blocks.title AS block_title
FROM
    articles
        JOIN
    users ON articles.user_id = users.id
        JOIN
    (
        SELECT
            article_id,
            MIN(id) AS block_id
        FROM
            blocks
        WHERE
                type = 'text'
        GROUP BY
            article_id
    ) AS filtered_blocks ON articles.id = filtered_blocks.article_id
        JOIN
    blocks ON filtered_blocks.block_id = blocks.id where status != 'developing' order by articles.id desc limit 5;";
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
            if ($articles) {
                while ($data = $articles->fetch_assoc()) {
                    renderArticleCard($data);
                }
            } else {
                echo "<h3>Статей нет</h3>";
            }
            ?>
        </div>
    </div>
</div>
<?php include('./components/footer.php') ?>
</body>
</html>