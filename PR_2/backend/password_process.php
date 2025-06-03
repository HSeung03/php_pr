<?php
// ✅ DB 연결
$conn = new mysqli("mysql", "root", "12345678", "board_login");

if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// ✅ POST 데이터 수신
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$password = $_POST['password'] ?? '';
$mode = $_POST['mode'] ?? '';

if ($id === 0 || $password === '' || $mode === '') {
    die("잘못된 접근입니다.");
}

// ✅ DB에서 비밀번호 조회
$stmt = $conn->prepare("SELECT password FROM board WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("글을 찾을 수 없습니다.");
}

$row = $result->fetch_assoc();

// ✅ 비밀번호 비교
if ($row['password'] !== $password) {
    echo "<h3>비밀번호가 일치하지 않습니다.</h3>";
    echo "<p><a href='../frontend/password_check.php'>← 다시 시도하기</a></p>";
    exit;
}

// ✅ 작업 분기
if ($mode === "edit") {
    header("Location: ../frontend/edit.php?id=$id");
    exit;
} elseif ($mode === "delete") {
    header("Location: delete_process.php?id=$id");
    exit;
} else {
    die("유효하지 않은 작업 요청입니다.");
}
?>
