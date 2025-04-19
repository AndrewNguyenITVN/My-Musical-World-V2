<?php
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['song_id'])) {
    $song_id = intval($_POST['song_id']);

    $stmt = $conn->prepare("UPDATE songs SET play_count = play_count + 1 WHERE song_id = ?");

    $stmt->bind_param("i", $song_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    $stmt->close();
}
?>