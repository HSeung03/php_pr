<?php
// 데이터베이스 연결
$conn = new mysqli("mysql", "root", "12345678", "board_login");
$conn->set_charset("utf8mb4");

// POST로 전달된 값 받기
$comment_id = $_POST['comment_id'] ?? '';
$post_id = $_POST['post_id'] ?? '';

// 필수 값 검증
if (empty($comment_id) || empty($post_id)) {
    header("Location: ../frontend/view.php?id=" . $post_id . "&error=missing_info");
    exit;
}

// 댓글 삭제 쿼리
$delete_sql = "DELETE FROM comments WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $comment_id);

// 실행
if ($delete_stmt->execute()) {
    header("Location: ../frontend/view.php?id=" . $post_id . "&status=comment_deleted");
    exit;
} else {
    header("Location: ../frontend/view.php?id=" . $post_id . "&error=delete_failed");
    exit;
}

$delete_stmt->close();
$conn->close();
?>
