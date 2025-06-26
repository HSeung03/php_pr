<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// 한 페이지에 표시할 게시글 수를 5개로 설정
$perpage = 5;
// URL파라미터를 정수로 가져옴 없으면 1을 기본값 
$page = (int)($_GET['page'] ?? 1);
// 페이지 번호가 1보다 작으면 1로 강제로 설정
if ($page < 1) $page = 1;

// 현재 페이지에서 게시글을 가져오기 시작할 OFFSET 건너뛸 게시글 계산
$start = ($page - 1) * $perpage;

// 'board' 테이블에서 게시글을 최신순으로 정렬, 현재 페이지에 해당하는 수-$perpage만큼 가져오는 것 
// OFFSET은 $start만큼의 게시글을 건너뛴 후 데이터를 가져오는 것 
$sql = "SELECT * FROM board ORDER BY id DESC LIMIT $perpage OFFSET $start";
// 위에서 정의한 SQL 쿼리를 실행
$result = $conn->query($sql);

// 'board' 테이블의 전체 게시글 수를 카운트 하는 쿼리 
$total_sql = "SELECT COUNT(*) AS total FROM board";
// 전체 게시글 수를 세는 쿼리를 실행합니다.
$total_result = $conn->query($total_sql);
// 쿼리 결과에서total값을 가져와 전체 게시글 수를 저장
$total_row = $total_result->fetch_assoc();
$total_posts = $total_row['total'];
// 전체 게시글 수와 페이지당 게시글 수를 이용하여 전체 페이지 수를 계산- 올림 처리
$total_pages = ceil($total_posts / $perpage);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시판 | 리스트</title>
</head>
<body>
    <h1>게시판 > 리스트</h1>

    <table border="1">
        <tr>
            <th>번호</th>
            <th>이름</th>
            <th>제목</th>
            <th>작성일</th>
        </tr>
        <?php
        // 게시글 번호를 계산하기 위한 변수를 초기화 (전체 게시글 수 - (현재 페이지 - 1) * 페이지당 게시글 수)
        $count = $total_posts - ($page - 1) * $perpage;
        // 쿼리 결과로 가져온 게시글이 1개 이상인지 확인
        if ($result->num_rows > 0) {
            // 가져온 게시글 각각에 대해 반복문을 실행
            while ($row = $result->fetch_assoc()) {
                // 테이블 행의 시작 태그를 출력.
                echo "<tr>";
                // 계산된 게시글 번호를 출력하고 1 감소
                echo "<td>" . $count-- . "</td>";
                // 게시글 작성자 이름을 출력 - HTML 이스케이프는 현재 적용되지 않음
                echo "<td>{$row['name']}</td>";
                // 게시글 제목을 출력하고, 클릭 시 해당 게시글의 상세보기 페이지view.php로 이동
                echo "<td><a href='view.php?id={$row['id']}'>{$row['subject']}</a></td>";
                // 게시글 작성일을 출력합니다.
                echo "<td>{$row['regdate']}</td>";
                // 테이블 행의 닫는 태그를 출력합니다.
                echo "</tr>";
            }
        } else {
            // 등록된 게시글이 없을 경우 - "등록된 글이 없습니다." 메시지를 출력
            // colspan='4'는 4개의 열에 걸쳐 표시함을 의미
            echo "<tr><td colspan='4'>등록된 글이 없습니다.</td></tr>";
        }
        ?>
    </table>

    <br><br>

    <!-- 페이지네이션 섹션의 시작 -->
    <?php
    // 페이지네이션에서 한 번에 표시할 페이지 번호의 범위를 5로 설정 예: 1~5, 6~10 등
    $pageRange = 5;
    // 현재 페이지가 속한 페이지 범위의 시작 페이지 번호를 계산
    $startPage = floor(($page - 1) / $pageRange) * $pageRange + 1;
    // 현재 페이지가 속한 페이지 범위의 끝 페이지 번호를 계산
    // 전체 페이지 수를 넘지 않도록 최소값을 선택
    $endPage = min($startPage + $pageRange - 1, $total_pages);

    // 전체 페이지가 1보다 많을 경우에만 페이지네이션 링크를 표시
    if ($total_pages > 1) {
        echo "<div>"; // 페이지네이션 링크들을 감싸는 div 시작

        // "<<" (처음으로) 링크를 출력합니다. 항상 1페이지로 이동
        echo "<a href='?page=1'>&laquo;</a> ";

        // "<" (이전 페이지) 링크를 출력, 현재 페이지에서 1을 뺀 값으로 이동하되, 최소 1페이지로 제한
        $prevPage = max(1, $page - 1);
        echo "<a href='?page=$prevPage'>&lt;</a> ";

        // 현재 페이지 범위에 해당하는 페이지 번호 링크들을 반복하여 출력
        for ($i = $startPage; $i <= $endPage; $i++) {
            // 현재 페이지인 경우, 강조하여 표시
            if ($i == $page) {
                echo "<strong>$i</strong> ";
            } else {
                // 현재 페이지가 아닌 경우, 해당 페이지로 이동하는 링크를 출력
                echo "<a href='?page=$i'>$i</a> ";
            }
        }

        // ">" (다음 페이지) 링크를 출력, 현재 페이지에서 1을 더한 값으로 이동하되, 최대 전체 페이지 수로 제한
        $nextPage = min($total_pages, $page + 1);
        echo "<a href='?page=$nextPage'>&gt;</a> ";

        // ">>" (마지막으로) 링크를 출력, 항상 마지막 페이지로 이동할 것 
        echo "<a href='?page=$total_pages'>&raquo;</a>";

        echo "</div>"; // 페이지네이션 링크들을 감싸는 div 닫기
    }
    ?>

    <br>
    <!-- 새 글을 작성할 수 있는 '글쓰기' 버튼을 만듭니다. 클릭 시 write.php 페이지로 이동합니다. -->
    <a href="write.php">
        <button type="button">글쓰기</button>
    </a>
</body>
</html>