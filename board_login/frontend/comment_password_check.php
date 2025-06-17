<?php
// 데이터베이스 연결
$conn = new mysqli("mysql", "root", "12345678", "board_login");
$conn->set_charset("utf8mb4");

// POST로 전달된 값 가져오기
$id = $_POST['id'] ?? ''; // 게시글 ID 또는 댓글 ID
$type = $_POST['type'] ?? ''; // 'post', 'comment_edit', 'comment_delete', 'post_change', 'comment_change' (추가된 타입 포함)
$post_id = $_POST['post_id'] ?? ''; // 댓글의 경우, 해당 게시글 ID (게시글의 경우 비어있을 수 있음)

$message = '';
$redirect_url = '';

if (empty($id) || empty($type)) {
    // 필수 정보 누락 시 특정 페이지로 리다이렉트
    header("Location: index.php?error=access_denied_missing_info");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $input_password = $_POST['password'];

    $table = '';
    $id_column = '';
    $success_redirect_base = ''; // 비밀번호 일치 시 리다이렉트할 기본 경로

    // 게시글 또는 댓글 테이블 및 리다이렉트 경로 결정
    if ($type === 'post' || $type === 'post_change') { // 'post_change' 타입도 동일하게 처리 (게시글 수정/삭제)
        $table = 'board';
        $id_column = 'id';
        $success_redirect_base = 'edit.php'; // 게시글 수정 페이지
    } elseif ($type === 'comment_edit' || $type === 'comment_change') { // 'comment_change' 타입도 동일하게 처리 (댓글 수정)
        $table = 'comments';
        $id_column = 'id';
        $success_redirect_base = 'comment_edit.php'; // 댓글 수정 페이지
    } elseif ($type === 'comment_delete') {
        $table = 'comments';
        $id_column = 'id';
        // 댓글 삭제 처리는 backend/comment_delete_process.php로 변경
        $success_redirect_base = '../backend/comment_delete_process.php';
    } else {
        // 유효하지 않은 type일 경우
        header("Location: index.php?error=access_denied_invalid_type");
        exit;
    }

    $sql = "SELECT password FROM " . $table . " WHERE " . $id_column . " = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];

        // 비밀번호 확인 (실제 서비스에서는 password_verify() 사용)
        if ($input_password === $stored_password) {
            // 비밀번호 일치: 해당 수정/삭제 페이지로 리다이렉트
            $redirect_url = $success_redirect_base . "?id=" . $id;

            // 댓글 관련 작업인 경우 post_id도 함께 전달
            if (strpos($type, 'comment') !== false) { // 타입에 'comment'가 포함된 경우
                $redirect_url .= "&post_id=" . $post_id;
                // 댓글 삭제 시 비밀번호도 함께 넘김 (GET 방식으로 전달)
                if ($type === 'comment_delete') {
                     $redirect_url .= "&password=" . urlencode($input_password);
                }
            }
            header("Location: " . $redirect_url);
            exit;
        } else {
            // 비밀번호 불일치 시
            $message = "비밀번호가 일치하지 않습니다.";
            // 사용자에게 다시 입력받기 위해 현재 페이지의 폼을 다시 표시 (메시지와 함께)
        }
    } else {
        // 항목을 찾을 수 없을 때
        $message = "해당하는 항목을 찾을 수 없습니다.";
        // 사용자에게 다시 입력받기 위해 현재 페이지의 폼을 다시 표시 (메시지와 함께)
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE HTML>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>비밀번호 확인</title>
</head>
<body>
    <h1>비밀번호를 입력해주세요.</h1>
    <p><a href="view.php?id=<?= $post_id ? $post_id : $id ?>">게시글로 돌아가기</a></p> <?php if ($message): ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php endif; ?>
    <form action="comment_password_check.php" method="post">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="type" value="<?= $type ?>">
        <input type="hidden" name="post_id" value="<?= $post_id ?>">
        <p>비밀번호: <input type="password" name="password" required></p>
        <p><button type="submit">확인</button></p>
    </form>
</body>
</html>