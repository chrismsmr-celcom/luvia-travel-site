<?php
// ==================== CONFIGURATION SUPABASE ====================
$supabase_url = 'https://ukbekfcjfcjcqrpxfpmq.supabase.co';
$supabase_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InVrYmVrZmNqZmNqY3FycHhmcG1xIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzQzNDk2NzcsImV4cCI6MjA4OTkyNTY3N30.KK3nxQOLTi3IZjYoRtrNC6mS_ixSsrZMI3J4WfxJVYU';

function supabaseRequest($endpoint, $method = 'GET', $data = null) {
    global $supabase_url, $supabase_key;
    $url = $supabase_url . '/rest/v1/' . $endpoint;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . $supabase_key,
        'Authorization: Bearer ' . $supabase_key,
        'Content-Type: application/json'
    ]);
    
    if($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    
    if($error || $httpCode !== 200) {
        error_log("Supabase error: $error (HTTP $httpCode)");
        return [];
    }
    
    $decoded = json_decode($response, true);
    return $decoded ?: [];
}

// ==================== CONFIGURATION DUFFEL (VOLS) ====================
$duffel_api_key = 'duffel_live_cS33jf4jEgY1E4r0TSg4VB2yMplqFFu3mJLqSeg10QI';
$duffel_api_url = 'https://api.duffel.com';

function duffelRequest($endpoint, $method = 'GET', $data = null) {
    global $duffel_api_key, $duffel_api_url;
    $url = $duffel_api_url . $endpoint;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $duffel_api_key,
        'Content-Type: application/json',
        'Accept: application/json',
        'Duffel-Version: v2'
    ]);
    
    if($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    
    if($httpCode === 200 || $httpCode === 201) {
        return json_decode($response, true);
    }
    
    return ['error' => true, 'http_code' => $httpCode, 'response' => $response];
}

// ==================== SERVICE VOLS ====================
function getAirportCode($city) {
    $codes = [
        'kinshasa' => 'FIH', 'lubumbashi' => 'FBM', 'goma' => 'GOM',
        'kisangani' => 'FKI', 'mbuji-mayi' => 'MJM', 'bukavu' => 'BKY',
        'matadi' => 'MAT', 'bandundu' => 'FDU', 'kananga' => 'KGA',
        'paris' => 'CDG', 'nairobi' => 'NBO', 'addis ababa' => 'ADD',
        'johannesburg' => 'JNB', 'bruxelles' => 'BRU', 'dubai' => 'DXB'
    ];
    
    $cityLower = strtolower(trim($city));
    return $codes[$cityLower] ?? strtoupper(substr($cityLower, 0, 3));
}

function searchFlightsDuffel($origin, $destination, $departureDate, $returnDate = null, $adults = 1, $children = 0, $infants = 0) {
    $originCode = getAirportCode($origin);
    $destinationCode = getAirportCode($destination);
    
    if(!$originCode || !$destinationCode) {
        return [];
    }
    
    $passengers = [];
    for($i = 0; $i < $adults; $i++) $passengers[] = ['age' => 30];
    for($i = 0; $i < $children; $i++) $passengers[] = ['age' => 8];
    for($i = 0; $i < $infants; $i++) $passengers[] = ['age' => 1];
    
    $slices = [[
        'origin' => $originCode,
        'destination' => $destinationCode,
        'departure_date' => $departureDate
    ]];
    
    if($returnDate) {
        $slices[] = [
            'origin' => $destinationCode,
            'destination' => $originCode,
            'departure_date' => $returnDate
        ];
    }
    
    $requestData = [
        'data' => [
            'type' => 'offer_request',
            'slices' => $slices,
            'passengers' => $passengers
        ]
    ];
    
    $response = duffelRequest('/air/offer_requests', 'POST', $requestData);
    
    if(isset($response['error']) || !isset($response['data']['offers'])) {
        return [];
    }
    
    $offers = [];
    foreach($response['data']['offers'] as $offer) {
        if(!isset($offer['slices'][0]['segments'][0])) continue;
        
        $slice = $offer['slices'][0];
        $segment = $slice['segments'][0];
        
        $departure = new DateTime($segment['departing_at']);
        $arrival = new DateTime($segment['arriving_at']);
        $duration = $departure->diff($arrival);
        $durationMinutes = ($duration->days * 24 * 60) + ($duration->h * 60) + $duration->i;
        
        $offers[] = [
            'airline' => $segment['marketing_carrier']['name'],
            'logo' => $segment['marketing_carrier']['logo_symbol_url'] ?? '',
            'flight_number' => $segment['marketing_carrier']['iata_code'] . $segment['marketing_carrier_flight_number'],
            'origin' => $slice['origin']['iata_code'],
            'destination' => $slice['destination']['iata_code'],
            'departure_time' => $segment['departing_at'],
            'arrival_time' => $segment['arriving_at'],
            'price' => $offer['total_amount'],
            'currency' => $offer['total_currency'],
            'class' => $segment['passengers'][0]['cabin_class_marketing_name'] ?? 'Economy',
            'duration' => $durationMinutes,
            'stops' => count($segment['stops'] ?? [])
        ];
    }
    
    return $offers;
}

