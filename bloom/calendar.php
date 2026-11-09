<?php
require_once 'includes/auth.php';
require_login();
require_once 'includes/db.php';
require_once 'includes/birthday_functions.php';
date_default_timezone_set('Africa/Lagos');

$stmt = $pdo->prepare('SELECT id, person_name, birth_date, relationship FROM birthdays WHERE user_id = ? ORDER BY person_name ASC');
$stmt->execute([(int) $_SESSION['user_id']]);
$birthdays = $stmt->fetchAll();
// JSON_HEX_* makes JSON safe inside an HTML script element.
$calendarJson = json_encode($birthdays, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
$pageTitle = 'Birthday Calendar';
$extraCss = 'dashboard.css';
require 'includes/header.php';
?>
<section class="dashboard-section"><div class="container">
  <div class="dashboard-heading">
    <div><span class="eyebrow">Your special dates</span><h1>Birthday Calendar 📅</h1><p>See every celebration at a glance. Birthdays repeat each year.</p></div>
    <a class="btn" href="add-birthday.php">+ Add Birthday</a>
  </div>
  <div class="calendar-layout">
    <section class="calendar-panel" aria-label="Monthly birthday calendar">
      <div class="calendar-toolbar">
        <div class="calendar-navigation"><button id="previousMonth" type="button" aria-label="Previous month">‹</button><h2 id="calendarMonth" aria-live="polite"></h2><button id="nextMonth" type="button" aria-label="Next month">›</button></div>
        <button id="todayMonth" type="button" class="filter-reset">This month</button>
      </div>
      <div class="calendar-grid" id="calendarGrid" role="group" aria-label="Dates in selected month"></div>
      <p class="calendar-hint">Select a date to see its birthdays. 🎂 marks a birthday.</p>
    </section>
    <aside class="calendar-details" aria-labelledby="selectedDayHeading">
      <h2 id="selectedDayHeading">Selected date</h2>
      <p id="selectedDate" class="selected-date"></p>
      <div id="selectedBirthdays" aria-live="polite"></div>
    </aside>
  </div>
  <p class="calendar-hint">February 29 birthdays are displayed on February 28 in non-leap years.</p>
  <a href="dashboard.php" class="calendar-back">← Back to dashboard</a>
</div></section>
<script id="birthdayCalendarData" type="application/json"><?= $calendarJson ?></script>
<script src="assets/js/calendar.js" defer></script>
<?php require 'includes/footer.php'; ?>
