<?php
$conn = new mysqli("mysql", "root", "12345678", "board_db");
$sql = "SELECT * FROM board ORDER BY id DESC";
$result = $conn->query($sql);
?>

<h3>게시판 > 리스트</h3>

<form action="list.php" method="get">
    <select name="search_type">
        <option value="subject">제목</option>
        <option value="content">내용</option>
    </select>
    <input type="search" name="search_query" placeholder="검색어 입력">
    <button type="submit">검색</button>
</form>

<br>
<table border="1">
    <tr>
        <th>번호</th><th>이름</th><th>제목</th><th>작성일</th>
    </tr>

    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['name'] ?></td>
        <td><a href="0523pr4.html?id=<?= $row['id'] ?>"><?= $row['subject'] ?></a></td>
        <td><?= $row['regdate'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>

<br>
<a href="0523pr4write.html"><button>글쓰기</button></a>