// ==================== CONFIGURATION LITEAPI ====================
$liteapi_private_key = getenv('LITEAPI_PRIVATE_KEY') ?: 'prod_f36d352f-3f98-49a0-9f51-504452c0bf22';
$liteapi_public_key = getenv('LITEAPI_PUBLIC_KEY') ?: 'prod_public_35390847-13b3-4d05-a0db-c520bd1c48b9';
$liteapi_search_base_url = 'https://api.liteapi.travel/v3.0';
$liteapi_booking_base_url = 'https://book.liteapi.travel/v3.0';

// ==================== CORE REQUEST UNIFIE ====================
function liteAPIRequest($url, $method = 'GET', $data = null) {
    global $liteapi_private_key;

    if (empty($liteapi_private_key)) {
        error_log("LiteAPI Error: API key manquante");
        return ['error' => true, 'message' => 'API key manquante'];
    }

    $ch = curl_init($url);
    $method = strtoupper($method);

    $headers = [
        'X-API-Key: ' . $liteapi_private_key,
        'Accept: application/json',
    ];

    if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data !== null) {
            $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        }
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 45,
        CURLOPT_CONNECTTIMEOUT => 20,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $curlErrno = curl_errno($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    error_log("LiteAPI Request: $method $url - HTTP $httpCode");

    if ($curlError) {
        error_log("LiteAPI CURL Error: $curlError (Errno: $curlErrno)");
        return ['error' => true, 'message' => $curlError, 'errno' => $curlErrno];
    }

    $decoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("LiteAPI JSON Error: " . json_last_error_msg());
        return [
            'error' => true,
            'http_code' => $httpCode,
            'message' => 'Reponse non JSON',
            'raw' => substr($response, 0, 1000)
        ];
    }

    if ($httpCode >= 200 && $httpCode < 300) {
        return $decoded;
    }

    $errorMessage = 'Erreur API inconnue';
    if (isset($decoded['error']['message'])) {
        $errorMessage = $decoded['error']['message'];
    } elseif (isset($decoded['message'])) {
        $errorMessage = $decoded['message'];
    } elseif (isset($decoded['error'])) {
        $errorMessage = is_string($decoded['error']) ? $decoded['error'] : json_encode($decoded['error']);
    }

    error_log("LiteAPI HTTP Error $httpCode: $errorMessage");
    
    return [
        'error' => true,
        'http_code' => $httpCode,
        'message' => $errorMessage,
        'raw' => $decoded
    ];
}
// ==================== SERVICE HOTELS ====================

function getCountryCodeFromCity($city) {
    $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($city) . "&format=json&limit=1&addressdetails=1";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_USERAGENT, 'LuviaTravel/1.0');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        if (isset($data[0]['address']['country_code'])) {
            return strtoupper($data[0]['address']['country_code']);
        }
    }
    return 'FR';
}

