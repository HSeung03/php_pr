<?php
#데이터베이스 직접 연결
$conn = new mysqli("mysql", "root", "12345678", "board_login");

if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

#폼에서 전송된 데이터 받기
$name = $_POST['name'] ?? '';
$password = $_POST['password'] ?? '';
$subject = $_POST['subject'] ?? '';
$content = $_POST['content'] ?? '';

# 유효성 검사
if ($name === '' || $password === '' || $subject === '' || $content === '') {
    die("모든 항목을 입력해주세요.");
}

#INSERT 처리
$sql = "INSERT INTO board (name, password, subject, content) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $password, $subject, $content);

if ($stmt->execute()) {
    header("Location: ../frontend/index.php");
    exit();
} else {
    echo "글 등록 실패: " . $conn->error;
}

$conn->close();
?>
