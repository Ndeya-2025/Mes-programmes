<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=taches_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Traitement du formulaire
$message = "";

if (isset($_POST['inscrire'])) {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT); // Sécurisé

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $message = "Cet email est déjà utilisé.";
    } else {
        // Insérer l'utilisateur
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
        $stmt->execute([$nom, $email, $mot_de_passe]);
        $message = "Inscription réussie ! Vous pouvez vous connecter.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <style>
        body { font-family: Arial; background: url('img1.jpg'); padding: 20px; }
        form { background: #fff; padding: 40px; border-radius: 5px; width: 300px; margin: auto; margin-top: 100px; }
        input, button { width: 100%; margin-top: 10px; padding: 10px; }
        .message { color: green; margin-top: 10px; text-align: center; }
    </style>
</head>
<body>
    <form method="post">
        <h2>Créer un compte</h2>
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <button type="submit" name="inscrire">S'inscrire</button>
        <br><br>
        <!-- Ici le lien vers la connexion -->
        <div class="link">
            Vous avez déjà un compte ? <a href="connexion.php">Se connecter</a>
        </div>

        <div class="message"><?php echo $message; ?></div>
    </form>
</body>
</html>