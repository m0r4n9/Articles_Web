<?php
require_once("../config/db.php");
$response = [];

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$category = $_GET['category'] ?? 'all';

$limit_articles = 1;
$offset = ($page - 1) * $limit_articles;
$query_category = $category !== 'all' ? "AND category = '$category'" : "";

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
if (!$articles) {
    error_log("Ошибка при выполнении запроса: " . mysqli_error($connect));
    http_response_code(500);
    exit;
}

// Кол-во статей
$sql_count_elements = "SELECT COUNT(*) AS total_rows FROM articles $completedArticles ;";
$count_articles = mysqli_query($connect, $sql_count_elements)->fetch_assoc()["total_rows"];

//while ($data = $articles->fetch_assoc()) {
//    var_dump($data);
//    // renderArticleCard($data);
//}

// Включение буферизации вывода для перехвата вывода скрипта
ob_start();
while ($data = $articles->fetch_assoc()) {
    include ("../components/render-article-card.php");
    renderArticleCard($data);
};
// Получение всего сгенерированного вывода и очистка буфера
$articles_html = ob_get_clean();

ob_start();
require("../components/pagination.php");
generate_pagination($page, $count_articles);
$pagination_html = ob_get_clean();

$response = [
    'articles' => $articles_html,
    'pagination' => $pagination_html
];

header('Content-Type: application/json');
echo json_encode($response);