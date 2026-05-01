<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/header.php'; ?>

<?php
// Récupérer la devise
$selectedCurrency = $_COOKIE['selected_currency'] ?? 'USD';
function displayPrice($priceInUSD) {
    global $selectedCurrency;
    return formatPrice($priceInUSD, $selectedCurrency);
}

$type = $_GET['type'] ?? '';
$itemData = $_GET['item'] ?? '';
$insuranceData = $_GET['insurance_data'] ?? '';
$packageData = $_GET['package_data'] ?? '';
$item = null;

// Gestion assurance
if($type === 'insurance' && !empty($insuranceData)) {
    $item = json_decode(urldecode($insuranceData), true);
} elseif($type === 'package' && !empty($packageData)) {
    $item = json_decode(urldecode($packageData), true);
} elseif(!empty($itemData)) {
    $item = json_decode(urldecode($itemData), true);
}

if(empty($type) || empty($item)) {
    header('Location: index.php');
    exit;
}

// Récupérer les paramètres supplémentaires
$origin = $_GET['origin'] ?? '';
$destination = $_GET['destination'] ?? '';
$departure_date = $_GET['departure_date'] ?? '';
$return_date = $_GET['return_date'] ?? '';
$adults = intval($_GET['adults'] ?? 1);
$children = intval($_GET['children'] ?? 0);
$infants = intval($_GET['infants'] ?? 0);
$passengers = $adults . ' adulte(s)';
if($children > 0) $passengers .= ', ' . $children . ' enfant(s)';
if($infants > 0) $passengers .= ', ' . $infants . ' bébé(s)';

$totalPrice = 0;
$serviceLabel = '';

switch($type) {
    case 'flight':
        $totalPrice = $item['price'];
        $serviceLabel = 'Vol ' . $item['airline'] . ' - ' . $item['flight_number'];
        break;
    case 'hotel':
        $totalPrice = $item['price_per_night'];
        $serviceLabel = 'Hôtel ' . $item['name'];
        break;
    case 'car':
        $totalPrice = $item['price_per_day'];
        $serviceLabel = 'Location ' . $item['model'];
        break;
    case 'package':
        $totalPrice = $item['total_price_usd'] ?? $item['price'] ?? 0;
        $serviceLabel = 'Package ' . ($item['package']['name'] ?? $item['name'] ?? 'Vacances');
        break;
    case 'insurance':
        $totalPrice = $item['total_price_usd'] ?? 0;
        $zoneNames = [
            'africa_asia' => 'Afrique & Asie',
            'schengen' => 'Espace Schengen',
            'world_wide' => 'Monde entier'
        ];
        $zoneName = $zoneNames[$item['zone']] ?? $item['zone'];
        $serviceLabel = 'Assurance ' . $zoneName . ' - ' . $item['duration'] . ' jours';
        break;
}
?>

