<?php
require 'db.php';
require 'functions.php';



// パラメータ取得
$keyword = $_GET['search'] ?? '';
$selectedGenre = $_GET['genre'] ?? '';

// 新着情報を取得
$news = getNews($pdo, $keyword, $selectedGenre);



// 投稿データの取得
if (!empty($keyword)) {
    $posts = searchPosts($pdo, $keyword);
} elseif (!empty($selectedGenre)) {
    $posts = fetchPostsByGenre($pdo, $selectedGenre);
} else {
    $posts = fetchPosts($pdo);
}

// 投稿を親子構造で整理
$organizedPosts = organizePosts($posts);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>掲示板</title>
    <script>
        function toggleReplyForm(postId) {
            const replyForm = document.getElementById(`reply-form-${postId}`);
            replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
        }
    </script>
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

     <!-- ホームセクション -->
     <section id="home">
        <h2>ようこそ</h2>
        <p>こちらは掲示板システムです。情報の投稿、検索、新着情報の確認、お問い合わせが可能です。</p>
    </section>


    <!-- 検索フォーム -->
    <section id="search">
        <h2>検索フォーム</h2>
        <form method="GET" action="">
            <input type="text" name="search" placeholder="検索" value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit">検索</button>
        </form>
    </section>

    <!-- 新着情報表示セクション -->
    <!-- <section>
        <h2>新着情報</h2>
        <?php if (!empty($news)): ?>
            <ul>
                <?php foreach ($news as $item): ?>
                    <li>
                        <strong><?= htmlspecialchars($item['title']) ?></strong>
                        <p>投稿日時: <?= htmlspecialchars($item['created_at']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>新着情報はありません。</p>
        <?php endif; ?>
    </section> -->

    
    <section id="news-list">
    <h2>新着情報</h2>
    <form method="GET" action="">
        <select name="genre" onchange="this.form.submit()">
            <option value="">全て</option>
            <option value="一般" <?= $selectedGenre === '一般' ? 'selected' : '' ?>>一般</option>
            <option value="アニメ" <?= $selectedGenre === 'アニメ' ? 'selected' : '' ?>>アニメ</option>
            <option value="ゲーム" <?= $selectedGenre === 'ゲーム' ? 'selected' : '' ?>>ゲーム</option>
            <option value="スポーツ" <?= $selectedGenre === 'スポーツ' ? 'selected' : '' ?>>スポーツ</option>
        </select>
    </form>

    <?php if (!empty($news)): ?>
        <ul>
            <?php foreach ($news as $item): ?>
                <li>
                    <strong><?= htmlspecialchars($item['title']) ?></strong>
                    <p>ジャンル: <?= htmlspecialchars($item['genre']) ?></p>
                    <p>投稿日時: <?= htmlspecialchars($item['created_at']) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>該当する新着情報はありません。</p>
    <?php endif; ?>
</section>




    <!-- 新着情報投稿フォーム -->
    <section id="news">
        <h2>新着情報投稿フォーム</h2>
        <form method="POST" action="submit_news.php">  <!--submit_news.phpは後で作る予定-->
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

     <!-- 投稿フォーム -->
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

    <!-- お問い合わせフォーム -->
    <section id="contact">
        <h2>お問い合わせフォーム</h2>
        <form method="POST" action="contact_submit.php">
            <input type="text" name="name" placeholder="名前" required>
            <input type="email" name="email" placeholder="メールアドレス" required>
            <textarea name="message" placeholder="メッセージ" required></textarea>
            <button type="submit">送信</button>
        </form>
    </section>



    <!-- 投稿一覧 -->
    <section id="view">
        <h2>投稿一覧</h2>
        <form method="GET" action="">
            <select name="genre" onchange="this.form.submit()">
                <option value="">全て</option>
                <option value="一般" <?= $selectedGenre === '一般' ? 'selected' : '' ?>>一般</option>
                <option value="アニメ" <?= $selectedGenre === 'アニメ' ? 'selected' : '' ?>>アニメ</option>
                <option value="ゲーム" <?= $selectedGenre === 'ゲーム' ? 'selected' : '' ?>>ゲーム</option>
                <option value="スポーツ" <?= $selectedGenre === 'スポーツ' ? 'selected' : '' ?>>スポーツ</option>
            </select>
        </form>
        <?php if (!empty($organizedPosts)): ?>
            <?php displayPosts($organizedPosts, $pdo); ?>
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

