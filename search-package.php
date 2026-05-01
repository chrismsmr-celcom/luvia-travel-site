<?php
// ==================== RECHERCHE DE PACKAGES ====================
// Les variables $results, $title, $error sont déjà définies dans search.php
// Ce fichier est inclus après que search.php a déjà fait tout le traitement

// Si $results n'est pas défini ou est vide, on affiche un message d'erreur
if (!isset($results) || empty($results) || !isset($results['package'])) {
    ?>
    <section class="pt-32 pb-20 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-6 max-w-5xl">
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Aucun package trouvé</h3>
                <p class="text-gray-500"><?php echo isset($error) ? htmlspecialchars($error) : 'Aucun package ne correspond à votre recherche.'; ?></p>
                <a href="index.php" class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Retour à l'accueil</a>
            </div>
        </div>
    </section>
    <?php
    return; // Arrête l'exécution de ce fichier
}

$package = $results['package'];
$selectedCurrency = $_COOKIE['selected_currency'] ?? 'USD';

// Fonction displayPrice si non définie
if (!function_exists('displayPrice')) {
    function displayPrice($priceInUSD) {
        global $selectedCurrency;
        if (function_exists('formatPrice')) {
            return formatPrice($priceInUSD, $selectedCurrency);
        }
        $priceCDF = $priceInUSD * 2900;
        return number_format($priceCDF, 0, ',', ' ') . ' FC';
    }
}
?>

