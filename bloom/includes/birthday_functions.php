<?php
require_once __DIR__ . '/auth.php';
function valid_birthday_date(string $value): bool {
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    if (!$date || $date->format('Y-m-d') !== $value){
         return false;
    }
    return true;
    // return $date <= new DateTimeImmutable('today');
}
function next_birthday(string $birthDate): DateTimeImmutable {
    $birth = new DateTimeImmutable($birthDate);
    $today = new DateTimeImmutable('today');
    $year = (int)$today->format('Y');
    $month = (int)$birth->format('n');
    $day = (int)$birth->format('j');
    // Feb 29 birthdays are celebrated Feb 28 in non-leap years.
    $makeDate = static function (int $year) use ($month, $day): DateTimeImmutable {
        $dayForYear = ($month === 2 && $day === 29 && !checkdate(2, 29, $year)) ? 28 : $day;
        return new DateTimeImmutable(sprintf('%04d-%02d-%02d', $year, $month, $dayForYear));
    };
    $next = $makeDate($year);
    return $next < $today ? $makeDate($year + 1) : $next;
}
function days_until(string $birthDate): int {
    $today = new DateTimeImmutable('today');
    return (int)$today->diff(next_birthday($birthDate))->format('%a');
}
function validate_birthday(array $post): array {
    $name = trim((string)($post['person_name'] ?? ''));
    $date = trim((string)($post['birth_date'] ?? ''));
    $relationship = trim((string)($post['relationship'] ?? ''));
    $notes = trim((string)($post['notes'] ?? ''));
    $errors = [];
    if ($name === '' || mb_strlen($name) > 100) $errors[] = 'Enter a name (maximum 100 characters).';
    if (!valid_birthday_date($date)) $errors[] = 'Enter a valid birthday date.';
    if (!in_array($relationship, ['Friend', 'Family', 'Colleague', 'Other'], true)) $errors[] = 'Choose a valid relationship.';
    if (mb_strlen($notes) > 2000) $errors[] = 'Notes must be 2000 characters or fewer.';
    return [$errors, $name, $date, $relationship, $notes];
}

// Returns presentation-ready birthday information using the same calendar timezone.
function birthday_details(string $birthDate): array {
    $today = new DateTimeImmutable('today');
    $next = next_birthday($birthDate);
    $days = (int)$today->diff($next)->format('%a');
    $birth = new DateTimeImmutable($birthDate);
    $age = (int)$next->format('Y') - (int)$birth->format('Y');
    return [
        'days_left' => $days,
        'next_date' => $next->format('j F Y'),
        'next_iso' => $next->format('Y-m-d'),
        'turning_age' => $age,
        'is_today' => $days === 0,
        'is_soon' => $days > 0 && $days <= 7,
    ];
}
function countdown_label(int $days): string {
    if ($days === 0) return '🎉 Birthday today!';
    if ($days === 1) return '🎁 Tomorrow!';
    return '⏳ ' . $days . ' days left';
}
