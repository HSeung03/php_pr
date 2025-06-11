<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");
if ($conn->connect_error) die("DB 연결 실패");
$conn->set_charset("utf8mb4");


$id = (int)($_POST['id'] ?? 0);

if ($id === 0) {
    die("잘못된 접근입니다.");
}

#board에 있는 특정 아이디의 데이터를 삭제
$stmt = $conn->prepare("DELETE FROM board WHERE id = ?");
#보안성 처리
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    #삭제 성공 시 바로 index.php로 
    header("Location: ../frontend/index.php");
    exit(); 
} else {
    echo "삭제 실패: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
