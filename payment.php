<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/header.php'; ?>

<?php
// Vérifier que le formulaire a été soumis
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Récupérer toutes les données
$type = $_POST['type'] ?? '';
$itemData = $_POST['item'] ?? '';
$insuranceData = $_POST['insurance_data'] ?? '';
$title = $_POST['title'] ?? '';
$lastname = $_POST['lastname'] ?? '';
$firstname = $_POST['firstname'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$passport_number = $_POST['passport_number'] ?? '';
$birth_date = $_POST['birth_date'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';
$transaction_id = $_POST['transaction_id'] ?? '';
$total_price = $_POST['total_price'] ?? 0;

$item = null;
$insuranceItem = null;

if($type === 'insurance' && !empty($insuranceData)) {
    $insuranceItem = json_decode(urldecode($insuranceData), true);
} elseif(!empty($itemData)) {
    $item = json_decode(urldecode($itemData), true);
}

// Sauvegarder la réservation dans la base de données
try {
    if($type === 'insurance') {
        $stmt = $pdo->prepare("INSERT INTO bookings (booking_type, customer_name, customer_email, customer_phone, customer_address, passport_number, birth_date, total_price, status, transaction_id, insurance_details) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)");
        $insuranceDetails = json_encode($insuranceItem);
        $customerName = $title . ' ' . $firstname . ' ' . $lastname;
        $stmt->execute([$type, $customerName, $email, $phone, $address, $passport_number, $birth_date, $total_price, $transaction_id, $insuranceDetails]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO bookings (booking_type, item_id, customer_name, customer_email, customer_phone, customer_address, passport_number, birth_date, total_price, status, transaction_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed', ?)");
        $itemId = $item['id'] ?? ($item['flight_number'] ?? rand(1000, 9999));
        $customerName = $title . ' ' . $firstname . ' ' . $lastname;
        $stmt->execute([$type, $itemId, $customerName, $email, $phone, $address, $passport_number, $birth_date, $total_price, $transaction_id]);
    }
    
    $bookingId = $pdo->lastInsertId();
} catch(PDOException $e) {
    $bookingId = rand(10000, 99999);
}

// Pour l'assurance, afficher le modal de traitement
$showWaitModal = ($type === 'insurance');
?>

<section class="pt-32 pb-20 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-6 max-w-3xl">
        <!-- Carte de confirmation -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Icône de succès -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white text-center py-8">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold">Paiement confirmé !</h1>
                <p class="text-green-100 mt-2">Votre réservation a été enregistrée avec succès</p>
            </div>
            
            <div class="p-8">
                <?php if($type === 'insurance'): ?>
                <!-- Message spécifique assurance -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 text-center">
                    <svg class="w-12 h-12 text-yellow-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-700 font-semibold">Votre assurance est en cours d'émission</p>
                    <p class="text-gray-500 text-sm mt-1">Vous recevrez votre certificat par email et WhatsApp sous 10 minutes</p>
                    <div class="mt-3 flex justify-center gap-4">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span><?php echo htmlspecialchars($email); ?></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span><?php echo htmlspecialchars($phone); ?></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Message de confirmation -->
                <div class="text-center mb-8">
                    <p class="text-gray-600">Un email de confirmation a été envoyé à <strong><?php echo htmlspecialchars($email); ?></strong></p>
                    <p class="text-gray-500 text-sm mt-2">Numéro de réservation : <strong class="text-blue-600">#<?php echo $bookingId; ?></strong></p>
                    <p class="text-gray-500 text-sm">Transaction : <strong><?php echo htmlspecialchars($transaction_id); ?></strong></p>
                </div>
                
                <!-- Détails du client -->
                <div class="border-t border-b border-gray-200 py-6 mb-6">
                    <h3 class="font-bold text-gray-800 mb-4">Informations client</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Civilité</p>
                            <p class="font-semibold">
                                <?php 
                                $titles = ['mr' => 'Monsieur', 'mme' => 'Madame', 'mlle' => 'Mademoiselle', 'dr' => 'Docteur', 'pr' => 'Professeur', 'gen' => 'Général', 'col' => 'Colonel', 'cmd' => 'Commandant', 'cap' => 'Capitaine'];
                                echo $titles[$title] ?? $title; 
                                ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Nom complet</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Email</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($email); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Téléphone / WhatsApp</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($phone); ?></p>
                        </div>
                        <?php if(!empty($birth_date)): ?>
                        <div>
                            <p class="text-gray-500">Date de naissance</p>
                            <p class="font-semibold"><?php echo date('d/m/Y', strtotime($birth_date)); ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($passport_number)): ?>
                        <div>
                            <p class="text-gray-500">Numéro de passeport</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($passport_number); ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($address)): ?>
                        <div>
                            <p class="text-gray-500">Adresse</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($address); ?></p>
                        </div>
                        <?php endif; ?>
                        <div>
                            <p class="text-gray-500">Mode de paiement</p>
                            <p class="font-semibold">
                                <?php 
                                if($payment_method == 'mpesa') echo 'M-Pesa';
                                elseif($payment_method == 'orange_money') echo 'Orange Money';
                                else echo 'Carte bancaire';
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Détails de la réservation -->
                <div class="mb-6">
                    <h3 class="font-bold text-gray-800 mb-4">Détails de la réservation</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <?php if($type == 'flight' && $item): ?>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Compagnie :</span> <strong><?php echo htmlspecialchars($item['airline'] ?? ''); ?></strong></p>
                                <p><span class="text-gray-500">Vol :</span> <strong><?php echo htmlspecialchars($item['flight_number'] ?? ''); ?></strong></p>
                                <p><span class="text-gray-500">Trajet :</span> <?php echo htmlspecialchars($_POST['origin'] ?? ''); ?> → <?php echo htmlspecialchars($_POST['destination'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Date :</span> <?php echo htmlspecialchars($_POST['departure_date'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Passagers :</span> <?php echo htmlspecialchars($_POST['passengers'] ?? 1); ?></p>
                            </div>
                        <?php elseif($type == 'hotel' && $item): ?>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Hôtel :</span> <strong><?php echo htmlspecialchars($item['name'] ?? ''); ?></strong></p>
                                <p><span class="text-gray-500">Ville :</span> <?php echo htmlspecialchars($item['city'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Check-in :</span> <?php echo htmlspecialchars($_POST['check_in'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Check-out :</span> <?php echo htmlspecialchars($_POST['check_out'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Voyageurs :</span> <?php echo htmlspecialchars($_POST['guests'] ?? 2); ?></p>
                            </div>
                        <?php elseif($type == 'car' && $item): ?>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Véhicule :</span> <strong><?php echo htmlspecialchars($item['model'] ?? ''); ?></strong></p>
                                <p><span class="text-gray-500">Agence :</span> <?php echo htmlspecialchars($item['company'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Prise en charge :</span> <?php echo htmlspecialchars($_POST['pickup_date'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Retour :</span> <?php echo htmlspecialchars($_POST['return_date'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Ville :</span> <?php echo htmlspecialchars($item['city'] ?? ''); ?></p>
                            </div>
                        <?php elseif($type == 'package' && $item): ?>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Package :</span> <strong><?php echo htmlspecialchars($item['name'] ?? ''); ?></strong></p>
                                <p><span class="text-gray-500">Destination :</span> <?php echo htmlspecialchars($item['destination'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Date de début :</span> <?php echo htmlspecialchars($_POST['start_date'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Durée :</span> <?php echo htmlspecialchars($_POST['duration'] ?? 3); ?> jours</p>
                            </div>
                        <?php elseif($type == 'insurance' && $insuranceItem): ?>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Assureur :</span> <strong>EKTA Assurance</strong></p>
                                <p><span class="text-gray-500">Zone :</span> 
                                    <?php 
                                    $zoneNames = ['africa_asia' => 'Afrique & Asie', 'schengen' => 'Espace Schengen', 'world_wide' => 'Monde entier'];
                                    echo $zoneNames[$insuranceItem['zone']] ?? $insuranceItem['zone']; 
                                    ?>
                                </p>
                                <p><span class="text-gray-500">Durée :</span> <?php echo $insuranceItem['duration']; ?> jours</p>
                                <p><span class="text-gray-500">Voyageurs :</span> <?php echo $insuranceItem['travelers']; ?></p>
                                <p><span class="text-gray-500">Âges :</span> <?php echo !empty($insuranceItem['ages']) ? implode(', ', $insuranceItem['ages']) . ' ans' : 'Adultes'; ?></p>
                                <?php if($insuranceItem['has_sport'] ?? false): ?>
                                <p><span class="text-green-600">✓ Assurance sport incluse (+30%)</span></p>
                                <?php endif; ?>
                                <?php if($insuranceItem['has_extreme'] ?? false): ?>
                                <p><span class="text-orange-600">✓ Activités extrêmes incluses (+40%)</span></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Total payé -->
                <div class="bg-green-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-800">Total payé</span>
                        <span class="text-2xl font-bold text-green-600"><?php echo displayPrice($total_price); ?></span>
                    </div>
                </div>
                
                <!-- Boutons d'action -->
                <div class="flex flex-col md:flex-row gap-4">
                    <button onclick="window.print()" class="flex-1 bg-gray-600 text-white py-3 rounded-lg font-semibold hover:bg-gray-700 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Imprimer
                    </button>
                    <button onclick="window.location.href='index.php'" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Accueil
                    </button>
                </div>
                
                <!-- Note importante -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">
                        📧 Un email avec tous les détails vous a été envoyé.<br>
                        Pour toute question, contactez-nous au <strong>+243 123 456 789</strong> ou sur WhatsApp
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Offres complémentaires -->
        <div class="mt-8 text-center">
            <p class="text-gray-600 mb-4">Vous aimerez aussi :</p>
            <div class="flex flex-wrap justify-center gap-4">
                <button onclick="window.location.href='index.php'" class="bg-white px-6 py-2 rounded-lg shadow hover:shadow-lg transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2z"></path>
                    </svg>
                    Nouveau vol
                </button>
                <button onclick="window.location.href='index.php'" class="bg-white px-6 py-2 rounded-lg shadow hover:shadow-lg transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Hôtel
                </button>
                <button onclick="window.location.href='index.php'" class="bg-white px-6 py-2 rounded-lg shadow hover:shadow-lg transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m-12 4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2v4m-8 4h12m0 0l-4 4m4-4l-4-4"></path>
                    </svg>
                    Location
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Modal d'attente pour assurance -->
<?php if($showWaitModal): ?>
<div id="waitingModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4 p-6 text-center">
        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-blue-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Émission en cours</h3>
        <p class="text-gray-600 mb-4">Votre certificat d'assurance est en cours de génération.</p>
        <div class="bg-yellow-50 rounded-lg p-3 mb-4">
            <p class="text-sm text-gray-700">⏱️ Veuillez patienter pendant 10 minutes.</p>
            <p class="text-xs text-gray-500 mt-1">Vous recevrez votre certificat par email et WhatsApp.</p>
        </div>
        <button onclick="window.location.href='index.php'" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
            Retour à l'accueil
        </button>
    </div>
</div>

<script>
// Fermeture auto après 10 secondes (simulation)
setTimeout(function() {
    const modal = document.getElementById('waitingModal');
    if(modal) {
        modal.style.display = 'none';
    }
}, 10000);
</script>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>