<section class="pt-32 pb-20 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-6 max-w-4xl">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- En-tête -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-8 py-6">
                <h1 class="text-2xl md:text-3xl font-bold">
                    <?php if($type === 'insurance'): ?>
                    Complétez vos informations pour l'émission de votre assurance
                    <?php else: ?>
                    Finaliser votre réservation
                    <?php endif; ?>
                </h1>
                <p class="text-blue-100 mt-2"><?php echo htmlspecialchars($serviceLabel); ?></p>
            </div>
            
            <!-- Scanner passport (uniquement pour vols) -->
            <?php if($type !== 'insurance' && $type !== 'package'): ?>
            <div class="px-8 pt-6">
                <button type="button" id="scanPassportBtn" class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2 hover:opacity-90 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Scanner mon passeport
                </button>
                <p class="text-xs text-gray-500 mt-2">Utilisez votre caméra pour scanner automatiquement les informations</p>
            </div>
            <?php endif; ?>
            
            <form action="payment.php" method="POST" class="p-8">
                <input type="hidden" name="type" value="<?php echo $type; ?>">
                <input type="hidden" name="item" value="<?php echo htmlspecialchars($itemData); ?>">
                <?php if($type === 'insurance'): ?>
                <input type="hidden" name="insurance_data" value="<?php echo htmlspecialchars($insuranceData); ?>">
                <?php endif; ?>
                <?php if($type === 'package'): ?>
                <input type="hidden" name="package_data" value="<?php echo htmlspecialchars($packageData); ?>">
                <?php endif; ?>
                <input type="hidden" name="total_price" value="<?php echo $totalPrice; ?>">
                <input type="hidden" name="origin" value="<?php echo $origin; ?>">
                <input type="hidden" name="destination" value="<?php echo $destination; ?>">
                <input type="hidden" name="departure_date" value="<?php echo $departure_date; ?>">
                <input type="hidden" name="passengers" value="<?php echo $adults + $children + $infants; ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Informations personnelles -->
                    <div>
                        <h2 class="text-xl font-bold mb-4 text-gray-800">
                            <?php if($type === 'insurance'): ?>
                            Vos informations pour l'assurance
                            <?php else: ?>
                            Vos informations
                            <?php endif; ?>
                        </h2>
                        
                        <div class="space-y-4">
                            <!-- Civilité -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Civilité *</label>
                                <select name="title" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                                    <option value="">Sélectionner</option>
                                    <option value="mr">Monsieur</option>
                                    <option value="mme">Madame</option>
                                    <option value="mlle">Mademoiselle</option>
                                    <option value="dr">Docteur</option>
                                    <option value="pr">Professeur</option>
                                    <option value="gen">Général</option>
                                    <option value="col">Colonel</option>
                                    <option value="cmd">Commandant</option>
                                    <option value="cap">Capitaine</option>
                                </select>
                            </div>
                            
                            <!-- Sexe (automatique mais modifiable) -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Sexe *</label>
                                <select name="gender" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                                    <option value="">Sélectionner</option>
                                    <option value="M">Masculin</option>
                                    <option value="F">Féminin</option>
                                    <option value="AUTRE">Autre / Non précisé</option>
                                </select>
                                <p class="text-xs text-gray-400 mt-1">Se remplit automatiquement selon la civilité, mais vous pouvez modifier si nécessaire</p>
                            </div>
                            
                            <!-- Nom et Prénom -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Nom *</label>
                                    <input type="text" name="lastname" required
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Prénom *</label>
                                    <input type="text" name="firstname" required
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            
                            <!-- Date de naissance (obligatoire pour assurance) - SANS restriction de date -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">
                                    Date de naissance <?php if($type === 'insurance'): ?>*<?php endif; ?>
                                </label>
                                <input type="date" name="birth_date" 
                                       <?php if($type === 'insurance'): ?>required<?php endif; ?>
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-400 mt-1">Format: JJ/MM/AAAA - Date de naissance réelle</p>
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Email *</label>
                                <input type="email" name="email" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <!-- Téléphone / WhatsApp -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">
                                    Téléphone / WhatsApp *
                                </label>
                                <input type="tel" name="phone" required placeholder="+243 XXX XXX XXX"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-400 mt-1">Vous recevrez votre certificat par WhatsApp</p>
                            </div>
                            
                            <!-- Adresse -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Adresse</label>
                                <textarea name="address" rows="2" placeholder="Votre adresse complète"
                                          class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                            
                            <!-- Numéro de passeport -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Numéro de passeport</label>
                                <input type="text" name="passport_number" placeholder="Ex: AA1234567"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-400 mt-1">Obligatoire pour les voyages internationaux</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Détails du voyage -->
                    <div>
                        <h2 class="text-xl font-bold mb-4 text-gray-800">Détails du voyage</h2>
                        
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <?php if($type == 'flight'): ?>
                                <div><span class="text-gray-500">Compagnie:</span> <span class="font-semibold"><?php echo $item['airline']; ?></span></div>
                                <div><span class="text-gray-500">Vol:</span> <span class="font-semibold"><?php echo $item['flight_number']; ?></span></div>
                                <div><span class="text-gray-500">Trajet:</span> <?php echo $origin; ?> → <?php echo $destination; ?></div>
                                <div><span class="text-gray-500">Date départ:</span> <?php echo date('d/m/Y', strtotime($departure_date)); ?></div>
                                <div><span class="text-gray-500">Heure départ:</span> <?php echo date('H:i', strtotime($item['departure_time'] ?? 'now')); ?></div>
                                <div><span class="text-gray-500">Heure arrivée:</span> <?php echo date('H:i', strtotime($item['arrival_time'] ?? 'now')); ?></div>
                                <div><span class="text-gray-500">Passagers:</span> <?php echo $passengers; ?></div>
                                <div><span class="text-gray-500">Classe:</span> <?php echo $item['class']; ?></div>
                                
                            <?php elseif($type == 'hotel'): ?>
                                <div><span class="text-gray-500">Hôtel:</span> <span class="font-semibold"><?php echo $item['name']; ?></span></div>
                                <div><span class="text-gray-500">Adresse:</span> <?php echo $item['address']; ?></div>
                                <div><span class="text-gray-500">Check-in:</span> <?php echo date('d/m/Y', strtotime($_GET['check_in'])); ?></div>
                                <div><span class="text-gray-500">Check-out:</span> <?php echo date('d/m/Y', strtotime($_GET['check_out'])); ?></div>
                                <div><span class="text-gray-500">Voyageurs:</span> <?php echo $_GET['guests'] ?? 2; ?> personnes</div>
                                <div><span class="text-gray-500">Note:</span> <?php echo str_repeat('★', $item['rating'] ?? 4); ?></div>
                                <input type="hidden" name="check_in" value="<?php echo $_GET['check_in']; ?>">
                                <input type="hidden" name="check_out" value="<?php echo $_GET['check_out']; ?>">
                                <input type="hidden" name="guests" value="<?php echo $_GET['guests'] ?? 2; ?>">
                                
                            <?php elseif($type == 'car'): ?>
                                <div><span class="text-gray-500">Véhicule:</span> <span class="font-semibold"><?php echo $item['model']; ?></span></div>
                                <div><span class="text-gray-500">Agence:</span> <?php echo $item['company']; ?></div>
                                <div><span class="text-gray-500">Prise en charge:</span> <?php echo date('d/m/Y', strtotime($_GET['pickup_date'])); ?></div>
                                <div><span class="text-gray-500">Retour:</span> <?php echo date('d/m/Y', strtotime($_GET['return_date'])); ?></div>
                                <div><span class="text-gray-500">Transmission:</span> <?php echo $item['transmission'] == 'automatic' ? 'Automatique' : 'Manuelle'; ?></div>
                                <input type="hidden" name="pickup_date" value="<?php echo $_GET['pickup_date']; ?>">
                                <input type="hidden" name="return_date" value="<?php echo $_GET['return_date']; ?>">
                                
                            <?php elseif($type == 'package'): ?>
                                <?php $package = $item['package'] ?? $item; ?>
                                <div><span class="text-gray-500">Package:</span> <span class="font-semibold"><?php echo $package['name']; ?></span></div>
                                <div><span class="text-gray-500">Destination:</span> <?php echo $package['destination']; ?></div>
                                <div><span class="text-gray-500">Durée:</span> <?php echo $package['duration_nights']; ?> nuits</div>
                                <div><span class="text-gray-500">Inclus:</span> <?php echo implode(', ', $package['includes']); ?></div>
                                <div><span class="text-gray-500">Voyageurs:</span> <?php echo $item['adults'] + $item['children']; ?> personnes</div>
                                <?php if($item['has_visa'] ?? false): ?>
                                <div><span class="text-green-600">✓ Visa inclus</span></div>
                                <?php endif; ?>
                                <?php if($item['has_transfer'] ?? false): ?>
                                <div><span class="text-blue-600">✓ Transfert aéroport inclus</span></div>
                                <?php endif; ?>
                                <?php if($item['has_insurance'] ?? false): ?>
                                <div><span class="text-purple-600">✓ Assurance voyage incluse</span></div>
                                <?php endif; ?>
                                <?php if($item['has_activities'] ?? false): ?>
                                <div><span class="text-orange-600">✓ Activités incluses</span></div>
                                <?php endif; ?>
                            
                            <?php elseif($type == 'insurance'): ?>
                                <div><span class="text-gray-500">Assureur:</span> <span class="font-semibold">EKTA Assurance</span></div>
                                <div><span class="text-gray-500">Zone:</span> <?php 
                                    $zoneNames = ['africa_asia' => 'Afrique & Asie', 'schengen' => 'Espace Schengen', 'world_wide' => 'Monde entier'];
                                    echo $zoneNames[$item['zone']] ?? $item['zone']; 
                                ?></div>
                                <div><span class="text-gray-500">Durée:</span> <?php echo $item['duration']; ?> jours</div>
                                <div><span class="text-gray-500">Voyageurs:</span> <?php echo $item['travelers']; ?></div>
                                <div><span class="text-gray-500">Âges:</span> <?php echo !empty($item['ages']) ? implode(', ', $item['ages']) . ' ans' : 'Adultes'; ?></div>
                                <?php if($item['has_sport'] ?? false): ?>
                                <div><span class="text-green-600">✓ Assurance sport incluse (+30%)</span></div>
                                <?php endif; ?>
                                <?php if($item['has_extreme'] ?? false): ?>
                                <div><span class="text-orange-600">✓ Activités extrêmes incluses (+40%)</span></div>
                                <?php endif; ?>
                                <div class="mt-2 pt-2 border-t">
                                    <div class="flex justify-between"><span class="text-gray-500">Couverture médicale:</span> <span class="font-semibold text-green-600"><?php echo $item['coverage']['medical'] ?? '50 000 USD'; ?></span></div>
                                    <div class="flex justify-between"><span class="text-gray-500">Rapatriement:</span> <span class="font-semibold text-green-600">Inclus</span></div>
                                    <div class="flex justify-between"><span class="text-gray-500">COVID-19:</span> <span class="font-semibold text-green-600">Inclus</span></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Récapitulatif prix -->
                        <div class="mt-6">
                            <h3 class="font-bold text-gray-800 mb-3">Récapitulatif</h3>
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total à payer</span>
                                    <span class="text-blue-600"><?php echo displayPrice($totalPrice); ?></span>
                                </div>
                                <p class="text-gray-500 text-xs text-right">
                                    <?php if($type == 'flight') echo 'par passager'; ?>
                                    <?php if($type == 'hotel') echo 'total pour le séjour'; ?>
                                    <?php if($type == 'car') echo 'total location'; ?>
                                    <?php if($type == 'package') echo 'total pour le groupe'; ?>
                                    <?php if($type == 'insurance') echo 'total pour tous les voyageurs'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mode de paiement -->
                <div class="mt-8">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Mode de paiement</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="border rounded-lg p-4 cursor-pointer hover:border-blue-500 transition">
                            <input type="radio" name="payment_method" value="mpesa" required class="mr-2">
                            <span class="font-semibold">M-Pesa</span>
                            <p class="text-xs text-gray-500 mt-1">Paiement mobile</p>
                        </label>
                        <label class="border rounded-lg p-4 cursor-pointer hover:border-blue-500 transition">
                            <input type="radio" name="payment_method" value="orange_money" required class="mr-2">
                            <span class="font-semibold">Orange Money</span>
                            <p class="text-xs text-gray-500 mt-1">Paiement mobile</p>
                        </label>
                        <label class="border rounded-lg p-4 cursor-pointer hover:border-blue-500 transition">
                            <input type="radio" name="payment_method" value="card" required class="mr-2">
                            <span class="font-semibold">Carte bancaire</span>
                            <p class="text-xs text-gray-500 mt-1">Visa / Mastercard</p>
                        </label>
                    </div>
                </div>
                
                <!-- Bouton récapitulatif -->
                <button type="button" id="openRecapModal" class="mt-8 w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-xl font-bold text-lg transition">
                    Voir le récapitulatif
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Modal scanner (uniquement pour vols) -->
<?php if($type !== 'insurance' && $type !== 'package'): ?>
<div id="scannerModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl max-w-lg w-full mx-4 overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="font-bold text-lg">Scanner votre passeport</h3>
            <button id="closeScannerBtn" class="text-gray-400 text-2xl">&times;</button>
        </div>
        <div class="p-4">
            <div id="reader" style="width: 100%;"></div>
            <p class="text-sm text-gray-500 mt-3 text-center">Placez votre passeport face à la caméra</p>
        </div>
        <div class="p-4 border-t">
            <button id="manualEntryBtn" class="w-full bg-gray-200 py-2 rounded-lg">Saisie manuelle</button>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal récapitulatif -->
