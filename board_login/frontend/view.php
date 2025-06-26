<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$id = $_GET['id'] ?? '';
// URL의 GET 요청에서 'reply_to' 파라미터(답글을 달 댓글 ID)를 가져옵니다. 값이 없으면 null을 사용합니다.
$reply_to = $_GET['reply_to'] ?? null;

if (!$id) {
    echo "ID가 지정되어 있지 않습니다.";
    exit;
}

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

// 댓글 데이터를 저장할 빈 배열을 초기화
$comments_raw = [];
// 해당 게시글에 속한 모든 댓글을 등록일 기준으로 오름차순 정렬하여 가져오는 SQL 쿼리를 준비
$comments_sql = "SELECT * FROM comments WHERE board_id = ? ORDER BY regdate ASC";
$comments_stmt = $conn->prepare($comments_sql);
$comments_stmt->bind_param("i", $id);
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();

// 가져온 각 댓글 데이터를 반복 처리
while ($c = $comments_result->fetch_assoc()) {
    // 'parent_id'가 설정되어 있지 않다면(최상위 댓글이라면) null로 설정- 대댓글이 아닌 경우
    if (!isset($c['parent_id'])) $c['parent_id'] = null;
    // 각 댓글을 'id'를 키로 사용하여 $comments_raw 배열에 저장
    $comments_raw[$c['id']] = $c;
}
// 댓글 쿼리 준비 객체 닫기
$comments_stmt->close();
// 게시글 쿼리 준비 객체 닫기
$stmt->close();
// 데이터베이스 연결을 닫기
$conn->close();

// 댓글의 계층 구조를 구성하기 위한 빈 배열을 초기화
$tree = [];
// $comments_raw 배열의 각 댓글을 참조(&c)하여 반복 처리
foreach ($comments_raw as $cid => &$c) {
    // 각 댓글에 자식 댓글을 담을 'children' 배열을 추가
    $c['children'] = [];

    // 'parent_id'가 비어있지 않고, 해당 'parent_id'를 가진 댓글이 $comments_raw에 존재한다면(대댓글이라면)
    if (!empty($c['parent_id']) && isset($comments_raw[$c['parent_id']])) {
        // 부모 댓글의 'children' 배열에 현재 댓글을 추가 - 참조로추가하여 원본 배열에 반영
        $comments_raw[$c['parent_id']]['children'][] = &$c;
    } else {
        // 'parent_id'가 없거나 유효하지 않다면 최상위 댓글이므로 $tree 배열에 추가
        $tree[] = &$c;
    }
}
// foreach 루프에서 사용된 참조 변수 $c를 해제(잠재적인 버그 방지)
unset($c);

// 최상위 댓글($tree)들을 등록일기준으로 오름차순 정렬
usort($tree, function($a, $b) {
    return strtotime($a['regdate']) - strtotime($b['regdate']);
});

