<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
if (!empty($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }
$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    if (!valid_csrf()) {
        $error = 'Invalid request. Refresh the page and try again.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter a valid email and password.';
    } else {
        $stmt = $pdo->prepare('SELECT id, full_name, password FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            unset($_SESSION['csrf_token']);
            header('Location: dashboard.php');
            exit;
        }
        $error = 'Invalid email or password.';
    }
}
$pageTitle = 'Login'; $extraCss = 'auth.css'; require 'includes/header.php';
?>
<section class="auth-section"><div class="auth-card">
  <div class="auth-heading"><span class="auth-emoji">💖</span><h1>Welcome back!</h1><p>Your next celebration is waiting.</p></div>
  <?php if (isset($_GET['registered'])): ?><div class="notice success-notice">Account created! You can log in now.</div><?php endif; ?>
  <?php if ($error): ?><div class="notice error-notice" role="alert"><?= e($error) ?></div><?php endif; ?>
  <form id="loginForm" method="post" action="login.php" novalidate autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="form-group"><label for="loginEmail">Email address</label><input id="loginEmail" name="email" type="email" value="<?= e($email) ?>" autocomplete="current" required><small class="field-error" id="loginEmailError"></small></div>
    <div class="form-group"><label for="loginPassword">Password</label><input id="loginPassword" name="password" type="password" autocomplete="new-password" required><small class="field-error" id="loginPasswordError"></small></div>
    <button class="btn btn-full" type="submit">Log In</button><p class="form-message" id="loginMessage" role="status" aria-live="polite"></p>
  </form><p class="auth-bottom">New to BirthdayBloom? <a href="register.php">Sign up</a></p>
</div></section>
<script src="assets/js/validation.js" defer></script>
<?php require 'includes/footer.php'; ?>
