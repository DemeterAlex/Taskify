<?php
session_start();
require __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'add') {
        $stmt = $pdo->prepare('INSERT INTO todos (user_id, title, due_date) VALUES (?, ?, ?)');
        $stmt->execute([
            $_SESSION['user_id'],
            trim($_POST['title']),
            $_POST['due_date']
        ]);
    }
    if ($_POST['action'] === 'complete') {
        $stmt = $pdo->prepare('DELETE FROM todos WHERE id = ? AND user_id = ?');
        $stmt->execute([
            $_POST['id'],
            $_SESSION['user_id']
        ]);
    }
    header('Location: todo.php');
    exit;
}
$stmt = $pdo->prepare('SELECT id, title, due_date FROM todos WHERE user_id = ? ORDER BY due_date ASC');
$stmt->execute([$_SESSION['user_id']]);
$todos = $stmt->fetchAll();
$pageTitle   = 'Taskify | Moje To-Do';
$noAuthCheck = true;
require __DIR__ . '/../templates/header.php';
?>
<div class="card todo-card">
  <a href="logout.php" id="logoutBtn">Odhlásit se</a>
  <h2>Moje To-Do</h2>
  <button id="addBtn" class="add-btn">+</button>
  <form id="addForm" class="add-form" method="post">
    <input type="hidden" name="action" value="add">
    <label for="title">Název</label>
    <input id="title" type="text" name="title" required>
    <label for="due_date">Do kdy</label>
    <input id="due_date" type="date" name="due_date" required>
    <button type="submit">Přidat</button>
  </form>
  <ul class="todo-list">
    <?php foreach ($todos as $t): ?>
      <?php $due = new DateTime($t['due_date']);
            $days = (int)(new DateTime())->diff($due)->format('%r%a');
            $prio = $days < 1 ? 'Vysoká' : ($days <= 3 ? 'Střední' : 'Nízká');
            $class = strtolower($prio);
      ?>
      <li>
        <span class="todo-title"><?= htmlspecialchars($t['title'], ENT_QUOTES) ?></span>
        <span class="todo-date"><?= htmlspecialchars($t['due_date'], ENT_QUOTES) ?></span>
        <span class="todo-priority <?= $class ?>"><?= $prio ?></span>
        <form method="post" class="complete-form">
          <input type="hidden" name="action" value="complete">
          <input type="hidden" name="id" value="<?= $t['id'] ?>">
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
  if (f.style.display === 'block') {
    f.animate([
      { height: f.scrollHeight + 'px', opacity: 1 },
      { height: '0px', opacity: 0 }
    ], { duration: 300 }).onfinish = function() { f.style.display = 'none'; };
  } else {
    f.style.display = 'block';
    f.animate([
      { height: '0px', opacity: 0 },
      { height: f.scrollHeight + 'px', opacity: 1 }
    ], { duration: 300 });
  }
});
</script>
</body>
</html>
