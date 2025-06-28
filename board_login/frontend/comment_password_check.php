<?php
// 데이터베이스 연결
$conn = new mysqli("mysql", "root", "12345678", "board_login");
$conn->set_charset("utf8mb4");

// POST로 전달된 값 가져오기
$id = $_POST['id'] ?? ''; // 게시글 ID 또는 댓글 ID
$type = $_POST['type'] ?? ''; // 'post', 'comment_edit', 'post_change', 'comment_change' (삭제 관련은 제거)
$post_id = $_POST['post_id'] ?? ''; // 댓글의 경우, 해당 게시글 ID (게시글의 경우 비어있을 수 있음)

$message = '';
$redirect_url = '';

if (empty($id) || empty($type)) {
    header("Location: index.php?error=access_denied_missing_info");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $input_password = $_POST['password'];

    $table = '';
    $id_column = '';
    $success_redirect_base = '';

    // 게시글 또는 댓글 테이블 및 리다이렉트 경로 결정
    if ($type === 'post' || $type === 'post_change') {
        $table = 'board';
        $id_column = 'id';
        $success_redirect_base = 'edit.php';
    } elseif ($type === 'comment_edit' || $type === 'comment_change') {
        $table = 'comments';
        $id_column = 'id';
        $success_redirect_base = 'comment_edit.php';
    } else {
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

        if ($input_password === $stored_password) {
            $redirect_url = $success_redirect_base . "?id=" . $id;

            if (strpos($type, 'comment') !== false) {
                $redirect_url .= "&post_id=" . $post_id;
            }

            header("Location: " . $redirect_url);
            exit;
        } else {
            $message = "비밀번호가 일치하지 않습니다.";
        }
    } else {
        $message = "해당하는 항목을 찾을 수 없습니다.";
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
    <p><a href="view.php?id=<?= $post_id ? $post_id : $id ?>">게시글로 돌아가기</a></p>
    <?php if ($message): ?>
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
