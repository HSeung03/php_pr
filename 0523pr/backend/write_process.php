<?php
$conn = new mysqli("mysql", "root", "12345678", "board_db");

// 입력값 정리 및 공백 제거
$name = trim($_POST['name']);
$subject = trim($_POST['subject']);
$content = trim($_POST['content']);
$regdate = date("Y-m-d H:i:s");

// 빈 값 방지
if ($name === '' || $subject === '' || $content === '') {
    echo "<script>alert('이름, 제목, 내용을 모두 입력해주세요.'); history.back();</script>";
    exit;
}

// prepare 문으로 SQL Injection 방지
$stmt = $conn->prepare("INSERT INTO board (name, subject, content, regdate) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $subject, $content, $regdate);

if ($stmt->execute()) {
    echo "<script>alert('등록되었습니다.'); location.href='../frontend/list.php';</script>";
} else {
    echo "<script>alert('등록 실패'); history.back();</script>";
}
?>
