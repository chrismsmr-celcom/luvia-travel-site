<?php
require_once 'includes/config.php';

header('Content-Type: application/json');

$page = intval($_GET['page'] ?? 1);
$city = $_GET['city'] ?? '';
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';
$adults = intval($_GET['adults'] ?? 1);
$children_ages = isset($_GET['children_ages']) ? explode(',', $_GET['children_ages']) : [];

if (empty($city) || empty($check_in) || empty($check_out)) {
    echo json_encode(['error' => 'Paramètres manquants']);
    exit;
}

$results = searchHotelsLiteAPI($city, $check_in, $check_out, $adults, $children_ages, null, $page, 30);

echo json_encode($results);
?>