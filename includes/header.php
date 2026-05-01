<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luvia - Votre agence de voyage en RDC</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Style simple pour le menu mobile */
        #mobile-menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 999999;
            padding-top: 60px;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        
        #mobile-menu.hidden {
            display: none;
        }
        
        #mobile-menu:not(.hidden) {
            display: block;
        }
        
        body.menu-open {
            overflow: hidden;
        }
        
        #mobile-menu-button {
            cursor: pointer;
        }
        
        /* Bouton fermer */
        .close-menu-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #4b5563;
            padding: 10px;
            border-radius: 50%;
            transition: all 0.2s;
        }
        
        .close-menu-btn:hover {
            background-color: #f3f4f6;
            color: #2563eb;
        }
        
        /* Logo styling */
        .logo-img {
            height: 50px;
            width: auto;
            max-width: 180px;
            object-fit: contain;
        }
        
        /* Responsive logo */
        @media (max-width: 768px) {
            .logo-img {
                height: 40px;
                max-width: 140px;
            }
        }
        
        /* Navigation links hover effect */
        .nav-link {
            position: relative;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #2563eb;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full z-50 transition-shadow duration-300">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <!-- Logo avec améliorations de lisibilité -->
                <div class="flex items-center">
                    <img src="https://ukbekfcjfcjcqrpxfpmq.supabase.co/storage/v1/object/public/logo%20luvia/Vertical_Lockup_on_Blue_texte_Background-removebg-preview.png" 
                         alt="Luvia - Agence de voyage en RDC"
                         class="logo-img"
                         loading="lazy">
                </div>
                
                <!-- Menu Desktop -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="index.php" class="nav-link text-gray-700 hover:text-blue-600 transition duration-300">Accueil</a>
                    <a href="discover.php" class="nav-link text-gray-700 hover:text-blue-600 transition duration-300">Découvrir</a>
                    <a href="about.php" class="nav-link text-gray-700 hover:text-blue-600 transition duration-300">À propos</a>
                    <a href="contact.php" class="nav-link text-gray-700 hover:text-blue-600 transition duration-300">Contact</a>
                    
                    <!-- Sélecteur de devise desktop -->
                    <div class="relative ml-4">
                        <button id="currencyButton" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-full transition">
                            <!-- Icône devise SVG -->
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span id="selectedCurrency"><?php echo $_COOKIE['selected_currency'] ?? 'USD'; ?></span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        
                        <div id="currencyDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg hidden z-50 border border-gray-100">
                            <?php $currencies = getExchangeRates(); ?>
                            <?php foreach($currencies as $code => $currency): ?>
                                <button onclick="changeCurrency('<?php echo $code; ?>')" 
                                        class="w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center justify-between transition first:rounded-t-xl last:rounded-b-xl">
                                    <span><?php echo $currency['name']; ?></span>
                                    <span class="font-semibold text-gray-600"><?php echo $currency['symbol']; ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile menu button + devise mobile -->
                <div class="flex items-center gap-4">
                    <!-- Sélecteur de devise mobile -->
                    <div class="relative md:hidden">
                        <button id="mobileCurrencyButton" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-full transition text-sm">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span id="mobileSelectedCurrency"><?php echo $_COOKIE['selected_currency'] ?? 'USD'; ?></span>
                        </button>
                        <div id="mobileCurrencyDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg hidden z-50 border border-gray-100">
                            <?php foreach($currencies as $code => $currency): ?>
                                <button onclick="changeCurrency('<?php echo $code; ?>')" 
                                        class="w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center justify-between transition first:rounded-t-xl last:rounded-b-xl">
                                    <span><?php echo $currency['name']; ?></span>
                                    <span class="font-semibold text-gray-600"><?php echo $currency['symbol']; ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Bouton menu mobile -->
                    <button id="mobile-menu-button" class="md:hidden text-gray-600 focus:outline-none hover:text-blue-600 transition" aria-label="Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Mobile menu panel avec bouton fermer -->
    <div id="mobile-menu" class="hidden">
        <!-- Bouton fermer -->
        <button id="close-menu-btn" class="close-menu-btn" aria-label="Fermer">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <div class="flex flex-col space-y-4 px-6 mt-8">
            <a href="index.php" class="text-gray-700 hover:text-blue-600 transition py-3 text-lg border-b border-gray-100 flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21.75m4.5 0h-5.25" />
                </svg>
                Accueil
            </a>
            <a href="discover.php" class="text-gray-700 hover:text-blue-600 transition py-3 text-lg border-b border-gray-100 flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                </svg>
                Découvrir
            </a>
            <a href="about.php" class="text-gray-700 hover:text-blue-600 transition py-3 text-lg border-b border-gray-100 flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 9h.008v.008H9V9zm.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                </svg>
                À propos
            </a>
            <a href="contact.php" class="text-gray-700 hover:text-blue-600 transition py-3 text-lg border-b border-gray-100 flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
                Contact
            </a>
        </div>
    </div>

    <!-- Overlay pour fermer le menu mobile (optionnel) -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" style="display: none;"></div>
</body>
</html>