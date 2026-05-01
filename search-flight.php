<?php

// ==================== RECHERCHE DE VOLS ====================

// Fonction de secours si elle n'existe pas dans config.php
if (!function_exists('searchFlightsDuffel')) {
    function searchFlightsDuffel($origin, $destination, $departure_date, $return_date = null, $adults = 1, $children = 0, $infants = 0) {
        // Données de démonstration
        $demo_results = [];
        $airlines = ['Congo Airways', 'Ethiopian Airlines', 'Kenya Airways', 'RwandAir', 'Air France'];
        
        for($i = 0; $i < 6; $i++) {
            $price = rand(300, 1200);
            $demo_results[] = [
                'airline' => $airlines[array_rand($airlines)],
                'flight_number' => 'FL' . rand(100, 999),
                'origin' => $origin ?: 'FIH',
                'destination' => $destination ?: 'FBM',
                'departure_time' => $departure_date . ' ' . rand(6, 22) . ':00:00',
                'arrival_time' => $departure_date . ' ' . rand(8, 23) . ':30:00',
                'price' => $price,
                'stops' => rand(0, 2),
                'class' => ['Economy', 'Premium Economy', 'Business'][rand(0, 2)],
                'logo' => ''
            ];
        }
        
        usort($demo_results, function($a, $b) {
            return $a['price'] - $b['price'];
        });
        
        return $demo_results;
    }
}

if (!function_exists('displayPrice')) {
    function displayPrice($price_usd) {
        $price_cdf = $price_usd * 2900;
        return number_format($price_cdf, 0, ',', ' ') . ' FC';
    }
}

// Récupération des paramètres
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

// Recherche des vols
$results = searchFlightsDuffel($origin, $destination, $departure_date, $return_date, $adults, $children, $infants);

// Titre avec gestion des cas vides
if(empty($origin) || empty($destination)) {
    $title = "Recherche de vols";
} else {
    $title = "Vols de $origin vers $destination";
    if($departure_date) {
        $title .= " - " . date('d/m/Y', strtotime($departure_date));
    }
}
?>

