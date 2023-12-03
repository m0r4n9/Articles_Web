<?php
include_once("./config/db.php");

session_start();
if (isset($_SESSION["user_id"])) {
    $auth_bool = true;
    $user_id = $_SESSION["user_id"];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $answer = false;
    $question_id = $_POST["question_id"];
    $user_id_form = $_POST["user_id"];
    $answer = $_POST["answer"];

    $sql_insert = "insert into answers (id, question_id, user_id, text) VALUES (null, $question_id, $user_id_form, '$answer');";
    $answer = mysqli_query($connect, $sql_insert);

    if (!$answer) {
        $error = true;
        echo "Erorr" . mysqli_error($connect);
    }

    header("Location: question-details.php?id=" . $question_id);
}


if (isset($_GET["id"])) {
    $id = $_GET["id"];
}

$sql_query_question = "select questions.id, user_id, title, text, date, username from questions join web.users u on u.id = questions.user_id where questions.id = $id;";
$sql_query_answer = "select answers.id, question_id, username, answers.text from answers join web.users u on u.id = answers.user_id where question_id = $id order by answers.id desc ;";

$question = mysqli_query($connect, $sql_query_question)->fetch_assoc();
$answers = mysqli_query($connect, $sql_query_answer);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./static/css/question-details.css">
    <title>Document</title>
</head>
<body>
<div class="app">
    <div class="container">

        <?php require_once("./components/navbar.php") ?>

        <div class="content">
            <div class="content__header">
                <p>Вопрос задал: <?= $question["username"] ?></p>
                <p>Публикация вопроса: <?= $question["date"] ?></p>
                <h1 class="content__title">
                    <?= $question["title"] ?>
                </h1>

            </div>

            <div class="content__body">
                <?= $question["text"] ?>
            </div>
        </div>

        <div class="answers">
            <div class="answers__header">
                <h2>Ответы</h2>
            </div>

            <div class="answers__container">
                <?php
                if ($error) {
                    echo "<h3>Произошла ошибка при ответе</h3>";
                }

                if ($auth_bool) {
                    echo "<form method='post' class='answer__form'>";

                    echo "<div class='form__wrapper'>";
                    echo "<label for='answer'>Ваш ответ:</label>";
                    echo "<textarea id='answer' name='answer' required></textarea>";
                    echo "<div>";

                    echo "<input type='submit' value='Ответить' class='answer__submit'/>";
                    echo "<input type='hidden' value='$id' name='question_id'/>";
                    echo "<input type='hidden' value='$user_id' name='user_id'/>";

                    echo "</form>";
                }
                ?>

                <div class="answers__content">

                    <?php
                    while ($data = $answers->fetch_assoc()) {
                        echo "<div>";

                        echo "<div>";
                        echo "<p>" . $data["username"] . "</p>";
                        echo "</div>";

                        echo "<div>";
                        echo "<p>" . $data["text"] . "</p>";
                        echo "</div>";

                        echo "</div>";
                    }
                    ?>

                </div>

            </div>
        </div>
    </div>
</div>
</body>
</html>