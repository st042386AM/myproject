<?php
// index.php
require 'db.php';
require 'functions.php';

// 投稿一覧を取得
$posts = [];

// 検索キーワードを取得
$keyword = $_GET['search'] ?? '';
if ($keyword) {
    $posts = searchPosts($pdo, $keyword);
} else {
    $selectedGenre = $_GET['genre'] ?? '';
    if ($selectedGenre) {
        $posts = fetchPostsByGenre($pdo, $selectedGenre);
    } else {
        $posts = fetchPosts($pdo);
    }
}

// 投稿を親子構造で整理
$organizedPosts = organizePosts($posts);

// 整理された投稿をHTMLに表示
function displayPosts($posts) {
    foreach ($posts as $post) {
        echo "<div class='post'>";
        echo "<p><strong>{$post['name']}</strong>: {$post['content']}</p>";
        echo "<small>投稿日: {$post['created_at']}</small>";
        echo "<div class='replies'>";
        displayPosts($post['replies']); // 返信を再帰的に表示
        echo "</div>";
        echo "</div>";
    }
}

?><!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>掲示板</title>
</head>
<body>
<header>
    <h1>掲示板</h1>
</header>
<nav>
    <a href="#home">ホーム</a>
    <a href="#search">検索</a>
    <a href="#news">新着情報</a>
    <a href="#post">投稿</a>
    <a href="#contact">お問い合わせ</a>
    <a href="#view">投稿一覧</a>
</nav>
<main>
    <section id="home">
        <h2>ようこそ</h2>
        <p>こちらは掲示板システムです。情報の投稿、検索、新着情報の確認、お問い合わせが可能です。</p>
    </section>
    <section id="search">
        <h2>検索フォーム</h2>
        <form method="GET" action="">
            <input type="text" name="search" placeholder="検索" value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit">検索</button>
        </form>
    </section>
    <section id="news">
        <h2>新着情報投稿フォーム</h2>
        <form method="POST" action="submit_news.php">  //submit_news.phpは後で作る予定
            <select name="genre">
                <option value="一般">一般</option>
                <option value="アニメ">アニメ</option>
                <option value="ゲーム">ゲーム</option>
                <option value="スポーツ">スポーツ</option>
            </select>
            <input type="text" name="title" placeholder="タイトル" required>
            <button type="submit">新着情報を追加</button>
        </form>
    </section>
    <section id="post">
        <h2>投稿フォーム</h2>
        <form method="POST" action="submit.php">
            <select name="genre">
                <option value="一般">一般</option>
                <option value="アニメ">アニメ</option>
                <option value="ゲーム">ゲーム</option>
                <option value="スポーツ">スポーツ</option>
            </select>
            <input type="text" name="name" placeholder="名前" required>
            <textarea name="content" placeholder="内容" required></textarea>
            <button type="submit">投稿</button>
        </form>
    </section>
    <section id="contact">
        <h2>お問い合わせフォーム</h2>
        <form method="POST" action="contact_submit.php">
            <input type="text" name="name" placeholder="名前" required>
            <input type="email" name="email" placeholder="メールアドレス" required>
            <textarea name="message" placeholder="メッセージ" required></textarea>
            <button type="submit">送信</button>
        </form>
    </section>
    <section id="view">
        <h2>投稿一覧</h2>
        <form method="GET" action="">
            <select name="genre" onchange="this.form.submit()">
                <option value="" <?= $selectedGenre === '' ? 'selected' : '' ?>>すべて</option>
                <option value="一般" <?= $selectedGenre === '一般' ? 'selected' : '' ?>>一般</option>
                <option value="アニメ" <?= $selectedGenre === 'アニメ' ? 'selected' : '' ?>>アニメ</option>
                <option value="ゲーム" <?= $selectedGenre === 'ゲーム' ? 'selected' : '' ?>>ゲーム</option>
                <option value="スポーツ" <?= $selectedGenre === 'スポーツ' ? 'selected' : '' ?>>スポーツ</option>
            </select>
        </form>
        <?php if (!empty($organizedPosts)): ?>
            <?php foreach ($organizedPosts as $post): ?>
                <div class="post">
                    <strong><?= htmlspecialchars($post['name']) ?></strong>
                    <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                    <small>投稿日: <?= htmlspecialchars($post['created_at']) ?></small>
                    <div class="reply-form">
                        <form method="POST" action="reply.php">
                            <input type="hidden" name="parent_id" value="<?= $post['id'] ?>">
                            <input type="text" name="name" placeholder="返信者の名前">
                            <textarea name="content" placeholder="返信内容"></textarea>
                            <button type="submit">返信</button>
                        </form>
                    </div>
                    <?php if (!empty($post['replies'])): ?>
                        <div class="reply">
                            <?php foreach ($post['replies'] as $reply): ?>
                                <div>
                                    <strong><?= htmlspecialchars($reply['name']) ?></strong>
                                    <p><?= nl2br(htmlspecialchars($reply['content'])) ?></p>
                                    <small>投稿日: <?= htmlspecialchars($reply['created_at']) ?></small>
                                </div>
                                <hr>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>該当する投稿はありません。</p>
        <?php endif; ?>
    </section>
</main>
<footer>
    <p>&copy; 2024 掲示板サイト</p>
</footer>
</body>
</html>
