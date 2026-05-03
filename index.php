<?php 
require_once 'includes/config.php'; 
require_once 'includes/header.php'; 
set_time_limit(60);
// Récupérer la devise choisie
$selectedCurrency = $_COOKIE['selected_currency'] ?? 'USD';

// Fonction helper pour afficher les prix convertis
function displayPrice($priceInUSD) {
    global $selectedCurrency;
    return formatPrice($priceInUSD, $selectedCurrency);
}

$today = date('Y-m-d');
?>
<style>
/* Styles pour l'autocomplétion */
.autocomplete-results {
    position: absolute;
    z-index: 9999;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    max-height: 300px;
    overflow-y: auto;
    min-width: 250px;
}

.autocomplete-item {
    padding: 10px 16px;
    cursor: pointer;
    transition: background-color 0.2s;
    border-bottom: 1px solid #f0f0f0;
}

.autocomplete-item:hover {
    background-color: #f3f4f6;
}

.autocomplete-item.selected {
    background-color: #eff6ff;
}

.relative {
    position: relative;
}
.destination-card {
    transition: all 0.3s ease;
}
.destination-card:hover {
    transform: scale(1.02);
}
    @media (max-width: 768px) {
        .desktop-only { display: none; }
        
        .mobile-search-container { 
            display: block; 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            right: 0; 
            background: white; 
            border-radius: 24px 24px 0 0; 
            box-shadow: 0 -4px 20px rgba(0,0,0,0.1); 
            z-index: 1000; 
            transition: all 0.3s ease;
        }
        
        .mobile-search-container.minimized {
            transform: translateY(calc(100% - 50px));
        }
        
        .mobile-handle {
            width: 50px;
            height: 5px;
            background-color: #d1d5db;
            border-radius: 10px;
            margin: 10px auto;
            cursor: pointer;
        }
        
        .mobile-content {
            max-height: 70vh;
            overflow-y: auto;
            padding: 0 16px 16px 16px;
        }
        
        .mobile-search-container.minimized .mobile-content {
            display: none;
        }
        
        .minimized-title {
            display: none;
            text-align: center;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
            color: #2563eb;
        }
        
        .mobile-search-container.minimized .minimized-title {
            display: block;
        }
        
        .mobile-search-container.minimized .mobile-tab-nav {
            display: none;
        }
        
        .mobile-tab { flex: 1; text-align: center; padding: 8px 0; font-size: 12px; color: #9ca3af; cursor: pointer; }
        .mobile-tab.active { color: #2563eb; border-bottom: 2px solid #2563eb; }
        .mobile-tab svg { width: 20px; height: 20px; margin: 0 auto 4px; display: block; }
        .mobile-field { background-color: #f9fafb; border-radius: 48px; padding: 12px 16px; margin-bottom: 12px; }
        .mobile-field input, .mobile-field select { background: transparent; border: none; outline: none; width: 100%; font-size: 15px; margin-left: 8px; }
        .search-btn-mobile { background-color: #2563eb; color: white; font-weight: bold; padding: 14px; border-radius: 48px; width: 100%; text-align: center; border: none; cursor: pointer; }
        .mobile-radio-group { display: flex; gap: 16px; margin-bottom: 16px; justify-content: center; }
        .mobile-radio { padding: 6px 16px; border-radius: 40px; font-size: 13px; background: #f3f4f6; color: #6b7280; cursor: pointer; }
        .mobile-radio.active { background: #2563eb; color: white; }
        
        .passenger-modal-mobile {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-radius: 24px 24px 0 0;
            padding: 20px;
            z-index: 1100;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
        }
        .overlay-mobile {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1050;
        }
    }
    @media (min-width: 769px) {
        .mobile-search-container { display: none; }
        .desktop-only { display: block; }
    }
    @media (max-width: 768px) { .desktop-only { display: none; } }
    
    .radio-custom { position: relative; cursor: pointer; }
    .radio-custom input { position: absolute; opacity: 0; }
    .radio-custom .radio-label { padding: 4px 16px; border-radius: 30px; font-size: 0.8rem; font-weight: 500; color: #6b7280; }
    .radio-custom input:checked + .radio-label { background-color: #eef2ff; color: #2563eb; }
    .service-btn { padding: 10px 24px; border-radius: 9999px; background-color: #f3f4f6; color: #4b5563; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
    .service-btn.active { background-color: #2563eb; color: white; }

</style>

<?php $today = date('Y-m-d'); ?>

<!-- ==================== VERSION DESKTOP ==================== -->
<div class="desktop-only">

    <section class="relative h-screen flex items-center justify-center overflow-hidden">
        <div id="hero-slider" class="absolute top-0 left-0 w-full h-full">
            <div class="slide absolute top-0 left-0 w-full h-full opacity-0 transition-opacity duration-1000"><img src="https://images.unsplash.com/photo-1494500764479-0c8f2919a3d8?w=1920" class="w-full h-full object-cover"></div>
            <div class="slide absolute top-0 left-0 w-full h-full opacity-0 transition-opacity duration-1000"><img src="https://images.unsplash.com/photo-1595125990323-885cec5217ff?w=1920" class="w-full h-full object-cover"></div>
            <div class="slide absolute top-0 left-0 w-full h-full opacity-0 transition-opacity duration-1000"><img src="https://images.unsplash.com/photo-1468821216911-c12c293b9787?w=1920" class="w-full h-full object-cover"></div>
            <div class="slide absolute top-0 left-0 w-full h-full opacity-0 transition-opacity duration-1000"><img src="https://images.unsplash.com/photo-1565867254334-10280784ff69?w=1920" class="w-full h-full object-cover"></div>
            <div class="slide absolute top-0 left-0 w-full h-full opacity-0 transition-opacity duration-1000"><img src="https://images.unsplash.com/photo-1511884642898-4c92249e20b6?w=1920" class="w-full h-full object-cover"></div>
            <div class="slide absolute top-0 left-0 w-full h-full opacity-0 transition-opacity duration-1000"><img src="https://images.unsplash.com/photo-1433838552652-f9a46b332c40?w=1920" class="w-full h-full object-cover"></div>
            <div class="slide absolute top-0 left-0 w-full h-full opacity-0 transition-opacity duration-1000"><img src="https://images.unsplash.com/34/BA1yLjNnQCI1yisIZGEi_2013-07-16_1922_IMG_9873.jpg?w=1920" class="w-full h-full object-cover"></div>
        </div>
        <div class="absolute top-0 left-0 w-full h-full bg-black bg-opacity-40"></div>
        <div class="relative z-10 text-center text-white px-4 max-w-7xl mx-auto w-full py-12">
            <h1 class="text-5xl md:text-7xl font-bold mb-4">Explorez l'Afrique avec Luvia</h1>
            <p class="text-xl md:text-2xl mb-12">Découvrez les merveilles de la RDC et de l'Afrique centrale</p>
            
            <div class="w-full">
                <div class="flex flex-wrap gap-0 mb-0 overflow-x-auto justify-left">
                    <button data-tab-desktop="flights" class="tab-btn-desktop active px-6 py-3 rounded-t-xl font-semibold text-sm bg-white text-blue-600 shadow-md">Vols</button>
                    <button data-tab-desktop="hotels" class="tab-btn-desktop px-6 py-3 rounded-t-xl font-semibold text-sm bg-gray-100 text-gray-700">Hôtels</button>
                    <button data-tab-desktop="cars" class="tab-btn-desktop px-6 py-3 rounded-t-xl font-semibold text-sm bg-gray-100 text-gray-700">Location</button>
                    <button data-tab-desktop="packages" class="tab-btn-desktop px-6 py-3 rounded-t-xl font-semibold text-sm bg-gray-100 text-gray-700">Packages</button>
                    <button data-tab-desktop="insurance" class="tab-btn-desktop px-6 py-3 rounded-t-xl font-semibold text-sm bg-gray-100 text-gray-700">Assurance</button>
                </div>
                
                <div class="bg-white rounded-2xl rounded-tl-none shadow-2xl overflow-hidden">
                    <!-- VOLS -->
                    <div id="flights-content-desktop" class="tab-content-desktop active p-5">
                        <div class="flex gap-6 mb-5 pb-3 border-b">
                            <label class="radio-custom"><input type="radio" name="trip-type-desktop" value="roundtrip" class="trip-type-radio-desktop" checked><span class="radio-label">Aller-retour</span></label>
                            <label class="radio-custom"><input type="radio" name="trip-type-desktop" value="oneway" class="trip-type-radio-desktop"><span class="radio-label">Aller simple</span></label>
                            <label class="radio-custom"><input type="radio" name="trip-type-desktop" value="multicity" class="trip-type-radio-desktop"><span class="radio-label">Multi-destinations</span></label>
                        </div>
                        
                        <form action="search.php" method="GET" class="flight-form-desktop">
                            <input type="hidden" name="type" value="flight">
                            <input type="hidden" name="flight_type" id="desktop-flight-type" value="roundtrip">
                            
                            <div id="roundtrip-fields-desktop">
                                <div class="flex flex-row items-stretch gap-0">
                                    <div class="flex-1 border-r"><div class="p-3"><label class="text-xs font-semibold text-gray-500 uppercase">Départ</label><div class="flex items-center gap-2 mt-1"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><input type="text" name="origin" placeholder="Ville de départ" autocomplete="off" class="w-full border-none focus:ring-0 outline-none text-sm"></div></div></div>
                                    <div class="flex-1 border-r"><div class="p-3"><label class="text-xs font-semibold text-gray-500 uppercase">Arrivée</label><div class="flex items-center gap-2 mt-1"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.66 0 3-4 3-9s-1.34-9-3-9m0 18c-1.66 0-3-4-3-9s1.34-9 3-9"/></svg><input type="text" name="destination" placeholder="Ville d'arrivée" autocomplete="off" class="w-full border-none focus:ring-0 outline-none text-sm"></div></div></div>
                                    <div class="flex-1 border-r"><div class="p-3"><label class="text-xs font-semibold text-gray-500 uppercase">Départ</label><div class="flex items-center gap-2 mt-1"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg><input type="date" name="departure_date" min="<?php echo $today; ?>" value="<?php echo $today; ?>" class="w-full border-none focus:ring-0 outline-none text-sm"></div></div></div>
                                    <div class="flex-1 border-r" id="desktop-return-date"><div class="p-3"><label class="text-xs font-semibold text-gray-500 uppercase">Retour</label><div class="flex items-center gap-2 mt-1"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg><input type="date" name="return_date" min="<?php echo $today; ?>" class="w-full border-none focus:ring-0 outline-none text-sm"></div></div></div>
                                    
                                    <div class="flex-1 relative" id="desktop-passenger-trigger">
                                        <div class="p-3 cursor-pointer">
                                            <label class="text-xs font-semibold text-gray-500 uppercase">Voyageurs</label>
                                            <div class="flex items-center gap-2 mt-1">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                <span id="desktop-passenger-display" class="text-gray-800 text-sm">1 adulte, Economy</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div><button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl h-full text-sm uppercase shadow-md" style="margin: 8px;">Rechercher</button></div>
                                </div>
                            </div>
                            
                            <div id="multicity-fields-desktop" class="hidden">
                                <div id="multicity-container-desktop"></div>
                                <button type="button" id="add-multicity-desktop" class="text-blue-600 text-sm mt-2">+ Ajouter un trajet</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- HOTELS -->
                    <div id="hotels-content-desktop" class="tab-content-desktop hidden p-5">
                        <form action="search.php" method="GET" class="flex flex-row items-stretch gap-0">
                            <input type="hidden" name="type" value="hotel">
                            <div class="flex-1 border-r">
                                <div class="p-3">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Destination</label>
                                    <div class="flex items-center gap-2 mt-1">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        <input type="text" name="city" placeholder="Ville ou hôtel" autocomplete="off" class="w-full border-none outline-none text-sm" required>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 border-r">
                                <div class="p-3">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Check-in</label>
                                    <div class="flex items-center gap-2 mt-1">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <input type="date" name="check_in" min="<?php echo $today; ?>" value="<?php echo $today; ?>" class="w-full border-none outline-none text-sm" required>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 border-r">
                                <div class="p-3">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Check-out</label>
                                    <div class="flex items-center gap-2 mt-1">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <input type="date" name="check_out" min="<?php echo $today; ?>" class="w-full border-none outline-none text-sm" required>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 border-r">
                                <div class="p-3">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Adultes</label>
                                    <div class="flex items-center gap-2 mt-1">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <input type="number" name="adults" value="2" min="1" class="w-full border-none outline-none text-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 border-r">
                                <div class="p-3">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Enfants (âges)</label>
                                    <div class="flex items-center gap-2 mt-1">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        <input type="text" name="children_ages" placeholder="Ex: 5,8,11" class="w-full border-none outline-none text-sm">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl text-sm uppercase shadow-md" style="margin: 8px;">Rechercher</button>
                            </div>
                        </form>
                    </div>                    
                    
                    <!-- CARS -->
                    <div id="cars-content-desktop" class="tab-content-desktop hidden p-5">
                        <form action="search.php" method="GET" class="flex flex-row items-stretch gap-0">
                            <input type="hidden" name="type" value="car">
                            <div class="flex-1 border-r"><div class="p-3"><label class="text-xs font-semibold text-gray-500 uppercase">Lieu</label><div class="flex items-center gap-2 mt-1"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg><input type="text" name="city" placeholder="Ville" autocomplete="off" class="w-full border-none outline-none text-sm"></div></div></div>
                            <div class="flex-1 border-r"><div class="p-3"><label class="text-xs font-semibold text-gray-500 uppercase">Prise en charge</label><div class="flex items-center gap-2 mt-1"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg><input type="date" name="pickup_date" min="<?php echo $today; ?>" value="<?php echo $today; ?>" class="w-full border-none outline-none text-sm"></div></div></div>
                            <div class="flex-1 border-r"><div class="p-3"><label class="text-xs font-semibold text-gray-500 uppercase">Retour</label><div class="flex items-center gap-2 mt-1"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg><input type="date" name="return_date" min="<?php echo $today; ?>" class="w-full border-none outline-none text-sm"></div></div></div>
                            <div class="flex-1 border-r"><div class="p-3"><label class="text-xs font-semibold text-gray-500 uppercase">Type</label><div class="flex items-center gap-2 mt-1"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg><select name="rental_type" class="w-full border-none outline-none text-sm bg-transparent"><option value="self">Voiture simple</option><option value="chauffeur_only">Avec chauffeur</option><option value="chauffeur_fuel">Chauffeur + fuel</option></select></div></div></div>
                            <div><button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl text-sm uppercase shadow-md" style="margin: 8px;">Rechercher</button></div>
                        </form>
                    </div>
                    
                    <!-- PACKAGES - VERSION CORRIGÉE -->
<div id="packages-content-desktop" class="tab-content-desktop hidden p-5">
    <form action="search.php" method="GET" class="flex flex-row flex-wrap items-stretch gap-0">
        <input type="hidden" name="type" value="package">
        
        <!-- Destination (obligatoire) -->
        <div class="flex-1 min-w-[200px] border-r">
            <div class="p-3">
                <label class="text-xs font-semibold text-gray-500 uppercase">Où voulez-vous aller ? *</label>
                <div class="flex items-center gap-2 mt-1">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <input type="text" name="destination" placeholder="Ex: Kinshasa, Paris, Dubai" autocomplete="off" class="w-full border-none outline-none text-sm" required>
                </div>
                <p class="text-xs text-gray-400 mt-1">Recherche par ville ou pays</p>
            </div>
        </div>
        
        <!-- Date début (optionnel) -->
        <div class="flex-1 min-w-[180px] border-r">
            <div class="p-3">
                <label class="text-xs font-semibold text-gray-500 uppercase">Date de départ</label>
                <div class="flex items-center gap-2 mt-1">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <input type="date" name="start_date" min="<?php echo $today; ?>" value="<?php echo $today; ?>" class="w-full border-none outline-none text-sm">
                </div>
            </div>
        </div>
        
        <!-- Durée (optionnel) -->
        <div class="flex-1 min-w-[120px] border-r">
            <div class="p-3">
                <label class="text-xs font-semibold text-gray-500 uppercase">Durée (nuits)</label>
                <div class="flex items-center gap-2 mt-1">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <input type="number" name="duration" placeholder="Nuits" min="1" max="30" value="3" class="w-full border-none outline-none text-sm">
                </div>
            </div>
        </div>
        
        <!-- Adultes -->
        <div class="flex-1 min-w-[100px] border-r">
            <div class="p-3">
                <label class="text-xs font-semibold text-gray-500 uppercase">Adultes</label>
                <div class="flex items-center gap-2 mt-1">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <input type="number" name="adults" value="2" min="1" max="10" class="w-full border-none outline-none text-sm">
                </div>
            </div>
        </div>
        
        <!-- Enfants -->
        <div class="flex-1 min-w-[100px] border-r">
            <div class="p-3">
                <label class="text-xs font-semibold text-gray-500 uppercase">Enfants</label>
                <div class="flex items-center gap-2 mt-1">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <input type="number" name="children" value="0" min="0" max="6" class="w-full border-none outline-none text-sm">
                </div>
            </div>
        </div>
        
        <!-- Bouton Rechercher -->
        <div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl text-sm uppercase shadow-md h-full" style="margin: 8px;">
                Rechercher
            </button>
        </div>
    </form>
                        </div>
                    
                    <!-- ASSURANCE - CORRIGÉE -->
                    <div id="insurance-content-desktop" class="tab-content-desktop hidden p-5">
                        <form action="search.php" method="GET" class="flex flex-row items-stretch gap-0">
                            <input type="hidden" name="type" value="insurance">
                            <div class="flex-1 border-r">
                                <div class="p-3">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Zone de couverture</label>
                                    <div class="flex items-center gap-2 mt-1">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <select name="zone" class="w-full border-none outline-none text-sm bg-transparent" required>
                                            <option value="africa_asia">Afrique & Asie</option>
                                            <option value="schengen">Espace Schengen (Europe)</option>
                                            <option value="world_wide">Monde entier</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 border-r">
                                <div class="p-3">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Durée (jours)</label>
                                    <div class="flex items-center gap-2 mt-1">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <input type="number" name="duration" placeholder="7" min="1" max="360" value="7" class="w-full border-none outline-none text-sm" required>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 border-r">
                                <div class="p-3">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Âges des voyageurs</label>
                                    <div class="flex items-center gap-2 mt-1">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        <input type="text" name="ages" placeholder="Ex: 30,25,8,5" class="w-full border-none outline-none text-sm" required>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl text-sm uppercase shadow-md" style="margin: 8px;">Rechercher</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-6 left-0 right-0 flex justify-center gap-2 z-20">
            <?php for($i=0;$i<7;$i++): ?><div class="slider-dot w-2 h-2 rounded-full bg-white bg-opacity-50 cursor-pointer"></div><?php endfor; ?>
        </div>
    </section>
</div>

<!-- ==================== VERSION MOBILE ==================== -->
<div class="mobile-search-container" id="mobileSearchContainer">
    <div class="mobile-handle" id="mobileHandle"></div>
    <div class="minimized-title">Rechercher un voyage</div>
    
    <div class="mobile-content">
        <div class="flex mb-4 mobile-tab-nav">
            <div class="mobile-tab active" data-mobile-tab="flights"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 19l9 2-9-18-9 18 9-2z"/></svg><span>Vols</span></div>
            <div class="mobile-tab" data-mobile-tab="hotels"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg><span>Hôtels</span></div>
            <div class="mobile-tab" data-mobile-tab="cars"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7h12m0 0l-4-4m4 4l-4 4m-12 4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2v4m-8 4h12m0 0l-4 4m4-4l-4-4"/></svg><span>Location</span></div>
            <div class="mobile-tab" data-mobile-tab="packages"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg><span>Packages</span></div>
            <div class="mobile-tab" data-mobile-tab="insurance"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg><span>Assurance</span></div>
        </div>
        
        <!-- Mobile Flights -->
        <div id="mobile-flights" class="mobile-tab-content active">
            <form action="search.php" method="GET">
                <input type="hidden" name="type" value="flight">
                <div class="mobile-radio-group"><span class="mobile-radio active" data-trip="roundtrip">Aller-retour</span><span class="mobile-radio" data-trip="oneway">Aller simple</span></div>
                <div class="mobile-field"><div class="flex items-center"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><input type="text" name="origin" placeholder="Départ" autocomplete="off" class="w-full"></div></div>
                <div class="mobile-field"><div class="flex items-center"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.66 0 3-4 3-9s-1.34-9-3-9m0 18c-1.66 0-3-4-3-9s1.34-9 3-9"/></svg><input type="text" name="destination" placeholder="Arrivée" autocomplete="off" class="w-full"></div></div>
                <div class="mobile-field"><div class="flex items-center"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg><input type="date" name="departure_date" min="<?php echo $today; ?>" class="w-full"></div></div>
                <div id="mobile-return-date" class="mobile-field"><div class="flex items-center"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg><input type="date" name="return_date" min="<?php echo $today; ?>" class="w-full"></div></div>
                
                <div class="mobile-field" id="mobile-passenger-trigger">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span id="mobile-passenger-display" class="ml-2">1 adulte, Economy</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                
                <button type="submit" class="search-btn-mobile">RECHERCHER</button>
            </form>
        </div>
        
        <!-- Mobile Hotels -->
        <div id="mobile-hotels" class="mobile-tab-content hidden">
            <form action="search.php" method="GET">
                <input type="hidden" name="type" value="hotel">
                <div class="mobile-field">
                    <input type="text" name="city" placeholder="Destination" autocomplete="off" class="w-full" required>
                </div>
                <div class="mobile-field">
                    <input type="date" name="check_in" min="<?php echo $today; ?>" class="w-full" required>
                </div>
                <div class="mobile-field">
                    <input type="date" name="check_out" min="<?php echo $today; ?>" class="w-full" required>
                </div>
                <div class="mobile-field">
                    <input type="number" name="adults" placeholder="Adultes" value="2" min="1" class="w-full">
                </div>
                <div class="mobile-field">
                    <input type="text" name="children_ages" placeholder="Âges enfants (ex: 5,8,11)" class="w-full">
                </div>
                <button type="submit" class="search-btn-mobile">RECHERCHER</button>
            </form>
        </div>

        <!-- Mobile Cars -->
        <div id="mobile-cars" class="mobile-tab-content hidden">
            <form action="search.php" method="GET">
                <input type="hidden" name="type" value="car">
                <div class="mobile-field"><input type="text" name="city" placeholder="Ville" autocomplete="off" class="w-full"></div>
                <div class="mobile-field"><input type="date" name="pickup_date" min="<?php echo $today; ?>" class="w-full"></div>
                <div class="mobile-field"><input type="date" name="return_date" min="<?php echo $today; ?>" class="w-full"></div>
                <button type="submit" class="search-btn-mobile">RECHERCHER</button>
            </form>
        </div>
        
        <!-- Mobile Packages -->
        <div id="mobile-packages" class="mobile-tab-content hidden">
            <form action="search.php" method="GET">
                <input type="hidden" name="type" value="package">
                <div class="mobile-field"><input type="text" name="destination" placeholder="Destination" class="w-full"></div>
                <div class="mobile-field"><input type="date" name="start_date" min="<?php echo $today; ?>" class="w-full"></div>
                <div class="mobile-field"><input type="number" name="duration" placeholder="Nuits" class="w-full"></div>
                <button type="submit" class="search-btn-mobile">RECHERCHER</button>
            </form>
        </div>
        
        <!-- Mobile Insurance - CORRIGÉE -->
        <div id="mobile-insurance" class="mobile-tab-content hidden">
            <form action="search.php" method="GET">
                <input type="hidden" name="type" value="insurance">
                <div class="mobile-field">
                    <select name="zone" class="w-full" required>
                        <option value="africa_asia">Afrique & Asie</option>
                        <option value="schengen">Espace Schengen</option>
                        <option value="world_wide">Monde entier</option>
                    </select>
                </div>
                <div class="mobile-field">
                    <input type="number" name="duration" placeholder="Durée (jours)" min="1" max="360" value="7" class="w-full" required>
                </div>
                <div class="mobile-field">
                    <input type="text" name="ages" placeholder="Âges (ex: 30,25,8,5)" class="w-full" required>
                </div>
                <button type="submit" class="search-btn-mobile">RECHERCHER</button>
            </form>
        </div>
    </div>
</div>

<!-- Mobile Passenger Modal -->
<div id="mobile-passenger-modal" class="passenger-modal-mobile hidden">
    <div class="p-5">
        <div class="flex justify-between items-center mb-4 pb-3 border-b">
            <span class="font-semibold text-lg">Voyageurs et classe</span>
            <button id="close-mobile-passenger-modal" class="text-gray-400 text-2xl">&times;</button>
        </div>
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <div><span class="font-medium">Adultes</span><div class="text-xs text-gray-500">12 ans et plus</div></div>
                <div class="flex items-center gap-3"><button class="mobile-minus w-8 h-8 rounded-full border" data-type="adults">-</button><span id="m-adults" class="w-8 text-center">1</span><button class="mobile-plus w-8 h-8 rounded-full border" data-type="adults">+</button></div>
            </div>
            <div class="flex justify-between items-center">
                <div><span class="font-medium">Enfants</span><div class="text-xs text-gray-500">2-11 ans</div></div>
                <div class="flex items-center gap-3"><button class="mobile-minus w-8 h-8 rounded-full border" data-type="children">-</button><span id="m-children" class="w-8 text-center">0</span><button class="mobile-plus w-8 h-8 rounded-full border" data-type="children">+</button></div>
            </div>
            <div class="flex justify-between items-center">
                <div><span class="font-medium">Bébés</span><div class="text-xs text-gray-500">0-23 mois</div></div>
                <div class="flex items-center gap-3"><button class="mobile-minus w-8 h-8 rounded-full border" data-type="infants">-</button><span id="m-infants" class="w-8 text-center">0</span><button class="mobile-plus w-8 h-8 rounded-full border" data-type="infants">+</button></div>
            </div>
            <div class="flex justify-between items-center pt-2 border-t">
                <div><span class="font-medium">Classe</span></div>
                <select id="m-class" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="economy">Economy</option>
                    <option value="premium_economy">Premium Economy</option>
                    <option value="business">Business</option>
                    <option value="first">First Class</option>
                </select>
            </div>
        </div>
        <button id="apply-mobile-passenger" class="w-full mt-5 bg-blue-600 text-white py-3 rounded-xl font-semibold">Appliquer</button>
    </div>
</div>
<div id="mobile-overlay" class="overlay-mobile hidden"></div>

<!-- MODAL DESKTOP -->
<div id="desktop-passenger-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 400px; background: white; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); z-index: 999999;">
    <div style="padding: 16px; border-bottom: 1px solid #e5e7eb;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 600; font-size: 18px;">Voyageurs et classe</span>
            <button onclick="document.getElementById('desktop-passenger-modal').style.display='none'" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
    </div>
    <div style="padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div><span style="font-weight: 500;">Adultes</span><div style="font-size: 12px; color: #6b7280;">12 ans et plus</div></div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <button onclick="window.desktopAdults = Math.max(1, (window.desktopAdults||1) - 1); document.getElementById('desktop-adults-val').textContent = window.desktopAdults" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid #d1d5db; background: white; cursor: pointer;">-</button>
                <span id="desktop-adults-val" style="width: 30px; text-align: center;">1</span>
                <button onclick="window.desktopAdults = Math.min(9, (window.desktopAdults||1) + 1); document.getElementById('desktop-adults-val').textContent = window.desktopAdults" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid #d1d5db; background: white; cursor: pointer;">+</button>
            </div>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div><span style="font-weight: 500;">Enfants</span><div style="font-size: 12px; color: #6b7280;">2-11 ans</div></div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <button onclick="window.desktopChildren = Math.max(0, (window.desktopChildren||0) - 1); document.getElementById('desktop-children-val').textContent = window.desktopChildren" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid #d1d5db; background: white; cursor: pointer;">-</button>
                <span id="desktop-children-val" style="width: 30px; text-align: center;">0</span>
                <button onclick="window.desktopChildren = Math.min(6, (window.desktopChildren||0) + 1); document.getElementById('desktop-children-val').textContent = window.desktopChildren" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid #d1d5db; background: white; cursor: pointer;">+</button>
            </div>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div><span style="font-weight: 500;">Bébés</span><div style="font-size: 12px; color: #6b7280;">0-23 mois</div></div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <button onclick="window.desktopInfants = Math.max(0, (window.desktopInfants||0) - 1); document.getElementById('desktop-infants-val').textContent = window.desktopInfants" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid #d1d5db; background: white; cursor: pointer;">-</button>
                <span id="desktop-infants-val" style="width: 30px; text-align: center;">0</span>
                <button onclick="window.desktopInfants = Math.min(3, (window.desktopInfants||0) + 1); document.getElementById('desktop-infants-val').textContent = window.desktopInfants" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid #d1d5db; background: white; cursor: pointer;">+</button>
            </div>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
            <span style="font-weight: 500;">Classe</span>
            <select id="desktop-class-select" style="border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px;">
                <option value="economy">Economy</option>
                <option value="premium_economy">Premium Economy</option>
                <option value="business">Business</option>
                <option value="first">First Class</option>
            </select>
        </div>
    </div>
    <div style="padding: 16px; border-top: 1px solid #e5e7eb;">
        <button onclick="
            const total = (window.desktopAdults||1) + (window.desktopChildren||0) + (window.desktopInfants||0);
            let text = total === 1 ? '1 passager' : total + ' passagers';
            const classSelect = document.getElementById('desktop-class-select');
            const classText = classSelect.options[classSelect.selectedIndex]?.text || 'Economy';
            text += ', ' + classText;
            document.getElementById('desktop-passenger-display').textContent = text;
            document.getElementById('desktop-passenger-modal').style.display = 'none';
        " style="width: 100%; background-color: #2563eb; color: white; padding: 10px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer;">
            Appliquer
        </button>
    </div>
</div>

<script>
// Initialisation des variables globales
window.desktopAdults = 1;
window.desktopChildren = 0;
window.desktopInfants = 0;
window.mAdults = 1;
window.mChildren = 0;
window.mInfants = 0;

// Slider
let currentSlide = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.slider-dot');

function showSlide(i) {
    slides.forEach((s, idx) => {
        s.classList.remove('opacity-100');
        s.classList.add('opacity-0');
        if (dots[idx]) {
            dots[idx].classList.remove('bg-opacity-100');
            dots[idx].classList.add('bg-opacity-50');
        }
    });
    if (slides[i]) {
        slides[i].classList.remove('opacity-0');
        slides[i].classList.add('opacity-100');
    }
    if (dots[i]) {
        dots[i].classList.remove('bg-opacity-50');
        dots[i].classList.add('bg-opacity-100');
    }
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
}

let interval = setInterval(nextSlide, 5000);
dots.forEach((d, i) => {
    if (d) {
        d.addEventListener('click', () => {
            clearInterval(interval);
            currentSlide = i;
            showSlide(currentSlide);
            interval = setInterval(nextSlide, 5000);
        });
    }
});
showSlide(0);

// Desktop Tabs
const desktopTabs = document.querySelectorAll('.tab-btn-desktop');
const desktopContents = document.querySelectorAll('.tab-content-desktop');

if (desktopTabs.length) {
    desktopTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const tabId = tab.dataset.tabDesktop;
            
            desktopTabs.forEach(t => {
                t.classList.remove('active', 'bg-white', 'text-blue-600', 'shadow-md');
                t.classList.add('bg-gray-100', 'text-gray-700');
            });
            tab.classList.remove('bg-gray-100', 'text-gray-700');
            tab.classList.add('active', 'bg-white', 'text-blue-600', 'shadow-md');
            
            desktopContents.forEach(c => c.classList.add('hidden'));
            const targetContent = document.getElementById(tabId + '-content-desktop');
            if (targetContent) targetContent.classList.remove('hidden');
        });
    });
}

// Desktop Trip Type
const tripRadios = document.querySelectorAll('.trip-type-radio-desktop');
const roundtripDiv = document.getElementById('roundtrip-fields-desktop');
const multicityDiv = document.getElementById('multicity-fields-desktop');
const returnDateDivDesktop = document.getElementById('desktop-return-date');

if (tripRadios.length) {
    tripRadios.forEach(r => {
        r.addEventListener('change', function() {
            const val = this.value;
            const flightTypeInput = document.getElementById('desktop-flight-type');
            if (flightTypeInput) flightTypeInput.value = val;
            
            if (val === 'oneway') {
                if (returnDateDivDesktop) returnDateDivDesktop.classList.add('hidden');
                if (roundtripDiv) roundtripDiv.classList.remove('hidden');
                if (multicityDiv) multicityDiv.classList.add('hidden');
            } else if (val === 'multicity') {
                if (returnDateDivDesktop) returnDateDivDesktop.classList.remove('hidden');
                if (roundtripDiv) roundtripDiv.classList.add('hidden');
                if (multicityDiv) multicityDiv.classList.remove('hidden');
                if (window.multiCount === 0 && typeof addMultiCityDesktop === 'function') {
                    addMultiCityDesktop();
                }
            } else {
                if (returnDateDivDesktop) returnDateDivDesktop.classList.remove('hidden');
                if (roundtripDiv) roundtripDiv.classList.remove('hidden');
                if (multicityDiv) multicityDiv.classList.add('hidden');
            }
        });
    });
}

// Multi-city Desktop
window.multiCount = 0;
function addMultiCityDesktop() {
    window.multiCount++;
    const container = document.getElementById('multicity-container-desktop');
    if (!container) return;
    const newDiv = document.createElement('div');
    newDiv.className = 'multicity-item border rounded-xl p-4 mb-3';
    newDiv.innerHTML = `<div class="flex justify-between mb-2"><span>Trajet ${window.multiCount}</span><button type="button" class="remove-multicity-desktop text-red-500 text-sm">Supprimer</button></div><div class="grid grid-cols-3 gap-3"><input type="text" name="multi_origin[]" placeholder="Départ" autocomplete="off" class="border rounded-lg p-2 text-sm"><input type="text" name="multi_destination[]" placeholder="Arrivée" autocomplete="off" class="border rounded-lg p-2 text-sm"><input type="date" name="multi_date[]" min="<?php echo $today; ?>" class="border rounded-lg p-2 text-sm"></div>`;
        container.appendChild(newDiv);
    const removeBtn = newDiv.querySelector('.remove-multicity-desktop');
    if (removeBtn) {
        removeBtn.addEventListener('click', () => {
            newDiv.remove();
            window.multiCount--;
        });
    }
}
const addMultiBtn = document.getElementById('add-multicity-desktop');
if (addMultiBtn) addMultiBtn.addEventListener('click', addMultiCityDesktop);

// Mobile Tabs
const mobileTabs = document.querySelectorAll('.mobile-tab');
const mobileContents = document.querySelectorAll('.mobile-tab-content');

if (mobileTabs.length) {
    mobileTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.mobileTab;
            
            mobileTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            mobileContents.forEach(c => c.classList.add('hidden'));
            const targetContent = document.getElementById(`mobile-${target}`);
            if (targetContent) targetContent.classList.remove('hidden');
        });
    });
}

// Mobile Trip Type
const mobileRadios = document.querySelectorAll('.mobile-radio');
const mobileReturnDiv = document.getElementById('mobile-return-date');
if (mobileRadios.length) {
    mobileRadios.forEach(radio => {
        radio.addEventListener('click', () => {
            mobileRadios.forEach(r => r.classList.remove('active'));
            radio.classList.add('active');
            if (mobileReturnDiv) {
                if (radio.textContent.trim() === 'Aller simple') {
                    mobileReturnDiv.classList.add('hidden');
                } else {
                    mobileReturnDiv.classList.remove('hidden');
                }
            }
        });
    });
}

// ==================== PASSAGERS DESKTOP ====================
const desktopTrigger = document.getElementById('desktop-passenger-trigger');
const desktopModal = document.getElementById('desktop-passenger-modal');

function updateDesktopDisplay() {
    document.getElementById('desktop-adults-val').textContent = window.desktopAdults;
    document.getElementById('desktop-children-val').textContent = window.desktopChildren;
    document.getElementById('desktop-infants-val').textContent = window.desktopInfants;
}

if (desktopTrigger && desktopModal) {
    desktopTrigger.addEventListener('click', function(e) {
        e.stopPropagation();
        e.preventDefault();
        updateDesktopDisplay();
        desktopModal.style.display = 'block';
    });
}

// ==================== PASSAGERS MOBILE ====================
const mobileTrigger = document.getElementById('mobile-passenger-trigger');
const mobileModal = document.getElementById('mobile-passenger-modal');
const mobileOverlay = document.getElementById('mobile-overlay');

function updateMobileDisplay() {
    document.getElementById('m-adults').textContent = window.mAdults;
    document.getElementById('m-children').textContent = window.mChildren;
    document.getElementById('m-infants').textContent = window.mInfants;
}

function closeMobileModal() {
    if (mobileModal) mobileModal.classList.add('hidden');
    if (mobileOverlay) mobileOverlay.classList.add('hidden');
}

if (mobileTrigger && mobileModal && mobileOverlay) {
    mobileTrigger.addEventListener('click', function() {
        updateMobileDisplay();
        mobileModal.classList.remove('hidden');
        mobileOverlay.classList.remove('hidden');
    });
}

const closeMobileBtn = document.getElementById('close-mobile-passenger-modal');
if (closeMobileBtn) closeMobileBtn.addEventListener('click', closeMobileModal);
if (mobileOverlay) mobileOverlay.addEventListener('click', closeMobileModal);

// Boutons plus/moins mobile
document.querySelectorAll('.mobile-plus').forEach(btn => {
    btn.addEventListener('click', function() {
        const type = this.dataset.type;
        if (type === 'adults') window.mAdults = Math.min(9, window.mAdults + 1);
        else if (type === 'children') window.mChildren = Math.min(6, window.mChildren + 1);
        else if (type === 'infants') window.mInfants = Math.min(3, window.mInfants + 1);
        updateMobileDisplay();
    });
});

document.querySelectorAll('.mobile-minus').forEach(btn => {
    btn.addEventListener('click', function() {
        const type = this.dataset.type;
        if (type === 'adults') window.mAdults = Math.max(1, window.mAdults - 1);
        else if (type === 'children') window.mChildren = Math.max(0, window.mChildren - 1);
        else if (type === 'infants') window.mInfants = Math.max(0, window.mInfants - 1);
        updateMobileDisplay();
    });
});

const applyMobileBtn = document.getElementById('apply-mobile-passenger');
if (applyMobileBtn) {
    applyMobileBtn.addEventListener('click', function() {
        const total = window.mAdults + window.mChildren + window.mInfants;
        let text = total === 1 ? '1 passager' : total + ' passagers';
        const classSelect = document.getElementById('m-class');
        if (classSelect) {
            const classText = classSelect.options[classSelect.selectedIndex]?.text || 'Economy';
            text += ', ' + classText;
        }
        document.getElementById('mobile-passenger-display').textContent = text;
        closeMobileModal();
    });
}

// Mobile minimisation
const mobileContainer = document.getElementById('mobileSearchContainer');
const mobileHandle = document.getElementById('mobileHandle');
let isMinimized = false;

if (mobileHandle) {
    mobileHandle.addEventListener('click', function() {
        if (isMinimized) {
            if (mobileContainer) mobileContainer.classList.remove('minimized');
            isMinimized = false;
        } else {
            if (mobileContainer) mobileContainer.classList.add('minimized');
            isMinimized = true;
        }
    });
}

// Validation des dates (empêcher les dates passées)
function validateDates() {
    const today = new Date().toISOString().split('T')[0];
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    dateInputs.forEach(input => {
        input.setAttribute('min', today);
        
        if (!input.value || input.value < today) {
            input.value = today;
        }
        
        input.addEventListener('change', function() {
            if (this.value < today) {
                this.value = today;
                if (window.showToast) {
                    showToast('Vous ne pouvez pas sélectionner une date passée', 'warning');
                }
            }
        });
    });
}

validateDates();

const dateObserver = new MutationObserver(function(mutations) {
    let shouldUpdate = false;
    mutations.forEach(function(mutation) {
        if (mutation.addedNodes.length) {
            shouldUpdate = true;
        }
    });
    if (shouldUpdate) validateDates();
});
dateObserver.observe(document.body, { childList: true, subtree: true });

console.log('Luvia - Site chargé avec succès');
class CityAutocomplete {
    constructor(inputElement, options = {}) {
        this.input = inputElement;
        this.options = {
            minChars: 2,
            delay: 300,
            maxResults: 10,
            ...options
        };
        
        this.currentRequest = null;
        this.resultsContainer = null;
        this.selectedIndex = -1;
        
        this.init();
    }
    
    init() {
        // Créer le conteneur des résultats
        this.resultsContainer = document.createElement('div');
        this.resultsContainer.className = 'autocomplete-results hidden';
        
        // S'assurer que le parent a position relative
        if (getComputedStyle(this.input.parentElement).position !== 'relative') {
            this.input.parentElement.style.position = 'relative';
        }
        this.input.parentElement.appendChild(this.resultsContainer);
        
        // Événements
        this.input.addEventListener('input', (e) => this.onInput(e));
        this.input.addEventListener('keydown', (e) => this.onKeyDown(e));
        this.input.addEventListener('blur', (e) => this.onBlur(e));
    }
    
    onInput(event) {
        const query = event.target.value.trim();
        
        if (query.length < this.options.minChars) {
            this.hideResults();
            return;
        }
        
        clearTimeout(this.debounceTimeout);
        this.debounceTimeout = setTimeout(() => this.search(query), this.options.delay);
    }
    
    async search(query) {
        if (this.currentRequest) {
            this.currentRequest.abort();
        }
        
        this.currentRequest = new AbortController();
        
        try {
            const response = await fetch(`includes/config.php?action=autocomplete&query=${encodeURIComponent(query)}`, {
                signal: this.currentRequest.signal
            });
            const data = await response.json();
            
            if (data.success && data.results) {
                this.displayResults(data.results);
            } else {
                this.hideResults();
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Autocomplete error:', error);
            }
        } finally {
            this.currentRequest = null;
        }
    }
    
    displayResults(results) {
        if (results.length === 0) {
            this.hideResults();
            return;
        }
        
        this.resultsContainer.innerHTML = '';
        this.selectedIndex = -1;
        
        results.slice(0, this.options.maxResults).forEach((result, index) => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            item.innerHTML = result.display;
            item.dataset.value = result.value;
            item.dataset.city = result.city || result.value;
            item.dataset.country = result.country || '';
            item.dataset.type = result.type;
            
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                this.selectResult(result);
            });
            
            item.addEventListener('mouseenter', () => {
                this.selectedIndex = index;
                this.highlightSelected();
            });
            
            this.resultsContainer.appendChild(item);
        });
        
        this.resultsContainer.classList.remove('hidden');
    }
    
    highlightSelected() {
        const items = this.resultsContainer.querySelectorAll('.autocomplete-item');
        items.forEach((item, index) => {
            if (index === this.selectedIndex) {
                item.classList.add('selected');
            } else {
                item.classList.remove('selected');
            }
        });
        
        if (this.selectedIndex >= 0 && items[this.selectedIndex]) {
            items[this.selectedIndex].scrollIntoView({ block: 'nearest' });
        }
    }
    
    selectResult(result) {
        this.input.value = result.value;
        this.input.dataset.city = result.city || result.value;
        this.input.dataset.country = result.country || '';
        this.input.dataset.type = result.type || 'city';
        this.hideResults();
        
        // Déclencher l'événement change
        this.input.dispatchEvent(new Event('change', { bubbles: true }));
    }
    
    onKeyDown(event) {
        const items = this.resultsContainer.querySelectorAll('.autocomplete-item');
        
        if (items.length === 0) return;
        
        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, items.length - 1);
                this.highlightSelected();
                break;
                
            case 'ArrowUp':
                event.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
                this.highlightSelected();
                break;
                
            case 'Enter':
                event.preventDefault();
                if (this.selectedIndex >= 0 && items[this.selectedIndex]) {
                    const value = items[this.selectedIndex].dataset.value;
                    const result = {
                        value: value,
                        city: items[this.selectedIndex].dataset.city,
                        country: items[this.selectedIndex].dataset.country,
                        type: items[this.selectedIndex].dataset.type
                    };
                    this.selectResult(result);
                }
                break;
                
            case 'Escape':
                this.hideResults();
                break;
        }
    }
    
    onBlur(event) {
        setTimeout(() => {
            if (!this.resultsContainer.contains(document.activeElement)) {
                this.hideResults();
            }
        }, 200);
    }
    
    hideResults() {
        this.resultsContainer.classList.add('hidden');
        this.resultsContainer.innerHTML = '';
        this.selectedIndex = -1;
    }
}

