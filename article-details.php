<?php
require_once("./config/db.php");
require_once("./components/render-article-block.php");
//include_once("./config/debug.php");

if (isset($_GET["id"])) {
    $id = $_GET["id"];
}

if ($id !== -1) {
    $fetch_article = "select articles.id, title, rating, user_id, date, username from articles join users u on u.id = articles.user_id where articles.id = '$id';";
    $fetch_blocks = "select * from blocks where article_id='$id'";
    $fetch_comments = "SELECT u.id, content, u.username FROM comments JOIN users u ON u.id = comments.user_id WHERE article_id = $id ORDER BY comments.id DESC";

    $article_details = mysqli_query($connect, $fetch_article)->fetch_assoc();
    $blocks = mysqli_query($connect, $fetch_blocks);
    $comments = mysqli_query($connect, $fetch_comments);
} else {
    echo "ID not found in the URL";
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["user_id"])) {
    $auth_bool = true;
    $user_id = $_SESSION["user_id"];
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./static/css/article-details.css">
    <title><?= $article_details["title"] ?></title>
</head>

<body>
<div class="app" style="background-color: #F0F0F0">
    <div class="container">
        <?php
        include_once("./components/navbar.php");
        ?>

        <div class="article">
            <div class="article__header">
                <div class="article__author">
                    <a href="./profile.php?id=<?= $article_details["user_id"] ?>">Автор
                        статьи: <?= $article_details["username"] ?></a>
                    <p>Дата создания: <?= $article_details["date"] ?></p>
                </div>
                <div class="article__title">
                    <h1><?= $article_details["title"] ?></h1>

                </div>
            </div>

            <div class="article__content">
                <?php
                while ($block = $blocks->fetch_assoc()) {
                    if ($block["type"] === "text") {
                        renderTextBlock($block["content"], $block["title"]);
                        continue;
                    }
                    if ($block["type"] === "code") {
                        renderCodeBlock($block["content"]);
                        continue;
                    }
                    if ($block["type"] === "image") {
                        renderImageBlock($block["content"], $block["label"]);
                    }
                }
                ?>
            </div>
        </div>

        <div class="comments" id="comment-form">
            <?php
            if ($auth_bool && $article_details["user_id"] != $user_id) : ?>
                <form id="form-comment" class='comments-form'>
                    <label for='comment'>Комментарий</label>
                    <div class='comments-form__wrapper'>
                        <textarea id='comment' name='comment' required></textarea>
                        <input type='hidden' name='article_id' value='<?= $id ?>'/>
                        <input type='hidden' name='user_id' value='<?= $user_id ?>'/>
                        <input type='submit' value='Отправить' class='comments-form__submit'/>
                    </div>
                </form>
            <?php else : ?>
                <div>
                    Вы не можете комментировать свою статью
                </div>
            <?php endif; ?>

            <div class="comments-content">
                <h2>Комментарии</h2>
                <div id="comments-list">
                    <?php
                    if ($comments) {
                        while ($data = $comments->fetch_assoc()) : ?>
                            <div class='comments-content__item'>
                                <p style='margin-top: 12px; font-size: 18px;'>
                                    Пользователь: <?= $data["username"] ?></p>
                                <p style='margin-top: 6px'><?= $data["content"] ?></p>
                                <a href='<?= "/profile.php?id=" . $data["id"] ?>' style='margin-top: 12px'>Перейти в
                                    профиль</a>
                            </div>
                        <?php endwhile;
                    } else
                        echo "<div>Комментарий пока нет</div>"
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once("./components/footer.php") ?>
<script>
    $(document).ready(function () {
        $("#form-comment").submit(function (e) {
            e.preventDefault();
            const comment = $("#comment").val();
            const articleId = $("[name='article_id']").val();

            $.ajax({
                url: "./ajax/addComment.php",
                type: 'post',
                data: {
                    comment,
                    articleId
                },
                success: function (response) {
                    $("#comments-list").html(response);
                    $("#comment").val("");
                }
            });
        });
    })
</script>
</body>

</html>
