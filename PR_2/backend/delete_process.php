<?php
    //📇データベース指定
    include ("db_connect_pass.php");

    //🔄️フォームから送られてきたデータを変数で受け取る
    $id = $_POST['id'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM board WHERE id = ? AND password = ?";
    $stmt = $conn -> prepare($sql);
    $stmt -> bind_param("is", $id, $password);    //i: int
    $stmt -> execute();
    $result = $stmt -> get_result();

    //❌投稿が存在しない場合は処理を中断
    if ($result -> num_rows === 0) {
        die("비밀번호가 틀렸거나 게시글이 존재하지 않습니다.");
    }
    $stmt -> close();

    //🛍️投稿を削除するSQLを準備
    $sql = "DELETE FROM board WHERE id = ?";
    $stmt = $conn -> prepare($sql);
    $stmt -> bind_param("i", $id);

    //🏃実行・結果表示
    if ($stmt -> execute()){
        echo "삭제가 완료되었습니다. <a href='../front/list.php'>복록으로</a>";
    } else {
        echo "삭제 실패: " .$conn -> error;
    }

    //🚪データベースから出る
    $stmt -> close();
    $conn -> close();
?>