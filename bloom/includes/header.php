<?php
require_once __DIR__ . '/auth.php';
$pageTitle = $pageTitle ?? 'BirthdayBloom';
$extraCss = $extraCss ?? '';
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="BirthdayBloom helps you remember the birthdays that matter.">
<title><?= e($pageTitle) ?> | BirthdayBloom</title>
<link rel="stylesheet" href="assets/css/style.css">
<?php if ($extraCss): ?><link rel="stylesheet" href="assets/css/<?= e($extraCss) ?>"><?php endif; ?>
<script src="assets/js/main.js" defer></script></head>
<body><header class="site-header"><nav class="container nav" aria-label="Main navigation">
<a class="brand" href="index.php"><span aria-hidden="true">🎂</span> Birthday<span>Bloom</span></a>
<button class="menu-toggle" type="button" aria-label="Open navigation" aria-controls="nav-links" aria-expanded="false">☰</button>
<div class="nav-links" id="nav-links"><a href="index.php">Home</a><a href="index.php#features">Features</a><a href="index.php#about">About</a>
<?php if (!empty($_SESSION['user_id'])): ?>
<a href="dashboard.php">Dashboard</a><a href="calendar.php">Calendar</a><a href="settings.php">Reminder Settings</a>
<form action="logout.php" method="post" class="nav-logout"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="btn btn-small" type="submit">Log Out</button></form>
<?php else: ?>
<a href="login.php">Login</a><a class="btn btn-small" href="register.php">Get Started</a>
<?php endif; ?>
</div></nav></header><main>
