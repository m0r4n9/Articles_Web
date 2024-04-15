<?php
$activePage = basename($_SERVER['PHP_SELF'], ".php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$auth_bool = false;

if (isset($_SESSION["user_id"])) {
    $auth_bool = true;
    $user_id = $_SESSION["user_id"];
}
?>

<div class="container__navbar">
    <div class="navbar">
        <nav class="navbar__leftSide">
            <ul class="navbar__list">
                <li>
                    <a href="/index.php"
                       class="navbar__link <?= ($activePage === 'index') ? 'active' : '' ?>">Главная</a>
                </li>
                <li>
                    <a href="../articles.php?page=1"
                       class="navbar__link <?= ($activePage === 'articles') ? 'active' : '' ?>">Статьи</a>
                </li>
                <li>
                    <a href="../questions.php"
                       class="navbar__link <?= ($activePage === 'questions') ? 'active' : '' ?>">Вопросы</a>
                </li>
            </ul>
        </nav>

        <div class="navbar__search">
            <label>
                <input id="search-articles" type="text" placeholder="Type to search">
            </label>
            <div id="found-articles" class="found-articles" style="display: none;">
                <div id="found-articles-title">
                    <p>Найденные статьи</p>
                    <button id="close-search-popup">Закрыть</button>
                </div>
                <div id="found-articles-content">

                </div>
            </div>
        </div>

        <div class="navbar__rightSide">
            <?php if ($auth_bool): ?>
                <div>
                    <a href="../create-article.php" class="navbar__btnWrite">Написать статью</a>
                    <a href="<?= '../profile.php?id=' . $user_id ?>" class="navbar__btn">Профиль</a>
                </div>
            <?php else: ?>
                <button id="popup-auth" class="navbar__btn">Авторизация</button>
            <?php endif; ?>


            <div id="authPopup" class="popup" style="display: none;">
                <div class="popup-content">
                    <button class="close">&times;</button>
                    <form id="authForm" class="authForm">
                        <div class="wrapper-input">
                            <input type="text" name="email" placeholder="Электронная почта" required>
                            <input type="password" name="password" placeholder="Пароль" required>
                        </div>
                        <button class="navbar__btn" type="submit">Войти</button>
                    </form>
                    <div class="reg_info">
                        Нет аккаунта?
                        <a href="../reg.php" class="reg_btn">Зарегистрироваться</a>
                    </div>
                    <div class="auth__error">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>