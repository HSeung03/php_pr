<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");
if ($conn->connect_error) die("DB 연결 실패");
$conn->set_charset("utf8mb4");

#POST로 받은 ID
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id === 0) {
    die("잘못된 접근입니다.");
}

#게시글 삭제
$stmt = $conn->prepare("DELETE FROM board WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "삭제가 완료되었습니다. <a href='../frontend/index.php'>목록으로</a>";
} else {
    echo "삭제 실패: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
