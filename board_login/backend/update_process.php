<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");

if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
#??연산자 값이 있으면 변수 저장 없으면 공백으로 
#미정의/NULL값으로 부터 에러를 방지 - 에러대신에 예측 가능한 ''을 대입
$id = (int)($_POST['id'] ?? 0);
$name = $_POST['name'] ?? '';
$subject = $_POST['subject'] ?? '';
$content = $_POST['content'] ?? '';

#빈칸일 경우 해당코드 종료 
if ($id === 0 || $name === '' || $subject === '' || $content === '') {
    die("모든 항목을 입력해야 합니다.");
}

#특정 id값의 데이터를 업데이트 하는 sql문 
$sql = "UPDATE board SET name = ?, subject = ?, content = ? WHERE id = ?";
$stmt = $conn->prepare($sql); 
$stmt->bind_param("sssi", $name, $subject, $content, $id);


if ($stmt->execute()) {
    header("Location: ../frontend/index.php");
    exit();
} else {
    echo "수정 실패: " . $conn->error;
}

$conn->close();
?>
