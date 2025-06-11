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

#Get받은 ID(WHERE조건)의 데이터를 UPDATE
#Set - 해당 변수들을 플레이스홀더의 값으로 SET(수정)
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
