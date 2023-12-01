<?php
include_once("./config/db.php");

session_start();
if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    echo $_POST["block_type_1"];

    $counter = 1;
    $dynamicBlocks = [];

    while(isset($_POST["block_type_" . $counter])) {
        echo $_POST["block_type_" . $counter];
        $counter++;
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
            <form method="post" class="form">
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
                    <input id="preview" type="file" name="image">
                </div>

                <input type="hidden" name="rating" value="0">
                <input type="hidden" name="user_id" value="<?= $user_id?>">

                <div id="blocks-container" class="blocks-container">
                    <div class="block">
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
                                <input type="text" name="block_content_1">
                            </label>
                        </div>
                    </div>
                </div>

                <button type="button" id="addBlockButton">Добавить блок</button>

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

        function removeBlock(button) {
            let blockDiv = button.parentElement;
            blockDiv.parentNode.removeChild(blockDiv);
            counter--;
        }

        function addBlock() {
            counter++;
            let newDiv = document.createElement("div");
            newDiv.innerHTML = `
                            <div>
                            <label for="block_type_${counter}">Тип блока:</label>
                                <select name="block_type_${counter}" id="block_type_${counter}" onchange="changeContent(this, ${counter})">
                                    <option value="text">Текст</option>
                                    <option value="image">Изображение</option>
                                    <option value="code">Код</option>
                                </select>
                        </div>

                        <div id="contentContainer_${counter}">
                            <label>
                                Контент
                                <input type="text" name="block_content_${counter}">
                            </label>
                        </div>
                        <button type="button" class="removeBlockButton">Удалить блок</button>
                `;
            document.getElementById("blocks-container").appendChild(newDiv);

            newDiv.querySelector(".removeBlockButton").addEventListener("click", function () {
                removeBlock(this);
            });
        }

        document.getElementById("addBlockButton").addEventListener("click", addBlock);
    });


    function changeContent(select, blockNumber) {
        let selectedType = select.value;
        let contentContainer = document.getElementById(`contentContainer_${blockNumber}`);

        contentContainer.innerHTML = "";

        if (selectedType === "text") {
            contentContainer.innerHTML = `
                    <label>
                        Контент
                        <input type="text" name="block_content_${blockNumber}">
                    </label>
                `;
        } else if (selectedType === "image") {
            contentContainer.innerHTML = `
                    <label>
                        Контент
                        <input type="file" name="block_content_${blockNumber}">
                    </label>
                `;
        } else if (selectedType === "code") {
            contentContainer.innerHTML = `
                    <label>
                        Контент
                        <textarea name="block_content_${blockNumber}"></textarea>
                    </label>
                `;
        }
    }
</script>

</body>
</html>