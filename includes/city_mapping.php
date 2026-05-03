<?php
// ==================== MAPPING LOCAL DES VILLES ET AEROPORTS ====================
// Ce fichier contient les données pour l'autocomplétion et la conversion ville -> IATA
// Mis à jour manuellement ou via script d'import

$cityAirportMapping = [
    // ==================== AFRIQUE ====================
    // RDC (République Démocratique du Congo)
    'kinshasa' => ['code' => 'FIH', 'name' => 'Kinshasa N\'Djili', 'country' => 'CD', 'type' => 'airport'],
    'lubumbashi' => ['code' => 'FBM', 'name' => 'Lubumbashi International', 'country' => 'CD', 'type' => 'airport'],
    'goma' => ['code' => 'GOM', 'name' => 'Goma International', 'country' => 'CD', 'type' => 'airport'],
    'kisangani' => ['code' => 'FKI', 'name' => 'Kisangani Bangoka', 'country' => 'CD', 'type' => 'airport'],
    'bukavu' => ['code' => 'BKY', 'name' => 'Bukavu Kavumu', 'country' => 'CD', 'type' => 'airport'],
    'matadi' => ['code' => 'MAT', 'name' => 'Matadi Tshimpi', 'country' => 'CD', 'type' => 'airport'],
    'bandundu' => ['code' => 'FDU', 'name' => 'Bandundu', 'country' => 'CD', 'type' => 'airport'],
    'kananga' => ['code' => 'KGA', 'name' => 'Kananga', 'country' => 'CD', 'type' => 'airport'],
    'mbuji mayi' => ['code' => 'MJM', 'name' => 'Mbuji Mayi', 'country' => 'CD', 'type' => 'airport'],
    'likasi' => ['code' => 'LIK', 'name' => 'Likasi', 'country' => 'CD', 'type' => 'airport'],
    'kolwezi' => ['code' => 'KWZ', 'name' => 'Kolwezi', 'country' => 'CD', 'type' => 'airport'],
    
    // Égypte
    'le caire' => ['code' => 'CAI', 'name' => 'Cairo International', 'country' => 'EG', 'type' => 'airport'],
    'cairo' => ['code' => 'CAI', 'name' => 'Cairo International', 'country' => 'EG', 'type' => 'airport'],
    'alexandrie' => ['code' => 'ALY', 'name' => 'Alexandria El Nouzha', 'country' => 'EG', 'type' => 'airport'],
    'alexandria' => ['code' => 'ALY', 'name' => 'Alexandria El Nouzha', 'country' => 'EG', 'type' => 'airport'],
    'louxor' => ['code' => 'LXR', 'name' => 'Luxor International', 'country' => 'EG', 'type' => 'airport'],
    'luxor' => ['code' => 'LXR', 'name' => 'Luxor International', 'country' => 'EG', 'type' => 'airport'],
    
    // Tanzanie
    'zanzibar' => ['code' => 'ZNZ', 'name' => 'Abeid Amani Karume', 'country' => 'TZ', 'type' => 'airport'],
    'dar es salaam' => ['code' => 'DAR', 'name' => 'Julius Nyerere', 'country' => 'TZ', 'type' => 'airport'],
    'arusha' => ['code' => 'ARK', 'name' => 'Arusha', 'country' => 'TZ', 'type' => 'airport'],
    'kilimandjaro' => ['code' => 'JRO', 'name' => 'Kilimanjaro International', 'country' => 'TZ', 'type' => 'airport'],
    
    // Maroc
    'marrakech' => ['code' => 'RAK', 'name' => 'Marrakech Menara', 'country' => 'MA', 'type' => 'airport'],
    'casablanca' => ['code' => 'CMN', 'name' => 'Mohammed V', 'country' => 'MA', 'type' => 'airport'],
    'rabat' => ['code' => 'RBA', 'name' => 'Rabat-Salé', 'country' => 'MA', 'type' => 'airport'],
    'tanger' => ['code' => 'TNG', 'name' => 'Tangier Ibn Battouta', 'country' => 'MA', 'type' => 'airport'],
    'fes' => ['code' => 'FEZ', 'name' => 'Fès-Saïss', 'country' => 'MA', 'type' => 'airport'],
    
    // Sénégal
    'dakar' => ['code' => 'DSS', 'name' => 'Blaise Diagne', 'country' => 'SN', 'type' => 'airport'],
    
    // Côte d'Ivoire
    'abidjan' => ['code' => 'ABJ', 'name' => 'Félix Houphouët-Boigny', 'country' => 'CI', 'type' => 'airport'],
    
    // Ghana
    'accra' => ['code' => 'ACC', 'name' => 'Kotoka International', 'country' => 'GH', 'type' => 'airport'],
    
    // Nigeria
    'lagos' => ['code' => 'LOS', 'name' => 'Murtala Muhammed', 'country' => 'NG', 'type' => 'airport'],
    
    // Kenya
    'nairobi' => ['code' => 'NBO', 'name' => 'Jomo Kenyatta', 'country' => 'KE', 'type' => 'airport'],
    'mombasa' => ['code' => 'MBA', 'name' => 'Moi International', 'country' => 'KE', 'type' => 'airport'],
    
    // Ouganda
    'kampala' => ['code' => 'EBB', 'name' => 'Entebbe', 'country' => 'UG', 'type' => 'airport'],
    
    // Rwanda
    'kigali' => ['code' => 'KGL', 'name' => 'Kigali International', 'country' => 'RW', 'type' => 'airport'],
    
    // Éthiopie
    'addis ababa' => ['code' => 'ADD', 'name' => 'Bole International', 'country' => 'ET', 'type' => 'airport'],
    
    // Afrique du Sud
    'johannesburg' => ['code' => 'JNB', 'name' => 'O.R. Tambo', 'country' => 'ZA', 'type' => 'airport'],
    'le cap' => ['code' => 'CPT', 'name' => 'Cape Town', 'country' => 'ZA', 'type' => 'airport'],
    'cape town' => ['code' => 'CPT', 'name' => 'Cape Town', 'country' => 'ZA', 'type' => 'airport'],
    
    // Tunisie
    'tunis' => ['code' => 'TUN', 'name' => 'Tunis-Carthage', 'country' => 'TN', 'type' => 'airport'],
    
    // Algérie
    'alger' => ['code' => 'ALG', 'name' => 'Houari Boumediene', 'country' => 'DZ', 'type' => 'airport'],
    'oran' => ['code' => 'ORN', 'name' => 'Oran Es Sénia', 'country' => 'DZ', 'type' => 'airport'],
    
    // ==================== EUROPE ====================
    // France
    'paris' => ['code' => 'CDG', 'name' => 'Charles de Gaulle', 'country' => 'FR', 'type' => 'airport'],
    'paris cdg' => ['code' => 'CDG', 'name' => 'Charles de Gaulle', 'country' => 'FR', 'type' => 'airport'],
    'paris orly' => ['code' => 'ORY', 'name' => 'Orly', 'country' => 'FR', 'type' => 'airport'],
    'marseille' => ['code' => 'MRS', 'name' => 'Marseille Provence', 'country' => 'FR', 'type' => 'airport'],
    'lyon' => ['code' => 'LYS', 'name' => 'Lyon Saint-Exupéry', 'country' => 'FR', 'type' => 'airport'],
    'toulouse' => ['code' => 'TLS', 'name' => 'Toulouse-Blagnac', 'country' => 'FR', 'type' => 'airport'],
    'nice' => ['code' => 'NCE', 'name' => 'Nice Côte d\'Azur', 'country' => 'FR', 'type' => 'airport'],
    'bordeaux' => ['code' => 'BOD', 'name' => 'Bordeaux-Mérignac', 'country' => 'FR', 'type' => 'airport'],
    'lille' => ['code' => 'LIL', 'name' => 'Lille-Lesquin', 'country' => 'FR', 'type' => 'airport'],
    'strasbourg' => ['code' => 'SXB', 'name' => 'Strasbourg-Entzheim', 'country' => 'FR', 'type' => 'airport'],
    'nantes' => ['code' => 'NTE', 'name' => 'Nantes Atlantique', 'country' => 'FR', 'type' => 'airport'],
    'montpellier' => ['code' => 'MPL', 'name' => 'Montpellier-Méditerranée', 'country' => 'FR', 'type' => 'airport'],
    
    // Royaume-Uni
    'londres' => ['code' => 'LHR', 'name' => 'Heathrow', 'country' => 'GB', 'type' => 'airport'],
    'london' => ['code' => 'LHR', 'name' => 'Heathrow', 'country' => 'GB', 'type' => 'airport'],
    'london heathrow' => ['code' => 'LHR', 'name' => 'Heathrow', 'country' => 'GB', 'type' => 'airport'],
    'london gatwick' => ['code' => 'LGW', 'name' => 'Gatwick', 'country' => 'GB', 'type' => 'airport'],
    'london stansted' => ['code' => 'STN', 'name' => 'Stansted', 'country' => 'GB', 'type' => 'airport'],
    'manchester' => ['code' => 'MAN', 'name' => 'Manchester', 'country' => 'GB', 'type' => 'airport'],
    'birmingham' => ['code' => 'BHX', 'name' => 'Birmingham', 'country' => 'GB', 'type' => 'airport'],
    'edinburgh' => ['code' => 'EDI', 'name' => 'Edinburgh', 'country' => 'GB', 'type' => 'airport'],
    'glasgow' => ['code' => 'GLA', 'name' => 'Glasgow', 'country' => 'GB', 'type' => 'airport'],
    
    // Allemagne
    'berlin' => ['code' => 'BER', 'name' => 'Brandenburg', 'country' => 'DE', 'type' => 'airport'],
    'munich' => ['code' => 'MUC', 'name' => 'Munich', 'country' => 'DE', 'type' => 'airport'],
    'frankfurt' => ['code' => 'FRA', 'name' => 'Frankfurt', 'country' => 'DE', 'type' => 'airport'],
    'hamburg' => ['code' => 'HAM', 'name' => 'Hamburg', 'country' => 'DE', 'type' => 'airport'],
    'cologne' => ['code' => 'CGN', 'name' => 'Cologne Bonn', 'country' => 'DE', 'type' => 'airport'],
    
    // Italie
    'rome' => ['code' => 'FCO', 'name' => 'Fiumicino', 'country' => 'IT', 'type' => 'airport'],
    'milan' => ['code' => 'MXP', 'name' => 'Malpensa', 'country' => 'IT', 'type' => 'airport'],
    'venise' => ['code' => 'VCE', 'name' => 'Marco Polo', 'country' => 'IT', 'type' => 'airport'],
    'florence' => ['code' => 'FLR', 'name' => 'Peretola', 'country' => 'IT', 'type' => 'airport'],
    'naples' => ['code' => 'NAP', 'name' => 'Naples', 'country' => 'IT', 'type' => 'airport'],
    
    // Espagne
    'barcelone' => ['code' => 'BCN', 'name' => 'Barcelona-El Prat', 'country' => 'ES', 'type' => 'airport'],
    'madrid' => ['code' => 'MAD', 'name' => 'Madrid-Barajas', 'country' => 'ES', 'type' => 'airport'],
    'valence' => ['code' => 'VLC', 'name' => 'Valencia', 'country' => 'ES', 'type' => 'airport'],
    'seville' => ['code' => 'SVQ', 'name' => 'Seville', 'country' => 'ES', 'type' => 'airport'],
    'malaga' => ['code' => 'AGP', 'name' => 'Málaga-Costa del Sol', 'country' => 'ES', 'type' => 'airport'],
    
    // Pays-Bas
    'amsterdam' => ['code' => 'AMS', 'name' => 'Schiphol', 'country' => 'NL', 'type' => 'airport'],
    
    // Belgique
    'bruxelles' => ['code' => 'BRU', 'name' => 'Brussels', 'country' => 'BE', 'type' => 'airport'],
    'brussels' => ['code' => 'BRU', 'name' => 'Brussels', 'country' => 'BE', 'type' => 'airport'],
    
    // Suisse
    'zurich' => ['code' => 'ZRH', 'name' => 'Zurich', 'country' => 'CH', 'type' => 'airport'],
    'geneve' => ['code' => 'GVA', 'name' => 'Geneva', 'country' => 'CH', 'type' => 'airport'],
    'genève' => ['code' => 'GVA', 'name' => 'Geneva', 'country' => 'CH', 'type' => 'airport'],
    
    // Autriche
    'vienne' => ['code' => 'VIE', 'name' => 'Vienna', 'country' => 'AT', 'type' => 'airport'],
    'vienna' => ['code' => 'VIE', 'name' => 'Vienna', 'country' => 'AT', 'type' => 'airport'],
    
    // République Tchèque
    'prague' => ['code' => 'PRG', 'name' => 'Václav Havel', 'country' => 'CZ', 'type' => 'airport'],
    
    // Hongrie
    'budapest' => ['code' => 'BUD', 'name' => 'Budapest', 'country' => 'HU', 'type' => 'airport'],
    
    // Pologne
    'varsovie' => ['code' => 'WAW', 'name' => 'Warsaw Chopin', 'country' => 'PL', 'type' => 'airport'],
    'warsaw' => ['code' => 'WAW', 'name' => 'Warsaw Chopin', 'country' => 'PL', 'type' => 'airport'],
    'cracovie' => ['code' => 'KRK', 'name' => 'Krakow', 'country' => 'PL', 'type' => 'airport'],
    
    // Grèce
    'athènes' => ['code' => 'ATH', 'name' => 'Eleftherios Venizelos', 'country' => 'GR', 'type' => 'airport'],
    'athens' => ['code' => 'ATH', 'name' => 'Eleftherios Venizelos', 'country' => 'GR', 'type' => 'airport'],
    
    // Portugal
    'lisbonne' => ['code' => 'LIS', 'name' => 'Lisbon Portela', 'country' => 'PT', 'type' => 'airport'],
    'lisbon' => ['code' => 'LIS', 'name' => 'Lisbon Portela', 'country' => 'PT', 'type' => 'airport'],
    'porto' => ['code' => 'OPO', 'name' => 'Porto', 'country' => 'PT', 'type' => 'airport'],
    
    // Irlande
    'dublin' => ['code' => 'DUB', 'name' => 'Dublin', 'country' => 'IE', 'type' => 'airport'],
    
    // Danemark
    'copenhague' => ['code' => 'CPH', 'name' => 'Copenhagen', 'country' => 'DK', 'type' => 'airport'],
    'copenhagen' => ['code' => 'CPH', 'name' => 'Copenhagen', 'country' => 'DK', 'type' => 'airport'],
    
    // Suède
    'stockholm' => ['code' => 'ARN', 'name' => 'Arlanda', 'country' => 'SE', 'type' => 'airport'],
    
    // Norvège
    'oslo' => ['code' => 'OSL', 'name' => 'Gardermoen', 'country' => 'NO', 'type' => 'airport'],
    
    // Finlande
    'helsinki' => ['code' => 'HEL', 'name' => 'Helsinki-Vantaa', 'country' => 'FI', 'type' => 'airport'],
    
    // Russie
    'moscou' => ['code' => 'SVO', 'name' => 'Sheremetyevo', 'country' => 'RU', 'type' => 'airport'],
    'moscow' => ['code' => 'SVO', 'name' => 'Sheremetyevo', 'country' => 'RU', 'type' => 'airport'],
    'saint pétersbourg' => ['code' => 'LED', 'name' => 'Pulkovo', 'country' => 'RU', 'type' => 'airport'],
    
    // ==================== AMÉRIQUES ====================
    // États-Unis
    'new york' => ['code' => 'JFK', 'name' => 'John F. Kennedy', 'country' => 'US', 'type' => 'airport'],
    'los angeles' => ['code' => 'LAX', 'name' => 'Los Angeles', 'country' => 'US', 'type' => 'airport'],
    'chicago' => ['code' => 'ORD', 'name' => "O'Hare", 'country' => 'US', 'type' => 'airport'],
    'miami' => ['code' => 'MIA', 'name' => 'Miami', 'country' => 'US', 'type' => 'airport'],
    'las vegas' => ['code' => 'LAS', 'name' => 'McCarran', 'country' => 'US', 'type' => 'airport'],
    'san francisco' => ['code' => 'SFO', 'name' => 'San Francisco', 'country' => 'US', 'type' => 'airport'],
    'boston' => ['code' => 'BOS', 'name' => 'Logan', 'country' => 'US', 'type' => 'airport'],
    'washington' => ['code' => 'IAD', 'name' => 'Dulles', 'country' => 'US', 'type' => 'airport'],
    'seattle' => ['code' => 'SEA', 'name' => 'Tacoma', 'country' => 'US', 'type' => 'airport'],
    'orlando' => ['code' => 'MCO', 'name' => 'Orlando', 'country' => 'US', 'type' => 'airport'],
    
    // Canada
    'toronto' => ['code' => 'YYZ', 'name' => 'Pearson', 'country' => 'CA', 'type' => 'airport'],
    'montreal' => ['code' => 'YUL', 'name' => 'Trudeau', 'country' => 'CA', 'type' => 'airport'],
    'vancouver' => ['code' => 'YVR', 'name' => 'Vancouver', 'country' => 'CA', 'type' => 'airport'],
    'quebec' => ['code' => 'YQB', 'name' => 'Québec City', 'country' => 'CA', 'type' => 'airport'],
    
    // Mexique
    'mexico' => ['code' => 'MEX', 'name' => 'Benito Juárez', 'country' => 'MX', 'type' => 'airport'],
    'cancun' => ['code' => 'CUN', 'name' => 'Cancún', 'country' => 'MX', 'type' => 'airport'],
    
    // Brésil
    'sao paulo' => ['code' => 'GRU', 'name' => 'Guarulhos', 'country' => 'BR', 'type' => 'airport'],
    'rio de janeiro' => ['code' => 'GIG', 'name' => 'Galeão', 'country' => 'BR', 'type' => 'airport'],
    'brasilia' => ['code' => 'BSB', 'name' => 'Brasília', 'country' => 'BR', 'type' => 'airport'],
    
    // Argentine
    'buenos aires' => ['code' => 'EZE', 'name' => 'Ezeiza', 'country' => 'AR', 'type' => 'airport'],
    
    // Chili
    'santiago' => ['code' => 'SCL', 'name' => 'Comodoro Arturo Merino Benítez', 'country' => 'CL', 'type' => 'airport'],
    
    // Pérou
    'lima' => ['code' => 'LIM', 'name' => 'Jorge Chávez', 'country' => 'PE', 'type' => 'airport'],
    
    // Colombie
    'bogota' => ['code' => 'BOG', 'name' => 'El Dorado', 'country' => 'CO', 'type' => 'airport'],
    'medellin' => ['code' => 'MDE', 'name' => 'José María Córdova', 'country' => 'CO', 'type' => 'airport'],
    
    // Venezuela
    'caracas' => ['code' => 'CCS', 'name' => 'Simón Bolívar', 'country' => 'VE', 'type' => 'airport'],
    
    // ==================== ASIE ====================
    // Japon
    'tokyo' => ['code' => 'NRT', 'name' => 'Narita', 'country' => 'JP', 'type' => 'airport'],
    'osaka' => ['code' => 'KIX', 'name' => 'Kansai', 'country' => 'JP', 'type' => 'airport'],
    'kyoto' => ['code' => 'ITM', 'name' => 'Itami', 'country' => 'JP', 'type' => 'airport'],
    
    // Corée du Sud
    'seoul' => ['code' => 'ICN', 'name' => 'Incheon', 'country' => 'KR', 'type' => 'airport'],
    
    // Chine
    'pekin' => ['code' => 'PEK', 'name' => 'Capital', 'country' => 'CN', 'type' => 'airport'],
    'beijing' => ['code' => 'PEK', 'name' => 'Capital', 'country' => 'CN', 'type' => 'airport'],
    'shanghai' => ['code' => 'PVG', 'name' => 'Pudong', 'country' => 'CN', 'type' => 'airport'],
    
    // Hong Kong
    'hong kong' => ['code' => 'HKG', 'name' => 'Hong Kong', 'country' => 'HK', 'type' => 'airport'],
    
    // Thaïlande
    'bangkok' => ['code' => 'BKK', 'name' => 'Suvarnabhumi', 'country' => 'TH', 'type' => 'airport'],
    'phuket' => ['code' => 'HKT', 'name' => 'Phuket', 'country' => 'TH', 'type' => 'airport'],
    'chiang mai' => ['code' => 'CNX', 'name' => 'Chiang Mai', 'country' => 'TH', 'type' => 'airport'],
    
    // Singapour
    'singapour' => ['code' => 'SIN', 'name' => 'Changi', 'country' => 'SG', 'type' => 'airport'],
    'singapore' => ['code' => 'SIN', 'name' => 'Changi', 'country' => 'SG', 'type' => 'airport'],
    
    // Malaisie
    'kuala lumpur' => ['code' => 'KUL', 'name' => 'Kuala Lumpur', 'country' => 'MY', 'type' => 'airport'],
    'penang' => ['code' => 'PEN', 'name' => 'Penang', 'country' => 'MY', 'type' => 'airport'],
    
    // Indonésie
    'jakarta' => ['code' => 'CGK', 'name' => 'Soekarno-Hatta', 'country' => 'ID', 'type' => 'airport'],
    'bali' => ['code' => 'DPS', 'name' => 'Ngurah Rai', 'country' => 'ID', 'type' => 'airport'],
    
    // Philippines
    'manille' => ['code' => 'MNL', 'name' => 'Ninoy Aquino', 'country' => 'PH', 'type' => 'airport'],
    'manila' => ['code' => 'MNL', 'name' => 'Ninoy Aquino', 'country' => 'PH', 'type' => 'airport'],
    
    // Inde
    'mumbai' => ['code' => 'BOM', 'name' => 'Chhatrapati Shivaji', 'country' => 'IN', 'type' => 'airport'],
    'delhi' => ['code' => 'DEL', 'name' => 'Indira Gandhi', 'country' => 'IN', 'type' => 'airport'],
    'bangalore' => ['code' => 'BLR', 'name' => 'Kempegowda', 'country' => 'IN', 'type' => 'airport'],
    'chennai' => ['code' => 'MAA', 'name' => 'Chennai', 'country' => 'IN', 'type' => 'airport'],
    
    // Vietnam
    'ho chi minh' => ['code' => 'SGN', 'name' => 'Tan Son Nhat', 'country' => 'VN', 'type' => 'airport'],
    'hanoi' => ['code' => 'HAN', 'name' => 'Noi Bai', 'country' => 'VN', 'type' => 'airport'],
    
    // ==================== MOYEN-ORIENT ====================
    'dubai' => ['code' => 'DXB', 'name' => 'Dubai', 'country' => 'AE', 'type' => 'airport'],
    'dubaï' => ['code' => 'DXB', 'name' => 'Dubai', 'country' => 'AE', 'type' => 'airport'],
    'abou dabi' => ['code' => 'AUH', 'name' => 'Abu Dhabi', 'country' => 'AE', 'type' => 'airport'],
    'abu dhabi' => ['code' => 'AUH', 'name' => 'Abu Dhabi', 'country' => 'AE', 'type' => 'airport'],
    'doha' => ['code' => 'DOH', 'name' => 'Hamad', 'country' => 'QA', 'type' => 'airport'],
    'riyad' => ['code' => 'RUH', 'name' => 'King Khalid', 'country' => 'SA', 'type' => 'airport'],
    'jeddah' => ['code' => 'JED', 'name' => 'King Abdulaziz', 'country' => 'SA', 'type' => 'airport'],
    'kuwait' => ['code' => 'KWI', 'name' => 'Kuwait', 'country' => 'KW', 'type' => 'airport'],
    'manama' => ['code' => 'BAH', 'name' => 'Bahrain', 'country' => 'BH', 'type' => 'airport'],
    'mascate' => ['code' => 'MCT', 'name' => 'Muscat', 'country' => 'OM', 'type' => 'airport'],
    'amman' => ['code' => 'AMM', 'name' => 'Queen Alia', 'country' => 'JO', 'type' => 'airport'],
    'beyrouth' => ['code' => 'BEY', 'name' => 'Rafic Hariri', 'country' => 'LB', 'type' => 'airport'],
    'tel aviv' => ['code' => 'TLV', 'name' => 'Ben Gurion', 'country' => 'IL', 'type' => 'airport'],
    'istanbul' => ['code' => 'IST', 'name' => 'Istanbul', 'country' => 'TR', 'type' => 'airport'],
    
    // ==================== OCÉANIE ====================
    'sydney' => ['code' => 'SYD', 'name' => 'Sydney', 'country' => 'AU', 'type' => 'airport'],
    'melbourne' => ['code' => 'MEL', 'name' => 'Tullamarine', 'country' => 'AU', 'type' => 'airport'],
    'brisbane' => ['code' => 'BNE', 'name' => 'Brisbane', 'country' => 'AU', 'type' => 'airport'],
    'perth' => ['code' => 'PER', 'name' => 'Perth', 'country' => 'AU', 'type' => 'airport'],
    'auckland' => ['code' => 'AKL', 'name' => 'Auckland', 'country' => 'NZ', 'type' => 'airport'],
    'wellington' => ['code' => 'WLG', 'name' => 'Wellington', 'country' => 'NZ', 'type' => 'airport'],
    'christchurch' => ['code' => 'CHC', 'name' => 'Christchurch', 'country' => 'NZ', 'type' => 'airport'],
];

