<?php
// 데이터베이스 연결
$conn = new mysqli("mysql", "root", "12345678", "board_login");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    header("Location: ../frontend/index.php?error=db_connection_failed");
    exit;
}

// 액션 분기
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get':
        header("Location: ../frontend/index.php?error=unsupported_get_action");
        break;

    case 'add':
        $board_id = $_POST['board_id'] ?? '';
        $parent_id = $_POST['parent_id'] ?? null;
        $author = $_POST['author'] ?? '';
        $password = $_POST['password'] ?? '';
        $content = $_POST['content'] ?? '';

        // 빈 문자열일 경우 null 처리
        $parent_id = ($parent_id === '' || $parent_id === null) ? null : (int)$parent_id;

        if (empty($board_id) || empty($author) || empty($password) || empty($content)) {
            header("Location: ../frontend/view.php?id=" . $board_id . "&error=missing_info");
            exit;
        }

        $sql = "INSERT INTO comments (board_id, parent_id, author, password, content) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // bind_param에서 null 허용을 위해 iisss로 처리하되, null은 set_null()처럼 따로 안 해도 됨
        $stmt->bind_param("iisss", $board_id, $parent_id, $author, $password, $content);

        if ($stmt->execute()) {
            header("Location: ../frontend/view.php?id=" . $board_id . "&status=comment_added");
            exit;
        } else {
            header("Location: ../frontend/view.php?id=" . $board_id . "&error=add_failed");
            exit;
        }

        $stmt->close();
        break;

    default:
        header("Location: ../frontend/index.php?error=unknown_action");
        break;
}

$conn->close();
?>
