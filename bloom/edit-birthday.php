<?php
require_once 'includes/auth.php'; require_login();
require_once 'includes/db.php'; require_once 'includes/birthday_functions.php';
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
if (!$id || $id<1) { http_response_code(404); exit('Birthday not found.'); }
$stmt=$pdo->prepare('SELECT * FROM birthdays WHERE id=? AND user_id=?');
$stmt->execute([$id,(int)$_SESSION['user_id']]); $birthday=$stmt->fetch();
if (!$birthday) { http_response_code(404); exit('Birthday not found.'); }
$errors=[]; $name=$birthday['person_name']; $date=$birthday['birth_date']; $relationship=$birthday['relationship']; $notes=$birthday['notes']??'';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!valid_csrf()) { http_response_code(403); $errors[]='Invalid form token. Refresh and try again.'; }
    [$validation,$name,$date,$relationship,$notes]=validate_birthday($_POST);
    $errors=array_merge($errors,$validation);
    if (!$errors) {
        $update=$pdo->prepare('UPDATE birthdays SET person_name=?, birth_date=?, relationship=?, notes=? WHERE id=? AND user_id=?');
        $update->execute([$name,$date,$relationship,$notes,$id,(int)$_SESSION['user_id']]);
        header('Location: dashboard.php?status=updated'); exit;
    }
}
$pageTitle='Edit Birthday'; $extraCss='dashboard.css'; require 'includes/header.php';
?>
<section class="dashboard-section"><div class="container"><div class="dashboard-panel form-panel">
<a href="dashboard.php">← Back to dashboard</a><h1>✏️ Edit birthday</h1>
<?php if($errors): ?><div class="notice error-notice" role="alert"><ul><?php foreach($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" id="birthdayForm" novalidate>
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="form-group"><label for="person_name">Person's name</label><input id="person_name" name="person_name" maxlength="100" required value="<?= e($name) ?>"></div>
<div class="form-group"><label for="birth_date">Date of birth</label><input type="date" id="birth_date" name="birth_date" required value="<?= e($date) ?>"></div>
<div class="form-group"><label for="relationship">Relationship</label><select id="relationship" name="relationship" required><?php foreach(['Friend','Family','Colleague','Other'] as $option): ?><option value="<?= e($option) ?>" <?= $relationship===$option?'selected':'' ?>><?= e($option) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label for="notes">Notes (optional)</label><textarea id="notes" name="notes" maxlength="2000" rows="4"><?= e($notes) ?></textarea></div>
<button class="btn" type="submit">Update birthday</button>
</form></div></div></section>
<script src="assets/js/birthday-form.js" defer></script>
<?php require 'includes/footer.php'; ?>
