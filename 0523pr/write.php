<h3>게시판 > 글쓰기</h3>

<form action="write_process.php" method="post">
    이름: <input type="text" name="name" required><br><br>
    제목: <input type="text" name="subject" required><br><br>
    내용:<br>
    <textarea name="content" rows="10" cols="50" required></textarea><br><br>
    <button type="submit">등록</button>
</form>

<br>
<a href="list.php"><button type="button">목록으로</button></a>