<div id="recapModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white p-4 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold">Récapitulatif de votre voyage</h2>
            <button id="closeRecapBtn" class="text-gray-400 text-2xl">&times;</button>
        </div>
        
        <div class="p-6 space-y-6">
            <?php if($type == 'flight'): ?>
            <!-- Détails du vol -->
            <div class="border-b pb-4">
                <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2z"></path>
                    </svg>
                    Détails du vol
                </h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500">Compagnie:</span> <span class="font-semibold"><?php echo $item['airline'] ?? '-'; ?></span></div>
                    <div><span class="text-gray-500">Vol:</span> <span class="font-semibold"><?php echo $item['flight_number'] ?? '-'; ?></span></div>
                    <div><span class="text-gray-500">Trajet:</span> <?php echo $origin ?? '-'; ?> → <?php echo $destination ?? '-'; ?></div>
                    <div><span class="text-gray-500">Date:</span> <?php echo date('d/m/Y', strtotime($departure_date ?? 'now')); ?></div>
                    <div><span class="text-gray-500">Heure départ:</span> <?php echo date('H:i', strtotime($item['departure_time'] ?? 'now')); ?></div>
                    <div><span class="text-gray-500">Heure arrivée:</span> <?php echo date('H:i', strtotime($item['arrival_time'] ?? 'now')); ?></div>
                    <div><span class="text-gray-500">Classe:</span> <?php echo $item['class'] ?? 'Economy'; ?></div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Informations passagers -->
            <div class="border-b pb-4">
                <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Passagers
                </h3>
                <p><?php echo $passengers; ?></p>
            </div>
            
            <!-- Conditions d'annulation -->
            <div class="border-b pb-4">
                <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Conditions d'annulation
                </h3>
                <div class="space-y-2 text-sm">
                    <p>✓ Annulation gratuite jusqu'à 24h avant le départ</p>
                    <p>✓ Remboursement intégral sous 5-7 jours ouvrés</p>
                    <p>✓ Modification gratuite sous réserve de disponibilité</p>
                    <p class="text-xs text-gray-500 mt-2">Après le délai de 24h, des frais d'annulation de 50% s'appliquent</p>
                </div>
            </div>
            
            <!-- Tarifs et taxes -->
            <div class="border-b pb-4">
                <h3 class="font-bold text-lg mb-3">Détail des tarifs</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span>Prix de base</span><span><?php echo displayPrice($totalPrice * 0.85); ?></span></div>
                    <div class="flex justify-between"><span>Taxes aéroport</span><span><?php echo displayPrice($totalPrice * 0.10); ?></span></div>
                    <div class="flex justify-between"><span>Frais de service</span><span><?php echo displayPrice($totalPrice * 0.05); ?></span></div>
                    <div class="flex justify-between font-bold pt-2 border-t"><span>Total</span><span class="text-blue-600"><?php echo displayPrice($totalPrice); ?></span></div>
                </div>
            </div>
            
            <!-- Petit rappel -->
            <div class="bg-blue-50 rounded-lg p-4 text-sm">
                <p class="font-semibold mb-1">À savoir avant de partir :</p>
                <p>✓ Passeport valide (6 mois minimum)</p>
                <p>✓ Visa selon destination</p>
                <p>✓ Vaccins à jour (Fièvre jaune obligatoire)</p>
            </div>
        </div>
        
        <div class="sticky bottom-0 bg-white p-4 border-t">
            <button id="confirmAndPayBtn" class="w-full bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700 transition">
                Je confirme et je paie
            </button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
