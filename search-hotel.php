<?php
// ==================== RECHERCHE D'HÔTELS ====================
// Ce fichier est inclus après que search.php a déjà fait le traitement
// Les variables $results, $title, $error, $check_in, $check_out, $adults, $city sont déjà définies

// Vérifier si les résultats sont déjà définis dans search.php
if (!isset($results) && !isset($error)) {
    $city = trim($_GET['city'] ?? '');
    $check_in = $_GET['check_in'] ?? '';
    $check_out = $_GET['check_out'] ?? '';
    $adults = max(1, intval($_GET['adults'] ?? 1));
    
    if (empty($city)) {
        $error = "La ville est obligatoire.";
        $results = [];
        $title = "Erreur - Recherche d'hotel";
    } elseif (empty($check_in) || empty($check_out)) {
        $error = "Les dates de sejour sont obligatoires.";
        $results = [];
        $title = "Erreur - Recherche d'hotel";
    } elseif (strtotime($check_in) >= strtotime($check_out)) {
        $error = "La date de depart doit etre apres la date d'arrivee.";
        $results = [];
        $title = "Erreur - Dates invalides";
    } else {
        $childrenAges = [];
        if (!empty($_GET['children_ages'])) {
            $ages = explode(',', $_GET['children_ages']);
            foreach ($ages as $age) {
                $ageInt = intval(trim($age));
                if ($ageInt >= 0 && $ageInt <= 17) {
                    $childrenAges[] = $ageInt;
                }
            }
        }
        $results = searchHotelsLiteAPI($city, $check_in, $check_out, $adults, $childrenAges);
        $nights = max(1, (int)((strtotime($check_out) - strtotime($check_in)) / 86400));
        $title = "Hotels a " . htmlspecialchars($city) . " - " . date('d/m/Y', strtotime($check_in)) . " → " . date('d/m/Y', strtotime($check_out)) . " ({$nights} nuit(s))";
    }
}

// Fonction displayPrice si non definie
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
?>

<style>
/* ==================== STYLES PRINCIPAUX ==================== */
.filters-sidebar {
    background: white;
    border-radius: 20px;
    padding: 24px;
    position: sticky;
    top: 100px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    max-height: calc(100vh - 120px);
    overflow-y: auto;
}

.filters-sidebar::-webkit-scrollbar {
    width: 6px;
}

.filters-sidebar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.filters-sidebar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.filter-section {
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 20px;
    margin-bottom: 20px;
}

.filter-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.filter-section h3 {
    font-weight: 600;
    margin-bottom: 16px;
    color: #1f2937;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
}

.filter-section h3 i {
    transition: transform 0.2s ease;
}

.filter-section.collapsed h3 i {
    transform: rotate(-90deg);
}

.filter-section.collapsed .filter-content {
    display: none;
}

.filter-option {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    cursor: pointer;
    padding: 6px 0;
    transition: color 0.2s ease;
}

.filter-option:hover {
    color: #2563eb;
}

.filter-option input[type="checkbox"],
.filter-option input[type="radio"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #2563eb;
}

.filter-option label {
    color: #4b5563;
    font-size: 14px;
    cursor: pointer;
    flex: 1;
}

.price-range {
    display: flex;
    gap: 12px;
    margin-top: 12px;
}

.price-range input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.price-range input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
}

/* Cartes hotel */
.hotel-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    overflow: hidden;
    cursor: pointer;
}

.hotel-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
}

.hotel-image {
    position: relative;
    overflow: hidden;
    height: 220px;
}

.hotel-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.hotel-card:hover .hotel-image img {
    transform: scale(1.05);
}

