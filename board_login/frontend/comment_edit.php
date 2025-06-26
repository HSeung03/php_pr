<?php
// 데이터베이스 연결
$conn = new mysqli("mysql", "root", "12345678", "board_login");
$conn->set_charset("utf8mb4");

// GET으로 전달된 댓글 ID와 게시글 ID 가져오기
$comment_id = $_GET['id'] ?? '';
$post_id = $_GET['post_id'] ?? ''; // 어떤 게시글의 댓글인지 확인

if (!$comment_id || !$post_id) {
    echo "잘못된 접근입니다.";
    exit;
}

// 댓글 정보 가져오기
$sql = "SELECT * FROM comments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $comment = $result->fetch_assoc();
} else {
    echo "댓글을 찾을 수 없습니다.";
    exit;
}

$stmt->close();
$conn->close();

// 줄바꿈 문자를 <br> 태그로 변환하는 헬퍼 함수 (출력 시 사용)
function nl2br_custom($str) {
    return str_replace(["\r\n", "\r", "\n"], "<br>", $str);
}
?>

<!DOCTYPE HTML>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>댓글 수정</title>
</head>
<body>
    <h1>댓글 수정</h1>
    <form action="../backend/comment_update_process.php" method="post">
        <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
        <input type="hidden" name="post_id" value="<?= $post_id ?>">
        <p>작성자: <input type="text" name="author" value="<?= $comment['author'] ?>"></p>
        <p>비밀번호: <input type="password" name="password" placeholder="비밀번호 재확인"></p>
        <p>내용:<br><textarea name="content" rows="10" cols="50"><?= $comment['content'] ?></textarea></p>
        <p>
            <button type="submit">수정 완료</button>
            <button type="button" onclick="location.href='view.php?id=<?= $post_id ?>'">취소</button>
        </p>
    </form>
</body>
</html>