<script>
// ==================== FORCER LA MISE EN MAJUSCULES ====================
function forceUpperCase() {
    const upperCaseFields = ['lastname', 'firstname', 'passport_number'];
    
    upperCaseFields.forEach(fieldName => {
        const input = document.querySelector(`input[name="${fieldName}"]`);
        if(input) {
            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        }
    });
}

// ==================== SEXE AUTOMATIQUE MAIS MODIFIABLE ====================
function initGenderLogic() {
    const titleSelect = document.querySelector('select[name="title"]');
    const genderSelect = document.querySelector('select[name="gender"]');
    
    if(titleSelect && genderSelect) {
        // Fonction pour définir le sexe par défaut selon la civilité
        function setDefaultGender() {
            const title = titleSelect.value;
            
            switch(title) {
                case 'mr':
                    if(!genderSelect.value || genderSelect.value === '') {
                        genderSelect.value = 'M';
                    }
                    break;
                case 'mme':
                case 'mlle':
                    if(!genderSelect.value || genderSelect.value === '') {
                        genderSelect.value = 'F';
                    }
                    break;
                case 'dr':
                case 'pr':
                case 'gen':
                case 'col':
                case 'cmd':
                case 'cap':
                    // Pour les titres professionnels, on ne force pas de sexe
                    if(!genderSelect.value || genderSelect.value === '') {
                        genderSelect.value = '';
                    }
                    break;
                default:
                    break;
            }
        }
        
        // Appliquer au changement de civilité
        titleSelect.addEventListener('change', function() {
            setDefaultGender();
        });
        
        // Appliquer une fois au chargement
        setDefaultGender();
    }
}

