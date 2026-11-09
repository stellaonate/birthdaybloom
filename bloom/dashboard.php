<?php
require_once 'includes/auth.php';
require_login();
require_once 'includes/db.php';
require_once 'includes/birthday_functions.php';
date_default_timezone_set('Africa/Lagos');

$stmt = $pdo->prepare('SELECT id, person_name, birth_date, relationship, notes, created_at FROM birthdays WHERE user_id = ?');
$stmt->execute([(int)$_SESSION['user_id']]);
$birthdays = $stmt->fetchAll();
foreach ($birthdays as &$birthday) {
    $birthday = array_merge($birthday, birthday_details($birthday['birth_date']));
}
unset($birthday);
usort($birthdays, static fn($a, $b) => $a['days_left'] <=> $b['days_left'] ?: strcmp($a['person_name'], $b['person_name']));
$total = count($birthdays);
$todayCount = count(array_filter($birthdays, static fn($b) => $b['is_today']));
$upcoming = count(array_filter($birthdays, static fn($b) => $b['is_soon']));
$nextBirthday = $birthdays[0] ?? null;
$settingsStmt = $pdo->prepare('SELECT reminders_enabled, reminder_days FROM user_settings WHERE user_id = ?');
$settingsStmt->execute([(int)$_SESSION['user_id']]);
$userSettings = $settingsStmt->fetch() ?: ['reminders_enabled' => 1, 'reminder_days' => 7];
$reminderWindow = (int)$userSettings['reminder_days'];
$reminders = (int)$userSettings['reminders_enabled'] === 1
    ? array_values(array_filter($birthdays, static fn($b) => $b['days_left'] <= $reminderWindow))
    : [];