// 문자열 내의 줄바꿈 문자(\r\n, \r, \n)를 HTML의 <br> 태그로 변환하는 사용자 정의 함수
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

    <h2><?= htmlspecialchars($row['subject']) ?></h2>
    <p>작성자: <?= htmlspecialchars($row['name']) ?></p>
    <p>
        작성일: <?= htmlspecialchars($row['regdate']) ?>
        <?php
        // 게시글에 'updated_at' 컬럼이 존재하고, 등록일과 수정일이 다를 경우에만 수정일을 표시
        if (isset($row['updated_at']) && $row['updated_at'] != $row['regdate']):
        ?>
            <br>수정일: <?= htmlspecialchars($row['updated_at']) ?>
        <?php endif; ?>
    </p>
    <p><?= nl2br_custom(htmlspecialchars($row['content'])) ?></p>

    <form action="comment_password_check.php" method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
        <input type="hidden" name="type" value="post_change">
        <button type="submit">게시글 변경</button>
    </form>

    <hr> <h3>댓글</h3> <h4 id="comment_form_title">
        <?php if ($reply_to): // reply_to값이 있다면 -답글 작성 중이라면 ?>
            <?php
            // 답글을 달 댓글의 작성자 이름을 가져오기, 해당 ID의 댓글이 없으면 '알 수 없음'으로 표시
            $reply_author = $comments_raw[$reply_to]['author'] ?? '알 수 없음';
            echo '"' . htmlspecialchars($reply_author) . '" 님에게 답글 작성 중';
            ?>
        <?php else: // 새 댓글 작성 중이라면 ?>
            새 댓글 작성
        <?php endif; ?>
    </h4>

    <form action="../backend/comments_api.php" method="post">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="board_id" value="<?= htmlspecialchars($row['id']) ?>">
        <input type="hidden" name="parent_id" value="<?= htmlspecialchars($reply_to) ?>">
        <p>
            작성자: <input type="text" name="author" required> 비밀번호: <input type="password" name="password" required> </p>
        <p>
            <textarea name="content" placeholder="댓글 내용을 입력하세요." required></textarea> </p>
        <p>
            <button type="submit">댓글 등록</button> <?php if ($reply_to): // 답글 작성 중이라면 '취소' 버튼을 표시 ?>
                <button type="button" onclick="location.href='view.php?id=<?= htmlspecialchars($row['id']) ?>'">취소</button>
            <?php endif; ?>
        </p>
    </form>

    <hr> <?php
    // 댓글과 대댓글을 계층적으로 렌더링하기 위한 재귀 함수입니다.
    // $comment: 현재 렌더링할 댓글/대댓글 데이터 배열
    // $current_post_id: 현재 게시글의 ID
    // $depth: 댓글의 깊이 (0은 최상위 댓글, 1은 1단계 대댓글 등)
    // $reply_to_id: 현재 답글 작성 중인 대상 댓글의 ID (사용자 UI 목적)
    function render_comment_recursive($comment, $current_post_id, $depth = 0, $reply_to_id = null) {
        ?>
        <div>
            <p><small>작성자: <?= htmlspecialchars($comment['author']) ?></small></p>
            <p><?= nl2br_custom(htmlspecialchars($comment['content'])) ?></p>

            <div>
                <small>
                    작성일: <?= htmlspecialchars($comment['regdate']) ?>
                    <?php
                    if (isset($comment['updated_at']) && $comment['regdate'] != $comment['updated_at']):
                    ?>
                        (수정됨)
                    <?php endif; ?>
                    <form action="comment_password_check.php" method="post">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($comment['id']) ?>">
                        <input type="hidden" name="type" value="comment_change">
                        <input type="hidden" name="post_id" value="<?= htmlspecialchars($current_post_id) ?>">
                        <button type="submit">변경</button> </form>
                    <?php if ($depth === 0): // 최상위 댓글에만 '답글' 버튼을 표시합니다. (대댓글에는 답글 버튼이 없음) ?>
                        <form action="view.php" method="get">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($current_post_id) ?>">
                            <input type="hidden" name="reply_to" value="<?= htmlspecialchars($comment['id']) ?>">
                            <button type="submit">답글</button> </form>
                    <?php endif; ?>
                </small>
            </div>
        </div>
        <?php
        // 현재 댓글의 자식 댓글들(대댓글)에 대해 재귀적으로 이 함수를 다시 호출하여 렌더링
        // 이때 깊이($depth)를 1 증가시켜 다음 레벨의 들여쓰기를 준비
        foreach ($comment['children'] as $child) {
            render_comment_recursive($child, $current_post_id, $depth + 1, $reply_to_id);
        }
    }

    // $tree 배열이 비어있지 않다면 (댓글이 존재한다면)
    if (!empty($tree)):
        // 최상위 댓글들을 순회하며 재귀 함수를 호출하여 모든 댓글/대댓글을 렌더링
        // 최상위 댓글이므로 깊이는 0으로 시작
        foreach ($tree as $comment) {
            render_comment_recursive($comment, $id, 0, $reply_to);
        }
    else:
        // 게시글에 댓글이 없을 경우 메시지를 출력
        echo "<p>아직 댓글이 없습니다.</p>";
    endif;
    ?>

    <p><a href="index.php">← 게시판 목록으로</a></p> </body>
</html>