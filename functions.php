<?php

// 新着情報を取得する関数
function getNews($pdo, $searchTerm = '') {
    if ($searchTerm) {
        $stmt = $pdo->prepare("SELECT * FROM news WHERE title LIKE :searchTerm ORDER BY created_at DESC");
        $stmt->execute(['searchTerm' => '%' . $searchTerm . '%']);
    } else {
        $stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


require 'db.php';

// データベース接続を使用して投稿を追加
function addPost($pdo, $name, $content, $genre) {
    $stmt = $pdo->prepare("INSERT INTO posts (name, content, genre, parent_id, created_at) VALUES (:name, :content, :genre, NULL, NOW())");
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    $stmt->bindParam(':genre', $genre, PDO::PARAM_STR);
    $stmt->execute();
}

// データベース接続を使用して返信を追加

function replyPost($pdo, $parent_id, $name, $content) {
    // 親投稿のジャンルを取得
    $stmt = $pdo->prepare("SELECT genre FROM posts WHERE id = :parent_id");
    $stmt->execute(['parent_id' => $parent_id]);
    $parent = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$parent) {
        throw new Exception("親投稿が見つかりません");
    }

    $genre = $parent['genre'];

    // 返信を挿入
    $stmt = $pdo->prepare("INSERT INTO posts (parent_id, name, content, genre, created_at) VALUES (:parent_id, :name, :content, :genre, NOW())");
    $stmt->execute([
        'parent_id' => $parent_id,
        'name' => $name,
        'content' => $content,
        'genre' => $genre,
    ]);
}


// 投稿一覧を取得する関数
function fetchPosts($pdo) {
    $stmt = $pdo->query("SELECT * FROM posts WHERE parent_id IS NULL ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 投稿をジャンルで絞り込む関数
function fetchPostsByGenre($pdo, $genre) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE genre = :genre AND parent_id IS NULL ORDER BY created_at DESC");
    $stmt->execute(['genre' => $genre]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 投稿を検索する関数
function searchPosts($pdo, $keyword) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE content LIKE :keyword AND parent_id IS NULL ORDER BY created_at DESC");
    $stmt->execute(['keyword' => "%$keyword%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 返信を取得する関数
function fetchReplies($pdo, $parent_id) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE parent_id = :parent_id ORDER BY created_at ASC");
    $stmt->execute(['parent_id' => $parent_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 投稿を親子構造で整理する関数
function organizePosts($posts) {
    $organized = [];
    $references = [];

    foreach ($posts as $post) {
        $post['replies'] = [];
        $references[$post['id']] = $post;
    }

    foreach ($references as $id => &$post) {
        if ($post['parent_id'] === null) {
            $organized[] = &$post;
        } else {
            $references[$post['parent_id']]['replies'][] = &$post;
        }
    }

    return $organized;
}


// 投稿を表示する関数
if (!function_exists('displayPosts')) {
    function displayPosts($posts, $pdo) {
        foreach ($posts as $post): ?>
            <div class="post">
                <strong><?= htmlspecialchars($post['name']) ?></strong>
                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                <small>投稿日: <?= htmlspecialchars($post['created_at']) ?></small>
                <button onclick="toggleReplyForm(<?= $post['id'] ?>)">返信</button>
                <div id="reply-form-<?= $post['id'] ?>" class="reply-form" style="display: none;">
                    <form method="POST" action="reply.php">
                        <input type="hidden" name="parent_id" value="<?= $post['id'] ?>">
                        <textarea name="content" placeholder="返信内容を入力"></textarea>
                        <button type="submit">返信</button>
                    </form>
                </div>

                <?php 
                // 返信があれば表示
                if (!empty($post['replies'])): ?>
                    <div class="replies">
                        <?php displayPosts($post['replies'], $pdo); ?>
                    </div>
                <?php else: ?>
                    <?php 
                    // 返信をデータベースから取得
                    $replies = fetchReplies($pdo, $post['id']);
                    foreach ($replies as $reply): ?>
                        <div class="reply">
                            <strong><?= htmlspecialchars($reply['name']) ?></strong>
                            <p><?= nl2br(htmlspecialchars($reply['content'])) ?></p>
                            <small>投稿日: <?= htmlspecialchars($reply['created_at']) ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach;
    }
}



?>



