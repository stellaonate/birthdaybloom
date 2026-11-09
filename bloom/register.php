<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
if (!empty($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }
$errors = [];
$name = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['full_name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');
    if (!valid_csrf()) $errors[] = 'Invalid request. Refresh the page and try again.';
    if (mb_strlen($name) < 2 || mb_strlen($name) > 100) $errors[] = 'Name must be 2–100 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';
    if (!$errors) {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') {
                $errors[] = 'This email address is already registered.';
            } else {
                error_log($ex->getMessage());
                $errors[] = 'Could not create your account. Please try again.';
            }
        }
    }
}
$pageTitle = 'Register'; $extraCss = 'auth.css'; require 'includes/header.php';
?>
<section class="auth-section"><div class="auth-card">
  <div class="auth-heading"><span class="auth-emoji">🎁</span><h1>Create your account</h1><p>Start keeping every special day close.</p></div>
  <?php if ($errors): ?><div class="notice error-notice" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <form id="registerForm" method="post" action="register.php" novalidate>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="form-group"><label for="fullName">Full name</label><input id="fullName" name="full_name" type="text" value="<?= e($name) ?>" maxlength="100" autocomplete="name" required><small class="field-error" id="nameError"></small></div>
    <div class="form-group"><label for="email">Email address</label><input id="email" name="email" type="email" value="<?= e($email) ?>" maxlength="150" autocomplete="email" required><small class="field-error" id="emailError"></small></div>
    <div class="form-group"><label for="password">Password</label><input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required><small class="field-error" id="passwordError"></small></div>
    <div class="form-group"><label for="confirmPassword">Confirm password</label><input id="confirmPassword" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required><small class="field-error" id="confirmError"></small></div>
    <button class="btn btn-full" type="submit">Create Account</button><p class="form-message" id="formMessage" role="status" aria-live="polite"></p>
  </form><p class="auth-bottom">Already have an account? <a href="login.php">Log in</a></p>
</div></section>
<script src="assets/js/validation.js" defer></script>
<?php require 'includes/footer.php'; ?>
