<?php
session_start();
$user_id = $_SESSION["user_id"] ?? false;
$article_id = $_GET["id"] ?? false;

if ($article_id && $user_id) {
    require_once("./config/db.php");
    $deleted = mysqli_query($connect, "delete from articles where id = $article_id and user_id = $user_id");

    if ($deleted) {
        header("Location: ./personal-articles.php?id=" . $user_id);
        exit();
    } else {
        echo mysqli_error($connect);
    }
}