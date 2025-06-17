<?php
// DB 연결
$conn = new mysqli("mysql", "root", "12345678", "board_login");
$conn->set_charset("utf8mb4");

$id = $_GET['id'] ?? '';
$reply_to = $_GET['reply_to'] ?? null;

if (!$id) {
    echo "ID가 지정되어 있지 않습니다.";
    exit;
}

// 게시글 가져오기
$sql = "SELECT * FROM board WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "포스트를 찾을 수 없습니다.";
    exit;
}

// 댓글 전체 가져오기
$comments_raw = [];
$comments_sql = "SELECT * FROM comments WHERE board_id = ? ORDER BY regdate ASC";
$comments_stmt = $conn->prepare($comments_sql);
$comments_stmt->bind_param("i", $id);
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();

while ($c = $comments_result->fetch_assoc()) {
    if (!isset($c['parent_id'])) $c['parent_id'] = null;
    $comments_raw[$c['id']] = $c;
}
$comments_stmt->close();
$stmt->close();
$conn->close();

// 댓글 계층 구조 구성
$tree = [];
foreach ($comments_raw as $cid => &$c) {
    $c['children'] = [];

    if (!empty($c['parent_id']) && isset($comments_raw[$c['parent_id']])) {
        $comments_raw[$c['parent_id']]['children'][] = &$c;
    } else {
        $tree[] = &$c;
    }
}
unset($c);

// 정렬
usort($tree, function($a, $b) {
    return strtotime($a['regdate']) - strtotime($b['regdate']);
});

// 줄바꿈 처리
function nl2br_custom($str) {
    return str_replace(["\r\n", "\r", "\n"], "<br>", $str);
}
?>

<!DOCTYPE HTML>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시판 | 상세보기</title>
</head>
<body>
    <h1>게시판 &gt; 상세보기</h1>

    <h2><?= $row['subject'] ?></h2>
    <p>작성자: <?= $row['name'] ?></p>
    <p>작성일: <?= $row['regdate'] ?></p>
    <p><?= nl2br_custom($row['content']) ?></p>

    <form action="comment_password_check.php" method="post">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <input type="hidden" name="type" value="post_change">
        <button type="submit">게시글 변경</button>
    </form>

    <hr>

    <h3>댓글</h3>

    <h4 id="comment_form_title">
        <?php if ($reply_to): ?>
            <?php
            $reply_author = $comments_raw[$reply_to]['author'] ?? '알 수 없음';
            echo '"' . $reply_author . '" 님에게 답글 작성 중';
            ?>
        <?php else: ?>
            새 댓글 작성
        <?php endif; ?>
    </h4>

    <form action="../backend/comments_api.php" method="post">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="board_id" value="<?= $row['id'] ?>">
        <input type="hidden" name="parent_id" value="<?= $reply_to ?>">
        <p>
            작성자: <input type="text" name="author" required>
            비밀번호: <input type="password" name="password" required>
        </p>
        <p>
            <textarea name="content" placeholder="댓글 내용을 입력하세요." required></textarea>
        </p>
        <p>
            <button type="submit">댓글 등록</button>
            <?php if ($reply_to): ?>
                <button type="button" onclick="location.href='view.php?id=<?= $row['id'] ?>'">취소</button>
            <?php endif; ?>
        </p>
    </form>

    <hr>

    <?php
    function render_comment_recursive($comment, $current_post_id, $depth = 0, $reply_to_id = null) {
        $indent = $depth * 30;
        ?>
        <div style="margin-left:<?= $indent ?>px; border-bottom:1px solid #ccc; padding-bottom:10px; margin-bottom:10px;">
            <p><small>작성자: <?= $comment['author'] ?></small></p>
            <p><?= nl2br_custom($comment['content']) ?></p>

            <div>
                <small>
                    작성일: <?= $comment['regdate'] ?>
                    <?php if ($comment['regdate'] != $comment['updated_at']): ?>
                        (수정됨)
                    <?php endif; ?>
                    <form action="comment_password_check.php" method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $comment['id'] ?>">
                        <input type="hidden" name="type" value="comment_change">
                        <input type="hidden" name="post_id" value="<?= $current_post_id ?>">
                        <button type="submit">변경</button>
                    </form>
                    <?php if ($depth === 0): ?>
                        <form action="view.php" method="get" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $current_post_id ?>">
                            <input type="hidden" name="reply_to" value="<?= $comment['id'] ?>">
                            <button type="submit">답글</button>
                        </form>
                    <?php endif; ?>
                </small>
            </div>
        </div>
        <?php

        foreach ($comment['children'] as $child) {
            render_comment_recursive($child, $current_post_id, $depth + 1, $reply_to_id);
        }
    }

    if (!empty($tree)):
        foreach ($tree as $comment) {
            render_comment_recursive($comment, $id, 0, $reply_to);
        }
    else:
        echo "<p>아직 댓글이 없습니다.</p>";
    endif;
    ?>

    <p><a href="index.php">← 게시판 목록으로</a></p>
</body>
</html>
