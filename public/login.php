<?php include '../includes/header.php'; ?>

<h2>Connexion</h2>

<form action="traitement_login.php" method="POST">
<input type="email" name="email" placeholder="Email">
<input type="password" name="password" placeholder="Mot de passe">
<button type="submit">Se connecter</button>
<a href="register.php">Créer un compte</a>
</form>

<?php include '../includes/footer.php'; ?>

