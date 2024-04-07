<?php
    function renderArticleCard($data): void {
        $link_details = "/article-details.php?id=" . $data["id"];

        echo "<article>";
        echo "<div class='article'>";
//        echo "<div class='article__header'>Автор: " . $data["username"] . "</div>";
        echo "<div>";
        echo "<p>Автор: ".$data["username"]."</p>";
        echo "<p>Дата: ".$data["date"]."</p>";
        echo "</div>";

        echo "<div class='article__title'><a href='$link_details'>" . $data["title"] . "</a></div>";


        echo "<div class='article__content'>";
        echo "<div class='article__preview'><img src='" . $data["image_url"] . "' alt=" . $data["title"] . " /></div>";
        echo "<div class='article__text'><p>" . $data["content"] . "</p></div>";
        echo "<div class='article__footer'><a href='$link_details' class='article__btn'>Читать далее</a></div>";

        echo "</div>";
        echo "</div>";
        echo "</article>";
    }