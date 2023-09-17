<!DOCTYPE html>
<html lang="ja">
<head>
    <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <?php
    $dsn ='mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    $sql = "CREATE TABLE IF NOT EXISTS kejiban"
    . " ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP," 
    . "password VARCHAR(255)"
    . ");";
    $stmt = $pdo->query($sql);
    
    //編集番号初期化
    $editname = "";
    $editcomment = "";
    
    //新規投稿
    if(isset($_POST["submit"]) && empty($_POST["edit_HDN"])){
        if($_POST["comment_pass"]!=""){
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $timestamp = date("Y/m/d/H:i:s");
            $password = $_POST["comment_pass"];
        
            if(!empty($name) && !empty($comment)){
                $sql = "INSERT INTO kejiban (name, comment, timestamp, password) VALUES (:name, :comment, :timestamp, :password)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->execute();
            }else{
                echo "名前とコメントを入力してください。<br><hr>";
            }
        }else{
            echo "パスワードを入力してください";
        }
        
    //削除処理
    }elseif(isset($_POST["delsub"]) && !empty($_POST["del"]) && empty($_POST["edit_HDN"])){
        $id = $_POST["del"];
        $del_pass =$_POST["del_pass"];
        $sql = "SELECT password FROM kejiban WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($results['password'] ==$del_pass){
           $id = $_POST["del"];
           $sql = 'delete from kejiban where id=:id';
           $stmt = $pdo->prepare($sql);
           $stmt->bindParam(':id', $id, PDO::PARAM_INT);
           $stmt->execute();
        }else{
            echo "パスワードが正しくありません";
        }
        
    //編集処理
    }elseif(isset($_POST["submit"]) && isset($_POST["name"]) && isset($_POST["comment"]) && isset($_POST["edit_HDN"])){
        $id = $_POST["edit_HDN"];
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $timestamp =date("Y/m/d/H:i:s");
            $password = $_POST["comment_pass"];
            $sql = 'UPDATE kejiban SET name=:name,comment=:comment,timestamp=:timestamp,password=:password WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $editnum = "";
    }
    
    //投稿フォームに表示する編集対象の定義
    if(isset($_POST["editsub"])){
        $id = $_POST["editnum"];
        $edit_pass =$_POST["edit_pass"];
        $sql = "SELECT password FROM kejiban WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($result['password'] ==$edit_pass){
        
        $editnum =$_POST["editnum"];
        $id = $editnum;
        $sql = 'SELECT * FROM kejiban WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
        $stmt->execute();
        $results = $stmt->fetchAll(); 
        foreach ($results as $row){
            $editname = $row['name'];
            $editcomment = $row['comment'];
        }
        }else{
            echo "パスワードが正しくありません";
            $edit_pass ="";
        }
    }  
    
    
    ?>
    <form method ="POST" action ="">
        名前：<input type ="text" name ="name" size ="10" value="<?php if(isset($editname)) echo $editname; ?>"><br>
        コメント：<input type ="text" name ="comment" value="<?php if(isset($editcomment)) echo $editcomment; ?>"><br>
        パスワード：<input type ="password" name ="comment_pass" value="<?php if(isset($edit_pass)) echo $edit_pass; ?>"><br>
        <input type ="submit" name ="submit"><br>
        <p>
        削除対象番号：<input type ="num" name ="del" size ="1"><br>
        パスワード：<input type ="password" name ="del_pass"><br>
        <input type ="submit" name ="delsub" value ="削除"><br>
        </p>
        編集対象番号：<input type ="text" name ="editnum" size ="1"><br>
        パスワード：<input type ="password" name ="edit_pass"><br>
        <input type ="submit" name ="editsub" value ="編集"><br>
        <input type="hidden" name="edit_HDN" value="<?php if(isset($editnum)) echo $editnum;?>">
    </form>
    
    <?php
    //ブラウザに表示
    $sql = 'SELECT * FROM kejiban';
    $stmt = $pdo->query($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();  
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['timestamp'];
    echo "<hr>";
    }
    

    ?>
</body>
</html>