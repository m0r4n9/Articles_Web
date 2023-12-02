<?php
include_once("./config/db.php");

session_start();
if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title_article = $_POST["title"];
    $category = $_POST["category"];

    $prev_image = $_FILES["image"];

    $file_name = $prev_image['name'];
    $file_tmp = $prev_image['tmp_name'];

    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $new_file_name = uniqid('', true) . "." . $file_ext;
    $destination = "static/images/" . $new_file_name;

    move_uploaded_file($file_tmp, $destination);

    $sql_insert_article = "insert into articles (id, title, category, rating, image, user_id) values (null, '$title_article', '$category', 0, '$destination', $user_id);";
    $article = mysqli_query($connect, $sql_insert_article);

    if ($article) {
        $article_id = mysqli_insert_id($connect);

        $counter = 1;
        $error_block = false;

        while (isset($_POST["block_type_" . $counter])) {
            $block_type = $_POST["block_type_" . $counter];

            if (isset($_POST["block_deleted_" . $counter])) {
                echo "SKIP!";
                echo $_POST["block_deleted_ . $counter"] . " - " . $counter;
                echo $block_type;
            } else {
                if ($block_type !== "image") {
                    $block_content = $_POST["block_content_" . $counter];

                    $query = "INSERT INTO blocks (id, title, type, content, label, article_id) VALUES (null, '', ?, ?, '', ?)";
                    $stmt = mysqli_prepare($connect, $query);

                    mysqli_stmt_bind_param($stmt, "sss", $block_type, $block_content, $article_id);

                    mysqli_stmt_execute($stmt);


                    if (mysqli_stmt_affected_rows($stmt) > 0) {
                        $block = true;
                    } else {
                        $block = false;
                    }

                    mysqli_stmt_close($stmt);

                } else {
                    $block_image = $_FILES["block_content_" . $counter];

                    $block_file_name = $block_image['name'];
                    $block_file_tmp = $block_image['tmp_name'];

                    $file_ext = pathinfo($block_file_name, PATHINFO_EXTENSION);
                    $block_file_new_name = uniqid('', true) . "." . $file_ext;
                    $block_image_destination = "static/images/" . $block_file_new_name;

                    move_uploaded_file($block_file_tmp, $block_image_destination);

                    if (move_uploaded_file($block_file_tmp, $block_image_destination)) {
                        echo "File uploaded successfully.";
                    } else {
                        echo "Error uploading file: " . $_FILES["block_content_" . $counter]['error'];
                    }

                    $block = mysqli_query($connect, "INSERT INTO blocks (id, title, type, content, label, article_id) VALUES (null, '', '$block_type', '$block_image_destination', '', $article_id)");
                }

//                if (!$block) {
//                    $error_block = true;
//                    break;
//                }
            }

            $counter++;
        }

        if ($error_block) {
            echo "Произошла ошибка!";
        }

    } else {
        echo "Error inserting article: " . mysqli_error($connect);
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
    <link rel="stylesheet" href="./static/css/create-article.css">
    <title>Создание статьи</title>
</head>
<body>
<div class="app">
    <div class="container">
        <?php
        include_once("./components/navbar.php");
        ?>

        <div class="content">
            <form method="post" class="form" enctype="multipart/form-data">
                <div class="form__wrapper">
                    <label for="title">Заголовой статьи</label>
                    <input id="title" type="text" name="title" required class="form__input">
                </div>

                <div class="form__wrapper">
                    <label for="category">Заголовой статьи</label>
                    <select name="category" id="category" style="margin-top: 4px;" required>
                        <option value="it">IT</option>
                        <option value="design">Дизайн</option>
                        <option value="news">Новостные технологии</option>
                    </select>
                </div>

                <div class="form__wrapper">
                    <label for="preview">Главное изображение</label>
                    <input id="preview" type="file" name="image" required>
                </div>

                <input type="hidden" name="rating" value="0">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">

                <div id="blocks-container" class="blocks-container">
                    <div class="block__wrapper">
                        <div>
                            <label for="block_type_1">Тип блока:</label>
                            <select name="block_type_1" id="block_type_1" onchange="changeContent(this, 1)">
                                <option value="text">Текст</option>
                                <option value="image">Изображение</option>
                                <option value="code">Код</option>
                            </select>
                        </div>

                        <div id="contentContainer_1">
                            <label>
                                Контент
                                <textarea name="block_content_1" cols="40" rows="3"></textarea>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="block__addBtn">
                    <button type="button" id="addBlockButton">Добавить блок</button>
                </div>


                <div class="form__footer">
                    <input type="submit" value="Создать">
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let counter = 1;

        function removeBlock(button, blockId) {
            let blockDiv = button.parentElement;
            blockDiv.classList.add("block-deleted");
            let hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = `block_deleted_${blockId}`;
            hiddenInput.value = "1";
            blockDiv.appendChild(hiddenInput);
        }

        function addBlock() {
            let blockId = ++counter;
            let newDiv = document.createElement("div");
            newDiv.className = "block__wrapper";
            newDiv.innerHTML = `
                            <div>
                            <label for="block_type_${blockId}">Тип блока:</label>
                                <select name="block_type_${blockId}" id="block_type_${blockId}" onchange="changeContent(this, ${blockId})">
                                    <option value="text">Текст</option>
                                    <option value="image">Изображение</option>
                                    <option value="code">Код</option>
                                </select>
                        </div>

                        <div id="contentContainer_${blockId}" class="block__content">
                            <label for="block_content_${blockId}">Контент</label>
                            <textarea id="block_content_${blockId}" name="block_content_${blockId}"></textarea>
                        </div>


                        <button class="removeBlockButton" type="button">Удалить блок</button>


                `;
            document.getElementById("blocks-container").appendChild(newDiv);

            newDiv.querySelector(".removeBlockButton").addEventListener("click", function () {
                removeBlock(this, blockId);
            });
        }

        document.getElementById("addBlockButton").addEventListener("click", addBlock);
    });


    function changeContent(select, blockNumber) {
        let selectedType = select.value;
        let contentContainer = document.getElementById(`contentContainer_${blockNumber}`);

        contentContainer.innerHTML = "";

        if (selectedType === "image") {
            contentContainer.innerHTML = `
                    <label>
                        Контент
                        <input required type="file" name="block_content_${blockNumber}">
                    </label>
                `;
        } else {
            contentContainer.innerHTML = `
                    <label for="block_content_${blockNumber}">Контент</label>
                    <textarea name="block_content_${blockNumber}"></textarea>
                `;
        }
    }
</script>

</body>
</html>