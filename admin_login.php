<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $pdo = get_db();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u AND role = 'admin' LIMIT 1");
    $stmt->execute(['u' => $username]);
    $row = $stmt->fetch();

    if ($row && password_verify($password, $row['password_hash'])) {
        login_user($row);
        header('Location: /admin/dashboard.php');
        exit;
    }
    $error = 'Incorrect username or password.';
}

require __DIR__ . '/includes/header.php';
?>

<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-icon admin">
      <svg class="icon" viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
    </div>
    <h3>Admin login</h3>
    <p class="sub">Restricted access — dataset uploads and oversight.</p>

    <form method="post">
      <label class="field-label">Username</label>
      <input type="text" name="username" placeholder="admin" autocomplete="username" required>
      <label class="field-label">Password</label>
      <input type="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
      <?php if ($error): ?><div class="field-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <button class="btn danger" type="submit">Log in</button>
    </form>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
