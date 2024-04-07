<?php
require_once("./config/db.php");

$id = -1;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["article_id"];
    $comment = $_POST["comment"];
    $user_id = $_POST["user_id"];

//    $insert_comment = "insert into comments (id, text, user_id, article_id) values (null, '$comment', $user_id, $id)";
//    mysqli_query($connect, $insert_comment);
    header("Location: article-details.php?id=" . urldecode($id));
}

if ($id === -1) {
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
    }
}

if ($id !== -1) {
    $fetch_article = "select articles.id, title, rating, user_id, date, username from articles join users u on u.id = articles.user_id where articles.id = '$id';";
    $fetch_blocks = "select * from blocks where article_id='$id'";
    $fetch_comments = "select u.id, text, username from comments join users u on u.id = comments.user_id where article_id = '$id' order by comments.id desc";

    $article_details = mysqli_query($connect, $fetch_article)->fetch_assoc();
    $blocks = mysqli_query($connect, $fetch_blocks);
    $comments = mysqli_query($connect, $fetch_comments);
} else {
    echo "ID not found in the URL";
}

session_start();
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
                if ($auth_bool && $article_details["user_id"] != $user_id): ?>
                    <form class='comments-form' method='post'>
                        <label for='comment'>Комментарий</label>
                        <div class='comments-form__wrapper'>
                            <textarea id='comment' name='comment' required></textarea>
                            <input type='hidden' name='article_id' value='$id'/>
                            <input type='hidden' name='user_id' value='$user_id'/>
                            <input type='submit' value='Отправить' class='comments-form__submit'/>
                        </div>
                    </form>

                <?php else: ?>
                    <div>
                        Вы не можете комментировать свою статью
                    </div>
                <?php endif; ?>

                <div class="comments-content">
                    <h2>Комментарии</h2>
                    <?php
                    while ($data = $comments->fetch_assoc()) {
                        echo "<div class='comments-content__item'>";

                        echo "<p style='margin-top: 12px; font-size: 18px;'>Пользователь: " . $data["username"] . "</p>";

                        echo "<p style='margin-top: 6px'>" . $data["text"] . "</p>";

                        $link_profile = "./profile.php?id=" . $data["id"];
                        echo "<a href='$link_profile' style='margin-top: 12px'>Перейти в профиль</a>";

                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>

<?php
function renderTextBlock($text, $title): void
{
    echo "<div>";
    if ($title) {
        echo "<h2>" . $title . "</h2>";
    }
    echo "<p class='text'>$text</p>";
    echo "</div>";
}

function renderCodeBlock($code): void
{
    echo "<pre class='code'>";
    echo "<code class='language-php'>" . nl2br(htmlspecialchars($code)) . "</code>";
    echo "</pre>";
}

function renderImageBlock($image_src, $label): void
{
    echo "<figure class='image'>";
    echo "<img style='max-width: 100%' src='$image_src' alt='image'/>";
    echo "<figcaption>$label</figcaption>";
    echo "</figure>";
}

?>