// RECHERCHE PRINCIPALE - Version optimisée avec limite dynamique
function searchHotelsLiteAPI($city, $checkIn, $checkOut, $adults = 1, $childrenAges = [], $guestNationality = null, $currency = 'USD') {
    global $liteapi_search_base_url;

    $city = trim((string) $city);
    if ($city === '') return [];
    if (empty($checkIn) || empty($checkOut)) return [];

    $countryCode = getCountryCodeFromCity($city);
    if ($guestNationality === null) $guestNationality = $countryCode;

    $checkInFormatted = date('Y-m-d', strtotime($checkIn));
    $checkOutFormatted = date('Y-m-d', strtotime($checkOut));
    $nights = max(1, (int)((strtotime($checkOutFormatted) - strtotime($checkInFormatted)) / 86400));

    $occupancy = ['adults' => max(1, (int)$adults)];
    if (!empty($childrenAges) && is_array($childrenAges)) {
        // Garder les âges y compris 0 (bébés)
        $filteredAges = [];
        foreach ($childrenAges as $age) {
            $ageInt = intval($age);
            if ($ageInt >= 0 && $ageInt <= 17) {
                $filteredAges[] = $ageInt;
            }
        }
        if (!empty($filteredAges)) {
            $occupancy['children'] = $filteredAges;
        }
    }

    // ÉTAPE 1: Récupérer les hôtels depuis Data API - LIMITE 500 (performance)
    $hotelsData = getHotelsFromDataAPI($countryCode, $city, 500);
    if (empty($hotelsData)) return [];
    
    // ÉTAPE 2: Récupérer les prix et disponibilités (par lots pour éviter timeout)
    $hotelIds = array_column($hotelsData, 'id');
    $allRatesData = [];
    
    // Traiter par lots de 100 pour éviter les timeouts
    $chunks = array_chunk($hotelIds, 100);
    foreach ($chunks as $chunk) {
        $ratesData = getRatesForHotels($chunk, $checkInFormatted, $checkOutFormatted, $occupancy, $guestNationality, $currency);
        $allRatesData = array_merge($allRatesData, $ratesData);
        if (count($chunks) > 1) {
            usleep(100000); // Pause 0.1s entre les lots
        }
    }
    
    if (empty($allRatesData)) return [];

    // ÉTAPE 3: Fusionner les données
    $results = [];
    foreach ($hotelsData as $hotel) {
        $hotelId = $hotel['id'];
        if (!isset($allRatesData[$hotelId])) continue;
        
        $rate = $allRatesData[$hotelId];
        
        $results[] = [
            'id' => $hotelId,
            'name' => $hotel['name'],
            'address' => $hotel['address'],
            'city' => $city,
            'price_usd' => $rate['price_per_night'],
            'price_total_usd' => $rate['total_price'],
            'nights' => $nights,
            'rating' => $hotel['rating'],
            'hotel_stars' => (float)$hotel['stars'],
            'image' => $hotel['image'],
            'offer_id' => $rate['offer_id'],
            'room_name' => $rate['room_name'],
            'country_code' => $countryCode,
            'board_type' => $rate['board_type'],
            'board_name' => $rate['board_name'],
            'breakfast_included' => $rate['breakfast_included'],
            'perks' => $rate['perks'],
            'all_rates' => $rate['all_rates']
        ];
    }

    usort($results, function($a, $b) {
        return $a['price_usd'] <=> $b['price_usd'];
    });

    error_log("searchHotelsLiteAPI: " . count($results) . " hotels found for $city");
    return $results;
}

function getHotelsFromDataAPI($countryCode, $cityName, $limit = 500) {
    global $liteapi_search_base_url;
    
    $allHotels = [];
    $offset = 0;
    $pageSize = min($limit, 200); // 200 par page max
    
    while ($offset < $limit) {
        $currentLimit = min($pageSize, $limit - $offset);
        
        $params = [
            'countryCode' => strtoupper(trim($countryCode)),
            'cityName' => trim($cityName),
            'limit' => $currentLimit,
            'offset' => $offset,
            'language' => 'fr',
        ];

        $url = rtrim($liteapi_search_base_url, '/') . '/data/hotels?' . http_build_query($params);
        
        $response = liteAPIRequest($url, 'GET');
        
        if (!empty($response['error'])) break;
        
        $rows = $response['data'] ?? [];
        if (!is_array($rows) || empty($rows)) break;
        
        foreach ($rows as $hotel) {
            $hotelId = $hotel['hotelId'] ?? ($hotel['id'] ?? null);
            if (!$hotelId) continue;
            
            // Éviter les doublons
            if (isset($allHotels[$hotelId])) continue;
            
            $stars = 0;
            if (isset($hotel['starRating'])) {
                $stars = (float)$hotel['starRating'];
            } elseif (isset($hotel['stars'])) {
                $stars = (float)$hotel['stars'];
            }

            $allHotels[$hotelId] = [
                'id' => $hotelId,
                'name' => $hotel['name'] ?? '',
                'address' => $hotel['address'] ?? '',
                'stars' => $stars,
                'rating' => $hotel['rating'] ?? 0,
                'image' => $hotel['mainPhoto'] ?? ($hotel['main_photo'] ?? $hotel['thumbnail'] ?? ''),
                'city' => $hotel['cityName'] ?? ($hotel['city'] ?? $cityName)
            ];
        }
        
        $offset += $currentLimit;
        
        // Si on a moins de résultats que demandé, c'est fini
        if (count($rows) < $currentLimit) break;
        
        // Pause pour ne pas surcharger l'API
        if ($offset < $limit) {
            usleep(100000); // 0.1 seconde
        }
    }
    
    $results = array_values($allHotels);
    error_log("getHotelsFromDataAPI: " . count($results) . " hotels found for $cityName");
    return $results;
}

