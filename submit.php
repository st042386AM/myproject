<?php
// 必要なファイルを読み込む
require 'db.php';
require 'functions.php';

// POSTリクエストの場合に投稿を処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '匿名'; // 名前が指定されていない場合は「匿名」にする
    $content = $_POST['content'] ?? '';
    $genre = $_POST['genre'] ?? '一般'; // ジャンルを取得

    addPost($pdo, $name, $content, $genre); // 投稿を追加
    header('Location: index.php'); // メイン画面にリダイレクト
    exit;
}
?>