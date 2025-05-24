<?php
// DB 연결 필요 (예: $pdo)

$id = $_POST['id'];
$input_password = $_POST['password'];

// DB에서 해당 글의 비밀번호 해시 가져오기
$sql = "SELECT password FROM board WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$row = $stmt->fetch();

if ($row && password_verify($input_password, $row['password'])) {
    // ✅ 비밀번호가 맞을 때
    // edit.php로 이동
    header("Location: edit.php?id=" . $id);
    exit;
} else {
    // ❌ 비밀번호 틀렸을 때
    echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>";
}
?>
