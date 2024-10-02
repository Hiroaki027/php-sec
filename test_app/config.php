<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
// ini_set関数で設定オプションの値を設定しており
// ini_set('設定の名前',iniディレクティブのデフォルト値)となっている
error_reporting(E_ALL);
// error_reporting関数で出力するエラーの種類を設定する
// 今回は引数に(E_ALL)が設定されているため全てのエラーを表示する内容になっている
set_error_handler('errorHandler');
// set_error_handler関数でエラーハンドラ関数を設定する、つまりエラー条件をカスタム設定している
// 今回は引数としてローカルで定義されているerrorHandler関数を指定している
function errorHandler($errNo, $errStr, $errFile, $errLine)
{
    if ($errNo === E_NOTICE || $errNo === E_WARNING) {
        $errTitle = $errNo === E_NOTICE ? 'Notice' : 'Warning';
        $escapedErrStr = htmlspecialchars($errStr);
        $escapedErrFile = htmlspecialchars($errFile);
        // htmlspecialcharsで特殊文字を指定した種類に変換している
        // 今回は引数として($errStr)と($errFire)が指定されている
        // $errStrはエラーメッセージが文字列で返され、$errFileはエラーが発生したファイル名を文字列で返している
        echo '<b>' . $errTitle . '</b>: ' . $escapedErrStr . ' in <b>' . $escapedErrFile . '</b> on line <b>' . $errLine . '</b>';
        exit;
    }

    return false;
}

define('DSN', 'mysql:dbname=php_lesson;host=localhost;unix_socket=/tmp/mysql.sock');
define('DB_USER', 'root');
define('DB_PASSWORD', 'aA01041008');
// define関数で定数を定義している
// define('定数名','定数の値')となっている
?>