<style>
.package-card { background: white; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: all 0.3s ease; overflow: hidden; }
.option-checkbox { width: 20px; height: 20px; cursor: pointer; accent-color: #2563eb; }
</style>

<section class="pt-32 pb-20 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-6 max-w-5xl">
        
        <!-- En-tête -->
        <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($title); ?></h1>
            </div>
            <div class="flex items-center gap-4 ml-12">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-gray-600">1 package trouvé</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    <span class="text-gray-600">Meilleurs prix garantis</span>
                </div>
            </div>
        </div>
        
        <!-- Carte principale du package -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="md:flex">
                <?php if(!empty($package['image'])): ?>
                <div class="md:w-2/5">
                    <img src="<?php echo htmlspecialchars($package['image']); ?>" alt="<?php echo htmlspecialchars($package['name']); ?>" class="w-full h-64 md:h-full object-cover">
                </div>
                <?php endif; ?>
                <div class="md:w-3/5 p-6">
                    <div class="flex justify-between items-start mb-4 flex-wrap gap-3">
                        <div>
                            <?php $cleanPackageName = html_entity_decode($package['name'], ENT_QUOTES, 'UTF-8'); ?>
                            <h2 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($cleanPackageName); ?></h2>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="flex items-center">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <svg class="w-4 h-4 <?php echo $i <= ($package['hotel_stars'] ?? 0) ? 'text-yellow-400' : 'text-gray-300'; ?> fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-gray-500 text-sm"><?php echo htmlspecialchars($package['hotel_name'] ?? ''); ?></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-3xl font-bold text-blue-600" id="main_total_price"><?php echo displayPrice($results['total_price_usd']); ?></span>
                            <p class="text-xs text-gray-500">Total pour <?php echo $results['adults'] + $results['children']; ?> personne(s)</p>
                        </div>
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars($package['description'] ?? ''); ?></p>
                    
                    <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span><?php echo $package['duration_nights'] ?? 0; ?> nuits</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span><?php echo htmlspecialchars($package['airline'] ?? ''); ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span><?php echo htmlspecialchars($package['hotel_name'] ?? ''); ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            </svg>
                            <span><?php echo htmlspecialchars($package['destination'] ?? ''); ?></span>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        <?php 
                        $includes = $package['includes'] ?? [];
                        if(is_string($includes)) {
                            $includes = json_decode($includes, true);
                        }
                        if(is_array($includes)):
                            foreach($includes as $item): 
                        ?>
                        <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <?php 
                            if($item == 'flight') echo 'Vol';
                            elseif($item == 'hotel') echo 'Hôtel';
                            elseif($item == 'car') echo 'Voiture';
                            elseif($item == 'activities') echo 'Activités';
                            else echo ucfirst($item);
                            ?>
                        </span>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Options supplémentaires avec calcul dynamique -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    Options supplémentaires
                </h3>
                
                <div class="space-y-3" id="options-container">
                    <!-- Visa -->
                    <?php if(($package['visa_price'] ?? 0) > 0): ?>
                    <label class="flex items-center justify-between cursor-pointer p-3 hover:bg-gray-50 rounded-lg transition">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="option_visa" class="option-checkbox" data-price="<?php echo $package['visa_price']; ?>" data-type="fixed">
                            <div>
                                <span class="text-gray-700 font-medium">Visa</span>
                                <p class="text-xs text-gray-400">Assistance pour l'obtention du visa</p>
                            </div>
                        </div>
                        <span class="text-green-600 font-semibold">+<?php echo displayPrice($package['visa_price']); ?></span>
                    </label>
                    <?php endif; ?>
                    
                    <!-- Transfert -->
                    <?php if(($package['transfer_price'] ?? 0) > 0): ?>
                    <label class="flex items-center justify-between cursor-pointer p-3 hover:bg-gray-50 rounded-lg transition">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="option_transfer" class="option-checkbox" data-price="<?php echo $package['transfer_price']; ?>" data-type="fixed">
                            <div>
                                <span class="text-gray-700 font-medium">Transfert aéroport</span>
                                <p class="text-xs text-gray-400">Prise en charge à l'arrivée et au départ</p>
                            </div>
                        </div>
                        <span class="text-blue-600 font-semibold">+<?php echo displayPrice($package['transfer_price']); ?></span>
                    </label>
                    <?php endif; ?>
                    
                    <!-- Assurance voyage -->
                    <label class="flex items-center justify-between cursor-pointer p-3 hover:bg-gray-50 rounded-lg transition">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="option_insurance" class="option-checkbox" data-percent="10" data-type="percent">
                            <div>
                                <span class="text-gray-700 font-medium">Assurance voyage</span>
                                <p class="text-xs text-gray-400">Protection médicale et rapatriement</p>
                            </div>
                        </div>
                        <span class="text-purple-600 font-semibold">+10%</span>
                    </label>
                    
                    <!-- Activités -->
                    <?php if(($package['activity1_price'] ?? 0) > 0): ?>
                    <label class="flex items-center justify-between cursor-pointer p-3 hover:bg-gray-50 rounded-lg transition">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="option_activities" class="option-checkbox" data-price="<?php echo ($package['activity1_price'] ?? 0) + ($package['activity2_price'] ?? 0) + ($package['activity3_price'] ?? 0); ?>" data-type="fixed">
                            <div>
                                <span class="text-gray-700 font-medium">Activités incluses</span>
                                <p class="text-xs text-gray-400">
                                    <?php 
                                    $acts = [];
                                    if($package['activity1_name'] ?? false) $acts[] = $package['activity1_name'];
                                    if($package['activity2_name'] ?? false) $acts[] = $package['activity2_name'];
                                    if($package['activity3_name'] ?? false) $acts[] = $package['activity3_name'];
                                    echo htmlspecialchars(implode(' • ', $acts));
                                    ?>
                                </p>
                            </div>
                        </div>
                        <span class="text-orange-600 font-semibold">+<?php echo displayPrice(($package['activity1_price'] ?? 0) + ($package['activity2_price'] ?? 0) + ($package['activity3_price'] ?? 0)); ?></span>
                    </label>
                    <?php endif; ?>
                </div>
                
                <div class="mt-4 pt-3 border-t border-gray-100 bg-blue-50 rounded-lg p-3">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-800">Prix de base</span>
                        <span id="base_price" data-base-price="<?php echo $results['base_price_usd']; ?>" class="font-semibold"><?php echo number_format($results['base_price_usd'], 2); ?> USD</span>
                    </div>
                    <div id="options_details" class="space-y-1 text-sm mt-2"></div>
                    <div class="flex justify-between text-lg font-bold mt-2 pt-2 border-t border-gray-200">
                        <span>Total</span>
                        <span id="total_price" class="text-blue-600"><?php echo number_format($results['total_price_usd'], 2); ?> USD</span>
                    </div>
                    <div id="total_price_fc" class="text-right text-xs text-gray-500 mt-1">
                        soit <?php echo number_format($results['total_price_fc'], 0, ',', ' '); ?> FC
                    </div>
                </div>
                
                <form action="booking.php" method="GET" class="mt-6" id="packageForm">
                    <input type="hidden" name="type" value="package">
                    <input type="hidden" name="package_data" id="package_data_input" value="<?php echo htmlspecialchars(json_encode($results)); ?>">
                    <input type="hidden" name="total_price" id="form_total_price" value="<?php echo $results['total_price_usd']; ?>">
                    <button type="submit" id="submitPackageBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Réserver ce package
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Activités détaillées -->
        <?php if(($package['activity1_name'] ?? false) || ($package['activity2_name'] ?? false) || ($package['activity3_name'] ?? false)): ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    Activités disponibles
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php if($package['activity1_name'] ?? false): ?>
                    <div class="border rounded-lg p-3">
                        <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($package['activity1_name']); ?></h4>
                        <p class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($package['activity1_description'] ?? ''); ?></p>
                        <p class="text-sm font-bold text-blue-600 mt-2"><?php echo displayPrice($package['activity1_price'] ?? 0); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($package['activity2_name'] ?? false): ?>
                    <div class="border rounded-lg p-3">
                        <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($package['activity2_name']); ?></h4>
                        <p class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($package['activity2_description'] ?? ''); ?></p>
                        <p class="text-sm font-bold text-blue-600 mt-2"><?php echo displayPrice($package['activity2_price'] ?? 0); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($package['activity3_name'] ?? false): ?>
                    <div class="border rounded-lg p-3">
                        <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($package['activity3_name']); ?></h4>
                        <p class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($package['activity3_description'] ?? ''); ?></p>
                        <p class="text-sm font-bold text-blue-600 mt-2"><?php echo displayPrice($package['activity3_price'] ?? 0); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Mentions légales -->
        <div class="bg-gray-50 rounded-lg p-4 mt-6">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                </svg>
                <div class="text-xs text-gray-500">
                    <p><strong>Information importante :</strong> Les prix indiqués sont par personne, sur la base d'une chambre double. Les options supplémentaires sont facultatives et peuvent être ajoutées lors de la réservation.</p>
                </div>
            </div>
        </div>
        
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calcul dynamique des prix des options
    const visaCheckbox = document.getElementById('option_visa');
    const transferCheckbox = document.getElementById('option_transfer');
    const insuranceCheckbox = document.getElementById('option_insurance');
    const activitiesCheckbox = document.getElementById('option_activities');
    
    const basePriceSpan = document.getElementById('base_price');
    const totalPriceSpan = document.getElementById('total_price');
    const totalPriceFcSpan = document.getElementById('total_price_fc');
    const formTotalPrice = document.getElementById('form_total_price');
    const packageDataInput = document.getElementById('package_data_input');
    const optionsDetails = document.getElementById('options_details');
    
    const exchangeRate = 2900;
    let basePrice = parseFloat(basePriceSpan?.dataset.basePrice || 0);
    let personMultiplier = <?php echo $results['person_multiplier']; ?>;
    
    function formatPriceUSD(price) {
        return price.toFixed(2) + ' USD';
    }
    
    function formatPriceFC(priceUSD) {
        return Math.round(priceUSD * exchangeRate).toLocaleString('fr-FR') + ' FC';
    }
    
    function calculateTotal() {
        let visaPrice = 0;
        let transferPrice = 0;
        let insurancePrice = 0;
        let activitiesPrice = 0;
        let optionsHtml = '';
        
        if(visaCheckbox?.checked && visaCheckbox.dataset.type === 'fixed') {
            visaPrice = parseFloat(visaCheckbox.dataset.price || 0) * personMultiplier;
            optionsHtml += '<div class="flex justify-between"><span class="text-gray-600">+ Visa</span><span class="text-green-600">' + visaPrice.toFixed(2) + ' USD</span></div>';
        }
        if(transferCheckbox?.checked && transferCheckbox.dataset.type === 'fixed') {
            transferPrice = parseFloat(transferCheckbox.dataset.price || 0) * personMultiplier;
            optionsHtml += '<div class="flex justify-between"><span class="text-gray-600">+ Transfert</span><span class="text-blue-600">' + transferPrice.toFixed(2) + ' USD</span></div>';
        }
        if(insuranceCheckbox?.checked && insuranceCheckbox.dataset.type === 'percent') {
            insurancePrice = basePrice * (parseFloat(insuranceCheckbox.dataset.percent || 0) / 100) * personMultiplier;
            optionsHtml += '<div class="flex justify-between"><span class="text-gray-600">+ Assurance</span><span class="text-purple-600">' + insurancePrice.toFixed(2) + ' USD</span></div>';
        }
        if(activitiesCheckbox?.checked && activitiesCheckbox.dataset.type === 'fixed') {
            activitiesPrice = parseFloat(activitiesCheckbox.dataset.price || 0) * personMultiplier;
            optionsHtml += '<div class="flex justify-between"><span class="text-gray-600">+ Activités</span><span class="text-orange-600">' + activitiesPrice.toFixed(2) + ' USD</span></div>';
        }
        
        const total = basePrice + visaPrice + transferPrice + insurancePrice + activitiesPrice;
        
        if(optionsDetails) {
            optionsDetails.innerHTML = optionsHtml || '<div class="text-gray-400 text-sm">Aucune option sélectionnée</div>';
        }
        
        if(totalPriceSpan) totalPriceSpan.textContent = formatPriceUSD(total);
        if(totalPriceFcSpan) totalPriceFcSpan.textContent = 'soit ' + formatPriceFC(total);
        if(formTotalPrice) formTotalPrice.value = total.toFixed(2);
        
        // Mettre à jour les données du package dans le formulaire
        if(packageDataInput && packageDataInput.value) {
            try {
                let packageData = JSON.parse(packageDataInput.value);
                packageData.has_visa = visaCheckbox?.checked || false;
                packageData.has_transfer = transferCheckbox?.checked || false;
                packageData.has_insurance = insuranceCheckbox?.checked || false;
                packageData.has_activities = activitiesCheckbox?.checked || false;
                packageData.visa_price_usd = visaPrice;
                packageData.transfer_price_usd = transferPrice;
                packageData.insurance_price_usd = insurancePrice;
                packageData.activities_price_usd = activitiesPrice;
                packageData.total_price_usd = total;
                packageData.total_price_fc = Math.round(total * exchangeRate);
                packageDataInput.value = JSON.stringify(packageData);
            } catch(e) {
                console.error('Error updating package data:', e);
            }
        }
    }
    
    // Attacher les événements
    const options = [visaCheckbox, transferCheckbox, insuranceCheckbox, activitiesCheckbox];
    options.forEach(opt => {
        if(opt) opt.addEventListener('change', calculateTotal);
    });
    
    // Calcul initial
    calculateTotal();
});
</script>