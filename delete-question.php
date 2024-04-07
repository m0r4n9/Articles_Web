<?php
session_start();
$user_id = $_SESSION["user_id"] ?? false;
$question_id = $_GET["id"] ?? false;

if ($question_id && $user_id) {
    require_once("./config/db.php");
    $deleted = mysqli_query($connect, "delete from questions where id = $question_id");

    if ($deleted) {
        header("Location: /questions.php");
        exit();
    } else {
        echo mysqli_error($connect);
    }
}