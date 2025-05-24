<?php
$conn = new mysqli("mysql", "root", "12345678", "board_db");

$id = $_POST['id'];
$name = $_POST['name'];
$subject = $_POST['subject'];
$content = $_POST['content'];

$stmt = $conn->prepare("UPDATE board SET name=?, subject=?, content=? WHERE id=?");
$stmt->bind_param("sssi", $name, $subject, $content, $id);

if ($stmt->execute()) {
    echo "<script>alert('수정 완료'); location.href='list.php';</script>";
} else {
    echo "<script>alert('수정 실패'); history.back();</script>";
}
?>
