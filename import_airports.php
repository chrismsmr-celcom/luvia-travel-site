<?php
// ==================== IMPORT AEROPORTS VIA PHP ====================

$supabase_url = 'https://ukbekfcjfcjcqrpxfpmq.supabase.co';
$supabase_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InVrYmVrZmNqZmNqY3FycHhmcG1xIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzQzNDk2NzcsImV4cCI6MjA4OTkyNTY3N30.KK3nxQOLTi3IZjYoRtrNC6mS_ixSsrZMI3J4WfxJVYU';

function supabaseRequestPHP($endpoint, $method = 'GET', $data = null) {
    global $supabase_url, $supabase_key;
    $url = $supabase_url . '/rest/v1/' . $endpoint;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . $supabase_key,
        'Authorization: Bearer ' . $supabase_key,
        'Content-Type: application/json'
    ]);
    
    if($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ['code' => $httpCode, 'response' => json_decode($response, true)];
}

// Télécharger les fichiers CSV
echo "Téléchargement des fichiers CSV...\n";
file_put_contents('airports.csv', file_get_contents('https://davidmegginson.github.io/ourairports-data/airports.csv'));
file_put_contents('countries.csv', file_get_contents('https://davidmegginson.github.io/ourairports-data/countries.csv'));
file_put_contents('regions.csv', file_get_contents('https://davidmegginson.github.io/ourairports-data/regions.csv'));

echo "Importation des aéroports...\n";
$airports = array_map('str_getcsv', file('airports.csv'));
$header = array_shift($airports);
$batch = [];
$count = 0;

foreach($airports as $row) {
    if(count($row) >= 19 && !empty($row[13])) { // Garder seulement ceux avec code IATA
        $batch[] = [
            'airport_id' => (int)$row[0],
            'ident' => $row[1],
            'type' => $row[2],
            'name' => $row[3],
            'latitude_deg' => (float)$row[4],
            'longitude_deg' => (float)$row[5],
            'elevation_ft' => (int)$row[6],
            'continent' => $row[7],
            'iso_country' => $row[8],
            'iso_region' => $row[9],
            'municipality' => $row[10],
            'scheduled_service' => $row[11],
            'icao_code' => $row[12],
            'iata_code' => $row[13],
            'gps_code' => $row[14],
            'local_code' => $row[15],
            'home_link' => $row[16],
            'wikipedia_link' => $row[17],
            'keywords' => $row[18]
        ];
        $count++;
        
        // Insertion par lots de 100
        if(count($batch) >= 100) {
            $result = supabaseRequestPHP('airports', 'POST', $batch);
            if($result['code'] >= 400) {
                echo "Erreur: " . print_r($result, true) . "\n";
            }
            $batch = [];
            echo "Importé $count aéroports...\n";
        }
    }
}

// Dernier lot
if(!empty($batch)) {
    supabaseRequestPHP('airports', 'POST', $batch);
}

echo "✅ $count aéroports importés !\n";
echo "Terminé !\n";