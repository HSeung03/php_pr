<?php
# DB 연결
$conn = new mysqli("mysql", "root", "12345678", "board_login");
# 문자셋 설정
$conn->set_charset("utf8mb4");

# GET으로 comment_id와 post_id 가져오기
$comment_id = $_GET['id'] ?? '';
$post_id = $_GET['post_id'] ?? '';

# ID가 없으면 오류 메시지 출력 후 종료
if (!$comment_id || !$post_id) {
    echo "잘못된 접근입니다.";
    exit;
}

# 댓글 정보 조회 SQL
$sql = "SELECT * FROM comments WHERE id = ?";
# SQL 쿼리 준비
$stmt = $conn->prepare($sql);
# 댓글 ID 바인딩 (정수형)
$stmt->bind_param("i", $comment_id);
# 쿼리 실행
$stmt->execute();
# 결과 가져오기
$result = $stmt->get_result();

# 댓글이 존재하면 데이터를 가져옴
if ($result->num_rows > 0) {
    $comment = $result->fetch_assoc();
} else {
    # 댓글을 찾을 수 없으면 오류 메시지 출력 후 종료
    echo "댓글을 찾을 수 없습니다.";
    exit;
}

# prepared statement 닫기
$stmt->close();
# DB 연결 닫기
$conn->close();
?>

<!DOCTYPE HTML>
<html lang="ko">
<head>
    # UTF-8 문자 인코딩 설정
    <meta charset="UTF-8">
    # 페이지 제목 설정
    <title>댓글 수정</title>
</head>
<body>
    # 페이지 제목
    <h1>댓글 수정</h1>

    # 댓글 수정 폼 시작
    <form action="../backend/comment_update_process.php" method="post">
        # 댓글 ID를 숨겨서 전달
        <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
        # 게시글 ID를 숨겨서 전달
        <input type="hidden" name="post_id" value="<?= $post_id ?>">

        # 작성자 입력 필드 (기존 값 표시)
        <p>작성자: <input type="text" name="author" value="<?= $comment['author'] ?>" required></p>
        # 내용 텍스트 영역 (기존 값 표시)
        <p>내용:<br><textarea name="content" rows="10" cols="50" required><?= $comment['content'] ?></textarea></p>

        <p>
            # 수정 완료 버튼
            <button type="submit">수정 완료</button>
        </p>
    </form>

    # 댓글 삭제 폼 시작
    <form action="../backend/comment_delete_process.php" method="post" style="margin-top:10px;">
        # 댓글 ID를 숨겨서 전달
        <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
        # 게시글 ID를 숨겨서 전달
        <input type="hidden" name="post_id" value="<?= $post_id ?>">

        # 삭제 버튼
        <button type="submit">삭제</button>
    </form>

    <p>
        # 취소 버튼 (게시글 상세 페이지로 이동)
        <button type="button" onclick="location.href='view.php?id=<?= $post_id ?>'">취소</button>
    </p>
</body>
</html>