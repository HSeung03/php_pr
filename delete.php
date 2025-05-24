<?php
$id = $_GET['id'];
?>

<h3>삭제 > 비밀번호 확인</h3>
<form action="0523pr4delete_nx.php" method="post">
  <input type="hidden" name="id" value="<?= $id ?>">
  비밀번호: <input type="password" name="password"><br><br>
  <button type="submit">삭제</button>
</form>