function getRatesForHotels($hotelIds, $checkIn, $checkOut, $occupancy, $guestNationality, $currency = 'USD') {
    global $liteapi_search_base_url;
    
    if (empty($hotelIds)) return [];
    
    $payload = [
        'hotelIds' => $hotelIds,
        'checkin' => $checkIn,
        'checkout' => $checkOut,
        'currency' => strtoupper($currency),
        'guestNationality' => strtoupper($guestNationality),
        'occupancies' => [$occupancy],
        'maxRatesPerHotel' => 20
        // includeHotelData retiré pour performance (déjà récupéré via Data API)
    ];

    $url = rtrim($liteapi_search_base_url, '/') . '/hotels/rates';
    
    $response = liteAPIRequest($url, 'POST', $payload);
    
    if (!empty($response['error'])) {
        error_log("Rates API Error: " . json_encode($response));
        return [];
    }
    
    $items = $response['data'] ?? [];
    if (!is_array($items) || empty($items)) {
        error_log("No data in rates response");
        return [];
    }

    $nights = (strtotime($checkOut) - strtotime($checkIn)) / 86400;
    $nights = max(1, (int)$nights);
    
    $rates = [];
    foreach ($items as $item) {
        $hotelId = $item['hotelId'] ?? null;
        if (!$hotelId) continue;

        $allRates = [];
        
        foreach (($item['roomTypes'] ?? []) as $roomType) {
            // Récupération des images de la chambre
            $roomImages = [];
            
            if (isset($roomType['images']) && is_array($roomType['images'])) {
                foreach ($roomType['images'] as $img) {
                    $url = $img['url'] ?? $img['main_photo'] ?? $img['thumbnail'] ?? '';
                    if (!empty($url)) $roomImages[] = $url;
                }
            }
            
            if (isset($roomType['photos']) && is_array($roomType['photos'])) {
                foreach ($roomType['photos'] as $img) {
                    $url = $img['url'] ?? $img['main_photo'] ?? $img['thumbnail'] ?? '';
                    if (!empty($url)) $roomImages[] = $url;
                }
            }
            
            foreach (($roomType['rates'] ?? []) as $rate) {
                $totalAmount = null;
                if (isset($rate['retailRate']['total'][0]['amount'])) {
                    $totalAmount = (float)$rate['retailRate']['total'][0]['amount'];
                } elseif (isset($rate['retailRate']['total']['amount'])) {
                    $totalAmount = (float)$rate['retailRate']['total']['amount'];
                }
                
                if ($totalAmount && $totalAmount > 0) {
                    $boardType = $rate['boardType'] ?? null;
                    $boardName = $rate['boardName'] ?? null;
                    $perks = $rate['perks'] ?? [];
                    $roomName = $rate['name'] ?? 'Chambre';
                    
                    $breakfastIncluded = false;
                    if (in_array($boardType, ['BB', 'BI', 'HB', 'FB'], true)) {
                        $breakfastIncluded = true;
                    } elseif (is_string($boardName) && stripos($boardName, 'breakfast') !== false) {
                        $breakfastIncluded = true;
                    }
                    
                    $isRefundable = false;
                    $refundableTag = $rate['cancellationPolicies']['refundableTag'] ?? '';
                    $isRefundable = $refundableTag === 'RFN';
                    
                    $maxOccupancy = $rate['maxOccupancy'] ?? 2;
                    
                    $bedType = 'Lit standard';
                    $roomNameLower = strtolower($roomName);
                    if (strpos($roomNameLower, 'king') !== false) $bedType = 'Lit King Size';
                    elseif (strpos($roomNameLower, 'queen') !== false) $bedType = 'Lit Queen Size';
                    elseif (strpos($roomNameLower, 'double') !== false) $bedType = 'Lit Double';
                    elseif (strpos($roomNameLower, 'single') !== false) $bedType = 'Lit Simple';
                    elseif (strpos($roomNameLower, 'twin') !== false) $bedType = 'Lits Jumeaux';
                    
                    $roomData = [
                        'price' => $totalAmount,
                        'price_per_night' => round($totalAmount / $nights, 2),
                        'offer_id' => $rate['offerId'] ?? null,
                        'room_name' => $roomName,
                        'board_type' => $boardType,
                        'board_name' => $boardName,
                        'breakfast_included' => $breakfastIncluded,
                        'perks' => $perks,
                        'max_occupancy' => $maxOccupancy,
                        'refundable' => $isRefundable,
                        'bed_type' => $bedType,
                        'description' => $rate['description'] ?? $roomType['description'] ?? '',
                        'images' => $roomImages
                    ];
                    
                    $allRates[] = $roomData;
                }
            }
        }
        
        usort($allRates, function($a, $b) {
            return $a['price'] <=> $b['price'];
        });

        if (!empty($allRates)) {
            $bestRate = $allRates[0];
            
            $rates[$hotelId] = [
                'price_per_night' => $bestRate['price_per_night'],
                'total_price' => $bestRate['price'],
                'nights' => $nights,
                'offer_id' => $bestRate['offer_id'],
                'room_name' => $bestRate['room_name'],
                'board_type' => $bestRate['board_type'],
                'board_name' => $bestRate['board_name'],
                'breakfast_included' => $bestRate['breakfast_included'],
                'perks' => $bestRate['perks'],
                'all_rates' => $allRates
            ];
        }
    }
    
    error_log("getRatesForHotels: Rates found for " . count($rates) . " hotels");
    return $rates;
}

