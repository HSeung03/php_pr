<?php
$conn = new mysqli("mysql", "root", "12345678", "board_db");

$id = $_POST['id'];

$stmt = $conn->prepare("DELETE FROM board WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('삭제 완료'); location.href='list.php';</script>";
} else {
    echo "<script>alert('삭제 실패'); history.back();</script>";
}
?>
