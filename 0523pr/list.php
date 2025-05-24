<?php
$conn = new mysqli("mysql", "root", "12345678", "board_db");
$sql = "SELECT * FROM board ORDER BY id DESC";
$result = $conn->query($sql);
?>

<h3>게시판 > 리스트</h3>

<!-- 검색 폼 -->
<form action="list.php" method="get">
    <select name="search_type">
        <option value="subject">제목</option>
        <option value="content">내용</option>
    </select>
    <input type="search" name="search_query" placeholder="검색어 입력">
    <button type="submit">검색</button>
</form>

<br>

<!-- 게시글 목록 테이블 -->
<table border="1">
    <tr>
        <th>번호</th>
        <th>이름</th>
        <th>제목</th>
        <th>작성일</th>
    </tr>

    <?php if ($result->num_rows === 0): ?>
        <tr>
            <td colspan="4">등록된 글이 없습니다.</td>
        </tr>
    <?php else: ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><a href="view.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['subject']) ?></a></td>
                <td><?= $row['regdate'] ?></td>
            </tr>
        <?php endwhile; ?>
    <?php endif; ?>
</table>

<br>

<!-- 글쓰기 버튼 (무조건 출력) -->
<a href="write.php"><button type="button">글쓰기</button></a>
