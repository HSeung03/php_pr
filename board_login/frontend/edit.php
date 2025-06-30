<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
$id = (int)($_GET['id'] ?? 0); #NULL 병합 연산

if ($id === 0) { #=== 연산자 사용 
    die("잘못된 접근입니다."); 
}

$sql = "SELECT * FROM board WHERE id = ?"; 
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

# 쿼리가 결과를 반환하지 않았거나 결과 세트가 비어 있는지(즉, 주어진 ID의 게시글을 찾을 수 없는지) 확인
if (!$result || $result->num_rows === 0) {
    // 게시글을 찾을 수 없다면, 스크립트 실행을 중지하고 오류 메시지를 출력
    die("게시글이 없습니다.");
}
# 찾은 게시글을 연관 배열 형태로 가져옴
$row = $result->fetch_assoc();
?>

<h2>변경</h2>
<form method="post" action="../backend/update_process.php">
    <input type="hidden" name="id" value="<?= $id ?>">
    <p>이름: <input type="text" name="name" value="<?= $row['name'] ?>"></p>
    <p>제목: <input type="text" name="subject" value="<?= $row['subject'] ?>"></p>
    <p>내용:<br>
    <textarea name="content" rows="10" cols="50"><?= $row['content'] ?></textarea></p>

    <button type="submit">수정</button>

    </form>

<form method="post" action="../backend/delete_process.php">
    <input type="hidden" name="id" value="<?= $id ?>">
    <button type="submit">삭제</button>
</form>