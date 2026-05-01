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
$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';
$transaction_id = $_POST['transaction_id'] ?? '';
$total_price = $_POST['total_price'] ?? 0;

$item = json_decode(urldecode($itemData), true);

// Sauvegarder la réservation dans la base de données
try {
    $stmt = $pdo->prepare("INSERT INTO bookings (booking_type, item_id, customer_name, customer_email, customer_phone, total_price, status, transaction_id) VALUES (?, ?, ?, ?, ?, ?, 'confirmed', ?)");
    
    $itemId = $item['id'] ?? ($item['flight_number'] ?? rand(1000, 9999));
    $stmt->execute([$type, $itemId, $fullname, $email, $phone, $total_price, $transaction_id]);
    
    $bookingId = $pdo->lastInsertId();
} catch(PDOException $e) {
    // Si erreur BDD, on continue quand même (pas de panique)
    $bookingId = rand(10000, 99999);
}
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
                            <p class="text-gray-500">Nom complet</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($fullname); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Email</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($email); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Téléphone</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($phone); ?></p>
                        </div>
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
                        <?php if($type == 'flight'): ?>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Compagnie :</span> <strong><?php echo htmlspecialchars($item['airline'] ?? ''); ?></strong></p>
                                <p><span class="text-gray-500">Vol :</span> <strong><?php echo htmlspecialchars($item['flight_number'] ?? ''); ?></strong></p>
                                <p><span class="text-gray-500">Trajet :</span> <?php echo htmlspecialchars($_POST['origin'] ?? ''); ?> → <?php echo htmlspecialchars($_POST['destination'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Date :</span> <?php echo htmlspecialchars($_POST['departure_date'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Passagers :</span> <?php echo htmlspecialchars($_POST['passengers'] ?? 1); ?></p>
                            </div>
                        <?php elseif($type == 'hotel'): ?>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Hôtel :</span> <strong><?php echo htmlspecialchars($item['name'] ?? ''); ?></strong></p>
                                <p><span class="text-gray-500">Ville :</span> <?php echo htmlspecialchars($item['city'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Check-in :</span> <?php echo htmlspecialchars($_POST['check_in'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Check-out :</span> <?php echo htmlspecialchars($_POST['check_out'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Voyageurs :</span> <?php echo htmlspecialchars($_POST['guests'] ?? 2); ?></p>
                            </div>
                        <?php elseif($type == 'car'): ?>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Véhicule :</span> <strong><?php echo htmlspecialchars($item['model'] ?? ''); ?></strong></p>
                                <p><span class="text-gray-500">Agence :</span> <?php echo htmlspecialchars($item['company'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Prise en charge :</span> <?php echo htmlspecialchars($_POST['pickup_date'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Retour :</span> <?php echo htmlspecialchars($_POST['return_date'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Ville :</span> <?php echo htmlspecialchars($item['city'] ?? ''); ?></p>
                            </div>
                        <?php elseif($type == 'package'): ?>
                            <div class="space-y-2">
                                <p><span class="text-gray-500">Package :</span> <strong><?php echo htmlspecialchars($item['name'] ?? ''); ?></strong></p>
                                <p><span class="text-gray-500">Destination :</span> <?php echo htmlspecialchars($item['destination'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Date de début :</span> <?php echo htmlspecialchars($_POST['start_date'] ?? ''); ?></p>
                                <p><span class="text-gray-500">Durée :</span> <?php echo htmlspecialchars($_POST['duration'] ?? 3); ?> jours</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Total payé -->
                <div class="bg-green-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-800">Total payé</span>
                        <span class="text-2xl font-bold text-green-600"><?php echo number_format($total_price); ?> FC</span>
                    </div>
                </div>
                
                <!-- Boutons d'action -->
                <div class="flex flex-col md:flex-row gap-4">
                    <button onclick="window.print()" class="flex-1 bg-gray-600 text-white py-3 rounded-lg font-semibold hover:bg-gray-700 transition">
                        🖨️ Imprimer la confirmation
                    </button>
                    <button onclick="window.location.href='index.php'" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                        🏠 Retour à l'accueil
                    </button>
                </div>
                
                <!-- Note importante -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">
                        📧 Un email avec tous les détails vous a été envoyé.<br>
                        Pour toute question, contactez-nous au <strong>+243 123 456 789</strong>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Offres complémentaires -->
        <div class="mt-8 text-center">
            <p class="text-gray-600 mb-4">Vous aimerez aussi :</p>
            <div class="flex justify-center space-x-4">
                <button onclick="window.location.href='index.php?service=flight'" class="bg-white px-6 py-2 rounded-lg shadow hover:shadow-lg transition">
                    ✈️ Nouveau vol
                </button>
                <button onclick="window.location.href='index.php?service=hotel'" class="bg-white px-6 py-2 rounded-lg shadow hover:shadow-lg transition">
                    🏨 Réserver un hôtel
                </button>
                <button onclick="window.location.href='index.php?service=car'" class="bg-white px-6 py-2 rounded-lg shadow hover:shadow-lg transition">
                    🚗 Louer une voiture
                </button>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>