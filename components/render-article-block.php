<?php
function renderTextBlock($text, $title): void
{
    echo "<div>";
    if ($title) {
        echo "<h2>" . $title . "</h2>";
    }
    echo "<p class='text'>$text</p>";
    echo "</div>";
}

function renderCodeBlock($code): void
{
    echo "<pre class='code'>";
    echo "<code class='language-php'>" . nl2br(htmlspecialchars($code)) . "</code>";
    echo "</pre>";
}

function renderImageBlock($image_src, $label): void
{
    echo "<figure class='image'>";
    echo "<img style='max-width: 100%' src='$image_src' alt='image'/>";
    echo "<figcaption>$label</figcaption>";
    echo "</figure>";
}

?>