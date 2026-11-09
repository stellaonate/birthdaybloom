<?php
require_once 'includes/auth.php'; require_login();
require_once 'includes/db.php'; require_once 'includes/birthday_functions.php';
$errors=[]; $name=''; $date=''; $relationship='Friend'; $notes='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!valid_csrf()) { http_response_code(403); $errors[]='Invalid form token. Refresh and try again.'; }
    [$validation,$name,$date,$relationship,$notes]=validate_birthday($_POST);
    $errors=array_merge($errors,$validation);
    if (!$errors) {
        $stmt=$pdo->prepare('INSERT INTO birthdays (user_id,person_name,birth_date,relationship,notes) VALUES (?,?,?,?,?)');
        $stmt->execute([(int)$_SESSION['user_id'],$name,$date,$relationship,$notes]);
        header('Location: dashboard.php?status=added'); exit;
    }
}
$pageTitle='Add Birthday'; $extraCss='dashboard.css'; require 'includes/header.php';
?>
<section class="dashboard-section"><div class="container"><div class="dashboard-panel form-panel">
<a href="dashboard.php">← Back to dashboard</a><h1>🎂 Add a birthday</h1><p>Save a special date so you can celebrate on time.</p>
<?php if($errors): ?><div class="notice error-notice" role="alert"><ul><?php foreach($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" id="birthdayForm" novalidate>
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="form-group"><label for="person_name">Person's name</label><input id="person_name" name="person_name" maxlength="100" required value="<?= e($name) ?>"></div>
<div class="form-group"><label for="birth_date">Date of birth</label><input type="date" id="birth_date" name="birth_date" required value="<?= e($date) ?>"></div>
<div class="form-group"><label for="relationship">Relationship</label><select id="relationship" name="relationship" required><?php foreach(['Friend','Family','Colleague','Other'] as $option): ?><option value="<?= e($option) ?>" <?= $relationship===$option?'selected':'' ?>><?= e($option) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label for="notes">Notes (optional)</label><textarea id="notes" name="notes" maxlength="2000" rows="4"><?= e($notes) ?></textarea></div>
<button class="btn" type="submit">Save birthday</button>
</form></div></div></section>
<script src="assets/js/birthday-form.js?v=2" defer></script>
<?php require 'includes/footer.php'; ?>
