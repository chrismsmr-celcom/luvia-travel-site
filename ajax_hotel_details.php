<?php
// Désactiver complètement les erreurs pour ce fichier (évite les warnings qui cassent le JSON)
error_reporting(0);
ini_set('display_errors', 0);

require_once 'includes/config.php';

// Test mode - doit être au tout début avant tout autre output
if (isset($_GET['test'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Test OK', 'get' => $_GET]);
    exit;
}

header('Content-Type: application/json');

// Récupération des paramètres GET
$hotelId = isset($_GET['hotel_id']) ? trim($_GET['hotel_id']) : '';
$checkIn = isset($_GET['check_in']) ? trim($_GET['check_in']) : '';
$checkOut = isset($_GET['check_out']) ? trim($_GET['check_out']) : '';
$adults = isset($_GET['adults']) ? intval($_GET['adults']) : 2;
$nights = isset($_GET['nights']) ? intval($_GET['nights']) : 1;

if (empty($hotelId)) {
    echo json_encode(['error' => 'Hotel ID required', 'received' => $_GET]);
    exit;
}

if (empty($checkIn) || empty($checkOut)) {
    echo json_encode(['error' => 'Dates required', 'check_in' => $checkIn, 'check_out' => $checkOut]);
    exit;
}

// Récupérer les détails complets de l'hôtel depuis /data/hotel
$url = rtrim($liteapi_search_base_url, '/') . "/data/hotel?hotelId=" . urlencode($hotelId) . "&language=fr";
$response = liteAPIRequest($url, 'GET');

if (!empty($response['error'])) {
    echo json_encode(['error' => 'Failed to fetch hotel details', 'message' => $response['message'] ?? 'Unknown error']);
    exit;
}

$hotelData = $response['data'] ?? [];

if (empty($hotelData)) {
    echo json_encode(['error' => 'No hotel data found', 'hotel_id' => $hotelId]);
    exit;
}

// Récupérer les chambres disponibles avec leurs prix
$checkInFormatted = date('Y-m-d', strtotime($checkIn));
$checkOutFormatted = date('Y-m-d', strtotime($checkOut));
$occupancy = ['adults' => max(1, $adults)];
$ratesData = getRatesForHotels([$hotelId], $checkInFormatted, $checkOutFormatted, $occupancy, 'FR');

$rooms = [];
if (!empty($ratesData[$hotelId]['all_rates'])) {
    $rooms = $ratesData[$hotelId]['all_rates'];
    
    // Enrichir chaque chambre avec les photos
    foreach ($rooms as &$room) {
        $roomName = $room['room_name'];
        $roomSpecificImages = [];
        
        // Chercher la chambre correspondante dans les données de l'hôtel
        if (isset($hotelData['rooms']) && is_array($hotelData['rooms'])) {
            foreach ($hotelData['rooms'] as $hotelRoom) {
                $hotelRoomName = $hotelRoom['roomName'] ?? '';
                if (!empty($hotelRoomName) && (stripos($roomName, $hotelRoomName) !== false || stripos($hotelRoomName, $roomName) !== false)) {
                    
                    if (isset($hotelRoom['photos']) && is_array($hotelRoom['photos'])) {
                        foreach ($hotelRoom['photos'] as $photo) {
                            if (!empty($photo['url'])) {
                                $roomSpecificImages[] = $photo['url'];
                            } elseif (!empty($photo['hd_url'])) {
                                $roomSpecificImages[] = $photo['hd_url'];
                            }
                        }
                    }
                    break;
                }
            }
        }
        
        // Si pas de photos de chambre, utiliser les photos de l'hôtel
        if (empty($roomSpecificImages) && isset($hotelData['hotelImages']) && is_array($hotelData['hotelImages'])) {
            foreach ($hotelData['hotelImages'] as $img) {
                if (!empty($img['url'])) {
                    $roomSpecificImages[] = $img['url'];
                }
            }
        }
        
        $room['images'] = $roomSpecificImages;
        
        // Ajouter d'autres infos si disponibles
        if (isset($hotelRoom['roomSizeSquare'])) {
            $room['room_size'] = $hotelRoom['roomSizeSquare'];
        }
        if (isset($hotelRoom['description'])) {
            $room['description'] = $hotelRoom['description'];
        }
    }
}

// Extraire les photos de l'hôtel pour la galerie
$hotelGallery = [];
if (isset($hotelData['hotelImages']) && is_array($hotelData['hotelImages'])) {
    foreach ($hotelData['hotelImages'] as $img) {
        if (!empty($img['url'])) {
            $hotelGallery[] = $img['url'];
        }
    }
}

// Réponse JSON
echo json_encode([
    'success' => true,
    'hotel' => [
        'id' => $hotelId,
        'name' => $hotelData['name'] ?? '',
        'address' => $hotelData['address'] ?? '',
        'rating' => $hotelData['starRating'] ?? 0,
        'checkin' => $hotelData['checkinCheckoutTimes']['checkin_start'] ?? '14:00',
        'checkout' => $hotelData['checkinCheckoutTimes']['checkout'] ?? '12:00',
        'gallery' => [
            'all' => $hotelGallery
        ]
    ],
    'rooms' => $rooms,
    'nights' => $nights
], JSON_UNESCAPED_SLASHES);
?>