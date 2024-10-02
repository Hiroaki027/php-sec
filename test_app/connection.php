<?php
require_once('config.php');
//requireはincludeとほぼ同義。
//違いとしてはエラーのレベルでスクリプトの処理が止まるか否か。includeは警告だけで処理は継続。
//_onceをつけることで1度の読み込みとして指定。既に読み込まれている場合は読み込まない。

// PDOクラスのインスタンス化
function connectPdo()
{
    try {
        return new PDO(DSN, DB_USER, DB_PASSWORD);
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit();
    }
}

//PDOExceptionクラス(PHPの定義済みクラス)では、PDOクラスが発生するエラーを表示
//規則としてスローしてはいけない為、throw文を使わない。
//try{}内でPDOインスタンスで発生した例外を捉える。
//catch{}内でPDOExceptionクラス内のエラーをどのように対処するかを記述
//上記ではechoでプロパティ$e内のものをgetMessageメソッドでユーザーに表記

function createTodoData($todoText)
{
    $dbh = connectPdo();
    $sql = 'INSERT INTO todos (content) VALUES (:todoText) ';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':todoText', $todoText, PDO::PARAM_STR);
    $stmt->execute();
}
//queryメソッド(PDO::query)でSQL ステートメントを準備し実行する。
//いわば、mysql内で打つクエリをphp内で記述し、その内容をmysqlに送っている。

function getAllRecords()
{
    $dbh = connectPdo();
    $sql = 'SELECT * FROM todos WHERE deleted_at IS NULL';
    return $dbh->query($sql)->fetchAll();
    
}
//fetchAll() で実行結果を全件配列で取得、そしてその結果をreturn

function updateTodoData($post)
{
    $dbh = connectPdo();
    $sql = 'UPDATE todos SET content = "' . $post['content'] . '" WHERE id = ' . $post['id'];
    $dbh->query($sql);
}

function getTodoTextById($id)
{
    $dbh = connectPdo();
    $sql = 'SELECT * FROM todos WHERE deleted_at IS NULL AND id ='.$id;
    $data = $dbh->query($sql)->fetch();
    return $data['content'];
}

function deleteTodoData($id)
{
    $dbh = connectPdo();
    $now = date('Y-m-d H:i:s');
    $sql = 'UPDATE todos SET deleted_at = "'.$now.'" WHERE id ='.$id;
    $dbh->query($sql);
}
?>