<?php
#DB 연결
$conn = new mysqli("mysql", "root", "12345678", "board_login");
if ($conn->connect_error) { 
    die("DB 연결 실패: " . $conn->connect_error); #연결 실패시 해당 메세지 출력하고 종료  
}
$conn->set_charset("utf8mb4"); #PHP와 Mysqli간의 문자 동기화화

#get 방식으로 전달된 id 값을 가져옴 , 
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
#isset($_Get)id가 라는 키가 실제로 있는지 확인 
if ($id === 0) {
    die("잘못된 접근입니다."); #die는 밑의 코드를 실행하지 않고 종료한다는 뜻 
}

#게시글 불러오기
$sql = "SELECT * FROM board WHERE id = $id"; #sql문을 저장하는 php 변수
#board로 부터 id에 있는 행의 모든(*)컬럼을 SELECT하고 그것을 $sql에 저장한다 
$result = $conn->query($sql);
#conn이라는 데이터베이스 연결 객체를 통해 $sql에 저장된 sql문장을 실행하고 그 결과를 $result에 저장
if (!$result || $result->num_rows === 0) { 
    die("게시글이 없습니다.");
}
#!$result 없음 , $result->num_rows === 0이라면 해당 메세지 출력후 종료 할 것 
$row = $result->fetch_assoc();
#result라는 객체를 통해 fetch_assoc(연관배열)함수를 사용해서 행을 하나씩 불러오고 그 데이터를 row에 대입
?> 



<h2>변경</h2>
<form method="post" action="../backend/update_process.php">
    <input type="hidden" name="id" value="<?= $id ?>">
<!-- hidden = 사용자 눈에 보이지 않음 1. 어떤 id를 수정하는지 숨겨서 보냄 2.로그인된 사용자 id를 같이 보냄 3. 보안 토큰 전달-->
    <p>이름: <input type="text" name="name" value="<?= $row['name'] ?>"></p>
    <p>제목: <input type="text" name="subject" value="<?= $row['subject'] ?>"></p>
    <p>내용:<br>
    <textarea name="content" rows="10" cols="50"><?= $row['content'] ?></textarea></p>

    <!-- 수정 -->
    <button type="submit">수정</button>

    <!-- 삭제는 별도 form -->
</form>

<!-- 삭제 버튼은 별도 form으로 POST 전송 -->
<form method="post" action="../backend/delete_process.php">
    <input type="hidden" name="id" value="<?= $id ?>">
    <button type="submit">삭제</button>
</form>