// ==================== VERSION OPTIMISÉE POUR LISTE (MIN RATES) ====================
function getMinRatesForHotels($hotelIds, $checkIn, $checkOut, $occupancy, $guestNationality, $currency = 'USD') {
    global $liteapi_search_base_url;
    
    if (empty($hotelIds)) return [];
    
    // Limiter à 200 pour la recherche de prix minimum (plus rapide)
    $hotelIds = array_slice($hotelIds, 0, 200);
    
    $payload = [
        'hotelIds' => $hotelIds,
        'checkin' => $checkIn,
        'checkout' => $checkOut,
        'currency' => strtoupper($currency),
        'guestNationality' => strtoupper($guestNationality),
        'occupancies' => [$occupancy]
    ];

    $url = rtrim($liteapi_search_base_url, '/') . '/hotels/min-rates';
    
    $response = liteAPIRequest($url, 'POST', $payload);
    
    if (!empty($response['error'])) return [];
    
    $items = $response['data'] ?? [];
    if (!is_array($items) || empty($items)) return [];

    $nights = (strtotime($checkOut) - strtotime($checkIn)) / 86400;
    $nights = max(1, (int)$nights);
    
    $rates = [];
    foreach ($items as $item) {
        $hotelId = $item['hotelId'] ?? null;
        if (!$hotelId) continue;
        
        $minPrice = null;
        $minOfferId = null;
        $minRoomName = null;
        
        foreach (($item['roomTypes'] ?? []) as $roomType) {
            foreach (($roomType['rates'] ?? []) as $rate) {
                // Structure différente pour min-rates
                $amount = $rate['rate']['amount'] ?? $rate['total']['amount'] ?? null;
                if ($amount && ($minPrice === null || $amount < $minPrice)) {
                    $minPrice = (float)$amount;
                    $minOfferId = $rate['offerId'] ?? null;
                    $minRoomName = $rate['name'] ?? $roomType['name'] ?? 'Chambre';
                }
            }
        }
        
        if ($minPrice !== null) {
            $rates[$hotelId] = [
                'price_per_night' => round($minPrice / $nights, 2),
                'total_price' => $minPrice,
                'nights' => $nights,
                'offer_id' => $minOfferId,
                'room_name' => $minRoomName
            ];
        }
    }
    
    error_log("getMinRatesForHotels: " . count($rates) . " hotels with min rates");
    return $rates;
}

