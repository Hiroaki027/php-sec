<?php
require_once('functions.php');

savePostedData($_POST);
header('Location: ./index.php');

//header関数でリダイレクト先を指定し遷移
?>