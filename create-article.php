<?php
require_once("./config/db.php");

session_start();
if (isset($_SESSION["user_id"])) {
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
            <form class="form" id="articleForm" enctype="multipart/form-data">
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
                <input type="hidden" name="user_id" value="<?= $user_id ?>">

                <div id="blocks-container" class="blocks-container">
                    <div class="block__wrapper" data-block-id="0">
                        <div>
                            <label>Тип блока:
                                <select name="blocks[0][type]" class="block-type-select"
                                        onchange="changeContent(this, 0)">
                                    <option value="text">Текст</option>
                                    <option value="image">Изображение</option>
                                    <option value="code">Код</option>
                                </select>
                            </label>
                        </div>
                        <div class="block__content" data-content-id="0">
                            <label>Контент
                                <textarea name="blocks[0][content]"></textarea>
                            </label>
                        </div>
                        <button class="removeBlockButton" type="button" style="display: none;">Удалить блок</button>
                    </div>
                </div>

                <div class="block__addBtn">
                    <button type="button" id="addBlockButton">Добавить блок</button>
                </div>


                <div class="form__footer">
                    <button type="button" id="submitArticle">Создать</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("./components/footer.php") ?>
<script>
    $(document).ready(function () {
        function updateRemoveButtons() {
            const $blocks = $("#blocks-container .block__wrapper");
            if ($blocks.length > 1) {
                $blocks.find(".removeBlockButton").show();
            } else {
                $blocks.find(".removeBlockButton").hide();
            }
        }

        $("#blocks-container").on("click", ".removeBlockButton", function () {
            $(this).closest(".block__wrapper").remove();
            updateRemoveButtons();
        });

        $("#addBlockButton").click(function () {
            let blockId = new Date().getTime();
            let newDiv = $(`
        <div class="block__wrapper" data-block-id="${blockId}">
            <div>
                <label>Тип блока:
                    <select name="blocks[${blockId}][type]" onchange="changeContent(this, ${blockId})" class="block-type-select">
                        <option value="text">Текст</option>
                        <option value="image">Изображение</option>
                        <option value="code">Код</option>
                    </select>
                </label>
            </div>
            <div class="block__content" data-content-id="${blockId}">
                <label>Контент
                    <textarea name="blocks[${blockId}][content]"></textarea>
                </label>
            </div>
            <button class="removeBlockButton" type="button">Удалить блок</button>
        </div>
    `);
            $("#blocks-container").append(newDiv);
            updateRemoveButtons();
        });

        window.changeContent = function (select, blockId) {
            const selectedType = $(select).val();
            const contentContainer = $(`div[data-content-id='${blockId}']`);
            contentContainer.empty();

            if (selectedType === "image") {
                contentContainer.html(`
                <label>Контент
                    <input required type="file" name="blocks[${blockId}][content]">
                </label>
            `);
            } else {
                contentContainer.html(`
                <label>Контент
                    <textarea name="blocks[${blockId}][content]"></textarea>
                </label>
            `);
            }
        };

        $("#submitArticle").click(function () {
            const formData = new FormData($("#articleForm")[0]);

            const blocks = [];
            $("#blocks-container .block__wrapper").each(function () {
                const blockId = $(this).data('block-id');
                const type = $(this).find('.block-type-select').val();
                const content = $(this).find('[name^="blocks["][name$="][content]"]').val();
                blocks.push({blockId, type, content});
            });

            formData.append('blocks', JSON.stringify(blocks));

            $.ajax({
                url: './ajax/createArticle.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    console.log('Ответ сервера: ', JSON.parse(response));
                },
            });
        });
    });

</script>
</body>
</html>