// ==================== RÉCUPÉRATION DES IMAGES DE L'HÔTEL ====================
function getHotelImages($hotelId) {
    global $liteapi_search_base_url;
    
    if (empty($hotelId)) return [];
    
    $url = rtrim($liteapi_search_base_url, '/') . "/data/hotel?hotelId=" . urlencode($hotelId) . "&language=fr";
    
    $response = liteAPIRequest($url, 'GET');
    
    if (!empty($response['error'])) {
        error_log("Hotel Images API Error for $hotelId: " . ($response['message'] ?? 'Unknown'));
        return [];
    }
    
    $hotelData = $response['data'] ?? [];
    if (empty($hotelData)) return [];
    
    $images = [
        'main_photo' => '',
        'hotel_photos' => [],
        'room_photos' => [],
        'all_photos' => []
    ];
    
    // Récupérer la photo principale
    if (isset($hotelData['mainPhoto'])) {
        $images['main_photo'] = $hotelData['mainPhoto'];
    } elseif (isset($hotelData['main_photo'])) {
        $images['main_photo'] = $hotelData['main_photo'];
    } elseif (isset($hotelData['thumbnail'])) {
        $images['main_photo'] = $hotelData['thumbnail'];
    }
    
    // Récupérer toutes les photos
    $photos = [];
    
    if (isset($hotelData['photos']) && is_array($hotelData['photos'])) {
        $photos = $hotelData['photos'];
    } elseif (isset($hotelData['images']) && is_array($hotelData['images'])) {
        $photos = $hotelData['images'];
    } elseif (isset($hotelData['media']) && is_array($hotelData['media'])) {
        $photos = $hotelData['media'];
    } elseif (isset($hotelData['gallery']) && is_array($hotelData['gallery'])) {
        $photos = $hotelData['gallery'];
    }
    
    foreach ($photos as $photo) {
        $url = '';
        if (is_string($photo)) {
            $url = $photo;
        } elseif (isset($photo['url'])) {
            $url = $photo['url'];
        } elseif (isset($photo['src'])) {
            $url = $photo['src'];
        } elseif (isset($photo['mainPhoto'])) {
            $url = $photo['mainPhoto'];
        } elseif (isset($photo['main_photo'])) {
            $url = $photo['main_photo'];
        } elseif (isset($photo['thumbnail'])) {
            $url = $photo['thumbnail'];
        } elseif (isset($photo['path'])) {
            $url = $photo['path'];
        }
        
        if (!empty($url)) {
            $images['all_photos'][] = $url;
            
            $category = '';
            if (isset($photo['category'])) {
                $category = strtolower($photo['category']);
            } elseif (isset($photo['type'])) {
                $category = strtolower($photo['type']);
            } elseif (isset($photo['tags']) && is_array($photo['tags'])) {
                $category = strtolower(implode(' ', $photo['tags']));
            }
            
            if (strpos($category, 'room') !== false || 
                strpos($category, 'chambre') !== false ||
                strpos($category, 'bed') !== false ||
                strpos($category, 'lit') !== false) {
                $images['room_photos'][] = $url;
            } else {
                $images['hotel_photos'][] = $url;
            }
        }
    }
    
    if (empty($images['room_photos']) && !empty($images['all_photos'])) {
        $images['room_photos'] = $images['all_photos'];
    }
    
    if (empty($images['all_photos'])) {
        $images['all_photos'] = ['https://picsum.photos/800/500?random=' . $hotelId];
        $images['room_photos'] = $images['all_photos'];
    }
    
    return $images;
}

// ==================== RÉCUPÉRATION DES DÉTAILS COMPLETS DE L'HÔTEL ====================
function getHotelDetails($hotelId) {
    global $liteapi_search_base_url;
    
    if (empty($hotelId)) return [];
    
    $url = rtrim($liteapi_search_base_url, '/') . "/data/hotel?hotelId=" . urlencode($hotelId) . "&language=fr";
    
    $response = liteAPIRequest($url, 'GET');
    
    if (!empty($response['error'])) {
        return [];
    }
    
    $hotelData = $response['data'] ?? [];
    
    $details = [
        'id' => $hotelId,
        'name' => $hotelData['name'] ?? '',
        'description' => $hotelData['description'] ?? '',
        'address' => $hotelData['address'] ?? '',
        'city' => $hotelData['cityName'] ?? '',
        'country' => $hotelData['countryName'] ?? '',
        'starRating' => (float)($hotelData['starRating'] ?? 0),
        'latitude' => $hotelData['latitude'] ?? null,
        'longitude' => $hotelData['longitude'] ?? null,
        'checkin' => $hotelData['checkinTime'] ?? '14:00',
        'checkout' => $hotelData['checkoutTime'] ?? '12:00',
        'facilities' => $hotelData['facilities'] ?? [],
        'images' => getHotelImages($hotelId)
    ];
    
    return $details;
}

// ==================== PREBOOK & BOOK ====================
function prebookRate($offerId, $usePaymentSdk = false) {
    global $liteapi_booking_base_url;
    $url = rtrim($liteapi_booking_base_url, '/') . '/rates/prebook';
    return liteAPIRequest($url, 'POST', [
        'offerId' => $offerId,
        'usePaymentSdk' => (bool)$usePaymentSdk,
    ]);
}

