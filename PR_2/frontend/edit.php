<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");
$id = $_POST['id'];

$row = $conn->query("SELECT * FROM board WHERE id = $id")->fetch_assoc();
?>

<h3>수정하기</h3>
<form action="../backend/update_process.php" method="post">
    <input type="hidden" name="id" value="<?= $id ?>">
    이름: <input type="text" name="name" value="<?= $row['name'] ?>"><br>
    제목: <input type="text" name="subject" value="<?= $row['subject'] ?>"><br>
    내용: <textarea name="content"><?= $row['content'] ?></textarea><br>
    비밀번호: <input type="password" name="password"><br>
    <input type="submit" value="수정">
</form>
