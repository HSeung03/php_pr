<?php
$id = $_POST['id'] ?? '';
$mode = $_POST['mode'] ?? '';

if (!$id || !$mode) {
    die("잘못된 접근입니다.");
}
?>

<h3>비밀번호 확인</h3>
<form action="<?= "../backend/password_process.php" ?>" method="post">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="mode" value="<?= $mode ?>">
    비밀번호: <input type="password" name="password" required><br>
    <input type="submit" value="확인">
</form>