function bookRate($prebookId, array $holder, array $guests, $paymentMethod = 'ACC_CREDIT_CARD') {
    global $liteapi_booking_base_url;
    $url = rtrim($liteapi_booking_base_url, '/') . '/rates/book';

    $payload = [
        'prebookId' => $prebookId,
        'holder' => [
            'firstName' => $holder['firstName'] ?? '',
            'lastName' => $holder['lastName'] ?? '',
            'email' => $holder['email'] ?? '',
            'phone' => $holder['phone'] ?? '',
        ],
        'guests' => $guests,
        'payment' => ['method' => $paymentMethod],
    ];

    return liteAPIRequest($url, 'POST', $payload);
}
// ==================== SERVICE VOITURES ====================
function searchCars($city, $rental_type, $car_type, $transmission) {
    $cars = [
        ['company' => 'Avis', 'city' => 'Kinshasa', 'model' => 'Toyota Land Cruiser', 'price_per_day' => 150000, 'seats' => 7, 'transmission' => 'automatic'],
        ['company' => 'Hertz', 'city' => 'Kinshasa', 'model' => 'Kia Sportage', 'price_per_day' => 85000, 'seats' => 5, 'transmission' => 'automatic'],
        ['company' => 'Europcar', 'city' => 'Lubumbashi', 'model' => 'Toyota Hilux', 'price_per_day' => 100000, 'seats' => 5, 'transmission' => 'manual']
    ];
    
    $results = array_values(array_filter($cars, function($c) use ($city) {
        return stripos($c['city'], $city) !== false;
    }));
    
    foreach($results as &$car) {
        if($rental_type == 'chauffeur_only') $car['price_per_day'] += 50000;
        if($rental_type == 'chauffeur_fuel') $car['price_per_day'] += 75000;
        $car['transmission'] = $transmission;
    }
    
    return $results;
}

// ==================== SERVICE ASSURANCES ====================
function getAssurancePrice($zone, $duration, $age) {
    $zoneMap = [
        'africa' => 'AFRICA_ASIA', 'asia' => 'AFRICA_ASIA', 'africa_asia' => 'AFRICA_ASIA',
        'shengen' => 'SCHENGEN', 'schengen' => 'SCHENGEN', 'europe' => 'SCHENGEN',
        'worldwide' => 'WORLD_WIDE', 'world_wide' => 'WORLD_WIDE'
    ];
    
    $dbZone = $zoneMap[strtolower($zone)] ?? $zone;
    
    $endpoint = "assurances?zone=eq.{$dbZone}&duree_min=lte.{$duration}&duree_max=gte.{$duration}&select=prix_enfant_0_12,prix_adulte_13_75";
    $data = supabaseRequest($endpoint, 'GET');
    
    if (empty($data) || !is_array($data)) {
        error_log("No assurance price found for zone: $dbZone, duration: $duration");
        return 0;
    }
    
    $priceField = ($age <= 12) ? 'prix_enfant_0_12' : 'prix_adulte_13_75';
    return $data[0][$priceField] ?? 0;
}

// ==================== SERVICE DESTINATIONS ====================
function getDestinations() {
    $data = supabaseRequest('destinations?select=*&order=id.asc');
    if(empty($data) || !is_array($data)) return [];
    
    $formatted = [];
    foreach($data as $item) {
        $formatted[] = [
            'id' => $item['id'],
            'name' => $item['nom'] ?? '',
            'image' => $item['image'] ?? '',
            'description' => $item['description'] ?? '',
            'type' => $item['type'] ?? 'hotel',
            'city' => $item['ville'] ?? '',
            'destination' => $item['destination'] ?? ''
        ];
    }
    return $formatted;
}

