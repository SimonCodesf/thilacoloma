#!/usr/bin/env python3
import re

# Lees het YAML bestand
with open('/Users/simon/Library/CloudStorage/OneDrive-Persoonlijk/Thila Coloma/Thilacoloma_statamic/content/globals/leidingsploegen.yaml', 'r') as f:
    content = f.read()

# Patroon om de eerste leider in elke tak te vinden en functie te wijzigen
# We zoeken naar 'leiding:' gevolgd door de eerste leider entry
pattern = r'(leiding:\s*\n\s*-\s*\n\s*id: [^\n]+\n\s*type: leider\n(?:\s*[^\n:]+:[^\n]*\n)*?\s*functie:) leiding(\n\s*enabled: true)'

def replace_functie(match):
    return match.group(1) + ' takleider' + match.group(2)

# Vervang alle eerste leiders van 'functie: leiding' naar 'functie: takleider'
new_content = re.sub(pattern, replace_functie, content)

# Schrijf het resultaat terug
with open('/Users/simon/Library/CloudStorage/OneDrive-Persoonlijk/Thila Coloma/Thilacoloma_statamic/content/globals/leidingsploegen.yaml', 'w') as f:
    f.write(new_content)

print("Takleiders functie succesvol bijgewerkt!")