<style>
/* Ton CSS existant - garde-le intact */
.filter-sidebar { background: white; border-radius: 16px; padding: 20px; position: sticky; top: 100px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.filter-section { border-bottom: 1px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 16px; }
.filter-section:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
.filter-section h3 { font-weight: 600; margin-bottom: 12px; color: #1f2937; font-size: 0.95rem; }
.filter-option { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; cursor: pointer; transition: color 0.2s ease; }
.filter-option:hover { color: #2563eb; }
.filter-option input { width: 16px; height: 16px; cursor: pointer; }
.filter-option label { color: #4b5563; font-size: 14px; cursor: pointer; flex: 1; }
.price-range { display: flex; gap: 10px; margin-top: 10px; }
.price-range input { width: 100%; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: border-color 0.2s ease; }
.price-range input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 2px rgba(37,99,235,0.1); }
.recommendation-card { background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 16px; padding: 16px; margin-bottom: 20px; position: relative; }
.recommendation-title { font-weight: 700; color: #1e40af; font-size: 1rem; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
.recommendation-wrapper { position: relative; }
.recommendation-grid { display: flex; overflow-x: auto; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; gap: 12px; padding-bottom: 10px; scroll-behavior: smooth; }
.recommendation-grid::-webkit-scrollbar { height: 6px; }
.recommendation-grid::-webkit-scrollbar-track { background: #e5e7eb; border-radius: 10px; }
.recommendation-grid::-webkit-scrollbar-thumb { background: #2563eb; border-radius: 10px; }
.recommendation-item { flex: 0 0 200px; scroll-snap-align: start; background: white; border-radius: 12px; padding: 12px; text-align: center; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.recommendation-item:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
.recommendation-price { font-size: 1rem; font-weight: 700; color: #2563eb; }
.recommendation-airline { font-size: 11px; color: #6b7280; margin-top: 4px; }
.rec-nav-btn { position: absolute; top: 50%; transform: translateY(-50%); background: white; border: 1px solid #e5e7eb; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.2s ease; }
.rec-nav-btn:hover { background: #f3f4f6; transform: translateY(-50%) scale(1.05); }
.rec-prev { left: -15px; }
.rec-next { right: -15px; }
.flight-card { background: white; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: all 0.3s ease; cursor: pointer; animation: fadeInUp 0.3s ease-out; position: relative; overflow: hidden; }
.flight-card:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(0,0,0,0.12); }
.flight-card::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(37,99,235,0.05), transparent); transition: left 0.5s ease; }
.flight-card:hover::before { left: 100%; }
#reset-filters { width: 100%; margin-top: 16px; background-color: #f3f4f6; color: #4b5563; padding: 10px; border-radius: 8px; font-size: 14px; font-weight: 500; transition: all 0.2s ease; border: none; cursor: pointer; }
#reset-filters:hover { background-color: #e5e7eb; color: #1f2937; }
#class-filter { width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px 12px; font-size: 14px; background-color: white; cursor: pointer; transition: border-color 0.2s ease; }
#class-filter:focus { outline: none; border-color: #2563eb; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@media (max-width: 768px) {
.filter-sidebar { position: fixed; top: 0; right: -100%; width: 85%; height: 100%; z-index: 1000; background: white; padding: 20px; transition: right 0.3s ease; border-radius: 0; overflow-y: auto; box-shadow: -2px 0 10px rgba(0,0,0,0.1); }
.filter-sidebar.open { right: 0; }
.filter-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; display: none; }
.filter-overlay.open { display: block; }
.filter-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #e5e7eb; }
.filter-header h2 { font-size: 18px; font-weight: 700; }
.close-filter { background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280; padding: 8px; }
.open-filter-btn { position: fixed; bottom: 20px; right: 20px; background: #2563eb; color: white; border: none; border-radius: 50px; padding: 12px 20px; font-size: 14px; font-weight: 600; z-index: 90; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: transform 0.2s ease; }
.open-filter-btn:hover { transform: scale(1.05); }
.recommendation-item { flex: 0 0 160px; }
.rec-prev { left: -10px; width: 32px; height: 32px; }
.rec-next { right: -10px; width: 32px; height: 32px; }
}
</style>

<button class="open-filter-btn md:hidden" id="openFilterBtn">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
    </svg>
    Filtrer
</button>

<div class="filter-overlay" id="filterOverlay"></div>

<section class="pt-32 pb-20 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-6 max-w-7xl">
        
        <!-- En-tête -->
        <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
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
                            <span class="text-gray-600"><?php echo count($results); ?> vol(s) trouvé(s)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                            <span class="text-gray-600">Meilleurs prix garantis</span>
                        </div>
                    </div>
                </div>
                <?php if(!empty($results)): ?>
                <div class="mt-3 md:mt-0">
                    <div class="bg-white rounded-lg px-4 py-2 shadow-sm">
                        <p class="text-xs text-gray-500">Prix moyen</p>
                        <p class="text-xl font-bold text-blue-600"><?php echo displayPrice(array_sum(array_column($results, 'price')) / count($results)); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if(!empty($results) && !empty($departure_date)): ?>
        <!-- Recommandations -->
        <div class="recommendation-card">
            <div class="recommendation-title">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span>Vous pourriez aussi aimer</span>
            </div>
            
            <div class="recommendation-wrapper">
                <button class="rec-nav-btn rec-prev" id="recPrevBtn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                
                <div class="recommendation-grid" id="recommendationGrid">
                    <?php 
                    $firstPrice = $results[0]['price'] ?? 0;
                    $alternativeDates = [];
                    if($departure_date) {
                        $alternativeDates = [
                            ['date' => date('Y-m-d', strtotime($departure_date . ' -3 days')), 'price' => max(0, $firstPrice - 50)],
                            ['date' => date('Y-m-d', strtotime($departure_date . ' +2 days')), 'price' => max(0, $firstPrice - 30)],
                            ['date' => date('Y-m-d', strtotime($departure_date . ' +5 days')), 'price' => $firstPrice + 20],
                            ['date' => date('Y-m-d', strtotime($departure_date . ' +7 days')), 'price' => max(0, $firstPrice - 80)],
                            ['date' => date('Y-m-d', strtotime($departure_date . ' +10 days')), 'price' => max(0, $firstPrice - 100)],
                        ];
                    }
                    foreach($alternativeDates as $alt):
                    ?>
                    <div class="recommendation-item" onclick="window.location.href='search.php?type=flight&origin=<?php echo urlencode($origin); ?>&destination=<?php echo urlencode($destination); ?>&departure_date=<?php echo $alt['date']; ?>'">
                        <div class="recommendation-price"><?php echo displayPrice($alt['price']); ?></div>
                        <div class="recommendation-airline"><?php echo date('d M', strtotime($alt['date'])); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <button class="rec-nav-btn rec-next" id="recNextBtn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar filtres -->
            <div class="lg:w-80 filter-sidebar" id="filterSidebar">
                <div class="filter-header md:hidden">
                    <h2>Filtrer les vols</h2>
                    <button class="close-filter" id="closeFilterBtn">&times;</button>
                </div>
                <h2 class="text-lg font-bold mb-4 hidden md:block">Filtrer les vols</h2>
                
                <div class="filter-section">
                    <h3>Prix</h3>
                    <div class="price-range">
                        <input type="number" id="price-min" placeholder="Min">
                        <input type="number" id="price-max" placeholder="Max">
                    </div>
                </div>
                
                <div class="filter-section">
                    <h3>Compagnies</h3>
                    <div id="airline-filters">
                        <?php 
                        $airlines = !empty($results) ? array_unique(array_column($results, 'airline')) : [];
                        foreach($airlines as $airline): 
                        ?>
                        <label class="filter-option">
                            <input type="checkbox" class="airline-filter" value="<?php echo htmlspecialchars($airline); ?>">
                            <span><?php echo htmlspecialchars($airline); ?></span>
                        </label>
                        <?php endforeach; ?>
                        <?php if(empty($airlines)): ?>
                        <p class="text-gray-500 text-sm">Aucune compagnie disponible</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="filter-section">
                    <h3>Escales</h3>
                    <label class="filter-option"><input type="radio" name="stops" value="direct" class="stops-filter"><span>Direct</span></label>
                    <label class="filter-option"><input type="radio" name="stops" value="1" class="stops-filter"><span>1 escale max</span></label>
                    <label class="filter-option"><input type="radio" name="stops" value="2" class="stops-filter"><span>2 escales max</span></label>
                </div>
                
                <div class="filter-section">
                    <h3>Cabine</h3>
                    <select id="class-filter" class="w-full border rounded-lg p-2 text-sm">
                        <option value="all">Toutes</option>
                        <option value="Economy">Economy</option>
                        <option value="Premium Economy">Premium Economy</option>
                        <option value="Business">Business</option>
                        <option value="First">First Class</option>
                    </select>
                </div>
                
                <button id="reset-filters" class="w-full mt-4 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300">Réinitialiser</button>
            </div>
            
            <!-- Résultats -->
            <div class="flex-1 space-y-6" id="results-container">
                <?php if(!empty($results)): ?>
                    <?php foreach($results as $item): ?>
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden flight-card" 
                         data-airline="<?php echo htmlspecialchars($item['airline']); ?>"
                         data-stops="<?php echo $item['stops']; ?>"
                         data-class="<?php echo $item['class']; ?>"
                         data-price="<?php echo $item['price']; ?>">
                        <div class="p-5">
                            <div class="flex flex-wrap justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-3">
                                        <?php if(!empty($item['logo'])): ?>
                                            <img src="<?php echo $item['logo']; ?>" class="w-10 h-10 object-contain">
                                        <?php else: ?>
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 19l9 2-9-18-9 18 9-2z"></path></svg>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <h3 class="text-lg font-bold"><?php echo htmlspecialchars($item['airline']); ?></h3>
                                            <p class="text-gray-500 text-xs">Vol <?php echo htmlspecialchars($item['flight_number']); ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-wrap items-center gap-4 mt-2">
                                        <div><p class="text-xl font-bold"><?php echo date('H:i', strtotime($item['departure_time'])); ?></p><p class="text-gray-600 text-sm"><?php echo htmlspecialchars($item['origin']); ?></p><p class="text-xs text-gray-400"><?php echo date('d/m', strtotime($item['departure_time'])); ?></p></div>
                                        <div class="flex flex-col items-center"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg><div class="w-16 h-px bg-gray-300 my-1"></div><p class="text-xs text-gray-500"><?php echo ($item['stops'] ?? 0) > 0 ? ($item['stops'] . ' escale(s)') : 'Direct'; ?></p></div>
                                        <div><p class="text-xl font-bold"><?php echo date('H:i', strtotime($item['arrival_time'])); ?></p><p class="text-gray-600 text-sm"><?php echo htmlspecialchars($item['destination']); ?></p><p class="text-xs text-gray-400"><?php echo date('d/m', strtotime($item['arrival_time'])); ?></p></div>
                                    </div>
                                    
                                    <div class="mt-2"><span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded"><?php echo htmlspecialchars($item['class']); ?></span></div>
                                </div>
                                
                                <div class="text-right mt-3 md:mt-0">
                                    <p class="text-2xl font-bold text-blue-600"><?php echo displayPrice($item['price']); ?></p>
                                    <p class="text-xs text-gray-500 mb-2">par personne</p>
                                    <form action="booking.php" method="GET">
                                        <input type="hidden" name="type" value="flight">
                                        <input type="hidden" name="item" value="<?php echo htmlspecialchars(json_encode($item)); ?>">
                                        <input type="hidden" name="origin" value="<?php echo htmlspecialchars($origin); ?>">
                                        <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination); ?>">
                                        <input type="hidden" name="departure_date" value="<?php echo htmlspecialchars($departure_date); ?>">
                                        <input type="hidden" name="return_date" value="<?php echo htmlspecialchars($return_date); ?>">
                                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700">Réserver</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Aucun vol trouvé</h3>
                        <p class="text-gray-500">Aucun vol ne correspond à votre recherche. Essayez d'autres dates ou destinations.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Menu mobile filtres
    const openBtn = document.getElementById('openFilterBtn');
    const closeBtn = document.getElementById('closeFilterBtn');
    const sidebar = document.getElementById('filterSidebar');
    const overlay = document.getElementById('filterOverlay');

    if(openBtn && sidebar && overlay) {
        openBtn.onclick = () => { sidebar.classList.add('open'); overlay.classList.add('open'); };
    }
    if(closeBtn && sidebar && overlay) {
        closeBtn.onclick = () => { sidebar.classList.remove('open'); overlay.classList.remove('open'); };
    }
    if(overlay) {
        overlay.onclick = () => { 
            sidebar?.classList.remove('open'); 
            overlay.classList.remove('open'); 
        };
    }

    // Filtres vols
    const flightCards = document.querySelectorAll('.flight-card');
    const airlineFilters = document.querySelectorAll('.airline-filter');
    const stopsFilters = document.querySelectorAll('.stops-filter');
    const classFilter = document.getElementById('class-filter');
    const priceMin = document.getElementById('price-min');
    const priceMax = document.getElementById('price-max');
    const resetFilters = document.getElementById('reset-filters');

    function applyFlightFilters() {
        if(flightCards.length === 0) return;
        
        const selectedAirlines = Array.from(airlineFilters).filter(cb => cb.checked).map(cb => cb.value);
        const selectedStops = document.querySelector('input[name="stops"]:checked')?.value;
        const selClass = classFilter?.value;
        const minP = parseFloat(priceMin?.value) || 0;
        const maxP = parseFloat(priceMax?.value) || Infinity;

        flightCards.forEach(card => {
            let show = true;
            if(selectedAirlines.length && !selectedAirlines.includes(card.dataset.airline)) show = false;
            if(show && selectedStops) {
                const stops = parseInt(card.dataset.stops) || 0;
                if(selectedStops === 'direct' && stops > 0) show = false;
                else if(selectedStops === '1' && stops > 1) show = false;
                else if(selectedStops === '2' && stops > 2) show = false;
            }
            if(show && selClass && selClass !== 'all' && card.dataset.class !== selClass) show = false;
            if(show && (parseFloat(card.dataset.price) < minP || parseFloat(card.dataset.price) > maxP)) show = false;
            card.style.display = show ? 'block' : 'none';
        });
    }

    if(flightCards.length > 0) {
        airlineFilters.forEach(f => f.addEventListener('change', applyFlightFilters));
        stopsFilters.forEach(f => f.addEventListener('change', applyFlightFilters));
        if(classFilter) classFilter.addEventListener('change', applyFlightFilters);
        if(priceMin) priceMin.addEventListener('input', applyFlightFilters);
        if(priceMax) priceMax.addEventListener('input', applyFlightFilters);
        if(resetFilters) {
            resetFilters.addEventListener('click', () => {
                airlineFilters.forEach(cb => cb.checked = false);
                stopsFilters.forEach(radio => radio.checked = false);
                if(classFilter) classFilter.value = 'all';
                if(priceMin) priceMin.value = '';
                if(priceMax) priceMax.value = '';
                applyFlightFilters();
            });
        }
    }

    // Carrousel recommandations
    const grid = document.getElementById('recommendationGrid');
    const prevBtn = document.getElementById('recPrevBtn');
    const nextBtn = document.getElementById('recNextBtn');
    
    if(grid && prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => grid.scrollBy({ left: -220, behavior: 'smooth' }));
        nextBtn.addEventListener('click', () => grid.scrollBy({ left: 220, behavior: 'smooth' }));
    }
});
</script>