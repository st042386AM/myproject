<?php
// 必要なファイルを読み込む
require 'db.php';
require 'functions.php';

// 投稿一覧を取得
$posts = fetchPosts($pdo);

// 検索キーワードを取得
$keyword = $_GET['search'] ?? '';
if ($keyword) {
    $posts = searchPosts($pdo, $keyword); // キーワードが指定されていれば検索結果を取得
}

// 投稿を親子構造で整理
$organizedPosts = [];
foreach ($posts as $post) {
    if ($post['parent_id'] === null) {
        $organizedPosts[$post['id']] = $post;
        $organizedPosts[$post['id']]['replies'] = [];
    } else {
        $organizedPosts[$post['parent_id']]['replies'][] = $post;
    }
}

    // 親投稿を逆順で整理することで、最新の親投稿が上に来るようにする
$organizedPosts = array_reverse($organizedPosts);
?>

<link rel="stylesheet" href="styles.css">

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>掲示板</title>
</head>
<body>
    <h1>掲示板</h1>

    <nav>
        <a href="#home">ホーム</a>
        <a href="#search">検索</a>
        <a href="#news">新着情報</a>
	    <a href="#post">投稿</a>
        <a href="#contact">お問い合わせ</a>
	    <a href="#view">投稿一覧</a>
    </nav>

     <!-- ホームセクション -->
     <section id="home">
            <h2>ようこそ</h2>
            <p>こちらは掲示板システムです。情報の投稿、検索、新着情報の確認、お問い合わせが可能です。</p>
        </section>


    <!-- 検索フォーム -->
     <section id="search">
    <form method="GET" action="">
        <input type="text" name="search" placeholder="検索" value="<?= htmlspecialchars($keyword) ?>">
        <button type="submit">検索</button>
    </form>
    </section>

    <!-- 新着情報の投稿フォーム -->
    <section id="news">
    <h2>新着情報投稿フォーム</h2>
    <form method="POST" action="submit_news.php">
        <input type="text" name="title" placeholder="タイトル" required>
        <select name="genre">
            <option value="一般">一般</option>
            <option value="アニメ">アニメ</option>
            <option value="ゲーム">ゲーム</option>
            <option value="スポーツ">スポーツ</option>
        </select>
        <button type="submit">新着情報を追加</button>
    </form>
    </section>

    <!-- 投稿フォーム -->
    <section id="post">
    <form method="POST" action="submit.php">
        <input type="text" name="name" placeholder="名前" required>
        <textarea name="content" placeholder="内容" required></textarea>
        <select name="genre">
            <option value="一般">一般</option>
            <option value="アニメ">アニメ</option>
            <option value="ゲーム">ゲーム</option>
            <option value="スポーツ">スポーツ</option>
        </select>
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

    <!-- 投稿一覧の表示 -->
    <section id="view">
    <h2>投稿一覧</h2>
<?php foreach ($organizedPosts as $post): ?>
    <div>
        <strong><?= htmlspecialchars($post['name']) ?></strong>
        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        <small>投稿日: <?= htmlspecialchars($post['created_at']) ?></small>

        <!-- 返信フォーム -->
        <form method="POST" action="reply.php">
            <input type="hidden" name="parent_id" value="<?= $post['id'] ?>">
            <input type="text" name="name" placeholder="返信者の名前">
            <textarea name="content" placeholder="返信内容"></textarea>
            <button type="submit">返信</button>
        </form>
        </section>

        <!-- 返信の表示 -->
        <?php if (!empty($post['replies'])): ?>
            <div style="margin-top: 20px; padding-left: 20px;">
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
    <hr>
<?php endforeach; ?>


</body>
</html>
