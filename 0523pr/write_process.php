<?php
$conn = new mysqli("mysql", "root", "12345678", "board_db");

$name = $_POST['name'];
$subject = $_POST['subject'];
$content = $_POST['content'];
$regdate = date("Y-m-d H:i:s");

$sql = "INSERT INTO board (name, subject, content, regdate)
        VALUES ('$name', '$subject', '$content', '$regdate')";

if ($conn->query($sql)) {
    echo "<script>alert('등록되었습니다.'); location.href='list.php';</script>";
} else {
    echo "<script>alert('등록 실패'); history.back();</script>";
}
?>
