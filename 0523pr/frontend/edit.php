<?php
$conn = new mysqli("mysql", "root", "12345678", "board_db");

$id = $_GET['id'] ?? '';
if ($id === '') {
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM board WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('해당 게시글이 존재하지 않습니다.'); history.back();</script>";
    exit;
}

$row = $result->fetch_assoc();
?>

<h3>게시글 수정</h3>
<form action="../backend/update_process.php" method="post">
  <input type="hidden" name="id" value="<?= $row['id'] ?>">
  이름: <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>"><br><br>
  제목: <input type="text" name="subject" value="<?= htmlspecialchars($row['subject']) ?>"><br><br>
  내용:<br>
  <textarea name="content" rows="10" cols="50"><?= htmlspecialchars($row['content']) ?></textarea><br><br>
  <button type="submit">수정</button>
</form>

<h3>게시글 삭제</h3>
<form action="../backend/delete_process.php" method="post" onsubmit="return confirm('정말 삭제하시겠습니까?');">
  <input type="hidden" name="id" value="<?= $row['id'] ?>">
  <button type="submit" style="color: red;">삭제</button>
</form>

<br>
<a href="list.php">목록으로</a>
