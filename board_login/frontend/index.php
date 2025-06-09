<?php
$conn = new mysqli("mysql", "root", "12345678", "board_login");
$conn->set_charset("utf8mb4");


$perpage = 5;
#페이지에 들어갈 수 있는 게시물 수

#현재 페이지
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
#사용자가 ?page=...을 URL에 넣었는지 확인 없다면 기본값은 1 
if ($page < 1) $page = 1; 
#음수거나 0일경우 1로 보정

#OFFSET - 데이터의 시작점점
$start = ($page - 1) * $perpage; 
#Limit 쿼리에서 사용할 시작 위치 계산 (0부터 시작), 앞서 perpage에 5를 선언했으니 0~5

#게시물 목록 조회
$sql = "SELECT * FROM board ORDER BY id DESC LIMIT $perpage OFFSET $start"; 
#sql문 board데이터를 내림차 순으로 5개의 데이터만 start값 부터
$result = $conn->query($sql); 
#연결객체 conn을 통해 실행하고 그결과를 result에 저장

#전체 게시물 수 조회
$total_sql = "SELECT COUNT(*) AS total FROM board"; 
#Count(*) board테이블의 게시글 수를 셈 , As total: 결과 컬럼이름을 total로 지정
$total_result = $conn->query($total_sql); 
#반환된게시글 수를 total_result에 저장 
$total_row = $total_result->fetch_assoc(); 
#연관 배열로 가져오는 함수 total_result의 데이터를 해당 함수로작업 그것을 total_row로 저장 
$total_posts = $total_row['total']; 
#위에서 가져온 배열에서 total 키의 값을 꺼냄
$total_pages = ceil($total_posts / $perpage); 
#총 게시글 수를 한페이지당 표시 수로나눈 뒤 ceil()함수를 써서 올림 계산 
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
        #전체 게시글 수에서 현재 페이지까지의 누적게시글 수를 뺌 -> 현재 페이지의 첫 번째 게시글 번호
        if ($result->num_rows > 0){ #$result:앞에서 실행한 SELECT * FROM board의 결과 그 결과에 행이 있는지 확인
            while ($row = $result->fetch_assoc()){ #게시글 목록을 한 줄씩 배열로 가져옴 
                echo "<tr>"; 
                echo "<td>" . $count-- . "</td>"; #게시글 번호 출력 (위에서 구한 $count) 출력 후 __로 숫자 하나 줄임 
                echo "<td>{$row['name']}</td>"; 
                echo "<td><a href='view.php?id={$row['id']}'>{$row['subject']}</a></td>";  
                echo "<td>{$row['regdate']}</td>";#작성자, 제목(클릭하면상세 보기로 이동) 작성일 출력 
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>등록된 글이 없습니다.</td></tr>"; #게시글이 없다면 메세지 출력
        }
        ?>
    </table>

    <br><br>

    <!--페이지네이션 -->
    <?php
    $pageRange = 5; #한번에 보여줄 페이지 버튼 수
    $startPage = floor(($page - 1) / $pageRange) * $pageRange + 1; 
    #현재 페이지가 속한 페이지 블록의 시작 번호 계산 page = 7, pageRange = 5 
    $endPage = min($startPage + $pageRange - 1, $total_pages); 
    #현재 블록의 마지막 페이지 번호 , 최대 전체 페이지 수를 넘지 않도록 제한 

    if ($startPage > 1) {
        echo "<a href='?page=1'>&laquo;</a> "; 
        #현재 페이지 블록이 2번째 이상이면 
        echo "<a href='?page=" . ($startPage - 1) . "'>&lt;</a> "; 
        #맨 처음 페이지, 이전 블록으로 이동하는 버튼 생성 
    }

    for ($i = $startPage; $i <= $endPage; $i++) { #현재 블록의 페이지 번호를 하나씩 출력
        if ($i == $page) { 
            echo "<strong>$i</strong> "; #현재 페이지는 굵게 표시
        } else {
            echo "<a href='?page=$i'>$i</a> ";
        }
    }

    if ($endPage < $total_pages) { #다음 블록이 있을 경우 
        echo "<a href='?page=" . ($endPage + 1) . "'>&gt;</a> ";
        echo "<a href='?page=$total_pages'>&raquo;</a>"; #다음 블록, 맨 마지막 페이지로 이동하는 버튼 출력 
    }
    ?>

    <br>
    <a href="write.php">
        <button type="button">글쓰기</button>
    </a>
</body>
</html>
