<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$mode = ($_GET['mode'] ?? 'signup') === 'login' ? 'login' : 'signup';
$error = '';
$pdo = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action   = ($_POST['action'] ?? 'signup') === 'login' ? 'login' : 'signup';
    $mode     = $action;
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($action === 'signup') {
        if ($username === '' || $password === '') {
            $error = 'Please fill in both fields.';
        } elseif (strlen($password) < 4) {
            $error = 'Password must be at least 4 characters.';
        } else {
            $check = $pdo->prepare("SELECT id FROM users WHERE username = :u");
            $check->execute(['u' => $username]);
            if ($check->fetch()) {
                $error = 'That username is already taken.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare(
                    "INSERT INTO users (username, password_hash, role) VALUES (:u, :p, 'contributor')"
                );
                $stmt->execute(['u' => $username, 'p' => $hash]);
                login_user(['id' => $pdo->lastInsertId(), 'username' => $username, 'role' => 'contributor']);
                header('Location: /contributor/add_word.php');
                exit;
            }
        }
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u AND role = 'contributor' LIMIT 1");
        $stmt->execute(['u' => $username]);
        $row = $stmt->fetch();

        if ($row && password_verify($password, $row['password_hash'])) {
            login_user($row);
            header('Location: /contributor/add_word.php');
            exit;
        }
        $error = 'Incorrect username or password.';
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-icon contrib">
      <svg class="icon" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.5 3-6 6.5-6s6.5 2.5 6.5 6"/><path d="M18 8h4M20 6v4"/></svg>
    </div>
    <h3><?= $mode === 'signup' ? 'Join as a contributor' : 'Welcome back' ?></h3>
    <p class="sub"><?= $mode === 'signup' ? 'Create an account to start adding words.' : 'Log in to add words to the dictionary.' ?></p>

    <div class="subtabs">
      <a href="?mode=signup" class="<?= $mode === 'signup' ? 'active' : '' ?>">Sign up</a>
      <a href="?mode=login" class="<?= $mode === 'login' ? 'active' : '' ?>">Log in</a>
    </div>

    <form method="post">
      <input type="hidden" name="action" value="<?= $mode ?>">
      <label class="field-label"><?= $mode === 'signup' ? 'Choose a username' : 'Username' ?></label>
      <input type="text" name="username" placeholder="e.g. juanb" autocomplete="username" required>
      <label class="field-label"><?= $mode === 'signup' ? 'Choose a password' : 'Password' ?></label>
      <input type="password" name="password" placeholder="<?= $mode === 'signup' ? 'At least 4 characters' : '••••••••' ?>" autocomplete="<?= $mode === 'signup' ? 'new-password' : 'current-password' ?>" required>
      <?php if ($error): ?><div class="field-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <button class="btn thread" type="submit"><?= $mode === 'signup' ? 'Create account' : 'Log in' ?></button>
    </form>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