.hotel-price {
    position: absolute;
    bottom: 12px;
    right: 12px;
    background: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: bold;
    color: #2563eb;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Styles pour les détails de chambre */
.room-gallery {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 10px;
    margin-bottom: 15px;
    scrollbar-width: thin;
}

.room-gallery::-webkit-scrollbar {
    height: 6px;
}

.room-gallery::-webkit-scrollbar-track {
    background: #e5e7eb;
    border-radius: 10px;
}

.room-gallery::-webkit-scrollbar-thumb {
    background: #2563eb;
    border-radius: 10px;
}

.room-gallery img {
    width: 100px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.room-gallery img:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.room-features {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 10px 0;
}

.room-feature {
    display: flex;
    align-items: center;
    gap: 5px;
    background: #f3f4f6;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
}

.room-feature svg {
    width: 14px;
    height: 14px;
}

.room-description {
    font-size: 14px;
    color: #4b5563;
    line-height: 1.5;
    margin: 10px 0;
}

.room-option {
    transition: all 0.2s ease;
}

.room-option.selected {
    border-color: #2563eb !important;
    background-color: #eff6ff !important;
}

/* Boutons */
.btn-reset {
    width: 100%;
    margin-top: 16px;
    background-color: #f3f4f6;
    color: #4b5563;
    padding: 10px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 500;
}

.btn-reset:hover {
    background-color: #e5e7eb;
    color: #1f2937;
}

.btn-primary {
    background: #2563eb;
    color: white;
    padding: 10px 20px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}

/* Tri */
.sort-dropdown {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 8px 16px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
}

.sort-dropdown:hover {
    border-color: #2563eb;
    box-shadow: 0 2px 8px rgba(37,99,235,0.1);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hotel-card {
    animation: fadeInUp 0.4s ease-out;
}

/* ==================== GALERIE HÔTEL ==================== */
.gallery-container {
    max-height: 500px;
    overflow-y: auto;
    border-radius: 12px;
    background: #f9fafb;
    padding: 4px;
}

.gallery-container::-webkit-scrollbar {
    width: 8px;
}

.gallery-container::-webkit-scrollbar-track {
    background: #e5e7eb;
    border-radius: 10px;
}

.gallery-container::-webkit-scrollbar-thumb {
    background: #2563eb;
    border-radius: 10px;
}

.gallery-container::-webkit-scrollbar-thumb:hover {
    background: #1d4ed8;
}

.hotel-gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px;
    padding: 8px;
}

/* Style des items de la galerie */
.gallery-item {
    position: relative;
    cursor: pointer;
    overflow: hidden;
    border-radius: 12px;
    background: #f3f4f6;
    transition: all 0.3s ease;
    aspect-ratio: 4 / 3;
}

.gallery-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.gallery-item.col-span-2 {
    grid-column: span 2;
}

.gallery-item.row-span-2 {
    grid-row: span 2;
    aspect-ratio: auto;
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.gallery-item:hover img {
    transform: scale(1.05);
}

.gallery-item .overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.gallery-item:hover .overlay {
    background: rgba(0,0,0,0.3);
}

.gallery-item .overlay svg {
    width: 40px;
    height: 40px;
    color: white;
    opacity: 0;
    transform: scale(0.5);
    transition: all 0.3s ease;
}

.gallery-item:hover .overlay svg {
    opacity: 1;
    transform: scale(1);
}

/* Bouton voir toutes les photos */
.see-all-btn-container {
    text-align: center;
    margin-top: 16px;
    padding: 12px;
    border-top: 1px solid #e5e7eb;
}

.see-all-btn {
    background: #2563eb;
    color: white;
    padding: 10px 24px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.see-all-btn:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37,99,235,0.3);
}

/* Animation pour l'ouverture de l'image */
@keyframes zoomIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* ==================== RESPONSIVE ==================== */
@media (max-width: 768px) {
    .filters-sidebar {
        position: fixed;
        top: 0;
        right: -100%;
        width: 85%;
        height: 100%;
        z-index: 1000;
        border-radius: 0;
        transition: right 0.3s ease;
        max-height: 100%;
    }
    
    .filters-sidebar.open {
        right: 0;
    }
    
    .filter-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 999;
        display: none;
    }
    
    .filter-overlay.open {
        display: block;
    }
    
    .open-filter-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 50px;
        padding: 12px 20px;
        font-size: 14px;
        font-weight: 600;
        z-index: 90;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    
    /* Galerie responsive */
    .gallery-container {
        max-height: 400px;
    }
    
    .hotel-gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 8px;
    }
    
    .gallery-item.col-span-2 {
        grid-column: span 1;
    }
    
    .room-gallery img {
        width: 80px;
        height: 60px;
    }
}

@media (max-width: 480px) {
    .gallery-container {
        max-height: 350px;
    }
    
    .hotel-gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 6px;
    }
    
    .room-gallery img {
        width: 70px;
        height: 55px;
    }
}
</style>
<button class="open-filter-btn md:hidden" id="openFilterBtn">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
    </svg>
    Filtrer
</button>

<div class="filter-overlay" id="filterOverlay"></div>

