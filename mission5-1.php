<!DOCTYPE html>
<html lang="ja">
<head>
    <title>bulletin board</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="mission5.css">
</head>
<body>
<?php
  try {
        error_reporting(E_ALL & ~E_NOTICE);
        $dsn='データベース名';
        $user='ユーザー名';
        $password='パスワード';
    //　DB名、ユーザー名、パスワード
        $pdo = new PDO($dsn, $user, $password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)); //MySQLのデータベースに接続
    //　DB接続完了
    /*  $sql = "CREATE TABLE IF NOT EXISTS tbtest2"
          ." ("
          . "id INT AUTO_INCREMENT PRIMARY KEY,"
          . "name char(32),"
          . "comment TEXT"
          .");";
        $stmt = $pdo->query($sql); */
    // tbtest2というテーブルを作成し、idカラム、nameカラム、コメントカラムを挿入
    /*$sql='ALTER TABLE tbtest2 ADD (password varchar(32), date timestamp);';
     $stmt = $pdo -> query($sql);*/ 
    //   tbtest2というテーブルにpasswordカラムとdateカラムを追加する
    
    
    $name = $_POST['name'];
    $comment = $_POST['comment'];
    $pw = $_POST['password'];
    $date=date("Y/m/d"."\t"."H:i:s");
    $you_cannot_look_this=$_POST["edit-num"];
    // 後で消される欄に表示される編集対象番号フォームから送信される数字を変数に代入
    
    if(!empty($_POST['name'])&&!empty($_POST['comment'])){
        // $you_cannot_look_this=$_POST["edit-num"];
        if(empty($you_cannot_look_this)){
        // もし編集対象番号（後で消される欄）に数字が入っていなければ
            $sql = $pdo -> prepare("INSERT INTO tbtest2 (name,comment,password,date) VALUES (:name, :comment, :password, :date)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':password', $pw, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> execute();
        // 名前、コメント、パスワードを保存して投稿
        }
        else{
        // もし編集対象番号（後で消される欄）の数字が入っていたら
            $sql='SELECT * FROM tbtest2';
            $stmt = $pdo->query($sql);
            $lines = $stmt->fetchAll();
            foreach($lines as $line){
                if($you_cannot_look_this==$line["id"]){
        // 編集対象番号（後で消される欄）の数字とデータベースのid（投稿番号）が一致したら
                    $sql = 'UPDATE tbtest2 SET name=:name,comment=:comment,password=:password WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment',  $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $you_cannot_look_this, PDO::PARAM_INT);
                    $stmt->bindParam(':password', $pw, PDO::PARAM_INT);
                    $stmt->execute();
                }
                
            }
            
        }
        
    }
    // 投稿機能
 
     if(!empty($_POST["delete"])&&!empty($_POST["password_to_del"])){
        // 　削除番号が送信されたら
        $id = $_POST["delete"];
        //   送信された削除番号を$idに代入
        $password_to_del=$_POST["password_to_del"];
        // 　送信された削除用パスワードを$password_to_delに代入
        $sql = 'SELECT * FROM tbtest2';
        $stmt = $pdo->query($sql);
        $lines = $stmt->fetchAll();
        foreach($lines as $del_data){
            if($id==$del_data["id"]&&$password_to_del==$del_data["password"]){
            // 投稿番号と削除対象番号が一致しないorパスワードと削除対象パスワードが一致しないとき
            // →投稿番号と削除対象番号が一致するandパスワードと削除対象パスワードが一致する　の逆
                $sql = 'delete from tbtest2 where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            //   $idに合致する投稿番号の投稿を削除
                $stmt->execute();
            }
            
        }
     }
    //  削除機能
    
    if(!empty($_POST["edit"])&&!empty($_POST["password_to_edit"])){
         $id = $_POST["edit"]; 
         // 変更する投稿の番号を取得し、$idに代入
         $password_to_edit = $_POST["password_to_edit"];
         /*編集ボタンの隣にあるパスワード入力欄から送られたパスワードを
         $password_to_editに代入*/
         $sql = 'SELECT * FROM tbtest2';
         $stmt = $pdo->query($sql);
         $lines = $stmt->fetchAll();
        // データベースの中身を全て取り出す
            foreach($lines as $edit_data){
            //   データベースの中身を一行ずつ読み込む     
                if($password_to_edit==$edit_data["password"]){
                    if($id==$edit_data["id"]){
                    // 編集フォーム（$_POST["edit"]）から送信された番号とデータベースのid（投稿番号）が一致したら
                        $edit_num=$edit_data["id"];
                        $editor=$edit_data["name"];
                        $edit_comment=$edit_data["comment"];
                        $edit_password=$edit_data["password"]; 
                    }
                    
                }
                
            }
        
    }
//  編集機能
  }
  catch (PDOException $e) {
  exit('データベースに接続できませんでした。' . $e->getMessage());
  }
  ?>
<form action="" method="post">
    <label for="name">名前</label><br>
    <input type="text" name="name" id="name" value="<?php if(!empty($editor)){echo $editor;}?>"><br>
    <label for="comment">コメント</label><br>
    <textarea name="comment" id="comment"><?php if(!empty($edit_comment)){echo $edit_comment;}?></textarea><br>
    <label for="password">パスワード</label><br>
    <input type="password" name="password" id="password" value="<?php if(!empty($edit_password)){echo $edit_password;}?>"><br>
    <input type="submit" id="submit" value="送信"><br>
    <input type="hidden" name="edit-num" value="<?php if(!empty($edit_num)){echo $edit_num;}?>"><br>
    <input type="text"name="delete" placeholder="削除対象番号">
    <input type="password" name="password_to_del" placeholder="パスワード">
    <input type="submit" id="submit" value="削除"><br>
    <input type="text"name="edit" placeholder="編集対象番号">
    <input type="password" name="password_to_edit" placeholder="パスワード">
    <input type="submit" id="submit" value="編集">
</form>
<?php
    $sql = 'SELECT * FROM tbtest2';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る-->
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['date']."<br>";
        echo "<hr>";
    };
?>



</body>
</html>