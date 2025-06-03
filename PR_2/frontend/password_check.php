<?php
$id = $_GET['id'];
$mode = $_GET['mode'];
?>

<h3>비밀번호 확인</h3>
<form action="<?= "../backend/{$mode}_process.php" ?>" method="post">
    <input type="hidden" name="id" value="<?= $id ?>">
    비밀번호: <input type="password" name="password"><br>
    <input type="submit" value="확인">
</form>