// ==================== SCANNER DE PASSEPORT ====================
<?php if($type !== 'insurance' && $type !== 'package'): ?>
const scannerModal = document.getElementById('scannerModal');
const scanBtn = document.getElementById('scanPassportBtn');
const closeScannerBtn = document.getElementById('closeScannerBtn');
let html5QrCode;

if(scanBtn) {
    scanBtn.addEventListener('click', () => {
        scannerModal.classList.remove('hidden');
        scannerModal.classList.add('flex');
        
        html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 150 } },
            (decodedText) => {
                const passportData = parsePassportMRZ(decodedText);
                if(passportData) {
                    document.querySelector('input[name="lastname"]').value = passportData.lastname.toUpperCase();
                    document.querySelector('input[name="firstname"]').value = passportData.firstname.toUpperCase();
                    document.querySelector('input[name="passport_number"]').value = passportData.number.toUpperCase();
                    html5QrCode.stop();
                    scannerModal.classList.add('hidden');
                    alert('Passeport scanné avec succès');
                }
            },
            (error) => { console.log(error); }
        ).catch(err => console.log(err));
    });
}

function parsePassportMRZ(mrz) {
    if(mrz.length > 88) {
        const lastName = mrz.substring(5, 44).replace(/</g, ' ').trim();
        const firstName = mrz.substring(44, 88).replace(/</g, ' ').trim();
        const passportNumber = mrz.substring(0, 9);
        return { lastname: lastName, firstname: firstName, number: passportNumber };
    }
    return null;
}

