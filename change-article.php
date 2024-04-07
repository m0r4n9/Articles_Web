<?php

if (isset($_GET["id"])) {
    $article_id = $_GET["id"];
}

require_once("./config/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTitle = $_POST['title'];
    $newCategory = $_POST['category'];
    $newStatus = $_POST["status"];

    $updateArticleQuery = "UPDATE articles SET title = '$newTitle', category = '$newCategory', status = '$newStatus' WHERE id = $article_id";
    $updated = mysqli_query($connect, $updateArticleQuery);

    if (!$updated) {
        echo mysqli_error($connect);
    }

    foreach ($_POST['blocks'] as $block_id => $block) {
        $newBlockTitle = $block['title'] ?? "";
        $newBlockContent = $block['content'];
        $newBlockLabel = $block['label'];

        $updateBlockQuery = "UPDATE blocks SET title = ?, content = ?, label = ? WHERE id = ?";
        $stmt = mysqli_prepare($connect, $updateBlockQuery);
        mysqli_stmt_bind_param($stmt, "sssi", $newBlockTitle, $newBlockContent, $newBlockLabel, $block_id);
        $updated_block = mysqli_stmt_execute($stmt);

        if (!$updated_block) {
            echo mysqli_error($connect);
            echo "<br/>";
        }
    }

    header("Location: change-article.php?id=" . $article_id);
    exit();
}

$fetch_articles = "select articles.id as id, title, category, image, user_id, date, u.id as user_id, username, status from articles join web.users u on u.id = articles.user_id where articles.id = $article_id";
$article = mysqli_query($connect, $fetch_articles)->fetch_assoc();

$blocks_result = mysqli_query($connect, "select blocks.*, blocks.id as block_id from blocks where article_id = $article_id");
if ($blocks_result->num_rows > 0) {
    while ($block = $blocks_result->fetch_assoc()) {
        $blocks[] = $block;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./static/css/reset.css">
    <link rel="stylesheet" href="./static/css/navbar.css">
    <link rel="stylesheet" href="./static/css/change-article.css">
    <title>Изменение статьи</title>
</head>
<body>
<div class="app">
    <div class="container">

        <?php
        include_once("./components/navbar.php");
        ?>

        <div class="articles">
            <form method="post" action="">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?= $article['title'] ?>" required><br>

                <label for="status">Статус статьи:</label>
                <select name="status" id="status">
                    <option value="developing" <?= ($article['status'] == 'developing') ? 'selected' : '' ?>>В разработке</option>
                    <option value="completed" <?= ($article['status'] == 'completed') ? 'selected' : '' ?>>Завершена</option>
                </select>
                
                <label for="category">Category:</label>
                <select name="category" id="category" required>
                    <option value="it" <?= ($article['category'] == 'it') ? 'selected' : '' ?>>IT</option>
                    <option value="design" <?= ($article['category'] == 'design') ? 'selected' : '' ?>>Design</option>
                    <option value="news" <?= ($article['category'] == 'news') ? 'selected' : '' ?>>News</option>
                </select><br>

                <?php foreach ($blocks as $block): ?>
                    <div class="block-container">
                        <h2 style="margin-bottom: 12px;">Тип блока: <?= $block["type"] ?></h2>

                        <?php if ($block["type"] !== "image"): ?>
                            <label for="blocks[<?= $block['block_id'] ?>][title]">Block Title:</label>
                            <input type="text" id="blocks[<?= $block['block_id'] ?>][title]"
                                   name="blocks[<?= $block['block_id'] ?>][title]"
                                   value="<?= $block['title'] ?>">
                        <?php endif; ?>
                        <br>

                        <label for="blocks[<?= $block['block_id'] ?>][type]">Block Type:</label>
                        <input type="text" id="blocks[<?= $block['block_id'] ?>][type]" value="<?= $block["type"] ?>"
                               readonly>


                        <label for="blocks[<?= $block['block_id'] ?>][content]">Block Content:</label>
                        <textarea id="blocks[<?= $block['block_id'] ?>][content]"
                                  <?= ($block["type"] === 'image' ? "readonly" : "") ?>
                                  name="blocks[<?= $block['block_id'] ?>][content]"><?= $block['content'] ?></textarea><br>

                        <label for="blocks[<?= $block['block_id'] ?>][label]">Block Label:</label>
                        <input type="text" id="blocks[<?= $block['block_id'] ?>][label]"
                               name="blocks[<?= $block['block_id'] ?>][label]"
                            <?= ($block["type"] !== 'image' ? "readonly" : "") ?>
                               value="<?= $block['label'] ?>">

                        <br>
                    </div>
                <?php endforeach; ?>

                <input type="submit" value="Save">
            </form>
        </div>

    </div>
</div>
</body>
</html>