<?php
session_start();
$user_id = $_SESSION["user_id"] ?? false;

if (isset($_GET["id"])) {
    $creator_id = $_GET["id"];
}

$can_edit = $user_id == $creator_id;

require_once("./config/db.php");
$fetch_articles = "select articles.id as id, title, category, image_url, user_id, date, u.id as user_id, username from articles join users u on u.id = articles.user_id where user_id = $creator_id order by id desc";
$articles = mysqli_query($connect, $fetch_articles);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./static/css/personal-articles.css">
    <title>Созданные статьи</title>
</head>
<body>
<div class="app">
    <div class="container">
        <?php include_once("./components/navbar.php"); ?>
        <div class="articles">
            <?php
            require_once("./components/render-article-card.php");
            while ($data = $articles->fetch_assoc()):
                $link_details = "/article-details.php?id=" . $data["id"];
                ?>
                <article data-article-id="<?= $data["id"] ?>">
                    <div class='article'>
                        <div>
                            <p>Автор: <?= $data["username"] ?></p>
                            <p>Дата: <?= $data["date"] ?></p>
                        </div>

                        <div class='article__title'><a href='<?= $link_details ?>'><?= $data["title"] ?></a></div>

                        <div class='article__content'>
                            <div class='article__preview'><img src='<?= $data["image"] ?>' alt='<?= $data["title"] ?>'/>
                            </div>
                            <div class='article__text'><p><?= $data["content"] ?></p></div>
                            <div class='article__footer'>
                                <a href='<?= $link_details ?>' class='article__btn'>Читать далее</a>
                                <?php if ($can_edit): ?>
                                    <a style='margin-left: 20px;' href='./change-article.php?id=<?= $data["id"] ?>'
                                       class='article__btn'>Изменить</a>
                                    <button class="delete-btn" value="<?= $data['id'] ?>">Удалить</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>

            <?php endwhile; ?>
        </div>

    </div>
</div>
<?php include_once("./components/footer.php") ?>
<script>
    $(document).ready(function () {
        $(".delete-btn").on('click', function () {
            const article_id = $(this).val();
            const confirmed = confirm('Вы уверены, что хотите удалить статью?');

            if (confirmed) {
                $.ajax({
                    url: './ajax/deleteArticle.php',
                    type: 'post',
                    data: {
                        article_id
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            $(`[data-article-id=${article_id}]`).remove();
                        } else {
                            console.log("Error:", response?.message);
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr);
                    }
                });
            }
        })
    });
</script>
</body>
</html>