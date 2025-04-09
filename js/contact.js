$(document).ready(function () {
    $('#contactForm').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: 'send_mail.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: {
                            popup: 'swal2-spotify'
                        }
                    });
                    $('#contactForm')[0].reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        customClass: {
                            popup: 'swal2-spotify'
                        }
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred. Please try again later.',
                    customClass: {
                        popup: 'swal2-spotify'
                    }
                });
            }
        });
    });
    $(".scroll ").click(function (event) {
        event.preventDefault();

        $('html,body').animate({
            scrollTop: $(this.hash).offset().top
        }, 1000);
    });
});