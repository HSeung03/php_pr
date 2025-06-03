<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");
$id = $_GET['id'];

$row = $conn->query("SELECT * FROM board WHERE id = $id")->fetch_assoc();
?>

<h3>상세보기</h3>
제목: <?= $row['subject'] ?><br>
작성자: <?= $row['name'] ?><br>
작성일: <?= $row['regdate'] ?><br>
내용: <p><?= $row['content'] ?></p>

<a href="password_check.php?id=<?= $id ?>&mode=edit">수정</a>
<a href="password_check.php?id=<?= $id ?>&mode=delete">삭제</a>
<a href="index.php">목록</a>
