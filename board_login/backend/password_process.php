<?php
#DB 연결
$conn = new mysqli("mysql", "root", "12345678", "board_login");

if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

#POST 데이터 수신
$id = (int)($_POST['id'] ?? 0);
$password = $_POST['password'] ?? '';

if ($id === 0 || $password === '') {
    die("잘못된 접근입니다.");
}

#해당 id의 비밀번호를 데이터베이스로 부터 불러옴  
$stmt = $conn->prepare("SELECT password FROM board WHERE id = ?");
#보안성을 위해 정수로 연결 
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("글을 찾을 수 없습니다.");
}

$row = $result->fetch_assoc();

#비밀번호 비교
if ($row['password'] !== $password) {
    echo "<h3>비밀번호가 일치하지 않습니다.</h3>";
    echo "<p><a href='../frontend/password_check.php'>← 다시 시도하기</a></p>";
    exit;
}

header("Location: ../frontend/edit.php?id=$id");
exit;
?>
