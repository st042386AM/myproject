<?php
require 'db.php';

// 投稿一覧を取得する関数
function fetchPosts($pdo) {
    $stmt = $pdo->query("SELECT * FROM posts WHERE parent_id IS NULL ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 新しい投稿を追加する関数
function addPost($pdo, $name, $content, $genre) {
    $stmt = $pdo->prepare("INSERT INTO posts (name, content, genre, created_at) VALUES (:name, :content, :genre, NOW())");
    $stmt->execute(['name' => $name, 'content' => $content, 'genre' => $genre]);
}

// 投稿を検索する関数
function searchPosts($pdo, $keyword) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE content LIKE :keyword AND parent_id IS NULL ORDER BY created_at DESC");
    $stmt->execute(['keyword' => "%$keyword%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 返信を追加する関数
function replyPost($pdo, $parent_id, $name, $content) {
    $stmt = $pdo->prepare("INSERT INTO posts (parent_id, name, content, created_at) VALUES (:parent_id, :name, :content, NOW())");
    $stmt->execute(['parent_id' => $parent_id, 'name' => $name, 'content' => $content]);
}

// ジャンル別の投稿を取得する関数
function fetchPostsByGenre($pdo, $genre) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE genre = :genre AND parent_id IS NULL ORDER BY created_at DESC");
    $stmt->execute(['genre' => $genre]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 指定した投稿の返信を取得する関数
function fetchReplies($pdo, $parent_id) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE parent_id = :parent_id ORDER BY created_at ASC");
    $stmt->execute(['parent_id' => $parent_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 投稿を親子構造で整理する関数
function organizePosts($posts) {
    $organized = [];
    $references = [];

    // 投稿を参照用配列に整理
    foreach ($posts as $post) {
        $post['replies'] = []; // 返信を格納する配列を追加
        $references[$post['id']] = $post;
    }

    // 投稿を親子構造に整理
    foreach ($references as $id => &$post) {
        if ($post['parent_id'] === null) {
            $organized[] = &$post; // 親投稿をルートに追加
        } else {
            $references[$post['parent_id']]['replies'][] = &$post; // 返信を親の'replies'に追加
        }
    }

    return $organized;
}


?>
