<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    
    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO news (title, created_at) VALUES (:title, NOW())");
        $stmt->execute(['title' => $title]);
    }

    header('Location: index.php');  // 投稿後にトップページにリダイレクト
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新着情報投稿</title>
</head>
<body>
    <h1>新着情報を投稿</h1>
    <form method="POST" action="submit_news.php">
        <input type="text" name="title" placeholder="タイトル" required>
        <button type="submit">投稿</button>
    </form>
</body>
</html>
