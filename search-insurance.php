<?php
// ==================== RECHERCHE D'ASSURANCES ====================
// Ce fichier est inclus après que search.php a déjà fait le traitement
// Les variables $results, $title, $error sont déjà définies dans search.php

// Si les résultats ne sont pas définis ou s'il y a une erreur, on affiche un message
if (!isset($results) || empty($results) || isset($error)) {
    ?>
    <section class="pt-32 pb-20 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-6 max-w-5xl">
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Erreur</h3>
                <p class="text-gray-500"><?php echo isset($error) ? htmlspecialchars($error) : 'Aucune assurance trouvée pour cette recherche.'; ?></p>
                <a href="index.php" class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Retour à l'accueil</a>
            </div>
        </div>
    </section>
    <?php
    return; // Arrête l'exécution de ce fichier
}

// Fonction displayPrice si non définie
$selectedCurrency = $_COOKIE['selected_currency'] ?? 'USD';
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

$zoneTitles = [
    'africa_asia' => 'Afrique & Asie', 
    'schengen' => 'Espace Schengen', 
    'world_wide' => 'Monde entier'
];
$zoneTitle = $zoneTitles[$results['zone']] ?? $results['zone'];
?>

<style>
.insurance-card { background: white; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: all 0.3s ease; }
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
                    <span class="text-gray-600">1 offre disponible</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    <span class="text-gray-600">Garantie COVID-19 incluse</span>
                </div>
            </div>
        </div>
        
        <!-- Carte principale de l'assurance -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100 flex-wrap gap-4">
                    <div class="flex items-center gap-4">
                        <img src="https://cdn.brandfetch.io/idAHK8-YeB/w/400/h/400/theme/dark/icon.jpeg?c=1bxid64Mup7aczewSAYMX&t=1770195014798" 
                             alt="EKTA Assurance" 
                             class="h-12 w-auto"
                             onerror="this.src='https://via.placeholder.com/120x40?text=EKTA'">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Assurance Médicale & Rapatriement</h2>
                            <p class="text-sm text-gray-500">Garantie COVID-19 incluse</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-bold text-blue-600" id="total_price_display"><?php echo displayPrice($results['total_price_usd']); ?></span>
                        <p class="text-xs text-gray-500">Total pour <?php echo $results['travelers']; ?> voyageur(s)</p>
                    </div>
                </div>
                
                <!-- Informations récapitulatives -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <svg class="w-5 h-5 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-xs text-gray-500">Zone</p>
                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($zoneTitle); ?></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <svg class="w-5 h-5 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-xs text-gray-500">Durée</p>
                        <p class="font-semibold text-gray-800"><?php echo $results['duration']; ?> jours</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <svg class="w-5 h-5 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <p class="text-xs text-gray-500">Voyageurs</p>
                        <p class="font-semibold text-gray-800"><?php echo $results['travelers']; ?></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <svg class="w-5 h-5 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"></path>
                        </svg>
                        <p class="text-xs text-gray-500">Âges</p>
                        <p class="font-semibold text-gray-800"><?php echo !empty($results['ages']) ? implode(', ', $results['ages']) . ' ans' : 'Adultes'; ?></p>
                    </div>
                </div>
                
                <!-- Détail des prix -->
                <div class="border rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-gray-800 mb-3">Détail du prix</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Prix de base</span>
                            <span id="base_price" data-base-price="<?php echo $results['base_price_usd']; ?>"><?php echo displayPrice($results['base_price_usd']); ?></span>
                        </div>
                        <?php if($results['has_sport']): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">+ Activités sportives (+30%)</span>
                            <span class="text-orange-600"><?php echo displayPrice($results['sport_amount']); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($results['has_extreme']): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">+ Activités extrêmes (+40%)</span>
                            <span class="text-red-600"><?php echo displayPrice($results['extreme_amount']); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($results['has_cruise']): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">+ Croisière (+20%)</span>
                            <span class="text-blue-600"><?php echo displayPrice($results['cruise_amount']); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($results['has_student']): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">+ Étudiant (-15%)</span>
                            <span class="text-green-600">-<?php echo displayPrice($results['student_amount']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Garanties incluses -->
                <div class="border rounded-lg p-4 mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <h4 class="font-semibold text-gray-800">Garanties incluses</h4>
                    </div>
                    <ul class="space-y-2 text-sm">
                        <li class="flex justify-between">
                            <span class="text-gray-600">Frais médicaux</span>
                            <span class="font-semibold text-green-600"><?php echo $results['coverage']['medical']; ?></span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600">Accident</span>
                            <span class="font-semibold text-green-600"><?php echo $results['coverage']['accident']; ?></span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600">Risques financiers</span>
                            <span class="font-semibold text-green-600"><?php echo $results['coverage']['financial']; ?></span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600">Rapatriement</span>
                            <span class="font-semibold text-green-600">Inclus</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600">COVID-19</span>
                            <span class="font-semibold text-green-600"><?php echo $results['coverage']['covid']; ?></span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600">Sport</span>
                            <span class="font-semibold <?php echo $results['has_sport'] ? 'text-green-600' : 'text-gray-500'; ?>">
                                <?php echo $results['coverage']['sport']; ?>
                            </span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600">Extrême</span>
                            <span class="font-semibold <?php echo $results['has_extreme'] ? 'text-green-600' : 'text-gray-500'; ?>">
                                <?php echo $results['coverage']['extreme']; ?>
                            </span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600">Croisière</span>
                            <span class="font-semibold <?php echo $results['has_cruise'] ? 'text-green-600' : 'text-gray-500'; ?>">
                                <?php echo $results['coverage']['cruise']; ?>
                            </span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600">Étudiant</span>
                            <span class="font-semibold <?php echo $results['has_student'] ? 'text-green-600' : 'text-gray-500'; ?>">
                                <?php echo $results['coverage']['student']; ?>
                            </span>
                        </li>
                    </ul>
                </div>
                
                <!-- Mentions légales -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                        </svg>
                        <div class="text-xs text-gray-500">
                            <p><strong>Information importante :</strong> Luvia est un distributeur agréé des assurances EKTA. Nous ne sommes pas un assureur. Les garanties décrites ci-dessus sont fournies par EKTA Assurance dans le cadre de leur contrat d'assurance.</p>
                            <p class="mt-2">Les activités sportives et extrêmes ne sont pas incluses dans le contrat de base. Les majorations s'appliquent si vous ajoutez ces garanties.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Formulaire de réservation -->
                <form action="booking.php" method="GET" id="insuranceForm">
                    <input type="hidden" name="type" value="insurance">
                    <input type="hidden" name="insurance_data" id="insurance_data_input" value="<?php echo htmlspecialchars(json_encode($results)); ?>">
                    <input type="hidden" name="total_price" id="form_total_price" value="<?php echo $results['total_price_usd']; ?>">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Souscrire à cette assurance
                    </button>
                </form>
            </div>
        </div>
        
    </div>
</section>