<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/header.php'; ?>

<?php
$hotelId = $_GET['id'] ?? 0;
$checkIn = $_GET['check_in'] ?? date('Y-m-d', strtotime('+1 day'));
$checkOut = $_GET['check_out'] ?? date('Y-m-d', strtotime('+3 days'));
$guests = $_GET['guests'] ?? 2;

$hotelDetails = getHotelDetails($hotelId, $checkIn, $checkOut, $guests);
$selectedCurrency = $_COOKIE['selected_currency'] ?? 'USD';

function displayPrice($priceInUSD) {
    global $selectedCurrency;
    return formatPrice($priceInUSD, $selectedCurrency);
}
?>

<section class="pt-32 pb-20 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-6 max-w-6xl">
        
        <!-- En-tête avec image principale -->
        <div class="relative rounded-2xl overflow-hidden mb-8 shadow-xl">
            <?php if(!empty($hotelDetails['images'][0])): ?>
                <img src="<?php echo $hotelDetails['images'][0]; ?>" alt="<?php echo $hotelDetails['name']; ?>" class="w-full h-96 object-cover">
            <?php else: ?>
                <div class="w-full h-96 bg-gradient-to-r from-blue-500 to-blue-700 flex items-center justify-center">
                    <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            <?php endif; ?>
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-8">
                <h1 class="text-4xl font-bold text-white"><?php echo htmlspecialchars($hotelDetails['name']); ?></h1>
                <div class="flex items-center gap-4 mt-2">
                    <div class="flex items-center gap-1">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <svg class="w-5 h-5 <?php echo $i <= ($hotelDetails['stars'] ?? 3) ? 'text-yellow-400' : 'text-gray-400'; ?> fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        <?php endfor; ?>
                    </div>
                    <span class="text-white"><?php echo $hotelDetails['rating']; ?> / 10</span>
                    <span class="text-white">(<?php echo $hotelDetails['review_count']; ?> avis)</span>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Colonne gauche - Infos -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Description -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">À propos de l'hôtel</h2>
                    <p class="text-gray-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($hotelDetails['description'])); ?></p>
                    <div class="mt-4 pt-4 border-t">
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <div><span class="font-semibold">Check-in:</span> <?php echo $hotelDetails['check_in_time']; ?></div>
                            <div><span class="font-semibold">Check-out:</span> <?php echo $hotelDetails['check_out_time']; ?></div>
                            <div><span class="font-semibold">Adresse:</span> <?php echo $hotelDetails['address']; ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Types de chambres -->
                <?php if(!empty($hotelDetails['rooms'])): ?>
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">Types de chambres</h2>
                    <div class="space-y-4">
                        <?php foreach($hotelDetails['rooms'] as $room): ?>
                        <div class="border rounded-xl p-4 hover:shadow-md transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($room['name']); ?></h3>
                                    <p class="text-gray-500 text-sm mt-1"><?php echo htmlspecialchars($room['description']); ?></p>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">Jusqu'à <?php echo $room['max_occupancy']; ?> personnes</span>
                                        <?php foreach($room['facilities'] as $facility): ?>
                                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded"><?php echo $facility; ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-blue-600"><?php echo displayPrice($room['price']); ?></p>
                                    <p class="text-xs text-gray-500">par nuit</p>
                                    <button class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">Réserver</button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Galerie photos -->
                <?php if(count($hotelDetails['images']) > 1): ?>
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">Galerie photos</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <?php foreach(array_slice($hotelDetails['images'], 1, 6) as $image): ?>
                            <img src="<?php echo $image; ?>" class="rounded-lg h-32 w-full object-cover cursor-pointer hover:opacity-80 transition" onclick="window.open(this.src)">
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Équipements -->
                <?php if(!empty($hotelDetails['amenities'])): ?>
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">Équipements</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <?php foreach($hotelDetails['amenities'] as $amenity): ?>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <?php echo htmlspecialchars($amenity); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Colonne droite - Réservation -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl p-6 sticky top-32">
                    <h3 class="text-xl font-bold mb-4">Votre séjour</h3>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Dates</span>
                            <span class="font-semibold"><?php echo date('d/m/Y', strtotime($checkIn)); ?> → <?php echo date('d/m/Y', strtotime($checkOut)); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Voyageurs</span>
                            <span class="font-semibold"><?php echo $guests; ?> personne(s)</span>
                        </div>
                        <div class="border-t pt-3 mt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total séjour</span>
                                <span class="text-blue-600"><?php echo displayPrice(array_sum(array_column($hotelDetails['rooms'], 'price'))); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <form action="booking.php" method="GET">
                        <input type="hidden" name="type" value="hotel">
                        <input type="hidden" name="item" value="<?php echo htmlspecialchars(json_encode($hotelDetails)); ?>">
                        <input type="hidden" name="check_in" value="<?php echo $checkIn; ?>">
                        <input type="hidden" name="check_out" value="<?php echo $checkOut; ?>">
                        <input type="hidden" name="guests" value="<?php echo $guests; ?>">
                        
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold text-lg transition">
                            Réserver maintenant
                        </button>
                    </form>
                    
                    <div class="mt-4 text-center text-sm text-gray-500">
                        <p>✓ Paiement sécurisé</p>
                        <p>✓ Annulation gratuite sous 24h</p>
                        <p>✓ Meilleur prix garanti</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>