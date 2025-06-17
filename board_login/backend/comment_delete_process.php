<?php
// 데이터베이스 연결
$conn = new mysqli("mysql", "root", "12345678", "board_login");
$conn->set_charset("utf8mb4");

// GET으로 전달된 값 가져오기 (comment_password_check.php에서 리다이렉트되므로 GET으로 받음)
$comment_id = $_GET['id'] ?? ''; // comment_password_check.php에서 'id'로 전달됨
$post_id = $_GET['post_id'] ?? ''; // 삭제 후 돌아갈 게시글 ID
$password = $_GET['password'] ?? ''; // comment_password_check.php에서 검증된 비밀번호

// 필요한 정보가 누락된 경우
if (empty($comment_id) || empty($post_id) || empty($password)) {
    // 오류 페이지 또는 이전 페이지로 리다이렉트
    header("Location: ../frontend/view.php?id=" . $post_id . "&error=missing_info");
    exit;
}

// 비밀번호 재확인 (보안을 위해 한 번 더 확인)
$check_sql = "SELECT password FROM comments WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $comment_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    $comment_row = $check_result->fetch_assoc();
    // 비밀번호 일치 여부 확인
    if ($comment_row['password'] === $password) {
        $delete_sql = "DELETE FROM comments WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $comment_id);

        if ($delete_stmt->execute()) {
            // 성공 시 frontend/view.php로 리다이렉트 (알림 메시지 없음)
            header("Location: ../frontend/view.php?id=" . $post_id . "&status=comment_deleted");
            exit;
        } else {
            // 삭제 실패 시 리다이렉트
            header("Location: ../frontend/view.php?id=" . $post_id . "&error=delete_failed");
            exit;
        }
        $delete_stmt->close();
    } else {
        // 비밀번호 불일치 시 리다이렉트
        header("Location: ../frontend/view.php?id=" . $post_id . "&error=password_mismatch");
        exit;
    }
} else {
    // 댓글을 찾을 수 없을 때 리다이렉트
    header("Location: ../frontend/view.php?id=" . $post_id . "&error=comment_not_found");
    exit;
}
$check_stmt->close();
$conn->close();
?>