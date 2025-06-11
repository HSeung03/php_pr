<?php

$conn = new mysqli("mysql", "root", "12345678", "board_login");
$conn->set_charset("utf8mb4");


$id = $_GET['id'] ?? '';


if (!$id) {
    echo "ID가 지정되어 있지 않습니다.";
    exit;
}

$sql = "SELECT * FROM board WHERE id = ?"; 
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $conn->get_result(); 

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "포스트를 찾을 수 없습니다.";
    exit;
}
?>

<!DOCTYPE HTML>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시판 | 상세보기</title>
</head>
<body>
    <h1>게시판 > 상세보기</h1>

    <h2><?php echo $row['subject']; ?></h2>
    <p><strong>작성자: </strong><?php echo $row['name']; ?></p>
    <p><strong>작성일: </strong><?php echo $row['regdate']; ?></p><br>
    <p><?php echo $row['content']; ?></p><br><br>

    <table>
        <tr>
            <td>
                <form action="password_check.php" method="post">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit">변경</button>
                </form>
            </td>
        </tr>
    </table>

    <p>게시판 목록으로 돌아가시겠습니까? <a href="index.php">돌아가기</a></p>
</body>
</html>
