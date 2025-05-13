<?php
session_start();

// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=taches_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Traitement du formulaire de connexion
$message = "";

if (isset($_POST['connecter'])) {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        $_SESSION['utilisateur_id'] = $utilisateur['id'];
        $_SESSION['nom'] = $utilisateur['nom'];
        header("Location: tableau.php"); // Redirige vers la page principale
        exit;
    } else {
        $message = "Identifiants incorrects.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <style>
        body { font-family: Arial; background: url('img2.jpg'); padding: 20px; }
        form { background: #fff; padding: 40px; border-radius: 5px; width: 300px; margin: auto;margin-top: 100px; }
        input, button { width: 100%; margin-top: 10px; padding: 10px; }
        .message { color: red; margin-top: 10px; text-align: center; }
    </style>
</head>
<body>
    <form method="post">
        <h2>Connexion</h2>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <button type="submit" name="connecter">Se connecter</button>
        <div class="link">
    Vous n'avez pas encore de compte ? <a href="inscription.php">S'inscrire</a>
</div>
        <div class="message"><?php echo $message; ?></div>
    </form>
</body>
</html>