#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import psycopg2
import csv
import os
from psycopg2.extras import execute_values

# Configuration Supabase (à remplacer par tes identifiants)
SUPABASE_HOST = "aws-0-eu-west-3.pooler.supabase.com"
SUPABASE_DB = "postgres"
SUPABASE_USER = "postgres.ukbekfcjfcjcqrpxfpmq"
SUPABASE_PASSWORD = "Nomadyumbrella2773@supabase"  # ← REMPLACE ICI
SUPABASE_PORT = 5432

def main():
    print("=== IMPORTATION DES DONNEES AEROPORTS ===")
    
    # Connexion à Supabase
    try:
        conn = psycopg2.connect(
            host=SUPABASE_HOST,
            database=SUPABASE_DB,
            user=SUPABASE_USER,
            password=SUPABASE_PASSWORD,
            port=SUPABASE_PORT
        )
        cur = conn.cursor()
        print("✅ Connexion à Supabase réussie")
    except Exception as e:
        print(f"❌ Erreur de connexion: {e}")
        return
    
    # Vider les tables avant d'importer
    print("🗑️  Vidage des tables existantes...")
    cur.execute("TRUNCATE TABLE airports RESTART IDENTITY CASCADE;")
    cur.execute("TRUNCATE TABLE countries RESTART IDENTITY CASCADE;")
    cur.execute("TRUNCATE TABLE regions RESTART IDENTITY CASCADE;")
    
    # Importer les aéroports
    print("📥 Importation des aéroports...")
    if not os.path.exists('airports.csv'):
        print("❌ Fichier airports.csv non trouvé! Télécharge-le d'abord avec wget")
        return
    
    with open('airports.csv', 'r', encoding='utf-8') as f:
        reader = csv.reader(f)
        next(reader)  # Skip header
        airports_data = []
        for row in reader:
            if len(row) >= 19:
                airports_data.append((
                    int(row[0]) if row[0] else None,
                    row[1] if row[1] else None,
                    row[2] if row[2] else None,
                    row[3] if row[3] else None,
                    float(row[4]) if row[4] else None,
                    float(row[5]) if row[5] else None,
                    int(row[6]) if row[6] else None,
                    row[7] if row[7] else None,
                    row[8] if row[8] else None,
                    row[9] if row[9] else None,
                    row[10] if row[10] else None,
                    row[11] if row[11] else None,
                    row[12] if row[12] else None,
                    row[13] if row[13] else None,
                    row[14] if row[14] else None,
                    row[15] if row[15] else None,
                    row[16] if row[16] else None,
                    row[17] if row[17] else None,
                    row[18] if row[18] else None
                ))
        
        execute_values(cur, """
            INSERT INTO airports (
                airport_id, ident, type, name, latitude_deg, longitude_deg, 
                elevation_ft, continent, iso_country, iso_region, municipality, 
                scheduled_service, icao_code, iata_code, gps_code, local_code, 
                home_link, wikipedia_link, keywords
            ) VALUES %s
        """, airports_data)
    
    print(f"✅ {len(airports_data)} aéroports importés")
    
    # Importer les pays
    print("📥 Importation des pays...")
    with open('countries.csv', 'r', encoding='utf-8') as f:
        reader = csv.reader(f)
        next(reader)
        countries_data = []
        for row in reader:
            if len(row) >= 6:
                countries_data.append((
                    int(row[0]) if row[0] else None,
                    row[1] if row[1] else None,
                    row[2] if row[2] else None,
                    row[3] if row[3] else None,
                    row[4] if row[4] else None,
                    row[5] if row[5] else None
                ))
        
        execute_values(cur, """
            INSERT INTO countries (country_id, code, name, continent, wikipedia_link, keywords)
            VALUES %s
        """, countries_data)
    
    print(f"✅ {len(countries_data)} pays importés")
    
    # Importer les régions
    print("📥 Importation des régions...")
    with open('regions.csv', 'r', encoding='utf-8') as f:
        reader = csv.reader(f)
        next(reader)
        regions_data = []
        for row in reader:
            if len(row) >= 7:
                regions_data.append((
                    int(row[0]) if row[0] else None,
                    row[1] if row[1] else None,
                    row[2] if row[2] else None,
                    row[3] if row[3] else None,
                    row[4] if row[4] else None,
                    row[5] if row[5] else None,
                    row[6] if row[6] else None,
                    row[7] if len(row) > 7 and row[7] else None
                ))
        
        execute_values(cur, """
            INSERT INTO regions (region_id, code, local_code, name, continent, iso_country, wikipedia_link, keywords)
            VALUES %s
        """, regions_data)
    
    print(f"✅ {len(regions_data)} régions importées")
    
    # Valider et fermer
    conn.commit()
    cur.close()
    conn.close()
    
    print("\n🎉 IMPORTATION TERMINEE AVEC SUCCES!")

if __name__ == "__main__":
    main()