$messages = ['added' => 'Birthday saved successfully!', 'updated' => 'Birthday updated successfully!', 'deleted' => 'Birthday deleted successfully!'];
$status = (string)($_GET['status'] ?? '');
$pageTitle = 'Dashboard';
$extraCss = 'dashboard.css';
require 'includes/header.php';
?>
<section class="dashboard-section"><div class="container">
  <div class="dashboard-heading">
    <div><span class="eyebrow">Your celebration space</span><h1>Welcome, <?= e((string)$_SESSION['full_name']) ?>! 🎉</h1><p>Keep every special day close, one celebration at a time.</p></div>
    <div class="dashboard-heading-actions"><a class="btn btn-outline" href="calendar.php">📅 Calendar</a><a class="btn" href="add-birthday.php">+ Add Birthday</a></div>
  </div>
  <?php if (isset($messages[$status])): ?><p class="success-banner" role="status"><?= e($messages[$status]) ?></p><?php endif; ?>
  <div class="stats-grid" aria-label="Birthday statistics">
    <div class="stat-card"><span>🎂 Saved Birthdays</span><strong><?= $total ?></strong><small>People you're celebrating</small></div>
    <div class="stat-card"><span>🎊 Birthdays Today</span><strong><?= $todayCount ?></strong><small><?= $todayCount ? 'Time to celebrate!' : 'No birthdays today' ?></small></div>
    <div class="stat-card"><span>🔔 Next 7 Days</span><strong><?= $upcoming ?></strong><small>Upcoming celebrations (excluding today)</small></div>
    <div class="stat-card"><span>📅 Next Birthday</span><strong class="stat-next"><?= $nextBirthday ? (int)$nextBirthday['days_left'] . ' days' : '—' ?></strong><small><?= $nextBirthday ? e($nextBirthday['person_name']) : 'Add your first birthday' ?></small></div>
  </div>

  <?php if ($todayCount > 0): ?>
  <section class="celebration-banner" aria-label="Today's birthday celebrations"><div class="celebration-icon" aria-hidden="true">🥳</div><div><h2>It's celebration time!</h2><p><?= $todayCount === 1 ? 'You have a birthday to celebrate today.' : 'You have ' . $todayCount . ' birthdays to celebrate today.' ?> Send them your warm wishes!</p></div><span aria-hidden="true">🎈 🎂 🎉</span></section>
  <?php endif; ?>

  <section class="reminder-panel" aria-labelledby="reminderTitle">
    <div class="panel-top"><div><span class="eyebrow">Don't miss a moment</span><h2 id="reminderTitle">🔔 Birthday reminders</h2><p>Your reminders based on your saved preferences.</p></div><span class="reminder-count"><?= (int)$userSettings['reminders_enabled'] === 1 ? count($reminders) . ' reminder' . (count($reminders) === 1 ? '' : 's') : 'Reminders off' ?></span></div>
    <?php if ((int)$userSettings['reminders_enabled'] !== 1): ?><p class="reminder-empty">In-app reminders are turned off. <a href="settings.php">Change your settings</a>.</p><?php elseif (!$reminders): ?><p class="reminder-empty">No birthdays in your selected reminder period. You're all caught up! ✨</p><?php else: ?>
    <div class="reminder-list">
    <?php foreach ($reminders as $reminder): ?>
      <div class="reminder-item <?= $reminder['is_today'] ? 'reminder-today' : '' ?>"><span class="reminder-emoji" aria-hidden="true"><?= $reminder['is_today'] ? '🎉' : '🎁' ?></span><div><strong><?= e($reminder['person_name']) ?></strong><span><?= e($reminder['next_date']) ?> · <?= e($reminder['relationship']) ?></span></div><span class="reminder-label"><?= $reminder['is_today'] ? 'Today!' : ($reminder['days_left'] === 1 ? 'Tomorrow' : $reminder['days_left'] . ' days') ?></span></div>
    <?php endforeach; ?>
    </div><?php endif; ?>
  </section>

  <section class="dashboard-panel" aria-labelledby="birthdaysTitle">
    <div class="panel-top"><div><h2 id="birthdaysTitle">Your birthdays</h2><p>Find birthdays quickly, filter relationships, and choose how to sort. <a href="settings.php">Reminder settings</a></p></div><span class="panel-total"><?= $total ?> saved</span></div>
    <div class="filters">
      <label class="filter-field filter-search" for="searchBirthdays"><span>Search</span><input id="searchBirthdays" type="search" placeholder="Search name or notes..."></label>
      <label class="filter-field" for="relationshipFilter"><span>Relationship</span><select id="relationshipFilter"><option value="all">All relationships</option><option value="Friend">Friends</option><option value="Family">Family</option><option value="Colleague">Colleagues</option><option value="Other">Other</option></select></label>
      <label class="filter-field" for="sortBirthdays"><span>Sort by</span><select id="sortBirthdays"><option value="upcoming">Next birthday</option><option value="name-asc">Name A–Z</option><option value="name-desc">Name Z–A</option><option value="month">Birth month</option><option value="recent">Recently added</option></select></label>
      <button type="button" id="resetFilters" class="filter-reset">Reset</button>
    </div>
    <p id="filterCount" class="filter-count" role="status" aria-live="polite"></p>
    <div id="birthdayList" class="birthday-grid">
    <?php foreach ($birthdays as $birthday): ?>
      <article class="birthday-card <?= $birthday['is_today'] ? 'birthday-card-today' : ($birthday['is_soon'] ? 'birthday-card-soon' : '') ?>" data-id="<?= (int)$birthday['id'] ?>" data-name="<?= e($birthday['person_name']) ?>" data-notes="<?= e($birthday['notes'] ?? '') ?>" data-relationship="<?= e($birthday['relationship']) ?>" data-days="<?= (int)$birthday['days_left'] ?>" data-month="<?= (int)date('n', strtotime($birthday['birth_date'])) ?>" data-day="<?= (int)date('j', strtotime($birthday['birth_date'])) ?>" data-created="<?= e($birthday['created_at']) ?>">
        <div class="card-top"><div class="avatar" aria-hidden="true"><?= $birthday['is_today'] ? '🥳' : ($birthday['is_soon'] ? '🎁' : '🎂') ?></div><span class="card-category"><?= e($birthday['relationship']) ?></span></div>
        <h3><?= e($birthday['person_name']) ?></h3><p class="next-date">📅 Date: <?= e($birthday['next_date']) ?></p>
        <?php if (trim((string)$birthday['notes']) !== ''): ?><p class="birthday-notes"><?= nl2br(e($birthday['notes'])) ?></p><?php endif; ?>
        <span class="countdown <?= $birthday['is_today'] ? 'countdown-today' : '' ?>"><?= e(countdown_label($birthday['days_left'])) ?></span>
        <div class="card-actions"><a class="edit-link" href="edit-birthday.php?id=<?= (int)$birthday['id'] ?>">Edit</a><form action="delete-birthday.php" method="post" class="delete-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$birthday['id'] ?>"><button type="submit" class="delete-button">Delete</button></form></div>
      </article>
    <?php endforeach; ?>
    </div>
    <p id="emptyState" class="empty-state" <?= $total > 0 ? 'hidden' : '' ?>><?= $total === 0 ? 'No birthdays yet. Click Add Birthday to get started!' : 'No birthdays match your search.' ?></p>
  </section>
</div></section>
<script src="assets/js/dashboard.js" defer></script>
<?php require 'includes/footer.php'; ?>