// ==================== SERVICE PACKAGES ====================
function getPackages() {
    $data = supabaseRequest('packages?select=*&order=created_at.asc');
    if(empty($data) || !is_array($data)) return [];
    
    $formatted = [];
    foreach($data as $item) {
        $inclus = $item['inclus'] ?? [];
        if(is_string($inclus)) {
            $inclus = json_decode($inclus, true);
        }
        if(!is_array($inclus)) $inclus = [];
        
        $hotelImagesChambre = $item['hotel_images_chambre'] ?? [];
        if(is_string($hotelImagesChambre)) {
            $hotelImagesChambre = json_decode($hotelImagesChambre, true);
        }
        if(!is_array($hotelImagesChambre)) $hotelImagesChambre = [];
        
        $hotelImagesTerrasse = $item['hotel_images_terrasse'] ?? [];
        if(is_string($hotelImagesTerrasse)) {
            $hotelImagesTerrasse = json_decode($hotelImagesTerrasse, true);
        }
        if(!is_array($hotelImagesTerrasse)) $hotelImagesTerrasse = [];
        
        $formatted[] = [
            'id' => $item['id'] ?? 'PKG-' . strtoupper(substr(uniqid(), -8)),
            'name' => $item['nom'] ?? 'Package sans nom',
            'destination' => $item['destination'] ?? '',
            'duration_nights' => intval($item['duree_nuits'] ?? 0),
            'description' => $item['description'] ?? '',
            'price' => floatval($item['prix_total'] ?? 0),
            'includes' => $inclus,
            'image' => $item['image_principale'] ?? '',
            'airline' => $item['compagnie_aerienne'] ?? '',
            'flight_price' => floatval($item['prix_vol'] ?? 0),
            'flight_class' => $item['classe_vol'] ?? '',
            'visa_price' => floatval($item['prix_visa'] ?? 0),
            'visa_required' => $item['visa_requis'] ?? true,
            'hotel_name' => $item['hotel_nom'] ?? '',
            'hotel_address' => $item['hotel_adresse'] ?? '',
            'hotel_stars' => intval($item['hotel_etoiles'] ?? 0),
            'hotel_view' => $item['hotel_vue'] ?? '',
            'room_type' => $item['type_chambre'] ?? '',
            'hotel_price' => floatval($item['prix_hotel'] ?? 0),
            'hotel_image' => $item['hotel_image_exterieur'] ?? '',
            'hotel_room_images' => $hotelImagesChambre,
            'hotel_terrace_images' => $hotelImagesTerrasse,
            'chambre_capacite' => intval($item['chambre_capacite'] ?? 2),
            'price_per_night' => floatval($item['prix_par_nuit'] ?? 0),
            'duree_jours' => intval($item['duree_jours'] ?? $item['duree_nuits'] ?? 0),
            'transfer_type' => $item['transfert_type'] ?? '',
            'transfer_price' => floatval($item['transfert_prix'] ?? 0),
            'transfer_details' => $item['transfert_details'] ?? '',
            'activity1_name' => $item['activite1_nom'] ?? '',
            'activity1_price' => floatval($item['activite1_prix'] ?? 0),
            'activity1_description' => $item['activite1_description'] ?? '',
            'activity2_name' => $item['activite2_nom'] ?? '',
            'activity2_price' => floatval($item['activite2_prix'] ?? 0),
            'activity2_description' => $item['activite2_description'] ?? '',
            'activity3_name' => $item['activite3_nom'] ?? '',
            'activity3_price' => floatval($item['activite3_prix'] ?? 0),
            'activity3_description' => $item['activite3_description'] ?? ''
        ];
    }
    return $formatted;
}

// ==================== SERVICE TAUX DE CHANGE ====================
function getExchangeRates() {
    return [
        'USD' => ['symbol' => '$', 'rate' => 1, 'name' => 'Dollar US'],
        'EUR' => ['symbol' => '€', 'rate' => 0.92, 'name' => 'Euro'],
        'FC' => ['symbol' => 'FC', 'rate' => 2900, 'name' => 'Franc Congolais'],
        'XAF' => ['symbol' => 'FCFA', 'rate' => 600, 'name' => 'Franc CFA'],
        'BTC' => ['symbol' => '₿', 'rate' => 0.000015, 'name' => 'Bitcoin']
    ];
}

function convertPrice($priceInUSD, $targetCurrency) {
    if($targetCurrency === 'USD') return $priceInUSD;
    $rates = getExchangeRates();
    $targetRate = $rates[$targetCurrency]['rate'] ?? 1;
    
    if($targetCurrency === 'FC' || $targetCurrency === 'XAF') return round($priceInUSD * $targetRate, 0);
    if($targetCurrency === 'BTC') return round($priceInUSD * $targetRate, 8);
    return round($priceInUSD * $targetRate, 2);
}

function formatPrice($price, $currency) {
    $rates = getExchangeRates();
    $symbol = $rates[$currency]['symbol'] ?? 'USD';
    $convertedPrice = convertPrice($price, $currency);
    
    if($currency === 'BTC') return $symbol . ' ' . number_format($convertedPrice, 8);
    if($currency === 'FC' || $currency === 'XAF') return number_format($convertedPrice, 0) . ' ' . $symbol;
    return $symbol . ' ' . number_format($convertedPrice, 2);
}

// ==================== UTILITAIRES ====================
function testLiteAPIConnection() {
    global $liteapi_search_base_url;
    
    $testPayload = [
        'checkin' => date('Y-m-d', strtotime('+30 days')),
        'checkout' => date('Y-m-d', strtotime('+32 days')),
        'cityName' => 'Kinshasa',
        'currency' => 'USD',
        'guestNationality' => 'CD',
        'occupancies' => [['adults' => 1]]
    ];
    
    $url = rtrim($liteapi_search_base_url, '/') . '/hotels/rates';
    $response = liteAPIRequest($url, 'POST', $testPayload);
    
    if (isset($response['error'])) {
        return ['success' => false, 'message' => $response['message']];
    }
    
    return ['success' => true, 'message' => 'Connexion OK'];
}

// ==================== CONNEXION PDO ====================
try {
    $pdo = new PDO("mysql:host=localhost;dbname=terra_voyage;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("MySQL connection error: " . $e->getMessage());
}
?>
