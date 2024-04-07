<?php
require_once("./config/db.php");
$completedArticles = "where status != 'developing'";

if (isset($_GET["category"])) {
    $category = $_GET["category"];
    $query_category = "";
    if ($category !== "all") {
        $query_category = " and category = '$category'";
        $completedArticles .= " and category = '$category'";
    }
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit_articles = 1;
$offset = ($page - 1) * $limit_articles;

$category = $_GET['category'] ?? 'all';

$slq_query = "SELECT
    articles.id,
    articles.title,
    articles.rating,
    articles.image_url,
    articles.date,
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
LEFT JOIN
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
LEFT JOIN
    blocks ON filtered_blocks.block_id = blocks.id
WHERE
    articles.status != 'developing'
    $query_category
ORDER BY
    articles.id DESC
LIMIT 
    $offset, $limit_articles";
$articles = mysqli_query($connect, $slq_query);

$sql_count_elements = "SELECT COUNT(*) AS total_rows FROM articles $completedArticles ;";
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
                <?php include_once("./components/pagination.php");
                generate_pagination($page, $count_articles);
                ?>
            </div>
        </div>

    </div>
</div>
<?php include("./components/footer.php") ?>
<script>
    $(document).ready(function () {
        $(document).on('click', '#pagination a', function (e) {
            e.preventDefault();
            const page = $(this).attr('href').split('page=')[1];
            const category = $('input[name="category"]:checked').val() || 'all';

            $.ajax({
                url: "./ajax/fetchArticles.php",
                type: 'get',
                data: {
                    page,
                    category
                },
                success: function (response) {
                    if (response?.articles) $('.content__articles').html(response.articles);
                    if (response?.pagination) $('.content__footer').html(response.pagination);

                    window.history.pushState({}, '', '?page=' + page + '&category=' + category);
                }
            });
        });
    });
</script>
</body>
</html>