// Mapping français -> anglais pour la recherche avancée
$frenchToEnglishCity = [
    'londres' => 'london', 'new york' => 'new york', 'los angeles' => 'los angeles',
    'san francisco' => 'san francisco', 'las vegas' => 'las vegas', 'hong kong' => 'hong kong',
    'buenos aires' => 'buenos aires', 'rio de janeiro' => 'rio de janeiro',
    'saint pétersbourg' => 'st petersburg', 'moscou' => 'moscow', 'vienne' => 'vienna',
    'copenhague' => 'copenhagen', 'prague' => 'prague', 'budapest' => 'budapest',
    'varsovie' => 'warsaw', 'athènes' => 'athens', 'dubaï' => 'dubai', 'le caire' => 'cairo',
    'kinshasa' => 'kinshasa', 'lubumbashi' => 'lubumbashi', 'goma' => 'goma',
    'zanzibar' => 'zanzibar', 'marrakech' => 'marrakech', 'casablanca' => 'casablanca',
    'singapour' => 'singapore', 'kuala lumpur' => 'kuala lumpur', 'barcelone' => 'barcelona',
    'amsterdam' => 'amsterdam', 'bruxelles' => 'brussels', 'genève' => 'geneva',
    'lisbonne' => 'lisbon', 'pekin' => 'beijing', 'manille' => 'manila'
];

