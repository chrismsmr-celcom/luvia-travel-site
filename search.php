<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$type = $_GET['type'] ?? 'flight';

// === VARIABLES COMMUNES ===
$results = [];
$error = null;
$title = 'Résultats de recherche';

// === VOLS ===
$origin = $_GET['origin'] ?? '';
$destination = $_GET['destination'] ?? '';
$departure_date = $_GET['departure_date'] ?? '';
$return_date = $_GET['return_date'] ?? null;

$passengerTypes = $_GET['passenger_type'] ?? ['adult'];
$adults = 0;
$children = 0;
$infants = 0;
foreach($passengerTypes as $pType) {
    if($pType == 'adult') $adults++;
    elseif($pType == 'child') $children++;
    elseif($pType == 'infant') $infants++;
}

// === HÔTELS ===
$city = trim($_GET['city'] ?? '');
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';
$adults_hotel = max(1, intval($_GET['adults'] ?? 1));
$children_ages = [];
if (!empty($_GET['children_ages'])) {
    $ages = explode(',', $_GET['children_ages']);
    foreach ($ages as $age) {
        $ageInt = intval(trim($age));
        if ($ageInt >= 0 && $ageInt <= 17) {
            $children_ages[] = $ageInt;
        }
    }
}

// === ASSURANCES ===
$zone = $_GET['zone'] ?? '';
$duration = intval($_GET['duration'] ?? 7);
$agesInput = $_GET['ages'] ?? '';
$hasSport = isset($_GET['sport']) && $_GET['sport'] === 'on';
$hasExtreme = isset($_GET['extreme']) && $_GET['extreme'] === 'on';
$hasCruise = isset($_GET['cruise']) && $_GET['cruise'] === 'on';
$hasStudent = isset($_GET['student']) && $_GET['student'] === 'on';

// === PACKAGES ===
$package_destination = trim($_GET['package_destination'] ?? $_GET['destination'] ?? '');
$start_date = $_GET['start_date'] ?? '';
$package_duration = intval($_GET['duration'] ?? 3);
$adults_package = intval($_GET['adults'] ?? 2);
$children_package = intval($_GET['children'] ?? 0);
$hasVisa = isset($_GET['visa']) && $_GET['visa'] === 'on';
$hasTransfer = isset($_GET['transfer']) && $_GET['transfer'] === 'on';
$hasInsurance = isset($_GET['insurance']) && $_GET['insurance'] === 'on';
$hasActivities = isset($_GET['activities']) && $_GET['activities'] === 'on';

// === VOITURES ===
$car_city = $_GET['car_city'] ?? $_GET['city'] ?? '';
$pickup_date = $_GET['pickup_date'] ?? '';
$car_return_date = $_GET['return_date'] ?? '';
$rental_type = $_GET['rental_type'] ?? 'self';
$car_type = $_GET['car_type'] ?? 'economy';
$transmission = $_GET['transmission'] ?? 'manual';

