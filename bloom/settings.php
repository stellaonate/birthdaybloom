<?php
require_once 'includes/auth.php';
require_login();
require_once 'includes/db.php';
$allowedDays = [0, 1, 3, 7, 14, 30];
$userId = (int)$_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT reminders_enabled, reminder_days FROM user_settings WHERE user_id = ?');
$stmt->execute([$userId]);
$settings = $stmt->fetch() ?: ['reminders_enabled' => 1, 'reminder_days' => 7];
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!valid_csrf()) {
        http_response_code(403); $error = 'Your form session expired. Refresh and try again.';
    } else {
        $enabled = isset($_POST['reminders_enabled']) ? 1 : 0;
        $days = filter_var($_POST['reminder_days'] ?? null, FILTER_VALIDATE_INT);
        if (!in_array($days, $allowedDays, true)) {
            $error = 'Choose one of the available reminder periods.';
        } else {
            $save = $pdo->prepare('INSERT INTO user_settings (user_id, reminders_enabled, reminder_days) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE reminders_enabled = VALUES(reminders_enabled), reminder_days = VALUES(reminder_days)');
            $save->execute([$userId, $enabled, $days]);
            header('Location: settings.php?saved=1'); exit;
        }
    }
}
$pageTitle = 'Reminder Settings'; $extraCss = 'dashboard.css';
require 'includes/header.php';
?>
<section class="dashboard-section"><div class="container">
  <div class="dashboard-heading"><div><span class="eyebrow">Make reminders work your way</span><h1>Reminder settings ⚙️</h1><p>Choose how early BirthdayBloom highlights upcoming birthdays.</p></div><a class="btn btn-outline" href="dashboard.php">Back to dashboard</a></div>
  <?php if (isset($_GET['saved'])): ?><p class="success-banner" role="status">Your reminder preferences have been saved.</p><?php endif; ?>
  <?php if ($error): ?><p class="error-message" role="alert"><?= e($error) ?></p><?php endif; ?>
  <section class="dashboard-panel settings-panel"><form method="post" action="settings.php">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label class="settings-toggle"><span><strong>Enable in-app reminders</strong><small>Show upcoming birthdays on your dashboard.</small></span><input type="checkbox" name="reminders_enabled" value="1" <?= (int)$settings['reminders_enabled'] === 1 ? 'checked' : '' ?>></label>
    <label class="filter-field settings-period" for="reminderDays"><span>Remind me this many days before a birthday</span><select id="reminderDays" name="reminder_days">
      <?php foreach ([0 => 'On the birthday only', 1 => '1 day before', 3 => '3 days before', 7 => '7 days before', 14 => '14 days before', 30 => '30 days before'] as $value => $label): ?>
        <option value="<?= $value ?>" <?= (int)$settings['reminder_days'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select></label>
    <p class="settings-help">These preferences control the reminders shown inside your dashboard. Email and push notifications are not enabled by this setting.</p>
    <button class="btn" type="submit">Save preferences</button>
  </form></section>
</div></section>
<?php require 'includes/footer.php'; ?>
