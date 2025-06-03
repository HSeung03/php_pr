<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시판 글쓰기</title>
</head>
<body>
    <h2>게시판 > 글쓰기</h2>

    <form action="../backend/write_process.php" method="post">
        <p>이름: <input type="text" name="name" placeholder="이름을 입력하세요." required></p>
        <p>비밀번호: <input type="password" name="password" placeholder="비밀번호를 입력하세요." required></p>
        <p>제목: <input type="text" name="subject" placeholder="제목을 입력하세요." required></p>
        <p>내용:</p>
        <p><textarea name="content" rows="10" cols="50" placeholder="내용을 입력하세요." required></textarea></p>

        <br>
        <button type="submit">등록</button>
    </form>

    <hr>
    <p>게시판으로 <a href="index.php">돌아가기</a></p>

</html>