// === TRAITEMENT PAR TYPE ===
switch($type) {
    // ==================== VOLS ====================
    case 'flight':
        $results = searchFlightsDuffel($origin, $destination, $departure_date, $return_date, $adults, $children, $infants);
        $title = "Vols de $origin vers $destination";
        if($departure_date) {
            $title .= " - " . date('d/m/Y', strtotime($departure_date));
        }
        break;
    
    // ==================== HÔTELS ====================
    case 'hotel':
    $city = trim($_GET['city'] ?? '');
    $check_in = $_GET['check_in'] ?? '';
    $check_out = $_GET['check_out'] ?? '';
    $adults_hotel = max(1, intval($_GET['adults'] ?? 1));
    
    if (empty($city)) {
        $error = "La ville est obligatoire.";
        $results = [];
        $title = "Erreur - Recherche d'hôtel";
    } elseif (empty($check_in) || empty($check_out)) {
        $error = "Les dates de séjour sont obligatoires.";
        $results = [];
        $title = "Erreur - Recherche d'hôtel";
    } elseif (strtotime($check_in) >= strtotime($check_out)) {
        $error = "La date de départ doit être après la date d'arrivée.";
        $results = [];
        $title = "Erreur - Dates invalides";
    } else {
        $children_ages = [];
        if (!empty($_GET['children_ages'])) {
            $ages = explode(',', $_GET['children_ages']);
            foreach ($ages as $age) {
                $ageInt = intval(trim($age));
                if ($ageInt >= 0 && $ageInt <= 17) {
                    $children_ages[] = $ageInt;
                }
            }
        }
        $results = searchHotelsLiteAPI($city, $check_in, $check_out, $adults_hotel, $children_ages);
        $nights = max(1, (int)((strtotime($check_out) - strtotime($check_in)) / 86400));
        $title = "Hôtels à " . htmlspecialchars($city) . " - " . date('d/m/Y', strtotime($check_in)) . " → " . date('d/m/Y', strtotime($check_out)) . " ({$nights} nuit(s))";
    }
    break;    
    // ==================== ASSURANCES ====================
    case 'insurance':
        if (empty($zone)) {
            $error = "La zone de couverture est obligatoire.";
            $results = [];
        } elseif ($duration <= 0) {
            $error = "La durée du voyage est obligatoire.";
            $results = [];
        } else {
            $ageList = [];
            if (!empty($agesInput)) {
                $ageList = explode(',', $agesInput);
                $ageList = array_map('intval', $ageList);
            }
            
            $priceMapping = [
                'africa_asia' => [
                    7 => ['adult' => 25, 'child' => 23], 14 => ['adult' => 30, 'child' => 28],
                    30 => ['adult' => 37, 'child' => 35], 60 => ['adult' => 42, 'child' => 40],
                    90 => ['adult' => 72, 'child' => 70], 180 => ['adult' => 82, 'child' => 80],
                    360 => ['adult' => 112, 'child' => 110]
                ],
                'schengen' => [
                    7 => ['adult' => 22, 'child' => 15], 14 => ['adult' => 26, 'child' => 20],
                    30 => ['adult' => 32, 'child' => 25], 60 => ['adult' => 42, 'child' => 30],
                    90 => ['adult' => 62, 'child' => 35], 180 => ['adult' => 72, 'child' => 40],
                    360 => ['adult' => 95, 'child' => 45]
                ],
                'world_wide' => [
                    7 => ['adult' => 35, 'child' => 25], 14 => ['adult' => 50, 'child' => 30],
                    30 => ['adult' => 80, 'child' => 45], 60 => ['adult' => 116, 'child' => 65],
                    90 => ['adult' => 150, 'child' => 78], 180 => ['adult' => 170, 'child' => 90],
                    360 => ['adult' => 260, 'child' => 135]
                ]
            ];
            
            $durationKey = 7;
            foreach ([7, 14, 30, 60, 90, 180, 360] as $key) {
                if ($duration <= $key) { $durationKey = $key; break; }
            }
            
            $prices = [];
            foreach ($ageList as $age) {
                if ($age <= 12) {
                    $price = $priceMapping[$zone][$durationKey]['child'] ?? 25;
                } else {
                    $price = $priceMapping[$zone][$durationKey]['adult'] ?? 35;
                }
                $prices[] = round($price, 2);
            }
            
            if (empty($prices)) {
                for ($i = 0; $i < 2; $i++) {
                    $price = $priceMapping[$zone][$durationKey]['adult'] ?? 35;
                    $prices[] = round($price, 2);
                }
            }
            
            $baseTotal = array_sum($prices);
            $sportAmount = $hasSport ? $baseTotal * 0.30 : 0;
            $extremeAmount = $hasExtreme ? $baseTotal * 0.40 : 0;
            $cruiseAmount = $hasCruise ? $baseTotal * 0.20 : 0;
            $studentAmount = $hasStudent ? $baseTotal * 0.15 : 0;
            $totalPrice = $baseTotal + $sportAmount + $extremeAmount + $cruiseAmount + $studentAmount;
            
            $coverageInfo = [
                'medical' => '50000 USD', 'accident' => '45000 USD', 'financial' => '5000 USD',
                'covid' => 'Inclus', 'sport' => $hasSport ? 'Inclus (+30%)' : 'Optionnel (+30%)',
                'extreme' => $hasExtreme ? 'Inclus (+40%)' : 'Optionnel (+40%)',
                'cruise' => $hasCruise ? 'Inclus (+20%)' : 'Optionnel (+20%)',
                'student' => $hasStudent ? 'Inclus (+15%)' : 'Optionnel (+15%)'
            ];
            
            $zoneNames = ['africa_asia' => 'Afrique & Asie', 'schengen' => 'Espace Schengen', 'world_wide' => 'Monde entier'];
            $results = [
                'zone' => $zone, 'duration' => $duration, 'travelers' => count($prices), 'ages' => $ageList,
                'price_per_traveler' => $prices, 'base_price_usd' => $baseTotal, 'total_price_usd' => $totalPrice,
                'total_price_cdf' => round($totalPrice * 2900, 0), 'has_sport' => $hasSport, 'has_extreme' => $hasExtreme,
                'has_cruise' => $hasCruise, 'has_student' => $hasStudent, 'sport_amount' => $sportAmount,
                'extreme_amount' => $extremeAmount, 'cruise_amount' => $cruiseAmount, 'student_amount' => $studentAmount,
                'coverage' => $coverageInfo
            ];
            $title = "Assurance " . ($zoneNames[$zone] ?? $zone) . " - " . $duration . " jours";
        }
        break;
    
    // ==================== PACKAGES ====================
    case 'package':
        if (empty($package_destination)) {
            $error = "La destination est obligatoire.";
            $results = [];
        } else {
            $allPackages = getPackages();
            $packages = array_filter($allPackages, function($pkg) use ($package_destination) {
                return stripos($pkg['destination'], $package_destination) !== false || stripos($pkg['name'], $package_destination) !== false;
            });
            
            if (empty($packages)) {
                $error = "Aucun package trouvé pour la destination : " . htmlspecialchars($package_destination);
                $results = [];
            } else {
                $package = reset($packages);
                $exchangeRate = 2900;
                $basePriceUSD = floatval($package['price'] ?? 0);
                $visaPriceUSD = floatval($package['visa_price'] ?? 0);
                $transferPriceUSD = ($hasTransfer && isset($package['transfer_price']) && $package['transfer_price'] > 0) ? floatval($package['transfer_price']) : 0;
                
                $activitiesPriceUSD = 0;
                if ($hasActivities) {
                    if (isset($package['activity1_price']) && $package['activity1_price'] > 0) $activitiesPriceUSD += floatval($package['activity1_price']);
                    if (isset($package['activity2_price']) && $package['activity2_price'] > 0) $activitiesPriceUSD += floatval($package['activity2_price']);
                    if (isset($package['activity3_price']) && $package['activity3_price'] > 0) $activitiesPriceUSD += floatval($package['activity3_price']);
                }
                
                $insurancePriceUSD = $hasInsurance ? $basePriceUSD * 0.10 : 0;
                $personMultiplier = $adults_package + ($children_package * 0.5);
                $totalPriceUSD = ($basePriceUSD + $transferPriceUSD + $insurancePriceUSD + $activitiesPriceUSD) * $personMultiplier;
                
                if ($hasVisa) {
                    $totalPriceUSD += $visaPriceUSD * $personMultiplier;
                }
                
                $totalPriceUSD = round($totalPriceUSD, 2);
                
                $results = [
                    'package' => $package, 'destination' => $package_destination, 'start_date' => $start_date,
                    'duration' => $package_duration, 'adults' => $adults_package, 'children' => $children_package,
                    'has_visa' => $hasVisa, 'has_transfer' => $hasTransfer, 'has_insurance' => $hasInsurance,
                    'has_activities' => $hasActivities, 'base_price_usd' => $basePriceUSD,
                    'base_price_fc' => round($basePriceUSD * $exchangeRate, 0),
                    'visa_price_usd' => $visaPriceUSD, 'visa_price_fc' => round($visaPriceUSD * $exchangeRate, 0),
                    'transfer_price_usd' => $transferPriceUSD, 'transfer_price_fc' => round($transferPriceUSD * $exchangeRate, 0),
                    'insurance_price_usd' => $insurancePriceUSD, 'insurance_price_fc' => round($insurancePriceUSD * $exchangeRate, 0),
                    'activities_price_usd' => $activitiesPriceUSD, 'activities_price_fc' => round($activitiesPriceUSD * $exchangeRate, 0),
                    'person_multiplier' => $personMultiplier, 'total_price_usd' => $totalPriceUSD,
                    'total_price_fc' => round($totalPriceUSD * $exchangeRate, 0)
                ];
                
                $cleanTitle = html_entity_decode($package['name'], ENT_QUOTES, 'UTF-8');
                $title = "Package " . $cleanTitle . " - " . $package_duration . " nuits";
            }
        }
        break;
    
    // ==================== VOITURES ====================
    case 'car':
        if (empty($car_city)) {
            $error = "La ville est obligatoire.";
            $results = [];
        } else {
            $results = searchCars($car_city, $rental_type, $car_type, $transmission);
            $title = "Location de voitures à " . htmlspecialchars($car_city);
            if($pickup_date) {
                $title .= " - Prise en charge: " . date('d/m/Y', strtotime($pickup_date));
            }
        }
        break;
    
    default:
        header('Location: index.php');
        exit;
}

// === FONCTION DISPLAY PRICE ===
$selectedCurrency = $_COOKIE['selected_currency'] ?? 'USD';
if (!function_exists('displayPrice')) {
    function displayPrice($priceInUSD) {
        global $selectedCurrency;
        return formatPrice($priceInUSD, $selectedCurrency);
    }
}

// === INCLUSION DU FICHIER D'AFFICHAGE SPÉCIFIQUE ===
$search_file = "search-$type.php";
if (file_exists($search_file)) {
    include $search_file;
} else {
    // Fallback: affichage générique
    ?>
    <div class="container mx-auto px-6 py-32">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <strong>Erreur :</strong> Le fichier <?php echo htmlspecialchars($search_file); ?> est introuvable.
        </div>
        <?php if(!empty($results) && is_array($results)): ?>
            <div class="mt-8">
                <h2 class="text-xl font-bold mb-4">Résultats trouvés : <?php echo count($results); ?></h2>
                <pre><?php print_r($results); ?></pre>
            </div>
        <?php elseif($error): ?>
            <div class="mt-4 text-red-600"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>
    <?php
}

require_once 'includes/footer.php';
?>