// Fonction pour obtenir le code IATA d'une ville
function getAirportCodeFromMapping($cityName) {
    global $cityAirportMapping, $frenchToEnglishCity;
    
    $cityLower = strtolower(trim($cityName));
    if (empty($cityLower)) return null;
    
    // Recherche exacte
    if (isset($cityAirportMapping[$cityLower])) {
        return $cityAirportMapping[$cityLower]['code'];
    }
    
    // Recherche via traduction française -> anglais
    if (isset($frenchToEnglishCity[$cityLower])) {
        $englishCity = $frenchToEnglishCity[$cityLower];
        if (isset($cityAirportMapping[$englishCity])) {
            return $cityAirportMapping[$englishCity]['code'];
        }
    }
    
    // Recherche partielle
    foreach ($cityAirportMapping as $key => $value) {
        if (strpos($cityLower, $key) !== false || strpos($key, $cityLower) !== false) {
            return $value['code'];
        }
    }
    
    return null;
}

// Fonction pour l'autocomplétion
function autocompleteFromMapping($query, $limit = 10) {
    global $cityAirportMapping, $frenchToEnglishCity;
    
    $query = trim($query);
    if (strlen($query) < 2) return [];
    
    $results = [];
    $queryLower = strtolower($query);
    $queryUpper = strtoupper($query);
    
    // 1. Recherche par code IATA (exact)
    foreach ($cityAirportMapping as $city => $info) {
        if ($info['code'] === $queryUpper) {
            $display = '<span class="main-name">' . $info['code'] . ' - ' . $info['name'] . '</span>';
            $display .= ' <span class="city-name" data-city="' . ucfirst($city) . '">(' . ucfirst($city) . ')</span>';
            
            $results[] = [
                'type' => 'airport',
                'code' => $info['code'],
                'name' => $info['name'],
                'city' => ucfirst($city),
                'country' => $info['country'],
                'display' => $display,
                'value' => $info['code']
            ];
            break;
        }
    }
    
    // 2. Recherche par nom de ville
    $searchCities = [$queryLower];
    if (isset($frenchToEnglishCity[$queryLower])) {
        $searchCities[] = $frenchToEnglishCity[$queryLower];
    }
    
    foreach ($searchCities as $searchCity) {
        foreach ($cityAirportMapping as $city => $info) {
            if (strpos($city, $searchCity) !== false) {
                $display = '<span class="main-name">' . $info['code'] . ' - ' . $info['name'] . '</span>';
                $display .= ' <span class="city-name" data-city="' . ucfirst($city) . '">(' . ucfirst($city) . ')</span>';
                
                $results[] = [
                    'type' => 'airport',
                    'code' => $info['code'],
                    'name' => $info['name'],
                    'city' => ucfirst($city),
                    'country' => $info['country'],
                    'display' => $display,
                    'value' => $info['code']
                ];
            }
        }
    }
    
    // Supprimer les doublons
    $uniqueResults = [];
    $seenCodes = [];
    foreach ($results as $result) {
        if (!in_array($result['code'], $seenCodes)) {
            $seenCodes[] = $result['code'];
            $uniqueResults[] = $result;
        }
    }
    
    // Si pas de résultats, proposer la ville comme suggestion
    if (empty($uniqueResults)) {
        $cityName = ucfirst($query);
        $display = '<span class="main-name">' . $cityName . '</span> <span class="city-name" data-city="' . $cityName . '">(Ville)</span>';
        
        $uniqueResults[] = [
            'type' => 'city',
            'name' => $cityName,
            'city' => $cityName,
            'display' => $display,
            'value' => $cityName
        ];
    }
    
    return array_slice($uniqueResults, 0, $limit);
}
?>