if(closeScannerBtn) {
    closeScannerBtn.addEventListener('click', () => {
        if(html5QrCode) html5QrCode.stop();
        scannerModal.classList.add('hidden');
        scannerModal.classList.remove('flex');
    });
}
<?php endif; ?>

// ==================== MODAL RÉCAPITULATIF ====================
const recapModal = document.getElementById('recapModal');
const openRecapBtn = document.getElementById('openRecapModal');
const closeRecapBtn = document.getElementById('closeRecapBtn');
const confirmPayBtn = document.getElementById('confirmAndPayBtn');

if(openRecapBtn) {
    openRecapBtn.addEventListener('click', () => {
        recapModal.classList.remove('hidden');
        recapModal.classList.add('flex');
    });
}

if(closeRecapBtn) {
    closeRecapBtn.addEventListener('click', () => {
        recapModal.classList.add('hidden');
        recapModal.classList.remove('flex');
    });
}

if(confirmPayBtn) {
    confirmPayBtn.addEventListener('click', () => {
        recapModal.classList.add('hidden');
        document.querySelector('form').submit();
    });
}

// ==================== DATE PICKER - DÉSACTIVER DATES PASSÉES (SAUF DATE NAISSANCE) ====================
function disablePastDates() {
    const today = new Date().toISOString().split('T')[0];
    const dateInputs = document.querySelectorAll('input[type="date"]:not([name="birth_date"]):not([id="birth_date"])');
    
    dateInputs.forEach(input => {
        input.setAttribute('min', today);
        
        if(input.value && input.value < today) {
            input.value = today;
        }
        
        input.addEventListener('change', function() {
            if(this.value && this.value < today) {
                this.value = today;
                if(window.showToast) {
                    showToast('Vous ne pouvez pas sélectionner une date passée', 'warning');
                }
            }
        });
    });
}

// ==================== INITIALISATION ====================
document.addEventListener('DOMContentLoaded', function() {
    forceUpperCase();
    initGenderLogic();
    disablePastDates();
});
</script>

<?php require_once 'includes/footer.php'; ?>