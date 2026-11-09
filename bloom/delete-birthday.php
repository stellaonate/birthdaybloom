<?php
require_once 'includes/auth.php'; require_login();
require_once 'includes/db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); header('Allow: POST'); exit('Method not allowed.'); }
if (!valid_csrf()) { http_response_code(403); exit('Invalid request token.'); }
$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if (!$id || $id<1) { http_response_code(400); exit('Invalid birthday ID.'); }
$stmt=$pdo->prepare('DELETE FROM birthdays WHERE id=? AND user_id=?');
$stmt->execute([$id,(int)$_SESSION['user_id']]);
header('Location: dashboard.php?status=deleted'); exit;
