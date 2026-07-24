<?php
require_once __DIR__ . '/includes/functions.php';

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $error = 'Your session expired. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        if (auth_login($username, $_POST['password'] ?? '')) {
            header('Location: manage.php');
            exit;
        }
        $error = 'Incorrect username or password.';
    }
}

$title = 'Log in';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head">
  <h1>Log in</h1>
  <p>Welcome back. Log in to see your saved drugs.</p>
</div>

<?php if ($error): ?>
  <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<form class="form-card" method="POST" action="login.php" style="max-width:420px">
  <?= csrf_field() ?>
  <div class="field">
    <label for="username">Username</label>
    <input class="input" type="text" id="username" name="username" value="<?= e($username) ?>" required autofocus>
  </div>
  <div class="field">
    <label for="password">Password</label>
    <input class="input" type="password" id="password" name="password" required>
  </div>
  <div class="form-actions">
    <button class="btn btn-primary" type="submit"><i class="fas fa-right-to-bracket"></i> Log in</button>
    <a class="btn btn-ghost" href="register.php">Create account</a>
  </div>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
