<?php
if (isset($_GET["id"])) {
    $article_id = $_GET["id"];
}

require_once("./config/db.php");

$fetch_articles = "select articles.id as id, title, category, image_url, user_id, date, u.id as user_id, username, status from articles join users u on u.id = articles.user_id where articles.id = $article_id";
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
    <link rel="stylesheet" href="./static/css/change-article.css">
    <title>Изменение статьи <?= $article['title'] ?></title>
</head>
<body>
<div class="app">
    <div class="container">

        <?php include_once("./components/navbar.php"); ?>

        <div class="articles">
            <form method="post" class="form">
                <label>
                    <input type="hidden" name="article_id" value="<?= $article_id ?>">
                </label>

                <label for="title">Заголовок:</label>
                <input type="text" id="title" name="title" value="<?= $article['title'] ?>" required><br>

                <label for="status">Статус статьи:</label>
                <select name="status" id="status">
                    <option value="developing" <?= ($article['status'] == 'developing') ? 'selected' : '' ?>>В
                        разработке
                    </option>
                    <option value="published" <?= ($article['status'] == 'published') ? 'selected' : '' ?>>Завершена
                    </option>
                </select>

                <label for="category">Category:</label>
                <select name="category" id="category" required>
                    <option value="it" <?= ($article['category'] == 'it') ? 'selected' : '' ?>>IT</option>
                    <option value="design" <?= ($article['category'] == 'design') ? 'selected' : '' ?>>Дизайн</option>
                    <option value="news" <?= ($article['category'] == 'news') ? 'selected' : '' ?>>Новости</option>
                </select><br>

                <?php foreach ($blocks as $block): ?>
                    <div class="block-container">
                        <h2 style="margin-bottom: 12px;">Тип блока: <?= $block["type"] ?></h2>

                        <?php if ($block["type"] !== "image"): ?>
                            <label for="blocks[<?= $block['block_id'] ?>][title]">Загаловок блока:</label>
                            <input type="text" id="blocks[<?= $block['block_id'] ?>][title]"
                                   name="blocks[<?= $block['block_id'] ?>][title]"
                                   value="<?= $block['title'] ?>">
                        <?php endif; ?>
                        <br>

                        <label for="blocks[<?= $block['block_id'] ?>][type]">Тип блок:</label>
                        <input type="text" id="blocks[<?= $block['block_id'] ?>][type]" value="<?= $block["type"] ?>"
                               readonly>


                        <label for="blocks[<?= $block['block_id'] ?>][content]">Контет:</label>
                        <textarea id="blocks[<?= $block['block_id'] ?>][content]"
                                  <?= ($block["type"] === 'image' ? "readonly" : "") ?>
                                  name="blocks[<?= $block['block_id'] ?>][content]"><?= $block['content'] ?></textarea><br>

                        <label for="blocks[<?= $block['block_id'] ?>][label]">Лейбл:</label>
                        <input type="text" id="blocks[<?= $block['block_id'] ?>][label]"
                               name="blocks[<?= $block['block_id'] ?>][label]"
                            <?= ($block["type"] !== 'image' ? "readonly" : "") ?>
                               value="<?= $block['label'] ?>">

                        <br>
                    </div>
                <?php endforeach; ?>
                <button type="button" class="submit_btn">
                    Сохранить
                </button>
            </form>
        </div>
    </div>
</div>
<?php include_once("./components/footer.php") ?>
<script>
    $(document).ready(function () {
        $(".submit_btn").on('click', function () {
            $.ajax({
                url: './ajax/updateArticle.php',
                type: 'post',
                data: $(".form").serialize(),
                success: function (response) {
                    console.log(response);
                }
            });
        });
    });
</script>
</body>
</html>