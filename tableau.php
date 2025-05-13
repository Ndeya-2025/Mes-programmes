<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: connexion.php");
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=taches_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if (isset($_GET['msg']) && $_GET['msg'] === 'modifiee') {
    $message = "Tâche modifiée avec succès.";
}
if (isset($_GET['msg']) && $_GET['msg'] === 'supprimee') {
    $message = "Tâche supprimée avec succès.";
}

$message = "";

// Ajouter une tâche
if (isset($_POST['ajouter'])) {
    $titre = $_POST['titre'];
    $stmt = $pdo->prepare("INSERT INTO taches (utilisateur_id, titre) VALUES (?, ?)");
    $stmt->execute([$_SESSION['utilisateur_id'], $titre]);
    $message = "Tâche ajoutée avec succès.";
}

// Supprimer la tâche
if (isset($_POST['action']) && $_POST['action'] === 'supprimer' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare("DELETE FROM taches WHERE id = ?");
    $stmt->execute([$id]);
    // $message = "Tâche supprimée avec succès.";
    header("Location: tableau.php?msg=supprimee");
exit;
}

// Préparer une tâche à modifier
if (isset($_GET['modifier'])) {
    $id = intval($_GET['modifier']);
    $stmt = $pdo->prepare("SELECT * FROM taches WHERE id = ?");
    $stmt->execute([$id]);
    $tache_a_modifier = $stmt->fetch();
}

// Valider une modification
if (isset($_POST['action']) && $_POST['action'] === 'valider_modification' && isset($_POST['id']) && isset($_POST['nouveau_titre'])) {
    $id = intval($_POST['id']);
    $nouveauTitre = $_POST['nouveau_titre'];
    $stmt = $pdo->prepare("UPDATE taches SET titre = ? WHERE id = ? AND utilisateur_id = ?");
    $stmt->execute([$nouveauTitre, $id, $_SESSION['utilisateur_id']]);
    // $message = "Tâche modifiée avec succès.";
    header("Location: tableau.php?msg=modifiee");
exit;
}
// Marquer comme fait / non fait
if (isset($_GET['check'])) {
    $id = $_GET['check'];
    $stmt = $pdo->prepare("UPDATE taches SET est_fait = NOT est_fait WHERE id = ? AND utilisateur_id = ?");
    $stmt->execute([$id, $_SESSION['utilisateur_id']]);
    header("Location: tableau.php");
    exit;
}

if (isset($_GET['msg']) && $_GET['msg'] === 'supprimée') {
    $message = "Tâche supprimée avec succès.";
}

// Récupérer les tâches
$stmt = $pdo->prepare("SELECT * FROM taches WHERE utilisateur_id = ? ORDER BY date_creation DESC");
$stmt->execute([$_SESSION['utilisateur_id']]);
$taches = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
    <style>
        body { font-family: Arial; padding: 20px; background: url('img3.jpg'); }
        .container { background: #fff; padding: 20px; border-radius: 8px; max-width: 700px; margin: auto; }
        h2 { text-align: center; }
        form { margin-bottom: 20px; }
        input[type="text"] { width: 80%; padding: 10px; }
        button { padding: 10px 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        .actions form { display: inline-block; }
        .logout { text-align: right; margin-bottom: 10px; }
        .logout a { color: red; text-decoration: none; }
        .message { text-align: center; color: green; margin-bottom: 10px; }

        .fait { color: green; text-decoration: line-through; }
        .action-buttons {
  display: inline-flex;
  gap: 8px; /* espace entre les boutons */
}

.action-btn {
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}

.edit-btn {
  background-color: #4CAF50;
  color: white;
}

.delete-btn {
  background-color: #f44336;
  color: white;
}
table {
  width: 100%;
  border-collapse: collapse;
  background-color:rgb(240, 197, 231);
  color:rgb(13, 12, 12);
  border-radius: 8px;
  overflow: hidden;
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #333;
}

th {
  background-color:rgb(166, 67, 125);
}

tr:hover {
  background-color: rgb(166, 67, 125);
}
    </style>
</head>
<body>
    <div class="container">
        <div class="logout">
            <a href="deconnexion.php">Déconnexion</a>
        </div>

        <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['nom']); ?> !</h2>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="titre" placeholder="Nouvelle tâche" required>
            <button type="submit" name="ajouter">Ajouter</button>
        </form>

        <?php if (count($taches) > 0): ?>
            <table>
                <tr>
                    <th>Fait</th>
                    <th>Tâche</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($taches as $t): ?>
                    <tr>
                        <td>
                            <a href="tableau.php?check=<?php echo $t['id']; ?>">
                                <input type="checkbox" <?php if ($t['est_fait']) echo "checked"; ?>>
                            </a>
                        </td>
                        <td class="<?php echo $t['est_fait'] ? 'fait' : ''; ?>">
                            <?php if (isset($_GET['modifier']) && $_GET['modifier'] == $t['id']): ?>
                                <form method="post" style="display:flex; gap:10px;">
    <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
    <input type="hidden" name="action" value="valider_modification">
    <input type="text" name="nouveau_titre" value="<?php echo htmlspecialchars($t['titre']); ?>" required>
    <button type="submit">Valider</button>
</form>                            <?php else: ?>
                                <?php echo htmlspecialchars($t['titre']); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $t['date_creation']; ?></td>
                        <td>
  <div class="action-buttons">
    <!-- Formulaire pour MODIFIER -->
             <form action="tableau.php" method="get" style="display: inline;">
  <input type="hidden" name="modifier" value="<?php echo $t['id']; ?>">
  <button class="action-btn edit-btn" type="submit">Modifier</button>
</form>

    <!-- Formulaire pour SUPPRIMER -->
    <form action="tableau.php" method="post" style="display: inline;" onsubmit="return confirm('Supprimer cette tâche ?');">
  <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
  <input type="hidden" name="action" value="supprimer">
  <button class="action-btn delete-btn" type="submit">Supprimer</button>
</form>  </div>
</td>                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Aucune tâche enregistrée.</p>
        <?php endif; ?>
    </div>
</body>
</html>