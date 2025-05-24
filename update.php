<?php
$id = $_GET['id'];
?>

<h3>수정 > 비밀번호 확인</h3>
<form action="0523pr4update_pass.php" method="get">
  <input type="hidden" name="id" value="<?= $id ?>">
  비밀번호: <input type="password" name="password"><br><br>
  <button type="submit">확인</button>
</form>
