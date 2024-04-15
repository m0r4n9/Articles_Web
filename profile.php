<?php
session_start();
$user_id = isset($_GET["id"]) ? intval($_GET["id"]) : -1;
if (isset($_SESSION["user_id"]) && intval($_SESSION["user_id"]) === $user_id) {
    $can_edit = true;
}
require_once("./config/db.php");
$slq_query_user = "select users.id, username, email, COUNT(a.id) as count_articles from users left join articles a on users.id = a.user_id where users.id = $user_id";
$user_data = mysqli_query($connect, $slq_query_user)->fetch_assoc();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./static/css/profile.css">
    <title>Профиль</title>
</head>
<body>
<div class="app">
    <div class="container">
        <?php include_once("./components/navbar.php") ?>
        <div class="card">
            <div class="card__header">
                <h1>Карточка пользователя: <span><?= $user_data["username"] ?></span></h1>
            </div>
            <div class="card__email">
                <p id="countArticles">Количество написанных статей: <?= $user_data["count_articles"] ?></p>
                <a href="<?= "./personal-articles.php?id=" . $user_data["id"] ?>">Посмотреть</a>
            </div>
            <div class="card__content" id="userDataContainer">
                <h2 class="card__subtitle">Информация о пользователе</h2>
                <form id="profile-data" class="form-grid">
                    <label for="name">Имя</label>
                    <input id="name" name="name" value="<?= $user_data['username'] ?>" type="text"
                           disabled>
                    <label for="email">Электронная почта</label>
                    <input id="email" name="email" value="<?= $user_data['email'] ?>" type="text" disabled>
                    <label for="phone">Номер телефона</label>
                    <input id="phone" name="phone" value="<?= $user_data['phone'] ?>" type="text" disabled>
                    <?php if ($can_edit): ?>
                        <div class="edit__action">
                            <button type="button" class="edit__btn">Редактировать</button>
                        </div>
                    <?php endif; ?>
                </form>

                <?php
                if ($can_edit) {
                    echo "<div class='logout'><a href='./logout.php'>Выйти из аккаунта</a></div>";
                }
                ?>
            </div>
        </div>
    </div>

    <?php include_once("./components/footer.php") ?>
    <script>
        $(document).ready(function () {
            let originalValues = {};

            $(".edit__btn").on('click', function () {
                $(".edit__action").append("" +
                    "<button type='button' class='save__btn'>Сохранить</button>" +
                    "<button type='button' class='cancel__btn' >Отменить</button>");

                $("input:disabled").each(function () {
                    originalValues[this.id] = $(this).val();
                    $(this).prop('disabled', false);
                });

                $(this).hide();
            });

            $('.edit__action').on('click', '.save__btn', function () {
                const name_data = $("#name");
                const email_data = $("#email");

                if (!name_data.val()) {
                    name_data.addClass('required');
                    return;
                }

                if (!email_data.val()) {
                    email_data.addClass('required');
                    return;
                }

                $.ajax({
                    url: './ajax/updateProfileData.php',
                    type: 'POST',
                    data: $("#profile-data").serialize(),
                    success: function (response) {
                        console.log("Response:", response);
                        if (response.status === 'success') {
                            $('.edit__btn').show();
                            $('#profile-data button:gt(0)').remove();
                            $(".card__header h1 span").text(response.name);

                            $("#profile-data input").each(function () {
                                $(this).prop('disabled', true);
                            });
                            originalValues = {};
                        }
                    }
                })
            });

            $(".edit__action").on('click', '.cancel__btn', function () {
                $(".edit__btn").show();

                $(".form-grid input").each(function () {
                    $(this).prop('disabled', true);
                    $(this).val(originalValues[this.id]);
                    $(this).removeClass('required');
                });

                $(this).remove();
                $(".save__btn").remove();
            });
        });
    </script>

</body>
</html>