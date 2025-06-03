<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");

$id = $_POST['id'];
$password = $_POST['password'];

$result = $conn->query("SELECT * FROM board WHERE id = $id");
$row = $result->fetch_assoc();

if ($row['password'] !== $password) {
    echo "<script>alert('비밀번호 불일치'); history.back();</script>";
    exit;
}

$name = $_POST['name'];
$subject = $_POST['subject'];
$content = $_POST['content'];

$conn->query("UPDATE board SET name='$name', subject='$subject', content='$content' WHERE id = $id");
header("Location: ../frontend/view.php?id=$id");
