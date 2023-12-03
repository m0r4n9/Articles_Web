<?php
include_once('./config/db.php');

session_start();
if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
    $auth_bool = true;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["question-title"];
    $answer = $_POST["question"];
    $date = date("Y-m-d");


    $sql_insert_question = "insert into questions (id, user_id, title, text, date) values (null, $user_id, '$title', '$answer', '$date');";
    $created_question = mysqli_query($connect, $sql_insert_question);

    if (!$created_question) {
        echo mysqli_error($connect);
    }


    $question_id = mysqli_insert_id($connect);
    echo $question_id;
    $link = "?id=" . $question_id;
    header("Location: question-details.php" . $link);
}

$sql_query_questions = "select questions.id, user_id, title, date, username from questions join web.users u on questions.user_id = u.id order by questions.id desc";
$questions = mysqli_query($connect, $sql_query_questions);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./static/css/questions.css">
    <title>Вопросы</title>
</head>
<body>
<div class="app">
    <div class="container">
        <?php
        include_once("./components/navbar.php");
        ?>
    </div>

    <div class="questionCreate">
        <button <?php echo (!$auth_bool) ? "disabled" : "" ?> id="question__btn">Задать вопрос</button>


        <?php
        if ($auth_bool) {
            echo "<div class='form' style='display: none;' id='form'></div>";
        }
        ?>
    </div>


    <div class="content">

        <?php
        while ($data = $questions->fetch_assoc()) {
            echo "<div class='question'>";

            echo "<div class='question__header'>";
            echo "<p>Пользователь: " . $data["username"] . "</p>";
            echo "<p>Вопрос создан: " . $data["date"] . "</p>";
            echo "</div>";

            echo "<div style='font-weight: bold' class='question__content'>";
            echo $data["title"];
            echo "</div>";


            echo "<div class='question__footer'>";

            $link_details = "?id=" . $data["id"];

            echo "<a href='./question-details.php$link_details'>Перейти к вопросу</a>";
            echo "</div>";

            echo "</div>";
        }
        ?>

    </div>
</div>

<script>
    function showForm() {
        var formDiv = document.getElementById('form');

        if (formDiv.style.display === 'none') {
            formDiv.style.display = 'block';

            formDiv.innerHTML = `
                    <form method="post">
                        <div class="form__inputWrapper">
                            <label for="question-title">Заголовок:</label>
                            <input type="text" id="question-title" name="question-title"/>
                        </div>

                        <div class="form__inputWrapper">
                            <label for="question">Ваш вопрос:</label>
                            <textarea id="question" name="question" rows="4" cols="50"></textarea>
                        </div>
                        <input class="question__btnSubmit" type="submit" value="Отправить"/>
                    </form>
                `;
        } else {
            formDiv.style.display = 'none';
            formDiv.innerHTML = '';
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        let questionButton = document.getElementById('question__btn');
        questionButton.addEventListener('click', showForm);
    })
</script>

</body>
</html>