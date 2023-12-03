<?php
session_start();

if (isset($_SESSION["user_id"]) && isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
    $user_id = $_SESSION["user_id"];
} else {
    header("Location: ../index.php");
    exit();
}

include_once("../../config/db.php");


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mas_id = $_POST["id_del"];

    if (count($mas_id) === 0) {
        echo "<h2>Нечего удалять</h2>";
    }

    for ($i = 0; $i < count($mas_id); $i++) {
        $id = $mas_id[$i];
        $sql_delete = "delete from articles where id = '$id'";
        $deleted = mysqli_query($connect, $sql_delete);

        if ($deleted) {
            echo "Статья удалена";
            header("Location: ../admin-articles.php");
            exit();
        } else {
            echo "Что-то пошло не так";
            echo mysqli_error($connect);
        }
    }
}

$sql_query_articles = "select * from articles;";
$articles = mysqli_query($connect, $sql_query_articles);


?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../../static/css/admin-articles.css">
    <title>Лист Статей</title>
</head>
<body>
<div class="app">
    <?php include_once("../../components/admin-navbar.php") ?>

    <div class="app__table">

        <form method="post">
            <input type="submit" value="Удалить?">
            <table class="table">
                <thead>
                <tr>
                    <th></th>
                    <th>Id</th>
                    <th>Заголовок</th>
                    <th>Категория</th>
                    <th>Id автора</th>
                </tr>
                </thead>

                <tbody>
                <?php
                while ($data = $articles->fetch_assoc()) {
                    echo "<tr>";

                    echo "<td><input type='checkbox' name='id_del[]' value='" . $data["id"] . "'/></td>";
                    echo "<td>" . $data["id"] . "</td>";
                    echo "<td>" . $data["title"] . "</td>";
                    echo "<td>" . $data["category"] . "</td>";
                    echo "<td>" . $data["user_id"] . "</td>";

                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </form>
    </div>
</div>
</body>
</html>