<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    header("Location: /index.php");
    exit();
}

require_once("../config/db.php");

$comment = $_POST['comment'];
$articleId = $_POST['articleId'];

$sql = "INSERT INTO comments (id, content, user_id, article_id) VALUES (null, '$comment', $userId, $articleId)";
$insert_comment = mysqli_query($connect, $sql);

$fetch_comments = "SELECT u.id, content, username FROM comments JOIN users u ON u.id = comments.user_id WHERE article_id = $articleId ORDER BY comments.id DESC";
$comments = mysqli_query($connect, $fetch_comments);

if ($comments) {
    while ($data = $comments->fetch_assoc()):?>
        <div class='comments-content__item'>
            <p style='margin-top: 12px; font-size: 18px;'>Пользователь: <?= $data["username"] ?></p>
            <p style='margin-top: 6px'><?= $data["content"] ?></p>
            <a href='<?= $link_profile ?>' style='margin-top: 12px'>Перейти в профиль</a>
        </div>
    <?php endwhile;
}