// ==================== INITIALISATION ====================
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser l'autocomplétion sur tous les champs ville
    const cityInputs = [
        // Version desktop
        'input[name="origin"]',
        'input[name="destination"]', 
        'input[name="city"]',
        // Version mobile - vols
        '#mobile-flights input[name="origin"]',
        '#mobile-flights input[name="destination"]',
        // Version mobile - hôtels
        '#mobile-hotels input[name="city"]',
        // Version mobile - location
        '#mobile-cars input[name="city"]',
        // Version mobile - packages
        '#mobile-packages input[name="destination"]'
    ];
    
    cityInputs.forEach(selector => {
        const inputs = document.querySelectorAll(selector);
        inputs.forEach(input => {
            if (!input._autocomplete) {
                input._autocomplete = new CityAutocomplete(input);
            }
        });
    });
    
    // Observer pour les champs ajoutés dynamiquement (multi-city)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1 && node.querySelectorAll) {
                    const newInputs = node.querySelectorAll('input[name="multi_origin[]"], input[name="multi_destination[]"]');
                    newInputs.forEach(input => {
                        if (!input._autocomplete) {
                            input._autocomplete = new CityAutocomplete(input);
                        }
                    });
                }
            });
        });
    });
    
    observer.observe(document.body, { childList: true, subtree: true });
});

