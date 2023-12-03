<?php
include_once("./config/db.php");

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page <= 0) {
    $page = 1;
}

if (isset($_GET["category"])) {
    $category = $_GET["category"];
    $query_category = "";
    $category !== "all" && $query_category = "where category = '$category'";
}

$limit_articles = 1;
$offset = ($page - 1) * $limit_articles;


$slq_query = "SELECT
    articles.id,
    articles.title,
    articles.rating,
    articles.image,
    articles.date,
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
            MAX(id) AS block_id
        FROM
            web.blocks
        WHERE
                type = 'text'
        GROUP BY
            article_id
    ) AS filtered_blocks ON articles.id = filtered_blocks.article_id
        JOIN
    web.blocks ON filtered_blocks.block_id = web.blocks.id $query_category  order by id DESC limit $offset, $limit_articles";
$articles = mysqli_query($connect, $slq_query);

$sql_count_elements = "SELECT COUNT(*) AS total_rows FROM articles $query_category ;";
$count_articles = mysqli_query($connect, $sql_count_elements)->fetch_assoc()["total_rows"];
?>

    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="./static/css/articles.css">
        <title>Статьи</title>
    </head>
    <body>

    <div class="app">
        <div class="container">

            <?php
            require_once("./components/navbar.php")
            ?>

            <div class="content">
                <div class="content__header">
                    <h1>Статьи</h1>
                    <div class="content__filters">
                        <form method="get" id="category__form" class="category__form">
                            <h2>Фильтрация по:</h2>
                            <div class="category__content">
                                <label id="checkbox-category">
                                    <input type="radio" <?php echo ($category === 'all') ? 'checked' : ''; ?>
                                           name="category" value="all">
                                    Все статьи
                                </label>

                                <label id="checkbox-category">
                                    <input type="radio" <?php echo ($category === 'it') ? 'checked' : ''; ?>
                                           name="category" value="it">
                                    IT
                                </label>

                                <label id="checkbox-category">
                                    <input type="radio" <?php echo ($category === 'design') ? 'checked' : ''; ?>
                                           name="category" value="design">
                                    Дизайн
                                </label>

                                <label id="checkbox-category">
                                    <input type="radio" <?php echo ($category === 'news') ? 'checked' : ''; ?>
                                           name="category" value="news">
                                    Новости технологий
                                </label>
                            </div>

                            <div class="category__footer">
                                <input type="submit" value="Применить" class="category__submit">
                                <input type="hidden" name="page" value="1">
                            </div>
                        </form>
                    </div>
                </div>

                <div class="content__articles">
                    <?php
                    require_once("./components/render-article-card.php");

                    while ($data = $articles->fetch_assoc()) {
                        renderArticleCard($data);
                    }
                    ?>
                </div>

                <div class="content__footer">
                    <?php
                    displayPagination($page, $count_articles, $category);
                    ?>
                </div>
            </div>

        </div>
    </div>
    </body>
    </html>


<?php

function displayPagination($currentPage, $totalPages, $category)
{

    echo "<ul class='content__pagination'>";

    $prevPage = ($currentPage > 1) ? $currentPage - 1 : 1;
    $nextPage = ($currentPage < $totalPages) ? $currentPage + 1 : $totalPages;

    $link_prev = "?page=$prevPage";
    $link_next = "?page=$nextPage";

    if (!empty($category)) {
        $link_prev = $link_prev . "&category=$category";
        $link_next = $link_next . "&category=$category";
    }
    echo "<li><a href='$link_prev'>&laquo; Назад</a></li>";

    // Отображение ссылок на страницы
    for ($i = 1; $i <= $totalPages; $i++) {
        $activeClass = ($i == $currentPage) ? 'active' : '';
        $link = "?page=$i";

        if (!empty($category)) {
            $link = $link . "&category=$category";
        }

        echo "<li class='$activeClass'><a href='$link'>$i</a></li>";
    }


    echo "<li><a href='$link_next'>Следующая &raquo;</a></li>";

    echo "</ul>";
}

?>