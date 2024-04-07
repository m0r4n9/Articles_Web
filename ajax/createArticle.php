<?php
require_once('../config/db.php');
session_start();

if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
    exit;
} else {
    $user_id = $_SESSION["user_id"];
}

$response = [];
header('Content-Type: application/json');

$title = $_POST["title"];
$category = $_POST["category"];
$date = date("Y-m-d");

$image = $_FILES['image'];
$imageName = $image['name'];
$imageTmpName = $image['tmp_name'];
$imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
$newImageName = uniqid('', true) . "." . $imageExtension;
$imageDestination = "../static/images/" . $newImageName;

$sql_insert_article = "INSERT INTO articles (id, title, category, status, rating, image_url, date, user_id)
VALUES (null, '$title', '$category', 'developing', 0, '$imageDestination', '$date', $user_id)";

if (mysqli_query($connect, $sql_insert_article)) {
    $article_id = mysqli_insert_id($connect);
    move_uploaded_file($imageTmpName, $imageDestination);

    $blocks = json_decode($_POST['blocks'], true);
    foreach ($blocks as $item) {
        $type = $item['type'];
        $content = mysqli_real_escape_string($connect, $item['content']);
        if ($type !== 'image') {
            $sql_insert_block = "INSERT INTO blocks (id, type, content, title, article_id) VALUES (null, '$type', '$content', '', $article_id)";
            mysqli_query($connect, $sql_insert_block);
        } else {
            $block_id = $item['blockId'];
            $block_image = $_FILES['blocks'];
            $block_imageName = $_FILES['blocks']['name'][$block_id]['content'];
            $block_tmp_image = $_FILES['blocks']['tmp_name'][$block_id]['content'];
            $block_image_exs = pathinfo($block_imageName, PATHINFO_EXTENSION);
            $block_imageNew = uniqid('', true) . "." . $block_image_exs;
            $block_imageDes = "../static/images/" . $block_imageNew;

            $sql_insert_block = "INSERT INTO blocks (id, type, content, title, article_id) VALUES (null, 'image', '$block_imageDes', '', $article_id)";
            if (mysqli_query($connect, $sql_insert_block)) {
                move_uploaded_file($block_tmp_image, $block_imageDes);
            }
        }
    }
}
echo json_encode($response);