console.log('Autocomplétion chargée avec succès');
</script>

<!-- Destinations populaires -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <h2 class="text-4xl font-bold text-center text-gray-800 mb-12">Destinations populaires</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php $destinations = getDestinations(); foreach($destinations as $destination): ?>
            <div class="group relative overflow-hidden rounded-2xl shadow-lg cursor-pointer" 
     onclick="window.location.href='<?php if($destination['type'] == 'hotel') { 
         echo 'search.php?type=hotel&city=' . urlencode($destination['city']); 
     } else { 
         echo 'package-detail.php?id=' . urlencode($destination['id']); 
     } ?>'">
                <img src="<?php echo $destination['image']; ?>" class="w-full h-80 object-cover group-hover:scale-110 transition">
                <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6 text-white">
                    <h3 class="text-2xl font-bold"><?php echo htmlspecialchars($destination['name']); ?></h3>
                    <p><?php echo htmlspecialchars($destination['description']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Packages exclusifs -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Nos packages exclusifs</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Des voyages clé en main pour une expérience inoubliable en Afrique</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php $packages = getPackages(); foreach($packages as $package): ?>
            <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 cursor-pointer" onclick="window.location.href='package-detail.php?id=<?php echo urlencode($package['id']); ?>'">
                <div class="relative h-64 overflow-hidden">
                    <img src="<?php echo $package['image']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-full px-4 py-1 shadow-lg">
                        <span class="text-blue-600 font-bold text-lg"><?php echo displayPrice($package['price']); ?></span>
                        <span class="text-gray-500 text-xs">/pers</span>
                    </div>
                    <div class="absolute bottom-4 left-4 bg-black/60 backdrop-blur-sm rounded-full px-3 py-1 text-white text-sm">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><?php echo $package['duration_nights']; ?> nuits
                    </div>
                    <div class="absolute top-4 left-4 bg-yellow-500/90 backdrop-blur-sm rounded-full px-2 py-1 text-white text-xs flex items-center">
                        <svg class="w-3 h-3 fill-current mr-1" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>4.9
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="text-xl font-bold text-gray-800 mb-1 group-hover:text-blue-600 transition"><?php echo htmlspecialchars($package['name']); ?></h3>
                    <div class="flex items-center text-gray-500 text-sm mb-3">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <?php echo htmlspecialchars($package['destination']); ?>
                    </div>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2"><?php echo htmlspecialchars(substr($package['description'], 0, 100)); ?>...</p>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <?php foreach($package['includes'] as $item): ?>
                        <div class="flex items-center gap-1 bg-gray-100 rounded-full px-3 py-1">
                            <?php if($item == 'flight'): ?>
                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 19l9 2-9-18-9 18 9-2z"></path></svg>
                            <span class="text-xs text-gray-700">Vol</span>
                            <?php elseif($item == 'hotel'): ?>
                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <span class="text-xs text-gray-700">Hôtel</span>
                            <?php elseif($item == 'car'): ?>
                            <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7h12m0 0l-4-4m4 4l-4 4m-12 4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2v4m-8 4h12m0 0l-4 4m4-4l-4-4"></path></svg>
                            <span class="text-xs text-gray-700">Voiture</span>
                            <?php elseif($item == 'activities'): ?>
                            <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            <span class="text-xs text-gray-700">Activités</span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <div>
                            <span class="text-2xl font-bold text-blue-600"><?php echo displayPrice($package['price']); ?></span>
                            <span class="text-gray-400 text-xs">/pers</span>
                        </div>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-full text-sm font-semibold transition flex items-center gap-2 shadow-md">
                            Voir détail
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>.line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}</style>
<?php require_once 'includes/footer.php'; ?>