<section class="pt-32 pb-20 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-6 max-w-7xl">
        
        <!-- En-tete -->
        <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($title); ?></h1>
                    </div>
                    <div class="flex items-center gap-4 ml-12">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-600" id="resultCount"><?php echo is_array($results) ? count($results) : 0; ?> hotel(s) trouve(s)</span>
                        </div>
                    </div>
                </div>
                
                <!-- Tri -->
                <div class="mt-3 md:mt-0">
                    <select id="sortBy" class="sort-dropdown">
                        <option value="recommended">Nos meilleurs choix</option>
                        <option value="price_asc">Prix croissant</option>
                        <option value="price_desc">Prix decroissant</option>
                        <option value="rating_desc">Note la plus elevee</option>
                        <option value="rating_asc">Note la plus basse</option>
                    </select>
                </div>
            </div>
        </div>
        
        <?php if(!empty($results) && is_array($results) && !isset($error)): ?>
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar filtres -->
            <div class="lg:w-80 filters-sidebar" id="filterSidebar">
                <div class="flex justify-between items-center mb-4 pb-3 border-b md:hidden">
                    <h2 class="text-lg font-bold">Filtrer les hotels</h2>
                    <button class="close-filter text-2xl" id="closeFilterBtn">&times;</button>
                </div>
                <h2 class="text-lg font-bold mb-4 hidden md:block">Filtrer les hotels</h2>
                
                <!-- Prix -->
                <div class="filter-section">
                    <h3 onclick="toggleSection(this)">
                        Prix par nuit
                        <i class="text-gray-400">▼</i>
                    </h3>
                    <div class="filter-content">
                        <div class="price-range">
                            <input type="number" id="price-min" placeholder="Min USD" step="10">
                            <input type="number" id="price-max" placeholder="Max USD" step="10">
                        </div>
                    </div>
                </div>
                
                <!-- Note par etoiles -->
                <div class="filter-section">
                    <h3 onclick="toggleSection(this)">
                        Note par etoiles
                        <i class="text-gray-400">▼</i>
                    </h3>
                    <div class="filter-content">
                        <?php for($i = 5; $i >= 1; $i--): ?>
                        <label class="filter-option">
                            <input type="checkbox" class="star-filter" value="<?php echo $i; ?>">
                            <label><?php echo str_repeat('★', $i) . str_repeat('☆', 5-$i); ?> <?php echo $i; ?> etoile(s)</label>
                        </label>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <!-- Note client -->
                <div class="filter-section">
                    <h3 onclick="toggleSection(this)">
                        Note client
                        <i class="text-gray-400">▼</i>
                    </h3>
                    <div class="filter-content">
                        <label class="filter-option">
                            <input type="checkbox" class="rating-filter" value="9">
                            <label>Merveilleux : 9+</label>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="rating-filter" value="8">
                            <label>Excellent : 8+</label>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="rating-filter" value="7">
                            <label>Bon : 7+</label>
                        </label>
                    </div>
                </div>
                
                <!-- Equipements populaires -->
                <div class="filter-section">
                    <h3 onclick="toggleSection(this)">
                        Equipements
                        <i class="text-gray-400">▼</i>
                    </h3>
                    <div class="filter-content">
                        <label class="filter-option">
                            <input type="checkbox" class="amenity-filter" value="wifi">
                            <label>Wi-Fi gratuit</label>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="amenity-filter" value="breakfast">
                            <label>Petit-dejeuner inclus</label>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="amenity-filter" value="parking">
                            <label>Parking gratuit</label>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="amenity-filter" value="pool">
                            <label>Piscine</label>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" class="amenity-filter" value="spa">
                            <label>Centre de bien-etre</label>
                        </label>
                    </div>
                </div>
                
                <button id="resetFilters" class="btn-reset">Reinitialiser les filtres</button>
            </div>
            
            <!-- Resultats -->
            <div class="flex-1">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="hotels-container"></div>
            </div>
        </div>
        
        <!-- Modal detail hotel COMPLET -->
