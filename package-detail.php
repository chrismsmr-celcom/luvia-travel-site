<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/header.php'; ?>

<?php
// Récupérer la devise choisie
$selectedCurrency = $_COOKIE['selected_currency'] ?? $_GET['currency'] ?? 'USD';
if(isset($_GET['currency'])) {
    setcookie('selected_currency', $_GET['currency'], time() + 86400 * 30, '/');
    $selectedCurrency = $_GET['currency'];
}
$selectedCurrency = $_COOKIE['selected_currency'] ?? 'USD';
// Fonction helper pour afficher les prix convertis
function displayPrice($priceInUSD) {
    global $selectedCurrency;
    return formatPrice($priceInUSD, $selectedCurrency);
}
$packageId = $_GET['id'] ?? 0;
$packages = getPackages();
$package = null;
foreach($packages as $p) {
    if($p['id'] == $packageId) {
        $package = $p;
        break;
    }
}
if(!$package) {
    header('Location: index.php');
    exit;
}
?>

<section class="pt-32 pb-20 bg-gray-50">
    <div class="container mx-auto px-6 max-w-6xl">
        <!-- En-tete du package -->
        <div class="mb-8">
            <a href="index.php" class="text-blue-600 hover:underline mb-4 inline-block flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Retour aux packages
            </a>
            <h1 class="text-4xl font-bold text-gray-800 mt-2"><?php echo htmlspecialchars($package['name']); ?></h1>
            <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($package['destination']); ?> • <?php echo $package['duration_nights']; ?> nuits</p>
        </div>
        
        <!-- Image principale -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <img src="<?php echo $package['image']; ?>" alt="<?php echo $package['name']; ?>" class="w-full h-96 object-cover">
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Colonne gauche - Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Description -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">Description du voyage</h2>
                    <p class="text-gray-600 leading-relaxed"><?php echo htmlspecialchars($package['description']); ?></p>
                </div>
                
                <!-- Details du vol -->
                <?php if(!empty($package['airline'])): ?>
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">Vol inclus</h2>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-lg"><?php echo htmlspecialchars($package['airline']); ?></p>
                            <p class="text-gray-600">Classe <?php echo htmlspecialchars($package['flight_class']); ?></p>
                            <p class="text-2xl font-bold text-blue-600"><?php echo displayPrice($package['flight_price']); ?> / pers</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Visa -->
                <?php if(!empty($package['visa_price'])): ?>
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">Visa</h2>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-lg">Visa inclus</p>
                            <p class="text-gray-600">Passeport requis</p>
                            <p class="text-2xl font-bold text-blue-600"><?php echo displayPrice($package['visa_price']); ?> / pers</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
               <!-- Hotel complet -->
