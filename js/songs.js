$(document).ready(function () {
    const albumInfo = {
        "vietnam_songs.php": 2,
        "english_songs.php": 3,
        "uploaded_songs.php": 4
    };

    let currentPage = window.location.href;
    let catId = 2;
    for (let page in albumInfo) {
        if (currentPage.includes(page)) {
            catId = albumInfo[page];
            break;
        }
    }

    function loadSongs(page) {
        $.ajax({
            url: "fetch_songs.php",
            type: "POST",
            data: {
                page_no: page,
                cat_id: catId
            },
            success: function (response) {
                $("#show-songs").html(response);
                $('html, body').scrollTop(0);
            },
            error: function () {
                $("#song-list").html("<div class='alert alert-danger'>Không thể tải danh sách bài hát</div>");
            }
        });
    }

    loadSongs(1);

    $(document).on("click", ".pagination a", function (e) {
        e.preventDefault();
        const page = $(this).data("page");
        loadSongs(page);
    });

    $(document).on("click", ".add-to-fav", function () {
        const songId = $(this).data("songid");
        const heartIcon = $(this).find("i.fa-heart");

        $.ajax({
            url: "add_favorite.php",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                song_id: songId
            }),
            success: function (response) {
                try {
                    let data = typeof response === 'object' ? response : JSON.parse(response);
                    if (data.status === 'success') {
                        heartIcon.addClass('text-danger');
                        Swal.fire({
                            title: 'Success',
                            text: data.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1000
                        });
                    } else {
                        Swal.fire(
                            data.status === 'warning' ? 'Warning' : 'Error',
                            data.message,
                            data.status
                        );
                    }
                } catch (e) {
                    console.error("JSON parse error:", e);
                    Swal.fire('Error', 'Invalid response from server', 'error');
                }
            },
            error: function (xhr, status, error) {
                console.error("Favorite AJAX Error:", error);
                try {
                    let response = JSON.parse(xhr.responseText);
                    Swal.fire('Error', response.message, 'error');
                } catch (e) {
                    Swal.fire('Error', 'Failed to connect to server', 'error');
                }
            }
        });
    });
});

