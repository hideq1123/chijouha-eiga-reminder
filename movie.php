<?php
$dir = __DIR__;
// phpQueryの読み込み
require_once($dir."/phpQuery-onefile.php");
//require_once("/Mail.phpまでのパスをここに書いて下さい/pear/Mail.php");//メール通知を使うならここのコメントアウトを外す
//require_once("/SMTP.phpまでのパスをここに書いて下さい/pear/Net/SMTP.php");//メール通知を使うならここのコメントアウトを外す
mb_language("Japanese");
mb_internal_encoding("UTF-8");


/*  // Gmail送信に関する情報を準備
$params = array(
    "host" => "ssl://smtp.gmail.com",
    "port" => 465,
    "auth" => true,
    "username" => "",  //送信元となるメールアドレスをダブルクォーテーションの中に書いて下さい
    "password" => "", //送信元のメールアドレスのログインパスワードをダブルクォーテーションの中に書いて下さい
    "debug" => false,
    "protocol"=>"SMTP_AUTH"
);
$mailObject = Mail::factory("smtp", $params);
// Gmail送信に関する情報を入力ここまで。メール通知を行う場合は11行目と22行目のコメントアウトを外す*/


// スクレイピング
$html = file_get_contents("https://movie.jorudan.co.jp/cinema/broadcast/");
$phpQueryObj = phpQuery::newDocument($html);


//テキストファイルに書き込む処理
$moji=array();
$moji = $phpQueryObj['.title'];
$fh = fopen($dir."/movie.txt", "w");
fwrite($fh,$moji);
fclose($fh);


$i = 1;
while($i <= 6){//最後あたりの行を削除する処理(公開中の映画のタイトルが入ってきてしまうため)
    // load the data and delete the line from the array
    $lines = file($dir.'/movie.txt');
    $last = sizeof($lines) - 1 ;
    unset($lines[$last]);

    // write the new data to the file
    $fp = fopen($dir.'/movie.txt', 'w');
    fwrite($fp, implode('', $lines));
    fclose($fp);

    $i++;
}
//テキストファイルに書き込む処理ここまで



$cnt = 0;
$file = fopen($dir."/movie.txt", "r");
$movie_title = "";
if($file){
    while ($line = fgets($file)) {
        $movie_title = array("タイタニック","ジュラシック","ハチ公物語");//ここに見たい映画タイトルを入力する。
        $movie_title_cnt = count($movie_title);

        echo $line;

        while($cnt < $movie_title_cnt){//登録した映画タイトルを検索する処理
            if(!strpos($line,$movie_title[$cnt]) === false){
                echo "該当する映画あり";

                /*  //メールで通知が必要な場合はこの行のコメントアウトを外す。
                $subject = "該当する映画あり";
                $mail = "";//送信先のメールアドレスをダブルクォーテーションの中に書いて下さい
                $message = $movie_title[$cnt]."がもうすぐ地上波で放映するかもです。";
                $headers = array(
                    "To" => $mail,
                    "From" => "",
                    "Subject" => mb_encode_mimeheader($subject) //16行目で指定した、送信元となるアドレスを、76行目のFromの右のダブルクォーテーションの中に書いて下さい
                );
                $message = mb_convert_encoding($message, "ISO-2022-JP", "UTF-8");
                $mailObject->send($mail, $headers, $message);
                //メール送信処理ここまで*/	//メールで通知が必要な場合はこの行のコメントアウトを外す。

            }
            $cnt++;
        }
        $cnt = 0;//登録した映画タイトルを検索する処理ここまで

    }
}
fclose($file);
?>