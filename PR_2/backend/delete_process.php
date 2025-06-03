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

$conn->query("DELETE FROM board WHERE id = $id");
header("Location: ../frontend/index.php");