<div id="hotelModal" class="fixed inset-0 bg-black bg-opacity-50 z-[1000] hidden items-center justify-center p-4" onclick="closeModal()">
    <div class="bg-white rounded-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center z-10">
            <h2 class="text-xl font-bold text-gray-800" id="modalTitle"></h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <div class="p-6">
            <!-- Image principale de l'hôtel -->
            <img id="modalImage" src="" alt="" class="w-full h-80 object-cover rounded-xl mb-6">
            
            <!-- En-tête avec étoiles et note -->
            <div class="flex flex-wrap justify-between items-start mb-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <div id="modalStars" class="flex items-center gap-1"></div>
                        <span id="modalRating" class="bg-green-100 text-green-700 px-2 py-1 rounded-lg text-sm font-semibold"></span>
                        <span id="modalReviews" class="text-gray-500 text-sm"></span>
                    </div>
                    <p id="modalAddress" class="text-gray-600">Adresse non disponible</p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-blue-600" id="modalPrice"></p>
                    <p class="text-sm text-gray-500">par nuit</p>
                    <p class="text-xs text-gray-400" id="modalTotalPrice"></p>
                </div>
            </div>
            
            <!-- Onglets -->
            <div class="border-b mb-6">
                <div class="flex gap-6 overflow-x-auto">
                    <button class="modal-tab active pb-2 border-b-2 border-blue-600 text-blue-600 font-semibold" data-tab="rooms">Chambres disponibles</button>
                    <button class="modal-tab pb-2 text-gray-500 hover:text-gray-700" data-tab="hotel-gallery">Galerie hôtel</button>
                    <button class="modal-tab pb-2 text-gray-500 hover:text-gray-700" data-tab="amenities">Équipements</button>
                    <button class="modal-tab pb-2 text-gray-500 hover:text-gray-700" data-tab="policies">Politiques</button>
                </div>
            </div>
            
            <!-- Section Galerie Hôtel -->
            <div id="tab-hotel-gallery" class="tab-content hidden">
                <div class="mb-4">
                    <h3 class="font-semibold text-lg mb-2">Découvrez l'hôtel</h3>
                    <p class="text-gray-600 text-sm mb-4">Photos de l'établissement</p>
                    <div class="gallery-container">
                        <div id="hotel-gallery-grid" class="hotel-gallery-grid"></div>
                    </div>
                </div>
            </div>
            
            <!-- Section Chambres disponibles -->
            <div id="tab-rooms" class="tab-content">
                <div id="rooms-list" class="space-y-4 max-h-96 overflow-y-auto"></div>
            </div>
            
            <!-- Section Équipements -->
            <div id="tab-amenities" class="tab-content hidden">
                <div class="mb-4">
                    <h3 class="font-semibold text-lg mb-2">Équipements de l'hôtel</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3" id="amenities-list"></div>
                </div>
            </div>
            
            <!-- Section Politiques -->
            <div id="tab-policies" class="tab-content hidden">
                <div class="mb-4">
                    <h3 class="font-semibold text-lg mb-2">Politiques</h3>
                    <div class="bg-gray-50 rounded-xl p-4 mb-4">
                        <h4 class="font-semibold mb-2">Politique d'annulation</h4>
                        <div id="cancellation-policy" class="text-sm text-gray-600"></div>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h4 class="font-semibold mb-2">Informations importantes</h4>
                        <div id="important-info" class="text-sm text-gray-600 space-y-2"></div>
                    </div>
                </div>
            </div>
            
            <!-- Section réservation -->
            <div class="mt-6 pt-4 border-t">
                <form action="booking.php" method="GET" id="bookingForm">
                    <input type="hidden" name="type" value="hotel" id="formType">
                    <input type="hidden" name="item" id="formItem">
                    <input type="hidden" name="check_in" value="<?php echo htmlspecialchars($check_in); ?>">
                    <input type="hidden" name="check_out" value="<?php echo htmlspecialchars($check_out); ?>">
                    <input type="hidden" name="guests" value="<?php echo $adults; ?>">
                    <input type="hidden" name="room_type" id="selectedRoomType" value="">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold transition">
                        Réserver maintenant
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>        
        <?php elseif(isset($error)): ?>
        <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Aucun hotel trouve</h3>
            <p class="text-gray-500"><?php echo htmlspecialchars($error); ?></p>
            <a href="index.php" class="inline-block mt-4 btn-primary">Retour a l'accueil</a>
        </div>
        <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg text-center">
            <p>Aucun hotel trouve pour cette recherche. Essayez d'autres dates ou une autre ville.</p>
        </div>
        <?php endif; ?>
        
    </div>
</section>

<script>
let hotelsData = <?php echo json_encode($results); ?>;

