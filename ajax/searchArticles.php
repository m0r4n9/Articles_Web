<?php
require_once ("../config/db.php");
include_once ("../components/render-article-card-search.php");

$response = [];
$query = $_POST['query'];

$sql = "SELECT id, title, image_url FROM articles WHERE title LIKE '%$query%';";
$articles = mysqli_query($connect, $sql);

while ($data = $articles->fetch_assoc()) {
    renderSmallArticleCard($data);
}

//echo json_encode($response);