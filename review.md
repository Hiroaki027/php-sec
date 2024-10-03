# PHP App ② レビュー

## XSS(クロスサイトスクリプティング)

### XSSとはどんな攻撃か、また攻撃者にどんなメリットがあるか説明してください。

* 一般的に利用されるような掲示板サイト等に悪意のあるスクリプトを罠としてしかけ
そのサイトにアクセスをしたユーザーに対して、そのスクリプトを実行させ
悪意のあるページに強制的に遷移させることでユーザーのCookie情報を保存する攻撃です。
* 攻撃者のメリットとして、悪意のあるページへ遷移してきたユーザーのCookie情報を
利用して、そのユーザー権限を用いて不正に利用することができる。
* 例としては、SNSのアカウントを乗っ取る行為や、ECサイトに不正ログインし
そのユーザー権限で買い物をしたりすることが挙げられる。

### `htmlspecialchars()`を`e()`として定義しなおすメリットを説明してください。

* `htmlspecialchars()`を`e()`に定義しなおすことで、メリットとして
第1に記述量が減ることが挙げられます。記述量が減ることで、可読性が高まり
記述が読みやすくなります。また、`e()`関数にすることで引数を`$text`にでき
`htmlspecialchars()`内の第1引数に`$text`が使え、再利用性が高まることです。

### `htmlspecialchars()`を使うことでなぜXSSが防げるのか説明してください。

* `htmlspecialchars()`関数によって第2引数で指定したflags定数の内容に合わせて
特殊文字に変換されているからです。今回の場合は第2引数に`ENT_QUOTES`が指定されている為
シングルクォートとダブルクォートが変換対象となっています。

## CSRF(クロスサイトリクエストフォージェリ)

### CSRFとはどんな攻撃か、また攻撃者にどんなメリットがあるか説明してください。

* 不特定多数のユーザーに意図しないリクエストを送信させる攻撃です。
本物のサイトに酷似した偽サイトを作成し、送信ボタン等を押した際に
悪意のある内容(脅迫文など)を、他サイトに送信させます。
* 攻撃者側に大きなメリットは特にないですが、不特定多数のユーザーが
意図せず誰かを攻撃して加害者側になってしまったり、リクエスト先の
サイト運営者側の信頼の失墜等につながる恐れがあります。

### SessionとCookieの違いを説明してください。

* Sessionはサーバー側に情報が一時的に保存されています。
* Cookkieはブラウザ側に情報が一時的に保存されています。

### `setToken()`が何をしているか説明してください。

* ランダムな文字列を16進数に変換し、それをトークンとして設定している。
```php
function setToken()
{
    $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16));
}
```
* `bin2hex(openssl_random_pseudo_bytes(16))`の`openssl_random_pseudo_bytes`関数でランダムな文字列を生成し
それを`bin2hex`関数で16進数に変換しており、それを`$_SESSION['token']`に代入しています。
`$_SESSION['token']`に代入することで、サーバー側とのやり取りの際に生成されたトークンを渡し
再度サーバーにリクエストを送る際にそのトークンを用いて同じセッションからのリクエストかを照合しています。

### `checkToken()`が何をしているか説明してください。

* セッション内で格納されているトークンが同じものか照合しています。

```php
function checkToken($token)
{
    if (empty($_SESSION['token']) || ($_SESSION['token'] !== $token)) {
        $_SESSION['err'] = '不正な操作です';
        redirectToPostedPage();
    }
}
```
* `if (empty($_SESSION['token']) || ($_SESSION['token'] !== $token))`で条件分岐を行っており
分岐条件として`($_SESSION['token'])`内が空っぽ、もしくは
`($_SESSION['token'] !== $token)`セッション内で格納しているトークンが等しくない場合は
 `$_SESSION['err'] = '不正な操作です'`の記述により、不正な操作ですと出力されます。
 そして`redirectToPostedPage()`関数によってリダイレクトされます。

### トークンを使うことでなぜCSRFが防げるのか説明してください。

* サーバー側にリクエストを送る際に、トークンが付与された状態で返されることで
再度リクエストを送った際に、そのトークンを照合しているからです。
もし、トークンが違う、もしくはトークンが付与されていないリクエストがあった場合に
不正なリクエストもしくは、意図しないリクエストと判断し、弾くことが可能になるからです。