function toggleSection(header) {
    const section = header.closest('.filter-section');
    if (section) section.classList.toggle('collapsed');
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function number_format(number, decimals = 0) {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
}

function formatPriceUSD(price) {
    const match = document.cookie.match(/selected_currency=([^;]+)/);
    const selectedCurrency = match ? match[1] : 'USD';
    if (selectedCurrency === 'USD') {
        return number_format(price, 2) + ' USD';
    } else {
        return number_format(price * 2900, 0) + ' FC';
    }
}

function renderHotels(hotels) {
    const container = document.getElementById('hotels-container');
    if (!container) return;
    
    if (hotels.length === 0) {
        container.innerHTML = '<div class="col-span-full text-center py-12">Aucun hotel ne correspond a vos criteres</div>';
        return;
    }
    
    container.innerHTML = hotels.map((hotel, idx) => {
        const cleanName = (hotel.name || 'Hotel').replace(/\[.*?\]|\(.*?\)/, '').trim();
        const priceUsd = hotel.price_usd || 0;
        const rating = hotel.rating || 0;
        const stars = hotel.hotel_stars || 0;
        const priceDisplay = formatPriceUSD(priceUsd);
        
        const safeHotel = {
            id: hotel.id,
            name: cleanName,
            address: hotel.address || '',
            price_usd: priceUsd,
            price_total_usd: hotel.price_total_usd || 0,
            nights: hotel.nights || 1,
            rating: rating,
            hotel_stars: stars,
            image: hotel.image || '',
            room_name: hotel.room_name || 'Chambre standard',
            all_rates: hotel.all_rates || []
        };
        
        const hotelJson = JSON.stringify(safeHotel).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
        
        return `
            <div class="hotel-card" onclick='showHotelDetailFromData(${hotelJson}, ${idx})'>
                <div class="hotel-image">
                    <img src="${escapeHtml(hotel.image) || 'https://picsum.photos/400/300?random=' + idx}" 
                         alt="${escapeHtml(cleanName)}"
                         onerror="this.src='https://picsum.photos/400/300?random=${idx}'">
                    <div class="hotel-price">${priceDisplay}<small style="font-size: 10px;">/nuit</small></div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-bold text-gray-800 line-clamp-1">${escapeHtml(cleanName)}</h3>
                        ${rating > 0 ? `
                        <div class="flex items-center gap-1 bg-green-100 px-2 py-1 rounded-lg">
                            <span class="font-bold text-green-700">${rating.toFixed(1)}</span>
                            <svg class="w-3 h-3 text-green-700" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                        ` : ''}
                    </div>
                    <div class="flex items-center gap-1 mb-2">
                        ${Array(5).fill(0).map((_, i) => `
                            <svg class="w-4 h-4 ${i < stars ? 'text-yellow-400' : 'text-gray-300'} fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        `).join('')}
                    </div>
                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">${escapeHtml((hotel.address || '').substring(0, 100))}</p>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                        <div class="text-sm text-gray-500">${Math.floor(Math.random() * 500) + 50} avis</div>
                        <button class="btn-primary text-sm px-4 py-2" onclick="event.stopPropagation(); this.closest('.hotel-card').click();">Voir disponibilites</button>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function applyFiltersAndSort() {
    let filteredHotels = [...hotelsData];
    
    const priceMin = parseFloat(document.getElementById('price-min')?.value);
    const priceMax = parseFloat(document.getElementById('price-max')?.value);
    
    if (!isNaN(priceMin) && priceMin > 0) filteredHotels = filteredHotels.filter(h => (h.price_usd || 0) >= priceMin);
    if (!isNaN(priceMax) && priceMax > 0) filteredHotels = filteredHotels.filter(h => (h.price_usd || 0) <= priceMax);
    
    const selectedStars = Array.from(document.querySelectorAll('.star-filter:checked')).map(cb => parseInt(cb.value));
    if (selectedStars.length > 0) filteredHotels = filteredHotels.filter(h => selectedStars.includes(h.hotel_stars || 0));
    
    const selectedRatings = Array.from(document.querySelectorAll('.rating-filter:checked')).map(cb => parseInt(cb.value));
    if (selectedRatings.length > 0) {
        const minRating = Math.min(...selectedRatings);
        filteredHotels = filteredHotels.filter(h => (h.rating || 0) >= minRating);
    }
    
    const sortBy = document.getElementById('sortBy')?.value || 'recommended';
    switch(sortBy) {
        case 'price_asc': filteredHotels.sort((a, b) => (a.price_usd || 0) - (b.price_usd || 0)); break;
        case 'price_desc': filteredHotels.sort((a, b) => (b.price_usd || 0) - (a.price_usd || 0)); break;
        case 'rating_desc': filteredHotels.sort((a, b) => (b.rating || 0) - (a.rating || 0)); break;
        case 'rating_asc': filteredHotels.sort((a, b) => (a.rating || 0) - (b.rating || 0)); break;
        default: filteredHotels.sort((a, b) => (a.price_usd || 0) - (b.price_usd || 0));
    }
    
    renderHotels(filteredHotels);
    const countSpan = document.getElementById('resultCount');
    if (countSpan) countSpan.innerHTML = filteredHotels.length + ' hotel(s) trouve(s)';
}

function displayHotelGallery(hotelGallery) {
    const galleryContainer = document.getElementById('hotel-gallery-grid');
    if (!galleryContainer) return;
    
    let allPhotos = [];
    
    if (hotelGallery && hotelGallery.all && hotelGallery.all.length > 0) {
        allPhotos = hotelGallery.all;
    } else if (hotelGallery && Array.isArray(hotelGallery)) {
        allPhotos = hotelGallery;
    }
    
    if (allPhotos.length === 0) {
        galleryContainer.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500">Aucune photo disponible</div>';
        return;
    }
    
    // Limiter l'affichage initial à 12 photos
    const displayPhotos = allPhotos.slice(0, 12);
    const hasMore = allPhotos.length > 12;
    
    galleryContainer.innerHTML = displayPhotos.map((img, idx) => {
        const isLarge = idx % 5 === 0 && idx % 2 === 0;
        const sizeClass = isLarge ? 'col-span-2 row-span-2' : '';
        
        return `
            <div class="gallery-item ${sizeClass}" onclick="openImageModal('${img}')">
                <img src="${img}" alt="Photo hôtel ${idx + 1}" 
                     loading="lazy"
                     onerror="this.src='https://picsum.photos/400/300?random=${idx}'">
                <div class="overlay">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                    </svg>
                </div>
            </div>
        `;
    }).join('');
    
    // Supprimer l'ancien bouton s'il existe
    const existingBtn = document.querySelector('.see-all-btn-container');
    if (existingBtn) existingBtn.remove();
    
    // Ajouter un bouton "Voir toutes les photos" si nécessaire
    if (hasMore) {
        const btnContainer = document.createElement('div');
        btnContainer.className = 'see-all-btn-container';
        btnContainer.innerHTML = `
            <button class="see-all-btn" onclick="openGalleryModal(${JSON.stringify(allPhotos)})">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                Voir toutes les photos (${allPhotos.length})
            </button>
        `;
        galleryContainer.parentNode.appendChild(btnContainer);
    }
}
// Fonction pour ouvrir un modal de galerie complète
function openGalleryModal(images) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-95 z-[1200] flex flex-col items-center justify-center p-4';
    modal.onclick = (e) => {
        if (e.target === modal) modal.remove();
    };
    
    let currentIndex = 0;
    
    const updateImage = () => {
        const img = images[currentIndex];
        imageContainer.innerHTML = `
            <div class="relative max-w-5xl w-full">
                <img src="${img}" alt="Photo galerie" class="w-full rounded-lg shadow-2xl max-h-[80vh] object-contain">
                <div class="absolute inset-0 flex items-center justify-between px-4">
                    <button class="prev-btn bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full p-2 transition">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button class="next-btn bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full p-2 transition">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
                <div class="absolute bottom-4 left-0 right-0 text-center text-white text-sm">
                    ${currentIndex + 1} / ${images.length}
                </div>
            </div>
        `;
        
        // Réattacher les événements
        document.querySelector('.prev-btn')?.addEventListener('click', (e) => {
            e.stopPropagation();
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updateImage();
        });
        document.querySelector('.next-btn')?.addEventListener('click', (e) => {
            e.stopPropagation();
            currentIndex = (currentIndex + 1) % images.length;
            updateImage();
        });
    };
    
    const imageContainer = document.createElement('div');
    imageContainer.className = 'flex-1 flex items-center justify-center';
    modal.appendChild(imageContainer);
    
    // Ajouter bouton fermeture
    const closeBtn = document.createElement('button');
    closeBtn.className = 'absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-75 transition z-10';
    closeBtn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
    closeBtn.onclick = () => modal.remove();
    modal.appendChild(closeBtn);
    
    document.body.appendChild(modal);
    updateImage();
}
async function showHotelDetailFromData(hotel, index) {
    const modal = document.getElementById('hotelModal');
    if (!modal) return;
    
    // Afficher un loader
    const roomsList = document.getElementById('rooms-list');
    if (roomsList) {
        roomsList.innerHTML = `
            <div class="text-center py-12">
                <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-500">Chargement des chambres...</p>
            </div>
        `;
    }
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Informations de base de l'hôtel
    document.getElementById('modalTitle').innerHTML = escapeHtml(hotel.name || 'Hotel');
    document.getElementById('modalImage').src = hotel.image || 'https://picsum.photos/800/400?random=' + index;
    document.getElementById('modalAddress').innerHTML = escapeHtml(hotel.address || 'Adresse non disponible');
    
    // Étoiles
    const stars = hotel.hotel_stars || 0;
    document.getElementById('modalStars').innerHTML = Array(5).fill(0).map((_, i) => `
        <svg class="w-5 h-5 ${i < stars ? 'text-yellow-400' : 'text-gray-300'} fill-current" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
        </svg>
    `).join('');
    
    // Note
    const rating = hotel.rating || 0;
    const ratingEl = document.getElementById('modalRating');
    ratingEl.innerHTML = rating > 0 ? rating.toFixed(1) + ' / 10' : 'Non note';
    ratingEl.className = rating > 0 ? 'bg-green-100 text-green-700 px-2 py-1 rounded-lg text-sm font-semibold' : 'bg-gray-100 text-gray-600 px-2 py-1 rounded-lg text-sm font-semibold';
    
    const reviewsSpan = document.getElementById('modalReviews');
    if (reviewsSpan) reviewsSpan.innerHTML = `${Math.floor(Math.random() * 500) + 50} avis verifies`;
    
    // Prix
    const priceUsd = hotel.price_usd || 0;
    const totalPrice = hotel.price_total_usd || (priceUsd * (hotel.nights || 1));
    document.getElementById('modalPrice').innerHTML = formatPriceUSD(priceUsd);
    const totalPriceSpan = document.getElementById('modalTotalPrice');
    if (totalPriceSpan) totalPriceSpan.innerHTML = `Total: ${formatPriceUSD(totalPrice)} pour ${hotel.nights || 1} nuit(s)`;
    
    // Appel AJAX pour charger les détails
    try {
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        
        const ajaxUrl = `ajax_hotel_details.php?hotel_id=${encodeURIComponent(hotel.id)}&check_in=${encodeURIComponent(params.get('check_in'))}&check_out=${encodeURIComponent(params.get('check_out'))}&adults=${encodeURIComponent(params.get('adults'))}&nights=${hotel.nights || 1}`;
        
        console.log('AJAX URL:', ajaxUrl);
        
        const response = await fetch(ajaxUrl);
        
        // Vérifier si la réponse est OK
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const text = await response.text();
        console.log('Raw response:', text.substring(0, 500));
        
        // Vérifier si la réponse est du JSON valide
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Invalid JSON:', text.substring(0, 200));
            throw new Error('La réponse du serveur n\'est pas du JSON valide');
        }
        
        if (data.error) {
            throw new Error(data.error);
        }
        
        // Afficher la galerie de l'hôtel
        if (data.success && data.hotel && data.hotel.gallery) {
            displayHotelGallery(data.hotel.gallery);
        } else if (data.hotel && data.hotel.gallery) {
            displayHotelGallery(data.hotel.gallery);
        }
        
        if (data.success && data.rooms && data.rooms.length > 0) {
            const allRates = data.rooms;
            
            roomsList.innerHTML = allRates.map((rate, idx) => {
                const breakfastText = rate.breakfast_included ? 'Petit-dejeuner inclus' : 'Sans petit-dejeuner';
                const breakfastClass = rate.breakfast_included ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500';
                const isRefundable = rate.refundable !== false;
                const cancellationText = isRefundable ? 'Annulation gratuite' : 'Non remboursable';
                const roomName = (rate.room_name || 'Chambre standard').replace(/\\"/g, '"').replace(/&#39;/g, "'");
                
                // Vraies images des chambres
                let roomImages = (rate.images && rate.images.length > 0) ? rate.images : [];
                if (roomImages.length === 0 && hotel.image) roomImages = [hotel.image];
                if (roomImages.length === 0) roomImages = ['https://picsum.photos/200/150?random=' + idx];
                
                const galleryHtml = `
                    <div class="room-gallery flex gap-2 overflow-x-auto pb-2 mb-3">
                        ${roomImages.slice(0, 4).map(img => `
                            <img src="${img}" alt="Chambre ${escapeHtml(roomName)}" 
                                 class="w-24 h-20 object-cover rounded-lg cursor-pointer hover:opacity-80 transition"
                                 onerror="this.src='https://picsum.photos/100/80?random=${idx}'"
                                 onclick="event.stopPropagation(); openImageModal('${img}')">
                        `).join('')}
                    </div>
                `;
                
                return `
                    <div class="border rounded-xl p-4 hover:shadow-lg transition cursor-pointer room-option ${idx === 0 ? 'selected border-blue-400 bg-blue-50' : 'border-gray-200'}" 
                         onclick="selectRoom(${idx}, ${JSON.stringify(rate).replace(/"/g, '&quot;')})"
                         data-room-idx="${idx}">
                        ${galleryHtml}
                        <div class="flex flex-wrap justify-between items-start gap-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2 flex-wrap">
                                    <h4 class="font-bold text-gray-800 text-lg">${escapeHtml(roomName)}</h4>
                                    <span class="${breakfastClass} text-xs px-2 py-1 rounded-full">${breakfastText}</span>
                                    <span class="${isRefundable ? 'bg-green-50 text-green-700' : 'bg-orange-50 text-orange-700'} text-xs px-2 py-1 rounded-full">${cancellationText}</span>
                                </div>
                                <div class="room-description text-sm text-gray-600 mb-2">${rate.description || 'Chambre confortable avec equipements modernes pour un sejour agreable.'}</div>
                                <div class="flex flex-wrap gap-3 text-sm text-gray-600 mt-2 pt-2 border-t">
                                    <div class="flex items-center gap-1"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg><span><strong>${rate.max_occupancy || 2}</strong> personnes</span></div>
                                    <div class="flex items-center gap-1"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path></svg><span><strong>${rate.bed_type || 'Lit standard'}</strong></span></div>
                                    <div class="flex items-center gap-1"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg><span><strong>${rate.room_size || '20'}</strong> m²</span></div>
                                </div>
                                ${rate.perks && rate.perks.length > 0 ? `
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        ${rate.perks.slice(0, 3).map(perk => `<span class="bg-purple-50 text-purple-600 text-xs px-2 py-1 rounded-full flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>${escapeHtml(typeof perk === 'object' ? (perk.name || perk) : perk)}</span>`).join('')}
                                    </div>
                                ` : ''}
                            </div>
                            <div class="text-right min-w-[150px]">
                                <p class="text-2xl font-bold text-blue-600">${formatPriceUSD(rate.price_per_night)}</p>
                                <p class="text-xs text-gray-500">par nuit</p>
                                <p class="text-xs text-gray-400 mt-1">Total: ${formatPriceUSD(rate.price)}</p>
                                <p class="text-xs text-gray-400">pour ${data.nights || hotel.nights || 1} nuit(s)</p>
                                <button class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition w-full" onclick="event.stopPropagation(); selectAndBook(${idx}, ${JSON.stringify(rate).replace(/"/g, '&quot;')})">Choisir</button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        } else {
            roomsList.innerHTML = `<div class="text-center py-12 text-gray-500"><svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg><p class="font-medium">Aucune chambre disponible</p><p class="text-sm mt-2">Essayez de modifier vos dates de sejour</p></div>`;
        }
    } catch (error) {
        console.error('Error loading hotel details:', error);
        if (roomsList) {
            roomsList.innerHTML = `
                <div class="text-center py-12 text-red-500">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <p class="font-medium">Erreur lors du chargement des chambres</p>
                    <p class="text-sm mt-2">${error.message}</p>
                    <button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm" onclick="location.reload()">Réessayer</button>
                </div>
            `;
        }
    }
    
    // Stocker les données pour la réservation
    const formItem = document.getElementById('formItem');
    if (formItem) {
        formItem.value = JSON.stringify({
            id: hotel.id, name: hotel.name, address: hotel.address, price_usd: priceUsd,
            total_price: totalPrice, nights: hotel.nights || 1, rating: rating, image: hotel.image,
            room_name: hotel.room_name || 'Chambre standard',
            bed_type: 'Lit standard',
            breakfast_included: false,
            cancellation_text: 'Non remboursable'
        });
    }
}
function openImageModal(imageUrl) {
    const imgModal = document.createElement('div');
    imgModal.className = 'fixed inset-0 bg-black bg-opacity-90 z-[1100] flex items-center justify-center p-4 cursor-pointer';
    imgModal.onclick = () => imgModal.remove();
    imgModal.innerHTML = `<div class="relative max-w-4xl w-full" onclick="event.stopPropagation()"><img src="${imageUrl}" class="w-full rounded-lg shadow-2xl"><button class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-75" onclick="this.closest('.fixed').remove()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div>`;
    document.body.appendChild(imgModal);
}

function selectRoom(index, rate) {
    document.querySelectorAll('.room-option').forEach(opt => {
        opt.classList.remove('border-blue-400', 'bg-blue-50', 'selected');
        opt.classList.add('border-gray-200');
    });
    const selected = document.querySelector(`.room-option[data-room-idx="${index}"]`);
    if (selected) {
        selected.classList.remove('border-gray-200');
        selected.classList.add('border-blue-400', 'bg-blue-50', 'selected');
    }
    
    const formItem = document.getElementById('formItem');
    if (formItem && formItem.value) {
        try {
            const hotelData = JSON.parse(formItem.value);
            hotelData.room_name = rate.room_name;
            hotelData.price_usd = rate.price_per_night;
            hotelData.total_price = rate.price;
            hotelData.offer_id = rate.offer_id;
            formItem.value = JSON.stringify(hotelData);
            const selectedRoomType = document.getElementById('selectedRoomType');
            if (selectedRoomType) selectedRoomType.value = rate.room_name;
            document.getElementById('modalPrice').innerHTML = formatPriceUSD(rate.price_per_night);
            const totalPriceSpan = document.getElementById('modalTotalPrice');
            if (totalPriceSpan) totalPriceSpan.innerHTML = `Total: ${formatPriceUSD(rate.price)} pour ${hotelData.nights || 1} nuit(s)`;
        } catch(e) { console.error('Error parsing hotel data:', e); }
    }
}

function selectAndBook(index, rate) {
    selectRoom(index, rate);
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) bookingForm.submit();
}

function closeModal() {
    const modal = document.getElementById('hotelModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

window.showHotelDetailFromData = showHotelDetailFromData;
window.closeModal = closeModal;
window.openImageModal = openImageModal;

document.querySelectorAll('.modal-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        const tabId = this.dataset.tab;
        document.querySelectorAll('.modal-tab').forEach(t => {
            t.classList.remove('active', 'border-b-2', 'border-blue-600', 'text-blue-600');
            t.classList.add('text-gray-500');
        });
        this.classList.add('active', 'border-b-2', 'border-blue-600', 'text-blue-600');
        this.classList.remove('text-gray-500');
        document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
        const tabContent = document.getElementById(`tab-${tabId}`);
        if (tabContent) tabContent.classList.remove('hidden');
    });
});

document.addEventListener('DOMContentLoaded', function() {
    renderHotels(hotelsData);
    
    const openBtn = document.getElementById('openFilterBtn');
    const closeBtn = document.getElementById('closeFilterBtn');
    const sidebar = document.getElementById('filterSidebar');
    const overlay = document.getElementById('filterOverlay');
    
    if (openBtn && sidebar) openBtn.onclick = () => { sidebar.classList.add('open'); if (overlay) overlay.classList.add('open'); };
    if (closeBtn && sidebar && overlay) closeBtn.onclick = () => { sidebar.classList.remove('open'); overlay.classList.remove('open'); };
    if (overlay) overlay.onclick = () => { if (sidebar) sidebar.classList.remove('open'); overlay.classList.remove('open'); };
    
    const priceMin = document.getElementById('price-min');
    const priceMax = document.getElementById('price-max');
    const starFilters = document.querySelectorAll('.star-filter');
    const ratingFilters = document.querySelectorAll('.rating-filter');
    const sortBy = document.getElementById('sortBy');
    const resetBtn = document.getElementById('resetFilters');
    const applyChanges = () => applyFiltersAndSort();
    
    if (priceMin) priceMin.addEventListener('input', applyChanges);
    if (priceMax) priceMax.addEventListener('input', applyChanges);
    starFilters.forEach(f => f.addEventListener('change', applyChanges));
    ratingFilters.forEach(f => f.addEventListener('change', applyChanges));
    if (sortBy) sortBy.addEventListener('change', applyChanges);
    
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            if (priceMin) priceMin.value = '';
            if (priceMax) priceMax.value = '';
            starFilters.forEach(f => f.checked = false);
            ratingFilters.forEach(f => f.checked = false);
            if (sortBy) sortBy.value = 'recommended';
            applyChanges();
        });
    }
});
</script>