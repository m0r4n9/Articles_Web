<?php
function renderSmallArticleCard($data): void
{
    $link_details = "/article-details.php?id=" . $data["id"];
    $title = htmlspecialchars($data["title"]);
    $image_url = htmlspecialchars($data["image_url"]);

    echo <<<HTML
        <div class="article-card">
            <div class="preview">
                <img src="$image_url" alt="Preview for $title">
            </div>
            <div class="title">
                <p>$title</p>
            </div>
        </div>
HTML;
}