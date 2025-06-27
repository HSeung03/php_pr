<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");
if ($conn->connect_error) die("DB 연결 실패: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

$id = $_GET['id'] ?? '';
$reply_to = $_GET['reply_to'] ?? null;
if (!$id) exit("ID가 지정되어 있지 않습니다.");

$sql = "SELECT * FROM board WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows < 1) exit("포스트를 찾을 수 없습니다.");
$row = $result->fetch_assoc();

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

usort($tree, function($a, $b) {
    return strtotime($a['regdate']) - strtotime($b['regdate']);
});

function nl2br_custom($str) {
    return str_replace(["\r\n", "\r", "\n"], "<br>", $str);
}
?>

<h1>게시판 > 상세보기</h1>
<h2><?= $row['subject'] ?></h2>
<p>작성자: <?= $row['name'] ?></p>
<p>
    작성일: <?= $row['regdate'] ?>
    <?php if (isset($row['updated_at']) && $row['updated_at'] != $row['regdate']): ?>
        <br>수정일: <?= $row['updated_at'] ?>
    <?php endif; ?>
</p>
<p><?= nl2br_custom($row['content']) ?></p>

<form action="comment_password_check.php" method="post">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    <input type="hidden" name="type" value="post_change">
    <button type="submit">게시글 변경</button>
</form>

<h3>댓글</h3>
<h4>
    <?php if ($reply_to): ?>
        <?php $reply_author = $comments_raw[$reply_to]['author'] ?? '알 수 없음'; ?>
        
        "<?= $reply_author ?>" 님에게 답글 작성 중
    <?php else: ?>
        새 댓글 작성
    <?php endif; ?>
</h4>

<form action="../backend/comments_api.php" method="post">
    <input type="hidden" name="action" value="add">
    <input type="hidden" name="board_id" value="<?= $row['id'] ?>">
    <input type="hidden" name="parent_id" value="<?= $reply_to ?>">
    <p>작성자: <input type="text" name="author" required> 비밀번호: <input type="password" name="password" required></p>
    <p><textarea name="content" required></textarea></p>
    <p>
        <button type="submit">댓글 등록</button>
        <?php if ($reply_to): ?>
            <button type="button" onclick="location.href='view.php?id=<?= $row['id'] ?>'">취소</button>
        <?php endif; ?>
    </p>
</form>

<?php
function render_comment_recursive($comment, $current_post_id, $depth = 0) {
    echo "<div>";
    echo "<p><small>작성자: {$comment['author']}</small></p>";
    echo "<p>" . nl2br_custom($comment['content']) . "</p>";
    echo "<div><small>작성일: {$comment['regdate']}";
    if (isset($comment['updated_at']) && $comment['regdate'] != $comment['updated_at']) echo " (수정됨)";
    echo "<form action='comment_password_check.php' method='post'>";
    echo "<input type='hidden' name='id' value='{$comment['id']}'>";
    echo "<input type='hidden' name='type' value='comment_change'>";
    echo "<input type='hidden' name='post_id' value='{$current_post_id}'>";
    echo "<button type='submit'>변경</button></form>";
    if ($depth === 0) {
        echo "<form action='view.php' method='get'>";
        echo "<input type='hidden' name='id' value='{$current_post_id}'>";
        echo "<input type='hidden' name='reply_to' value='{$comment['id']}'>";
        echo "<button type='submit'>답글</button></form>";
    }
    echo "</small></div></div>";

    foreach ($comment['children'] as $child) {
        render_comment_recursive($child, $current_post_id, $depth + 1);
    }
}

if (!empty($tree)) {
    foreach ($tree as $comment) {
        render_comment_recursive($comment, $id);
    }
} else {
    echo "<p>아직 댓글이 없습니다.</p>";
}
?>

<p><a href="index.php">← 게시판 목록으로</a></p>
