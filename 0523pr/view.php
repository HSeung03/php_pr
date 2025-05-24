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
    echo "<script>alert('게시글이 존재하지 않습니다.'); history.back();</script>";
    exit;
}

$row = $result->fetch_assoc();
?>

<h3>게시판 > 상세보기</h3>

<h2><?= htmlspecialchars($row['subject']) ?></h2>
<p><strong>작성자:</strong> <?= htmlspecialchars($row['name']) ?></p>
<p><strong>작성일:</strong> <?= $row['regdate'] ?></p>
<p><?= nl2br(htmlspecialchars($row['content'])) ?></p>

<!-- 수정 버튼 -->
<form action="edit.php" method="get" style="display:inline;">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    <button type="submit">수정</button>
</form>

<!-- 삭제 버튼 -->
<form action="delete_process.php" method="post" style="display:inline;" onsubmit="return confirm('정말 삭제하시겠습니까?');">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    <button type="submit">삭제</button>
</form>

<br><br>
<a href="list.php">게시판 목록으로 돌아가기</a>
