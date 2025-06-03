<?php
// DB 연결
$conn = new mysqli("mysql", "root", "12345678", "board_login");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// id 가져오기
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    die("잘못된 접근입니다.");
}

// 게시글 가져오기
$sql = "SELECT * FROM board WHERE id = $id";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    die("게시글을 찾을 수 없습니다.");
}

$row = $result->fetch_assoc();
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
