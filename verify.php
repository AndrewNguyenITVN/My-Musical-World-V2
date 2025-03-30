<?php
include('connection.php');
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Verify Account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="KEERTHANA KUTEERA LOGO-BLACK-01.png" type="image/png">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php
    if (isset($_GET['email_address']) && isset($_GET['activation_code'])) {
        $email_address = mysqli_real_escape_string($conn, $_GET['email_address']);
        $activation_code = mysqli_real_escape_string($conn, $_GET['activation_code']);

        $search = mysqli_query($conn, "SELECT email_address, activation_code, confirm_status, username FROM user WHERE email_address = '$email_address' AND activation_code = '$activation_code' AND confirm_status = '0'");
        if (mysqli_num_rows($search) > 0) {
            $row = mysqli_fetch_assoc($search);
            $username = $row['username'];
            mysqli_query($conn, "UPDATE user SET confirm_status = '1' WHERE email_address = '$email_address'");
            echo "<script>
            setTimeout(function () {
                Swal.fire({
                    title: 'Success',
                    icon: 'success',
                    text: 'Thank you $username, your account has been activated successfully!'
                }).then(() => {
                    window.location.href = 'index.php';
                });
            }, 500);
        </script>";
        } else {
            echo "<script>
            setTimeout(function () {
                Swal.fire('Oops...', 'The URL is either invalid or the account has already been activated.', 'error');
            }, 500);
        </script>";
        }
    } else {
        echo "<script>
        setTimeout(function () {
            Swal.fire('Oops...', 'Invalid approach. Please use the link that was sent to your email.', 'error');
        }, 500);
    </script>";
    }
    ?>

</body>

</html>