<script src="../static/js/jquery-3.7.1.js"></script>
<script>
    $(document).ready(function () {
        let debounceTimer;

        const $searchArticles = $('#search-articles');

        $searchArticles.on('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                const query = $('#search-articles').val();
                if (query.length > 0) {
                    $("#found-articles").show();
                    $.ajax({
                        url: '../ajax/searchArticles.php',
                        type: 'POST',
                        data: {query},
                        success: function (response) {
                            $("#found-articles-content").html(response);
                        }
                    });
                } else {
                    $('#search-results').empty();
                    $("#found-articles").hide();
                }
            }, 500);
        });

        $searchArticles.click(function () {
            if ($('#search-articles').val()) {
                $("#found-articles").show();
            }
        });

        $("#close-search-popup").click(function () {
            $("#found-articles").hide();
        });
    });
</script>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["user_id"])): ?>
    <script>
        $(document).ready(function () {
            $('#popup-auth').click(function (e) {
                e.preventDefault();
                $('#authPopup').slideToggle({
                    duration: 200,
                });
            });

            $('.close').click(function () {
                $('#authPopup').slideUp({
                    duration: 200
                });
            });

            $('#authForm').submit(function (e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "./ajax/authUser.php",
                    data: $("#authForm").serialize(),
                    beforeSend: function () {
                        $(".auth__error").empty();
                    },
                    success: function (response) {
                        console.log(response);
                        if (response.status === 'success') {
                            location.reload();
                            $('#authPopup').slideUp({
                                duration: 200
                            });
                        } else {
                            $('.auth__error').append(`<p>${response.message}</p>`);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 401) $(".auth__error").html(`<p>${xhr?.responseJSON.message}</p>`);
                    }
                });

            })
        });
    </script>
<?php endif; ?>