## SQLインジェクション

### SQLインジェクションとはどんな攻撃か、また攻撃者にどんなメリットがあるか説明してください。

* 不正なSQL文を挿入し、データベースにそのSQL分を実行させ、データベースを不正に操作する攻撃です。
* また、攻撃者側のメリットとして個人情報や企業の秘密情報を不正に取得することが可能で
取得したデータを盾に、金銭の要求や脅迫を個人もしくは企業にする点がメリットとしてあります。


### `->prepare()`の返り値と、またこのメソッドが何をしているか説明してください。

* `PDOStatement`オブジェクトを返り値としてかえしています。
* また、`prepare()`メソッドでは、`execute()`メソッドで実行されるSQL文の準備をしています。
```php
function createTodoData($todoText)
{
    $dbh = connectPdo();
    $sql = 'INSERT INTO todos (content) VALUES (:todoText) ';
    $stmt = $dbh->prepare($sql);
```
上記の場合は、`prepare($sql)`で`$sql`を引数としている為
`'INSERT INTO todos (content) VALUES (:todoText) '`が準備されています。

### `->bindValue()`が何をしているか説明してください。

* `bindValue()`メソッドでは、`preparre`メソッドで準備されているSQL文中の
プレースホルダーに値を括り付けています。
```php
function createTodoData($todoText)
{
    $dbh = connectPdo();
    $sql = 'INSERT INTO todos (content) VALUES (:todoText) ';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':todoText', $todoText, PDO::PARAM_STR);
    $stmt->execute();
}
```
上記の場合は、`$stmt = $dbh->prepare($sql)`の記述により`$sql = 'INSERT INTO todos (content) VALUES (:todoText) '`内の`:todoText`がプレースホルダーにあたり
そのプレースホルダーに対して`$stmt->bindValue(':todoText', $todoText, PDO::PARAM_STR);`
の記述によって`$todoText`を`:todoText`に括り付け、`PDO::PARAM_STR`でデータ型を指定しています。

### 今回の対策でなぜSQLインジェクションが防げるのか説明してください。

* `prepare`メソッドで実行準備をかけ、`bindValue`メソッドで値とデータ型を指定し括り付けてから`excute`メソッドで実行
といった処理に変更することで、悪意のあるSQL文を指定した型として認識させているからです。

## バリデーション

### バリデーションの目的について説明してください。

* ユーザーが値を入力したときに、制限内での入力どうかを確認する為です。
開発者側の意図していない値が入力されることを防ぐ為でもあります。

### `validate()`が何をしているか説明してください。

* 入力フォーム内を空白で作成や更新を行おうとすると「入力がありません」と出力されます。
```php
function validate($post)
{
    if (isset($post['content']) && $post['content'] === '') {
        $_SESSION['err'] = '入力がありません';
        redirectToPostedPage();
    }
}
```
* `if (isset($post['content']) && $post['content'] === '')`の記述により条件分岐として
もし、`$post['content']`内に値が入っているかどうかを確認し、かつ値がなにも入っていない場合は
`$_SESSION['err'] = '入力がありません';`によって、「入力がありません」と出力され
`redirectToPostedPage()`によってリダイレクトしています。
```php
function redirectToPostedPage()
{
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
```
上記の記述内の`header('Location: ' . $_SERVER['HTTP_REFERER']);`により
リダイレクト先がリファラ、つまり直前のリンクとなっているので、作成及び更新ボタンを押した
リンク(同一ページ)にリダイレクトしています。

### `isset($post['content'])`はなぜ必要か、無い場合どうなるか説明してください。

* `isset($post['content'])`の記述により値がフォーム内に入力されているかを確認している。
その為、この記述がない場合はフォーム内の要素の有無を確認しない為、値が何も入っていない状態でも
作成や更新が処理として進んでしまいます。

## その他

### `unsetError()`を実行しないとどうなるか説明してください。

* バリデーションによって、エラーメッセージが出力された場合に
そのメッセージが消されずに、ずっと出力されたままになります。