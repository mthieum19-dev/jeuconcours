<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){
    echo json_encode(['status'=>'error','message'=>'Utilisateur non connecté']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if(!isset($data['score'])){
    echo json_encode(['status'=>'error','message'=>'Score manquant']);
    exit;
}

$host = "localhost";
$db = "jeu_alliance";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("INSERT INTO scores (user_id, username, score) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $_SESSION['username'], $data['score']]);

    echo json_encode(['status'=>'success']);
} catch (PDOException $e) {
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
?>
