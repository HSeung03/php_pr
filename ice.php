<?php
// 임시로 id값 받아오기 (실제 환경에선 DB로 불러옵니다)
$id = $_GET['id'] ?? 0;

// 가짜 데이터 예시 (DB 없이 테스트용)
if ($id == 1) {
    $title = "아이스크림";
    $author = "김효찬";
    $date = "2025-05-20 11:50:30";
    $content = "맛있다";
} else {
    echo "존재하지 않는 글입니다.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시판 상세보기</title>
</head>
<body>
    <h3>게시판 > 상세보기</h3>

    <h4><?= $title ?></h4>
    <p><strong>작성자:</strong> <?= $author ?></p>
    <p><strong>작성일:</strong> <?= $date ?></p>
    <p><?= nl2br($content) ?></p>

    <table>
        <tr>
            <td>
                <!-- 수정 폼 -->
                <form action="0523pr4update.php" method="get">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit">수정</button>
                </form>
            </td>
            <td>
                <!-- 삭제 폼 -->
                <form action="0523pr4delete.php" method="get">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit">삭제</button>
                </form>
            </td>
        </tr>
    </table>

    <br>
    <a href="0523pr4.html">게시판 목록으로 돌아가기</a>
</body>
</html>
