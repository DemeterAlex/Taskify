<?php
require __DIR__ . '/../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle   = 'Taskify';
$noAuthCheck = true;
require __DIR__ . '/../templates/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'add') {
        $title    = trim($_POST['title']);
        $due_date = $_POST['due_date'];
        if ($title && $due_date) {
            $stmt = $pdo->prepare('INSERT INTO todos (user_id,title,due_date) VALUES (?,?,?)');
            $stmt->execute([$_SESSION['user_id'], $title, $due_date]);
        }
    }
    if ($_POST['action'] === 'complete') {
        $stmt = $pdo->prepare('DELETE FROM todos WHERE id=? AND user_id=?');
        $stmt->execute([$_POST['id'], $_SESSION['user_id']]);
    }
    header('Location: todo.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id,title,due_date FROM todos WHERE user_id=? ORDER BY due_date ASC');
$stmt->execute([$_SESSION['user_id']]);
$todos = $stmt->fetchAll();
?>

<div class="card todo-card">
  <a href="logout.php" id="logoutBtn">Odhlásit se</a>
  <h2>Moje To-Do</h2>
  <button id="addBtn" class="add-btn">+</button>

  <form id="addForm" class="add-form" method="post">
    <input type="hidden" name="action" value="add">
    <label>Název</label>
    <input type="text" name="title" required>
    <label>Do kdy</label>
    <input type="date" name="due_date" required>
    <button type="submit">Přidat</button>
  </form>

  <ul class="todo-list">
    <?php foreach ($todos as $t): ?>
      <?php
        $due      = new DateTime($t['due_date']);
        $now      = new DateTime();
        $days     = (int)$now->diff($due)->format('%r%a');
        $priority = $days < 1   ? 'vysoká'
                  : ($days <= 3 ? 'střední' : 'nízká');
        $class    = $days < 1   ? 'high'
                  : ($days <= 3 ? 'medium' : 'low');
      ?>
      <li>
        <span class="todo-title"><?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?></span>
        <span class="todo-date"><?= htmlspecialchars($t['due_date'], ENT_QUOTES, 'UTF-8') ?></span>
        <span class="todo-priority <?= $class ?>"><?= $priority ?></span>
        <form method="post" class="complete-form">
          <input type="hidden" name="action" value="complete">
          <input type="hidden" name="id"     value="<?= $t['id'] ?>">
          <button type="submit" class="complete-btn">&#10003;</button>
        </form>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

<script>
  document.getElementById('addForm').style.display = 'none';
  document.getElementById('addBtn').addEventListener('click', function() {
    var f = document.getElementById('addForm');
    f.style.display = (f.style.display === 'block' ? 'none' : 'block');
  });
</script>

</body>
</html>
