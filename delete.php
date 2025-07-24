<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Remove image file if exists
    $result = $conn->query("SELECT image_path FROM messages WHERE id = $id");
    if ($row = $result->fetch_assoc()) {
        if (!empty($row['image_path']) && file_exists($row['image_path'])) {
            unlink($row['image_path']);
        }
    }

    // Delete record from DB
    $conn->query("DELETE FROM messages WHERE id = $id");
}

header("Location: index.php");
exit;
?>
