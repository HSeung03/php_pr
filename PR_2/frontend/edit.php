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



<h2>변경</h2>
<form method="post" action="../backend/update_process.php">
    <input type="hidden" name="id" value="<?= $id ?>">

    <p>이름: <input type="text" name="name" value="<?= $row['name'] ?>"></p>
    <p>제목: <input type="text" name="subject" value="<?= $row['subject'] ?>"></p>
    <p>내용:<br>
    <textarea name="content" rows="10" cols="50"><?= $row['content'] ?></textarea></p>

    <!-- 수정 -->
    <button type="submit">수정</button>

    <!-- 삭제는 별도 form -->
</form>

<!-- 🔥 삭제 버튼은 별도 form으로 POST 전송 -->
<form method="post" action="../backend/delete_process.php" onsubmit="return confirm('정말 삭제하시겠습니까?');">
    <input type="hidden" name="id" value="<?= $id ?>">
    <button type="submit">삭제</button>
</form>
