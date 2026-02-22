<?php
session_start();

/* =======================
   CONFIG BASE DE DONNÉES
   ======================= */
$host = "localhost";
$dbname = "L'Alliance CFE-UNSA Énergies 2026";
$user = "root";
$pass = "";

$message = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $e) {
    die("Erreur de connexion à la base de données");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!empty($_POST['prenom']) && !empty($_POST['nom']) && !empty($_POST['email']) && !empty($_POST['entreprise'])) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO participants (prenom, nom, email, entreprise)
                VALUES (:prenom, :nom, :email, :entreprise)
            ");
           $stmt->execute([
    ':prenom' => trim($_POST['prenom']),
    ':nom' => trim($_POST['nom']),
    ':email' => trim($_POST['email']),
    ':entreprise' => trim($_POST['entreprise'])
]);

$_SESSION['inscrit'] = true;
$_SESSION['user_id'] = $pdo->lastInsertId();
$_SESSION['username'] = trim($_POST['prenom']) . ' ' . trim($_POST['nom']);
header("Location: jeu.php");
exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "⚠️ Cette adresse email est déjà inscrite.";
            } else {
                $message = "❌ Erreur lors de l'inscription.";
            }
        }
    } else {
        $message = "⚠️ Tous les champs sont obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Inscription – Jeu Alliance</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
:root {
  --bleu: #00d4ff;
  --vert: #00ff88;
  --fond: #0f0f0f;
  --texte: #fff;
  --container-bg: #111;
  --input-bg: #222;
  --input-border: #444;
}
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
body { background: linear-gradient(135deg,#0a0a0a,#1a1a1a); display:flex; justify-content:center; align-items:center; min-height:100vh; color:var(--texte);}
.container { background: var(--container-bg); padding:50px 40px; border-radius:20px; box-shadow:0 25px 50px rgba(0,255,136,0.2); width:100%; max-width:480px; text-align:center;}
.header h1 { font-size:2.5rem; background:linear-gradient(90deg,var(--bleu),var(--vert)); -webkit-background-clip:text; -webkit-text-fill-color:transparent;}
.header span{ font-weight:bold;}
.header p { margin-top:10px; font-size:1rem; color:#aaa;}
input { width:100%; padding:16px; margin-bottom:18px; border-radius:12px; border:1px solid var(--input-border); background:var(--input-bg); color:var(--texte); font-size:1rem; transition:0.3s ease,box-shadow 0.3s ease;}
input:focus { border-color: var(--vert); box-shadow:0 0 10px var(--vert); outline:none;}
button { width:100%; padding:16px; background:linear-gradient(135deg,var(--bleu),var(--vert)); color:#000; border:none; border-radius:14px; font-size:1.1rem; font-weight:bold; cursor:pointer; transition:0.3s ease,box-shadow 0.3s ease,transform 0.2s ease;}
button:hover { transform: scale(1.05); box-shadow:0 8px 25px rgba(0,255,136,0.6);}
.message { margin-bottom:18px; font-weight:bold; color:var(--vert);}
.footer { margin-top:25px; font-size:0.9rem; color:#777;}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h1>CFE <span>UNSA</span></h1>
    <p>Énergies – Jeu découverte</p>
  </div>

  <?php if($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="POST">
    <input type="text" name="prenom" placeholder="Prénom" required>
    <input type="text" name="nom" placeholder="Nom" required>
    <input type="email" name="email" placeholder="Adresse mail" required>
    <input type="text" name="entreprise" placeholder="Entreprise" required>
    <button type="submit">Je participe</button>
  </form>

  <div class="footer">Données utilisées uniquement dans le cadre du jeu</div>
</div>
</body>
</html>
