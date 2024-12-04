<?php
// 必要なファイルを読み込む
require 'db.php';
require 'functions.php';

// POSTリクエストの場合に返信を処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parent_id = $_POST['parent_id']; // 親投稿のID
    $name = $_POST['name'] ?? '匿名'; // 名前が指定されていない場合は「匿名」にする
    $content = $_POST['content'] ?? '';

    replyPost($pdo, $parent_id, $name, $content); // 返信を追加
    header('Location: index.php'); // メイン画面にリダイレクト
    exit;
}
?>