<?php
require __DIR__ . '/../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: todo.php');
    exit;
}

$noAuthCheck = true;
$pageTitle   = 'Taskify | Registrace';
require __DIR__ . '/../templates/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if ($username && $email && $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO users (username,email,password) VALUES (?,?,?)');
        try {
            $stmt->execute([$username,$email,$hash]);
            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Uživatel nebo e-mail již existuje.';
        }
    } else {
        $error = 'Vyplň prosím všechna pole.';
    }
}
?>

<div class="card">
  <h2>Registrace</h2>

  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post" class="auth-form">
    <label for="username">Uživatelské jméno</label>
    <input id="username" type="text" name="username" required>

    <label for="email">E-mail</label>
    <input id="email" type="email" name="email" required>

    <label for="password">Heslo</label>
    <input id="password" type="password" name="password" required>

    <button type="submit">Registrovat se</button>
  </form>

  <a href="login.php" class="auth-link">Máte účet? Přihlaste se</a>
</div>

</body>
</html>
