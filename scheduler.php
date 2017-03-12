
<?php

$dsn='mysql:dbname=schedule;host=localhost;charset=utf8';//DB接続のために使う変数（使用するDBの名前指定）
$user='testuser';
$password = '';


try {
    $pdo = new PDO($dsn,$user,$password);//PDOクラスのインスタンス生成、DB接続。コンストラクタでdbnameとかusernameとかpasswordを指定

    /*データベースの中身を取得*/
      $sql = "SELECT * FROM newdetail ORDER BY datetime1 asc"; //scheduleという名のテーブルから、*(カラム全部)を取り出して変数に格納
      $stmt_serect = $pdo->query($sql); //PDOクラスのインスタンスの中のqueryメソッドにアクセスしている。おそらくsql文を作成しているのであろう。
      //PDOStatementクラスを呼び出す。
      $result=$stmt_serect->fetch(PDO::FETCH_ASSOC); //PDOStatementの中のfetchという名のメソッドにアクセス。連想配列として返す。[カラム名]=>値。

      if (isset($_POST["regist"])) {
        $error_message=[];

        if (isset($_POST["year"]) && $_POST["year"]) {//name="year"のテキスト欄に入力があり、文字列じゃなく、2017年以降を入力していたら実行
          $year = $_POST["year"];//name="year"のテキスト欄の入力内容を$yearという名前の変数に代入
        }else{
          $error_message[]="年を正しく入力してください";
        }

        if (isset($_POST["month"]) && $_POST["month"]) {
          $month = $_POST["month"];
        }else{
          $error_message[]="月を正しく入力してください";
        }

        if (isset($_POST["day"]) && $_POST["day"]) {
          $day = $_POST["day"];
        }else{
          $error_message[]="日を正しく入力してください";
        }

        if (isset($_POST["hour"]) && $_POST["hour"]) {
          $hour = $_POST["hour"];
        }else{
          $error_message[]="時間を正しく入力してください";
        }

        if (isset($_POST["minute"]) && $_POST["minute"]) {
          $minute = $_POST["minute"];
        }else{
          $error_message[]="分を正しく入力してください";
        }

        if (isset($_POST["title"]) && $_POST["title"]) {
          $title = $_POST["title"];
        }else{
          $error_message[]="タイトルを正しく入力してください";
        }

        if (isset($_POST["contents"]) && $_POST["contents"]) {
          $contents = $_POST["contents"];
        }else{
          $error_message[]="内容を正しく入力してください";
        }
      }
      $second=0;
      $dt = new DateTime();
      $dt->setDate($year, $month, $day);
      $dt->setTime($hour,$minute);
      $datetime1 =$dt->format('Y-m-d H:i');

      /*データベースに指定の値を挿入*/
      $stmt_insert = $pdo -> prepare("INSERT INTO newdetail (id,datetime1,title,contents) VALUES (:id,:datetime1,:title,:contents)");//指定したテーブルから指定したカラムをデータベースからひっぱってくる処理のsql文をphpで使えるようにしている。
      $stmt_insert->bindParam(':title', $title, PDO::PARAM_STR);//文字列として値をパラメーターに格納している//bindParamはbindValueの上じゃなきゃダメらしい
      $stmt_insert->bindParam(':contents', $contents, PDO::PARAM_STR);//文字列として値をパラメーターに格納している//bindParamはbindValueの上じゃなきゃダメらしい
      $stmt_insert->bindParam(':datetime1', $datetime1, PDO::PARAM_STR);
      $stmt_insert->bindValue(':id', $id, PDO::PARAM_INT);

      $stmt_insert->execute();//ここでインサートの文を実行している

      if(isset($_POST["method"]) && $_POST["method"]=="delete"){
      $sql_delete = "delete from newdetail where id = :delete_id";
      $stmt_delete = $pdo->prepare($sql_delete);
      $flag = $stmt_delete->execute(array(':delete_id' => $_POST["id"]));//指定したidの行を削除
    }

    if(isset($_POST["method"]) && $_POST["method"]=="put"){


      $sql_update='update newdetail set title =? where id=?';
      $stmt_update=$pdo->prepare($sql_update);
      $flag_update=$stmt_update->execute(array('2020',$_POST["id"]));//指定したidの行の指定した値を書き換え
    }

  } catch (PDOException $e) {//PDOExceptionクラス（例外クラス）を使用して、$eという変数に代入するげ！！
      header('Content-Type: text/plain; charset=UTF-8', true, 500);//HTTPヘッダを送信。ページの内容の送信。
      exit($e->getMessage());//exit()は()の中のメッセージを出力して現在のスクリプトを終了する。スクリプトを関に詳しく聞こう。
  }

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>index</title>
  <link rel = "stylesheet" type = "text/css" href = "index.css" />
  <style>
  body{
    font-family:'小塚ゴシック Pro6N R','ヒラギノ角ゴ Pro W3','Hiragino Kaku Gothic Pro','メイリオ',Meiryo,'ＭＳ Ｐゴシック', Arial, sans-serif;
  }
  </style>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>

