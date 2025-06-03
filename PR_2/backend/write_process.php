<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");

$name = $_POST['name'];
$subject = $_POST['subject'];
$content = $_POST['content'];
$password = $_POST['password'];
$regdate = date("Y-m-d H:i:s");

$sql = "INSERT INTO board (name, subject, content, password, regdate) 
        VALUES ('$name', '$subject', '$content', '$password', '$regdate')";
$conn->query($sql);

header("Location: ../frontend/index.php");
