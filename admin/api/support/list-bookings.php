<?php
require_once '../../common/auth.php';
require_once '../../common/response.php';

requireRole([ROLE_SUPPORT]);

$sql = "SELECT b.*, r.title AS room_name, ba.staff_id
        FROM bookings b
        LEFT JOIN rooms r ON b.room_id = r.id
        LEFT JOIN booking_assignments ba ON ba.booking_id = b.id
        ORDER BY b.id DESC";
$stmt = $pdo->query($sql);
$bookings = $stmt->fetchAll();

jsonResponse('success', 'Support bookings fetched', ['bookings' => $bookings]);
?>