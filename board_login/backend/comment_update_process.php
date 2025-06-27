<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");
$conn->set_charset("utf8mb4");

$comment_id = $_POST['comment_id'] ?? '';
$post_id = $_POST['post_id'] ?? '';
$author = $_POST['author'] ?? '';
$password = $_POST['password'] ?? '';
$content = $_POST['content'] ?? '';

if (!$comment_id || !$post_id || !$author || !$password || !$content) {
    header("Location: ../frontend/view.php?id=$post_id&error=missing_info");
    exit;
}

$sql = "SELECT password FROM comments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if ($row['password'] === $password) {
        $update_sql = "UPDATE comments SET author = ?, content = ?, updated_at = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $author, $content, $comment_id);

        if ($update_stmt->execute()) {
            header("Location: ../frontend/view.php?id=$post_id&status=comment_updated");
            exit;
        } else {
            header("Location: ../frontend/view.php?id=$post_id&error=update_failed");
            exit;
        }
    } else {
        header("Location: ../frontend/view.php?id=$post_id&error=password_mismatch");
        exit;
    }
} else {
    header("Location: ../frontend/view.php?id=$post_id&error=comment_not_found");
    exit;
}

$stmt->close();
$conn->close();
