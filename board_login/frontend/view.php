<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");
if ($conn->connect_error) die("DB 연결 실패: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

$id = $_GET['id'] ?? '';
$reply_to = $_GET['reply_to'] ?? null;
if (!$id) exit("ID가 지정되어 있지 않습니다.");

$sql = "SELECT * FROM board WHERE id = ?";
# SQL 쿼리 실행을 위한 prepared statement 생성 , 바인딩
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
# 쿼리 결과 가져오기
$result = $stmt->get_result();
if (!$result || $result->num_rows < 1) exit("포스트를 찾을 수 없습니다.");
# 결과 행을 연관 배열로 가져오기
$row = $result->fetch_assoc();

# 댓글 데이터가 들어갈 배열 초기화
$comments_raw = [];
# 오름차순으로 정렬
$comments_sql = "SELECT * FROM comments WHERE board_id = ? ORDER BY regdate ASC";
$comments_stmt = $conn->prepare($comments_sql);
$comments_stmt->bind_param("i", $id);
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();

# 모든 댓글 데이터를 반복하면서 변수에 저장 
while ($c = $comments_result->fetch_assoc()) {
    # 'parent_id'가 없으면 null로 반환
    if (!isset($c['parent_id'])) $c['parent_id'] = null;
    # ID로 댓글 저장 
    $comments_raw[$c['id']] = $c;
}

$comments_stmt->close();
$stmt->close();
$conn->close();

#일반 댓글이 들어갈 배열 초기화
$tree = [];
# 각 댓글을 순회하면서 키와 값을 분리
foreach ($comments_raw as $cid => &$c) {
    #대댓글은 데이터베이스에 없어서 임의로 배열 설정 
    $c['children'] = [];
    # parent_id가 있을 경우 parent_id[childer]으로 참조 설정
    if (!empty($c['parent_id']) && isset($comments_raw[$c['parent_id']])) {
        $comments_raw[$c['parent_id']]['children'][] = &$c;
    } else {
        # parent_id가 없으면 tree에 저장 
        $tree[] = &$c;
    }
}
# 메모리 누수 방지로 참조 해제
unset($c);

# 등록일이  빠른 순으로 정렬 날짜 - 날짜
usort($tree, function($a, $b) {
    return strtotime($a['regdate']) - strtotime($b['regdate']);
});

# 배열안의 문자를 <br>줄바꿈으로 반환 
function nl2br_custom($str) {
    return str_replace(["\r\n", "\r", "\n"], "<br>", $str);
}
?>


<h1>게시판 > 상세보기</h1>
<h2><?= $row['subject'] ?></h2>
<p>작성자: <?= $row['name'] ?></p>
<p>작성일: <?= $row['regdate'] ?></p>
<p><?= nl2br_custom($row['content']) ?></p>

<form action="comment_password_check.php" method="post">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    <input type="hidden" name="type" value="post_change">
    <button type="submit">게시글 변경</button>
</form>

<h3>댓글</h3>
<h4>
    <?php # 답글 작성 중인 경우
    if ($reply_to): ?>
        <?php # 답글 대상 작성자 이름 가져오기
        $reply_author = $comments_raw[$reply_to]['author'] ?? '알 수 없음'; ?>
        "<?= $reply_author ?>" 님에게 답글 작성 중
    <?php # 새 댓글 작성 중인 경우
    else: ?>
        새 댓글 작성
    <?php endif; ?>
</h4>


<form action="../backend/comments_api.php" method="post">
    <input type="hidden" name="board_id" value="<?= $row['id'] ?>">
    <input type="hidden" name="parent_id" value="<?= $reply_to ?>">
    <p>작성자: <input type="text" name="author" required> 비밀번호: <input type="password" name="password" required></p>
    <p><textarea name="content" required></textarea></p>
    <p>
        <button type="submit">댓글 등록</button>
        <?php # 답글 작성 중인 경우 취소 버튼 표시
        if ($reply_to): ?>
            <button type="button" onclick="location.href='view.php?id=<?= $row['id'] ?>'">취소</button>
        <?php endif; ?>
    </p>
</form>

<?php
# 댓글이 없을때 까지 반복하는 함수 
function render_comment_recursive($comment, $current_post_id, $depth = 0) {
    # 들여쓰기 문자열
    $indent = str_repeat("&nbsp;", $depth * 4);

    # 댓글 컨테이너 시작
    echo "<div>";
    # 작성자 정보 표시 (들여쓰기 적용)
    echo "<p><small>{$indent}작성자: {$comment['author']}</small></p>";
    # 댓글 내용 표시 (들여쓰기 및 개행 문자 변환 적용)
    echo "<p>{$indent}" . nl2br_custom($comment['content']) . "</p>";


    echo "<div><small>{$indent}작성일: {$comment['regdate']}";

    # 댓글 변경 폼 시작
    echo "<form action='comment_password_check.php' method='post'>";
    # 댓글 ID를 숨겨진 필드로 전달
    echo "<input type='hidden' name='id' value='{$comment['id']}'>";
    # 요청 타입을 'comment_change'로 설정
    echo "<input type='hidden' name='type' value='comment_change'>";
    # 현재 게시글 ID를 숨겨진 필드로 전달
    echo "<input type='hidden' name='post_id' value='{$current_post_id}'>";
    echo "{$indent}<button type='submit'>변경</button>";
    echo "</form>";

    # 답글 버튼 (최상위 댓글만)
    if ($depth === 0) {
        echo "<form action='view.php' method='get'>";
        echo "<input type='hidden' name='id' value='{$current_post_id}'>";
        echo "<input type='hidden' name='reply_to' value='{$comment['id']}'>";
        echo "{$indent}<button type='submit'>답글</button>";
        echo "</form>";
    }

    echo "</small></div>";
    echo "</div>";

    # 현재 댓글의 자식 댓글들을 재귀적으로 렌더링
    foreach ($comment['children'] as $child) {
        render_comment_recursive($child, $current_post_id, $depth + 1);
    }
}

# Tree가 있을경우에 함수 호출
if (!empty($tree)) {
    # 일반댓글을 순회하면서 호출 
    foreach ($tree as $comment) {
        render_comment_recursive($comment, $id);
    }
} else {
    # 댓글이 없으면 메시지 출력
    echo "<p>아직 댓글이 없습니다.</p>";
}
?>

<p><a href="index.php">← 게시판 목록으로</a></p>