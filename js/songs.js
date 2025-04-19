
let currentAudio = null;

document.addEventListener('play', function (e) {
    // chỉ xử lý khi target là thẻ <audio>
    if (e.target.tagName === 'AUDIO') {
        const audio = e.target;

        // 1. Pause và reset audio cũ (nếu có)
        if (currentAudio && currentAudio !== audio) {
            currentAudio.pause();
            currentAudio.currentTime = 0;
            // gỡ đánh dấu đã cộng lượt nghe (nếu có)
            $(currentAudio).removeData("counted");
        }
        currentAudio = audio;

        // 2. Cộng lượt nghe (chỉ lần đầu play của mỗi thẻ audio)
        const $audio = $(audio);
        if (!$audio.data("counted")) {
            // lấy songId từ button “yêu thích”
            const songId = $audio.closest(".card-body")
                .find(".add-to-fav")
                .data("songid");
            $.post("update_play_count.php", { song_id: songId })
                .done(function () {
                    $audio.data("counted", true);
                    // cập nhật hiển thị lượt nghe
                    const $countSpan = $audio.closest(".card-body")
                        .find(".listen-count .count-number");
                    let current = parseInt($countSpan.text(), 10) || 0;
                    $countSpan.text(current + 1);
                });
        }
    }
}, true);

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
            // success: function (response) {
            //     $("#show-songs").html(response);
            //     $('html, body').scrollTop(0);
            // },
            success: function (response) {
                $("#song-list").html(response.songs);
                $("#pagination").html(response.pagination);
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
