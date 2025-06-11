<?php
#데이터베이스 직접 연결
$conn = new mysqli("mysql", "root", "12345678", "board_login");

if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

#있으면 대입 없으면 빈칸 처리 
$name = $_POST['name'] ?? '';
$password = $_POST['password'] ?? '';
$subject = $_POST['subject'] ?? '';
$content = $_POST['content'] ?? '';

#빈칸일 경우 코드 종료
if ($name === '' || $password === '' || $subject === '' || $content === '') {
    die("모든 항목을 입력해주세요.");
}

#새로운 행 value는 입력한 값이 실제로 채워지는 공간
$sql = "INSERT INTO board (name, password, subject, content) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
#bind = 연결 - 자료형까지 지정 
$stmt->bind_param("ssss", $name, $password, $subject, $content);

if ($stmt->execute()) {
    header("Location: ../frontend/index.php");
    exit();
} else {
    echo "글 등록 실패: " . $conn->error;
}

$conn->close();
?>
