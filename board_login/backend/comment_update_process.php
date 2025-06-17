<?php
// 데이터베이스 연결
$conn = new mysqli("mysql", "root", "12345678", "board_login");
$conn->set_charset("utf8mb4");

// POST로 전달된 값 가져오기
$comment_id = $_POST['comment_id'] ?? '';
$post_id = $_POST['post_id'] ?? ''; // 수정 후 돌아갈 게시글 ID
$author = $_POST['author'] ?? '';
$password = $_POST['password'] ?? '';
$content = $_POST['content'] ?? '';

// 필수 입력 항목이 누락된 경우
if (empty($comment_id) || empty($post_id) || empty($author) || empty($password) || empty($content)) {
    // 오류 페이지 또는 이전 페이지로 리다이렉트
    header("Location: ../frontend/view.php?id=" . $post_id . "&error=missing_info");
    exit;
}

// 비밀번호 확인 및 댓글 업데이트
$check_sql = "SELECT password FROM comments WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $comment_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    $comment_row = $check_result->fetch_assoc();
    // 실제 서비스에서는 password_verify()를 사용하여 암호화된 비밀번호를 확인해야 합니다.
    if ($comment_row['password'] === $password) {
        $update_sql = "UPDATE comments SET author = ?, content = ?, updated_at = NOW() WHERE id = ?"; // updated_at 추가
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $author, $content, $comment_id);
        if ($update_stmt->execute()) {
            // 성공 시 frontend/view.php로 리다이렉트 (알림 메시지 없음)
            header("Location: ../frontend/view.php?id=" . $post_id . "&status=comment_updated");
            exit;
        } else {
            // 수정 실패 시 리다이렉트
            header("Location: ../frontend/view.php?id=" . $post_id . "&error=update_failed");
            exit;
        }
        $update_stmt->close();
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