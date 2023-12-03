<?php
$activePage = basename($_SERVER['PHP_SELF'], ".php");
session_start();

if (isset($_SESSION["user_id"])) {
    $auth_bool = true;
    $user_id = $_SESSION["user_id"];
}
?>

<div class="container__navbar">
    <div class="navbar">
        <div class="navbar__leftSide">
            <nav>
                <ul class="navbar__list">
                    <li>
                        <a href="/index.php" class="navbar__link <?= ($activePage === 'index') ? 'active' : '' ?>">Главная</a>
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
        </div>

        <div class="navbar__rightSide">
            <?php
            if ($auth_bool) {
                echo "<div>";
                echo "<a href='../create-article.php' class='navbar__btnWrite'>Написать статью</a>";
                $link_profile = "../profile.php?id=" . $user_id;
                echo "<a href='$link_profile' class='navbar__btn'>Профиль</a>";
                echo "</div>";
            } else {
                echo "<a href='../auth.php' class='navbar__btn'>Войти</a>";
            }
            ?>
        </div>

    </div>
</div>