<body>

  <div class="container">
    <div class="top_right">  <?php date_default_timezone_set('Asia/Tokyo');
    echo "<p>本日</p>";
    echo date("Y年m月d日 G:i");?>
  </div>
    <div id="title">
      <img id="webIcon" src="images/webIcon.png"><h1 id="webTitle">My.Scheduler</h1>
    </div>
    <div id="writeText">

      <h2>スケジュール記入欄</h2>
      <form class="form" action="scheduler.php" method="post">
        年：<select name="year">
          <?php
          for($i=2017;$i<=2020;$i++){
            echo "<option value='{$i}'>{$i}年</option>";
          }
          ?>
        </select>

        <br>
        月：<select name="month">
          <?php
          for($i=1;$i<=12;$i++){
            echo "<option value='{$i}'>{$i}月</option>";
          }
          ?>


        </select>
        <br>
        日：<select name="day">
          <?php
          for($i=1;$i<=31;$i++){
            echo "<option value='{$i}'>{$i}日</option>";
          }
          ?>

        </select>

        <br>

        時間：<select name="hour">
          <?php
          for($i=0;$i<=24;$i++){
            if(strlen($i)==1){
            echo "<option value='0{$i}'>0{$i}</option>";
          }
          else if(strlen($i)>1){
            echo "<option value='{$i}'>{$i}</option>";
          }
          }
          ?>
        </select>
        :
        <select name="minute">
          <?php
          for($i=0;$i<=59;$i++){
            if(strlen($i)==1){
            echo "<option value='0{$i}'>0{$i}</option>";
          }
          else if(strlen($i)>1){
            echo "<option value='{$i}'>{$i}</option>";
          }
          }
          ?>
        </select>
        ~
        <br>


        <br>
        <br>
        タイトル
        <br>
        <input class="buttonType" type="text" name="title">
        <br>
        内容
        <br>
        <textarea class="buttonType" name="contents"></textarea>
        <br>
        <input type="hidden" name="regist"><button class="buttonType form-position" type="submit">スケジュールを登録する +</button>
        <br>
        <br>
        <div id="error"><?php // エラーメッセージを出力する
        if (isset($error_message)) {
            foreach ($error_message as $message) {
                print("※".$message);
                print("<br>");
            }
        }?></div>

      </form>

      <div id="nearSchedule">

        <h2>直近の予定</h2>

      </div>
    </div>


    <div id="displayText">

      <h2>今後の予定</h2>

      <?php

      $stmt_serect->execute();//ここでセレクトの文を実行している

      while($result = $stmt_serect->fetch(PDO::FETCH_ASSOC)){
              echo "<div class='suceduleBorder'>";
              $date= $result['datetime1'];
              echo date('Y/m/d', strtotime($date));
              echo "<br>".date('H:i~', strtotime($date));
              echo '<br>タイトル：'.$result['title'];
              echo '<br>内容：'.$result['contents'];
              //echo '<br>'.$result['id'];
              $id_result=$result['id'];
              echo '<form action="scheduler.php" method="post"><input type="hidden" name="method" value="delete"><input type="hidden" name="id" value="'. $id_result .'"><button class="buttonType right" type="submit">削除</button></form>';
              echo '<form action="scheduler.php" method="post"><input type="hidden" name="method" value="put"><input type="hidden" name="id" value="'. $id_result .'"><button class="buttonType right" type="submit">編集</button></form>'.'<br><br><br>';
              //echo '<p class="buttonType right put">あ</p>';
              //echo '<form class="form_none" action="scheduler.php" method="post">年：<select name="year1"></select></form>';
              echo "</div>";
          }


      ?>



    </div>

  </div>


<script type="text/javascript" src="scheduler_script.js"></script>
</body>

</html>
