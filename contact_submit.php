<?php
require 'db.php';

// POSTリクエストの場合にお問い合わせ内容を処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    // 必要なバリデーションや保存処理を追加することをお勧めします

    // お問い合わせ内容をデータベースに保存する処理
    $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message, created_at) VALUES (:name, :email, :message, NOW())");
    $stmt->execute(['name' => $name, 'email' => $email, 'message' => $message]);

    header('Location: thank_you.php'); // 送信後のページにリダイレクト
    exit;
}
?>
