<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");
$conn->set_charset("utf8mb4");

$perpage = 5;
$page = (int)($_GET['page'] ?? 1);
if ($page < 1) $page = 1;

$start = ($page - 1) * $perpage;

$sql = "SELECT * FROM board ORDER BY id DESC LIMIT $perpage OFFSET $start";
$result = $conn->query($sql);

$total_sql = "SELECT COUNT(*) AS total FROM board";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_posts = $total_row['total'];
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
        $count = $total_posts - ($page - 1) * $perpage;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $count-- . "</td>";
                echo "<td>{$row['name']}</td>";
                echo "<td><a href='view.php?id={$row['id']}'>{$row['subject']}</a></td>";
                echo "<td>{$row['regdate']}</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>등록된 글이 없습니다.</td></tr>";
        }
        ?>
    </table>

    <br><br>

    <!-- 페이지네이션 -->
    <?php
    $pageRange = 5;
    $startPage = floor(($page - 1) / $pageRange) * $pageRange + 1;
    $endPage = min($startPage + $pageRange - 1, $total_pages);


    if ($total_pages > 1) {
        echo "<div>";

    // << 처음
        echo "<a href='?page=1'>&laquo;</a> ";

    // < 이전
        $prevPage = max(1, $page - 1);
        echo "<a href='?page=$prevPage'>&lt;</a> ";

    // 페이지 번호
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $page) {
            echo "<strong>$i</strong> ";
        } else {
            echo "<a href='?page=$i'>$i</a> ";
        }
    }

    
        $nextPage = min($total_pages, $page + 1);
        echo "<a href='?page=$nextPage'>&gt;</a> ";

    
        echo "<a href='?page=$total_pages'>&raquo;</a>";

        echo "</div>";
}
?>

    <br>
    <a href="write.php">
        <button type="button">글쓰기</button>
    </a>
</body>
</html>
