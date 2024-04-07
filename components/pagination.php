<?php
function generate_pagination($current_page, $total_pages)
{
    echo '<div id="pagination" class="pagination">';
    if ($current_page !== 1) {
        echo '<a href="?page=1">1</a>';
        if ($current_page !== 2) {
            echo ' ... ';
        }
    }

    if ($current_page > 2) {
        echo '<a href="?page=' . ($current_page - 1) . '">' . ($current_page - 1) . '</a>';
    }

    echo '<a href="?page=' . $current_page . '" class="active">' . $current_page . '</a>';

    if ($current_page < $total_pages) {
        echo '<a href="?page=' . ($current_page + 1) . '">' . ($current_page + 1) . '</a>';
        if ($current_page < $total_pages - 1) {
            echo ' ... ';
        }
    }

    if ($current_page < $total_pages - 1) {
        echo '<a href="?page=' . $total_pages . '">' . $total_pages . '</a>';
    }

    echo '</div>';
}
