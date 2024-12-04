<?php
require 'db.php';

// 投稿一覧を取得する関数
function fetchPosts($pdo) {
    $stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC"); // 投稿を新しい順に取得
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 新しい投稿を追加する関数
function addPost($pdo, $name, $content, $genre) {
    $stmt = $pdo->prepare("INSERT INTO posts (name, content, genre) VALUES (:name, :content, :genre)"); // 投稿をデータベースに挿入
    $stmt->execute(['name' => $name, 'content' => $content, 'genre' =>$genre]);
}

// 投稿を検索する関数
function searchPosts($pdo, $keyword) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE content LIKE :keyword ORDER BY created_at DESC"); // キーワードで検索
    $stmt->execute(['keyword' => "%$keyword%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($posts)) {
        echo "<p>検索結果がありません。</p>";
    }
    
}

// 投稿を編集する関数
function updatePost($pdo, $id, $content) {
    $stmt = $pdo->prepare("UPDATE posts SET content = :content WHERE id = :id"); // 指定した投稿を更新
    $stmt->execute(['content' => $content, 'id' => $id]);
}

// 返信を追加する関数
function replyPost($pdo, $parent_id, $name, $content) {
    $stmt = $pdo->prepare("INSERT INTO posts (parent_id, name, content, created_at) VALUES (:parent_id, :name, :content, NOW())"); // 親投稿に返信を追加
    $stmt->execute(['parent_id' => $parent_id, 'name' => $name, 'content' => $content]);
}
?>