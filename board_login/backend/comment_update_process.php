<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");
$conn->set_charset("utf8mb4");
#필수 값 불러오기 
$comment_id = $_POST['comment_id'] ?? '';
$post_id = $_POST['post_id'] ?? '';
$author = $_POST['author'] ?? '';
$content = $_POST['content'] ?? '';
#id, post_id,사용자, 댓글 중 하나라도 없으면 종료
if (!$comment_id || !$post_id || !$author || !$content) {
    header("Location: ../frontend/view.php?id=$post_id&error=missing_info");
    exit;
}

#update문으로 
$update_sql = "UPDATE comments SET author = ?, content = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("ssi", $author, $content, $comment_id);

if ($update_stmt->execute()) {
    $update_stmt->close();
    header("Location: ../frontend/view.php?id=$post_id&status=comment_updated");
    exit;
} else {
    $update_stmt->close();
    header("Location: ../frontend/view.php?id=$post_id&error=update_failed");
    exit;
}

$conn->close();
?>
