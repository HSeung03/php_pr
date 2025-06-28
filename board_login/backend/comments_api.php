<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    exit("DB 연결 실패");
}

#전달 받은 데이터 처리 없으면 빈칸 - parent_id는 없으면 null 
$board_id = $_POST['board_id'] ?? '';
$parent_id = $_POST['parent_id'] ?? null;
$author = $_POST['author'] ?? '';
$password = $_POST['password'] ?? '';
$content = $_POST['content'] ?? '';

#조건이 참이면 A 거짓이면 B 
$parent_id = ($parent_id === '' || $parent_id === null) ? null : (int)$parent_id;

#빈칸 확인 
if (empty($board_id) || empty($author) || empty($password) || empty($content)) {
    exit("필수 항목 누락");
}

#데이터베이스에 저장 INSERT 
$sql = "INSERT INTO comments (board_id, parent_id, author, password, content) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisss", $board_id, $parent_id, $author, $password, $content);

// 실행 및 리디렉션
if ($stmt->execute()) {
    header("Location: ../frontend/view.php?id=" . $board_id);
    exit;
} else {
    exit("댓글 등록 실패");
}

$stmt->close();
$conn->close();
?>
