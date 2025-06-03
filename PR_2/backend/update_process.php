<?php
// ✅ 데이터베이스 연결
$conn = new mysqli("mysql", "root", "12345678", "board_login");

if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// 📥 폼에서 전달된 데이터 받기
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$name = $_POST['name'] ?? '';
$subject = $_POST['subject'] ?? '';
$content = $_POST['content'] ?? '';

// ❗유효성 검사
if ($id === 0 || $name === '' || $subject === '' || $content === '') {
    die("모든 항목을 입력해야 합니다.");
}

// 🔄 SQL 업데이트 처리 (Prepared Statement 사용)
$sql = "UPDATE board SET name = ?, subject = ?, content = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $name, $subject, $content, $id);

// ✅ 실행
if ($stmt->execute()) {
    header("Location: ../frontend/index.php");
    exit();
} else {
    echo "수정 실패: " . $conn->error;
}

$conn->close();
?>
