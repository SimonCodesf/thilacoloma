#!/usr/bin/env python3
import re

# Lees het YAML bestand
with open('/Users/simon/Library/CloudStorage/OneDrive-Persoonlijk/Thila Coloma/Thilacoloma_statamic/content/globals/leidingsploegen.yaml', 'r') as f:
    content = f.read()

# Functie om voornaam en achternaam te splitsen
def split_name(full_name):
    parts = full_name.strip().split()
    if len(parts) == 1:
        return parts[0], ''
    elif len(parts) == 2:
        return parts[0], parts[1]
    else:
        # Voor namen zoals "Thomas Van De Sompel" -> voornaam: "Thomas", achternaam: "Van De Sompel"
        return parts[0], ' '.join(parts[1:])

# Regex patroon om voornaam velden te vinden die volledige namen bevatten
pattern = r"voornaam: '([^']+)'\s*\n\s*totem:"

def replace_name(match):
    full_name = match.group(1)
    # Skip als het al alleen een voornaam is (geen spaties)
    if ' ' not in full_name:
        return match.group(0)
    
    voornaam, achternaam = split_name(full_name)
    
    # Vervang de match met voornaam + achternaam structuur
    return f"voornaam: '{voornaam}'\n          achternaam: '{achternaam}'\n          totem:"

# Pas de replacements toe
new_content = re.sub(pattern, replace_name, content)

# Schrijf het resultaat terug
with open('/Users/simon/Library/CloudStorage/OneDrive-Persoonlijk/Thila Coloma/Thilacoloma_statamic/content/globals/leidingsploegen.yaml', 'w') as f:
    f.write(new_content)

print("Namen succesvol gesplitst!")
