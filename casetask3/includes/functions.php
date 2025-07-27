<?php
require_once 'config.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getPostById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getPostsByUser($userId, $currentUserId = null) {
    global $pdo;
    
    if ($userId == $currentUserId) {
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? AND visibility != 'private' ORDER BY created_at DESC");
        $stmt->execute([$userId]);
    }
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFeedPosts($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT p.*, u.username 
        FROM posts p
        JOIN users u ON p.user_id = u.id
        JOIN subscriptions s ON p.user_id = s.subscribed_to_id
        WHERE s.subscriber_id = ? AND p.visibility = 'public'
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$userId]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function isSubscribed($subscriberId, $subscribedToId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM subscriptions WHERE subscriber_id = ? AND subscribed_to_id = ?");
    $stmt->execute([$subscriberId, $subscribedToId]);
    return $stmt->fetch() ? true : false;
}

function getPostTags($postId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT t.id, t.name 
        FROM tags t
        JOIN post_tags pt ON t.id = pt.tag_id
        WHERE pt.post_id = ?
    ");
    $stmt->execute([$postId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCommentsForPost($postId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT c.*, u.username 
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.post_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$postId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllTags() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM tags ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPostsByTag($tagId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT p.*, u.username 
        FROM posts p
        JOIN users u ON p.user_id = u.id
        JOIN post_tags pt ON p.id = pt.post_id
        WHERE pt.tag_id = ? AND p.visibility = 'public'
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$tagId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


?>