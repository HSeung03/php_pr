<?php
$id = $_GET['id'] ?? 0;

// 실제로는 DB에서 $id 기준으로 글 데이터를 가져와야 합니다
// 예제: 글 ID가 1일 때 고정된 값 사용
if ($id == 1) {
    $author = "김효찬";
    $title = "아이스크림";
    $content = "맛있다";
} else {
    echo "수정할 글이 없습니다.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시글 수정</title>
</head>
<body>
    <h3>게시판 > 게시글 수정</h3>

    <form action="edit_process.php" method="post">
        <input type="hidden" name="id" value="<?= $id ?>">

        이름: <input type="text" name="author" value="<?= $author ?>"><br><br>
        제목: <input type="text" name="title" value="<?= $title ?>"><br><br>
        내용:<br>
        <textarea name="content" rows="5" cols="40"><?= $content ?></textarea><br><br>

        <button type="submit">수정</button>
        <a href="view.php?id=<?= $id ?>"><button type="button">취소</button></a>
    </form>
</body>
</html>
