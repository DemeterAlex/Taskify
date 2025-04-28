<?php
require __DIR__ . '/../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: todo.php');
    exit;
}

$noAuthCheck = true;
$pageTitle   = 'Taskify | Přihlášení';
require __DIR__ . '/../templates/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email && $password) {
        $stmt = $pdo->prepare('SELECT id,password FROM users WHERE email=?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password,$user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            header('Location: todo.php');
            exit;
        } else {
            $error = 'Neplatný e-mail nebo heslo.';
        }
    } else {
        $error = 'Vyplň prosím všechna pole.';
    }
}
?>

<div class="card">
  <h2>Přihlášení</h2>

  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post" class="auth-form">
    <label for="email">E-mail</label>
    <input id="email" type="email" name="email" required>

    <label for="password">Heslo</label>
    <input id="password" type="password" name="password" required>

    <button type="submit">Přihlásit se</button>
  </form>

  <a href="register.php" class="auth-link">Nemáte účet? Zaregistrujte se</a>
</div>

</body>
</html>
