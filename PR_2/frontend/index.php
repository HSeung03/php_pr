<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$total_result = $conn->query("SELECT COUNT(*) as cnt FROM board")->fetch_assoc();
$total = $total_result['cnt'];
$total_page = ceil($total / $limit);

$result = $conn->query("SELECT * FROM board ORDER BY id DESC LIMIT $limit OFFSET $offset");
?>

<h3>게시판</h3>
<a href="write.php">글쓰기</a>
<table border="1">
    <tr><th>번호</th><th>제목</th><th>작성자</th><th>작성일</th></tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><a href="view.php?id=<?= $row['id'] ?>"><?= $row['subject'] ?></a></td>
        <td><?= $row['name'] ?></td>
        <td><?= $row['regdate'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>

<div>
    <?php for ($i = 1; $i <= $total_page; $i++): ?>
        <a href="?page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
