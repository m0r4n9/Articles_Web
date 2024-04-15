<?php
$response = [];

require_once("../config/db.php");

$article_id = $_POST['article_id'];
$newTitle = $_POST['title'];
$newCategory = $_POST['category'];
$newStatus = $_POST["status"];

$updateArticleQuery = "UPDATE articles SET title = '$newTitle', category = '$newCategory', status = '$newStatus' WHERE id = $article_id";
$updated = mysqli_query($connect, $updateArticleQuery);

if (!$updated) {
    $response[] = ['status' => 'error', 'message' => mysqli_error($connect)];
}

// GOOD

foreach ($_POST['blocks'] as $block_id => $block) {
    $newBlockTitle = $block['title'] ?? "";
    $newBlockContent = $block['content'];
    $newBlockLabel = $block['label'];

    $updateBlockQuery = "UPDATE blocks SET title = ?, content = ? WHERE id = $block_id";
    $stmt = mysqli_prepare($connect, $updateBlockQuery);
    mysqli_stmt_bind_param($stmt, "ss", $newBlockTitle, $newBlockContent);
    $updated_block = mysqli_stmt_execute($stmt);

    if (!$updated_block) {
        $response[] = ['status' => 'block_error', 'message' => mysqli_error($connect), 'block_type' => $block[$newBlockContent]];
    }
}


echo json_encode($response);