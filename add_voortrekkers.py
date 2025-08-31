#!/usr/bin/env python3
"""
Script om Voortrekkers leiding toe te voegen aan leidingsploegen.yaml
"""

import yaml
import re

# Voortrekkers data
voortrekkers_data = {
    "id": "vt",
    "type": "leidingsploeg", 
    "tak_naam": "Voortrekkers",
    "tak_groep": "voortrekkers",
    "actief": True,
    "takleider": "Yu-lan Elshout", 
    "takleider_email": "sambar@thilacoloma.be",
    "leiding": [
        {
            "id": "vt1",
            "type": "leider",
            "voornaam": "Yu-lan",
            "achternaam": "Elshout", 
            "totem": "Volhardende Sambar",
            "functie": "takleider",
            "email": "sambar@thilacoloma.be",
            "enabled": True
        },
        {
            "id": "vt2", 
            "type": "leider",
            "voornaam": "Seppe",
            "achternaam": "Laurent",
            "totem": "Ondernemende Baya", 
            "functie": "leiding",
            "email": "baya@thilacoloma.be",
            "enabled": True
        },
        {
            "id": "vt3",
            "type": "leider", 
            "voornaam": "Bert",
            "achternaam": "De Landtsheer",
            "totem": "Opmerkzame Taipan",
            "functie": "leiding", 
            "email": "taipan@thilacoloma.be",
            "enabled": True
        },
        {
            "id": "vt4",
            "type": "leider",
            "voornaam": "Shayan", 
            "achternaam": "Ghadiri",
            "totem": "Joviale Pumba",
            "functie": "leiding",
            "email": "pumba@thilacoloma.be", 
            "enabled": True
        },
        {
            "id": "vt5",
            "type": "leider",
            "voornaam": "Sofie",
            "achternaam": "Denoulet",
            "totem": "Hartelijke Albatros",
            "functie": "leiding", 
            "email": "albatros@thilacoloma.be",
            "enabled": True
        }
    ]
}

def add_voortrekkers():
    filepath = "content/globals/leidingsploegen.yaml"
    
    try:
        # Read current file
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Parse YAML
        data = yaml.safe_load(content)
        
        # Check if voortrekkers already exists
        existing_names = [ploeg.get('tak_naam', '') for ploeg in data['data']['leidingsploegen']]
        if 'Voortrekkers' in existing_names:
            print("⚠️ Voortrekkers already exists in globals")
            return False
            
        # Add voortrekkers to the list
        data['data']['leidingsploegen'].append(voortrekkers_data)
        
        # Write back to file
        with open(filepath, 'w', encoding='utf-8') as f:
            yaml.dump(data, f, default_flow_style=False, allow_unicode=True, sort_keys=False)
        
        print("✅ Voortrekkers successfully added to leidingsploegen.yaml")
        return True
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    add_voortrekkers()
