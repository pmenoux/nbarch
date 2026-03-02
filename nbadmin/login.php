<?php
// Page de connexion (pas de layout admin)
$error = get_flash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/nbadmin/css/admin.css">
</head>
<body class="login-body">
<div class="login-box">
    <h1 class="login-logo">NB.ARCH</h1>
    <p class="login-sub">Administration</p>

    <?php if ($error): ?>
    <div class="flash flash-<?= $error['type'] ?>"><?= e($error['msg']) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= APP_URL ?>/actions/login.php">
        <?= csrf_field() ?>
        <label for="login">Identifiant</label>
        <input type="text" id="login" name="login" required autofocus>

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Se connecter</button>
    </form>
</div>
</body>
</html>
