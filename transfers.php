<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$selectedCurrency = $_COOKIE['selected_currency'] ?? 'USD';

function displayPrice($priceInUSD) {
    global $selectedCurrency;
    return formatPrice($priceInUSD, $selectedCurrency);
}

$departure = isset($_GET['departure']) ? trim($_GET['departure']) : 'Kinshasa';
$arrival = isset($_GET['arrival']) ? trim($_GET['arrival']) : 'Gombe';
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d', strtotime('+1 day'));
$passengers = isset($_GET['passengers']) ? intval($_GET['passengers']) : 1;

$transfers = searchAvailableTransfers($departure, $arrival, $date, $passengers);
$airports = getTransferAirports();
$vehicleTypes = getVehicleTypes();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transferts Aéroport - Luvia Travel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .transfer-card { transition: all 0.3s ease; }
        .transfer-card:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-gray-50">

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-800 to-blue-600 text-white py-16">
    <div class="container mx-auto px-6">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Transferts Aéroport</h1>
        <p class="text-xl opacity-90">Service de navette fiable et confortable pour vos déplacements en RDC</p>
    </div>
</section>

<!-- Formulaire de recherche -->
<section class="py-8 bg-white shadow-md">
    <div class="container mx-auto px-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Départ</label>
                <select name="departure" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="Kinshasa" <?php echo $departure == 'Kinshasa' ? 'selected' : ''; ?>>Kinshasa (FIH)</option>
                    <option value="Lubumbashi" <?php echo $departure == 'Lubumbashi' ? 'selected' : ''; ?>>Lubumbashi (FBM)</option>
                    <option value="Goma" <?php echo $departure == 'Goma' ? 'selected' : ''; ?>>Goma (GOM)</option>
                    <option value="Kisangani" <?php echo $departure == 'Kisangani' ? 'selected' : ''; ?>>Kisangani (FKI)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Arrivée</label>
                <select name="arrival" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="Gombe" <?php echo $arrival == 'Gombe' ? 'selected' : ''; ?>>Gombe</option>
                    <option value="Ngaliema" <?php echo $arrival == 'Ngaliema' ? 'selected' : ''; ?>>Ngaliema</option>
                    <option value="Limete" <?php echo $arrival == 'Limete' ? 'selected' : ''; ?>>Limete</option>
                    <option value="Kasa-Vubu" <?php echo $arrival == 'Kasa-Vubu' ? 'selected' : ''; ?>>Kasa-Vubu</option>
                    <option value="Bandundu" <?php echo $arrival == 'Bandundu' ? 'selected' : ''; ?>>Bandundu</option>
                    <option value="Matadi" <?php echo $arrival == 'Matadi' ? 'selected' : ''; ?>>Matadi</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Date</label>
                <input type="date" name="date" value="<?php echo $date; ?>" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Passagers</label>
                <input type="number" name="passengers" value="<?php echo $passengers; ?>" min="1" max="20" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="md:col-span-4">
                <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-2 rounded-lg transition">
                    Rechercher un transfert
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Résultats -->
<section class="py-12">
    <div class="container mx-auto px-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            Transferts disponibles (<?php echo count($transfers); ?> résultats)
        </h2>
        
        <?php if (empty($transfers)): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <p class="text-yellow-700">Aucun transfert disponible pour cette recherche.</p>
                <p class="text-gray-500 mt-2">Veuillez modifier vos critères ou nous contacter directement.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($transfers as $transfer): ?>
                <div class="transfer-card bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">
                                    <?php echo htmlspecialchars($transfer['departure_city']); ?> 
                                    <span class="text-gray-400">→</span> 
                                    <?php echo htmlspecialchars($transfer['arrival_city']); ?>
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    <?php echo htmlspecialchars($transfer['departure_airport_name'] ?? $transfer['departure_city']); ?>
                                </p>
                            </div>
                            <div class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">
                                <?php echo ucfirst($transfer['vehicle_type']); ?>
                            </div>
                        </div>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Max: <?php echo $transfer['max_passengers']; ?> passagers</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Durée: ~<?php echo $transfer['duration_minutes']; ?> min</span>
                            </div>
                            <?php if($transfer['included_services']): ?>
                            <div class="flex flex-wrap gap-1 mt-2">
                                <?php foreach($transfer['included_services'] as $service): ?>
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full"><?php echo $service; ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="border-t pt-4 mt-2 flex justify-between items-center">
                            <div>
                                <span class="text-2xl font-bold text-blue-600"><?php echo displayPrice($transfer['price_usd']); ?></span>
                                <span class="text-gray-500 text-sm">/ trajet</span>
                            </div>
                            <button onclick="bookTransfer(<?php echo $transfer['id']; ?>)" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold transition">
                                Réserver
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Info section -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-800 mb-2">Conducteurs professionnels</h3>
                <p class="text-gray-500 text-sm">Tous nos chauffeurs sont expérimentés et parlent français</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-800 mb-2">Ponctualité garantie</h3>
                <p class="text-gray-500 text-sm">Nous vous attendons à l'aéroport avec un panneau à votre nom</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-800 mb-2">Support 24/7</h3>
                <p class="text-gray-500 text-sm">Notre équipe est disponible à tout moment pour vous assister</p>
            </div>
        </div>
    </div>
</section>

<script>
function bookTransfer(transferId) {
    window.location.href = 'booking.php?type=transfer&id=' + transferId;
}
</script>

<?php require_once 'includes/footer.php'; ?>
