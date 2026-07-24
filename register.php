<?php
require_once __DIR__ . '/includes/functions.php';

// Already have an account? Nothing to do here.
if (is_logged_in()) {
    header('Location: manage.php');
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $error = 'Your session expired. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';

        if ($password !== $confirm) {
            $error = 'The two passwords do not match.';
        } else {
            $result = auth_register($username, $password);
            if ($result === true) {
                header('Location: manage.php');
                exit;
            }
            $error = $result;
        }
    }
}

$title = 'Create account';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head">
  <h1>Create your account</h1>
  <p>Save the drugs you have been trying so you can get back to them any time, on any device.</p>
</div>

<?php if ($error): ?>
  <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<form class="form-card" method="POST" action="register.php" style="max-width:420px">
  <?= csrf_field() ?>
  <div class="field">
    <label for="username">Username</label>
    <input class="input" type="text" id="username" name="username" value="<?= e($username) ?>" required autofocus>
    <span class="hint">3-30 characters: letters, numbers, . _ -</span>
  </div>
  <div class="field">
    <label for="password">Password</label>
    <input class="input" type="password" id="password" name="password" required>
    <span class="hint">At least 8 characters. There is no reset, so keep it safe.</span>
  </div>
  <div class="field">
    <label for="confirm">Confirm password</label>
    <input class="input" type="password" id="confirm" name="confirm" required>
  </div>
  <div class="form-actions">
    <button class="btn btn-primary" type="submit"><i class="fas fa-user-plus"></i> Create account</button>
    <a class="btn btn-ghost" href="login.php">I already have one</a>
  </div>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