<!-- Hotel complet -->
<?php if(!empty($package['hotel_name'])): ?>
<div class="bg-white rounded-2xl shadow-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Votre hotel</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <img src="<?php echo $package['hotel_image']; ?>" alt="Hotel" class="rounded-xl w-full h-64 object-cover cursor-pointer" onclick="openImageModal(this.src)">
        </div>
        <div>
            <h3 class="text-xl font-bold"><?php echo htmlspecialchars($package['hotel_name']); ?></h3>
            <div class="flex items-center gap-1 mt-1">
                <?php for($i=1; $i<=5; $i++): ?>
                    <?php if($i <= ($package['hotel_stars'] ?? 3)): ?>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <?php else: ?>
                        <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
            <p class="text-gray-600 text-sm mt-2"><?php echo htmlspecialchars($package['hotel_address']); ?></p>
            <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($package['hotel_view']); ?></p>
            <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($package['room_type']); ?></p>
            
            <div class="flex items-center gap-2 mt-2 text-sm text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Capacité : <?php echo $package['chambre_capacite'] ?? 2; ?> personnes</span>
            </div>
            
            <div class="flex items-center gap-2 mt-1 text-sm text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Séjour : <?php echo $package['duration_nights']; ?> nuits</span>
            </div>
            
            <div class="mt-3">
                <p class="text-2xl font-bold text-blue-600"><?php echo displayPrice($package['hotel_price']); ?></p>
                <p class="text-gray-500 text-sm">pour le séjour complet</p>
                <?php if(!empty($package['hotel_price']) && !empty($package['duration_nights'])): ?>
                    <p class="text-gray-500 text-xs mt-1">Soit <?php echo displayPrice($package['hotel_price'] / $package['duration_nights']); ?> par nuit</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Galerie chambre -->
    <?php if(!empty($package['hotel_room_images']) && is_array($package['hotel_room_images'])): ?>
    <div class="mt-4">
        <h4 class="font-semibold mb-2">Photos de la chambre</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <?php foreach($package['hotel_room_images'] as $img): ?>
                <img src="<?php echo $img; ?>" class="rounded-lg h-24 w-full object-cover cursor-pointer hover:opacity-80 transition" onclick="openImageModal(this.src)">
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Galerie terrasse -->
    <?php if(!empty($package['hotel_terrace_images']) && is_array($package['hotel_terrace_images'])): ?>
    <div class="mt-4">
        <h4 class="font-semibold mb-2">Vue depuis la terrasse</h4>
        <div class="grid grid-cols-2 gap-2">
            <?php foreach($package['hotel_terrace_images'] as $img): ?>
                <img src="<?php echo $img; ?>" class="rounded-lg h-32 w-full object-cover cursor-pointer hover:opacity-80 transition" onclick="openImageModal(this.src)">
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>                
                <!-- Activites detailees -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">Activites incluses</h2>
                    <div class="space-y-4">
                        <?php if(!empty($package['activity1_name'])): ?>
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl">
                            <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="font-semibold"><?php echo htmlspecialchars($package['activity1_name']); ?></p>
                                <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($package['activity1_description']); ?></p>
                                <p class="text-2xl font-bold text-blue-600 mt-1"><?php echo displayPrice($package['activity1_price']); ?> / pers</p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($package['activity2_name'])): ?>
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl">
                            <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="font-semibold"><?php echo htmlspecialchars($package['activity2_name']); ?></p>
                                <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($package['activity2_description']); ?></p>
                                <p class="text-2xl font-bold text-blue-600 mt-1"><?php echo displayPrice($package['activity2_price']); ?> / pers</p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($package['activity3_name'])): ?>
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl">
                            <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="font-semibold"><?php echo htmlspecialchars($package['activity3_name']); ?></p>
                                <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($package['activity3_description']); ?></p>
                                <p class="text-2xl font-bold text-blue-600 mt-1"><?php echo displayPrice($package['activity3_price']); ?> / pers</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Colonne droite - Prix et reservation -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl p-6 sticky top-32">
                    <h3 class="text-xl font-bold mb-4">Votre voyage</h3>
                    
                    <!-- Breakdown des prix -->
                    <div class="border-b pb-3 mb-3">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Vol</span>
                            <span class="font-semibold"><?php echo displayPrice($package['flight_price']); ?></span>
                        </div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Visa</span>
                            <span class="font-semibold"><?php echo displayPrice($package['visa_price']); ?></span>
                        </div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Hotel (<?php echo $package['duration_nights']; ?> nuits)</span>
                            <span class="font-semibold"><?php echo displayPrice($package['hotel_price']); ?></span>
                        </div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Transfert</span>
                            <span class="font-semibold"><?php echo displayPrice($package['transfer_price']); ?></span>
                        </div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Activites</span>
                            <span class="font-semibold"><?php echo displayPrice(($package['activity1_price'] ?? 0) + ($package['activity2_price'] ?? 0) + ($package['activity3_price'] ?? 0)); ?></span>
                        </div>
                    </div>
                    
                    <div class="border-b pb-4 mb-4">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span class="text-blue-600"><?php echo displayPrice($package['price']); ?></span>
                        </div>
                        <p class="text-gray-500 text-xs text-right">par personne</p>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Duree</span>
                            <span class="font-semibold"><?php echo $package['duration_nights']; ?> nuits</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Destination</span>
                            <span class="font-semibold"><?php echo $package['destination']; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Inclus</span>
                            <span class="font-semibold"><?php echo count($package['includes']); ?> services</span>
                        </div>
                    </div>
                    
                    <form action="booking.php" method="GET">
                        <input type="hidden" name="type" value="package">
                        <input type="hidden" name="item" value="<?php echo htmlspecialchars(json_encode($package)); ?>">
                        <input type="hidden" name="destination" value="<?php echo $package['destination']; ?>">
                        <input type="hidden" name="duration" value="<?php echo $package['duration_nights']; ?>">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Date de debut</label>
                            <input type="date" name="start_date" required class="w-full border rounded-lg p-3">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Nombre de voyageurs</label>
                            <input type="number" name="travelers" value="2" min="1" class="w-full border rounded-lg p-3">
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold text-lg transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Reserver maintenant
                        </button>
                    </form>
                    
                    <div class="mt-4 text-center text-sm text-gray-500">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Paiement securise</span>
                        </div>
                        <div class="flex items-center justify-center gap-2 mt-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Annulation gratuite sous 24h</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Modal pour agrandir les images -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center" onclick="closeImageModal()">
    <div class="relative max-w-5xl w-full mx-4">
        <img id="modalImage" src="" alt="Image agrandie" class="w-full h-auto max-h-[90vh] object-contain">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-4xl hover:text-gray-300 transition">&times;</button>
    </div>
</div>

<script>
// Fonction pour ouvrir le modal
function openImageModal(src) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    modalImg.src = src;
    document.body.style.overflow = 'hidden';
}

// Fonction pour fermer le modal
function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

// Fermer avec la touche Echap
document.addEventListener('keydown', function(e) {
    if(e.key === 'Escape') {
        closeImageModal();
    }
});

</script>

<style>
    #imageModal {
        transition: all 0.3s ease;
    }
    #imageModal img {
        animation: zoomIn 0.2s ease;
    }
    @keyframes zoomIn {
        from {
            transform: scale(0.9);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>

<?php require_once 'includes/footer.php'; ?>