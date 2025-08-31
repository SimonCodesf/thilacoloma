#!/usr/bin/env python3
"""
Script om alle takken-pagina's te updaten met de nieuwe globals structuur
"""

import re
import os

# Lijst van takken-pagina's om te updaten
TAKKEN_FILES = [
    'resources/views/verkenners.antlers.html',
    'resources/views/voortrekkers.antlers.html'
]

# Pattern voor oude leiding structuur
OLD_PATTERN = r'''(\s+)<div class="leiding-grid">
\s+\{\{ leidingsploegen:leidingsploegen \}\}
\s+\{\{ if tak_naam == "([^"]+)" \}\}
\s+<!-- Takleider -->
\s+\{\{ if takleider \}\}
\s+<div class="leiding-card takleider">
\s+<div class="leiding-foto">
\s+<img src="/assets/images/\{\{ site_instellingen:scoutsjaar \}\}/\{\{ takleider:foto \}\}" alt="\{\{ takleider:totem_naam \}\} \{\{ takleider:totem_dier \}\}">
\s+</div>
\s+<div class="leiding-info">
\s+<h3><strong>\{\{ takleider:totem_naam \}\}</strong> \{\{ takleider:totem_dier \}\}</h3>
\s+\{\{ if takleider:echte_naam \}\}<p class="echte-naam">\{\{ takleider:echte_naam \}\} – Takleider</p>\{\{ /if \}\}
\s+\{\{ if takleider:email \}\}<a href="mailto:\{\{ takleider:email \}\}" class="email">\{\{ takleider:email \}\}</a>\{\{ /if \}\}
\s+\{\{ if takleider:telefoon \}\}<p class="telefoon">\{\{ takleider:telefoon \}\}</p>\{\{ /if \}\}
\s+</div>
\s+</div>
\s+\{\{ /if \}\}
\s+
\s+<!-- Andere leiding -->
\s+\{\{ leiding \}\}
\s+<div class="leiding-card">
\s+<div class="leiding-foto">
\s+<img src="/assets/images/\{\{ site_instellingen:scoutsjaar \}\}/\{\{ foto \}\}" alt="\{\{ totem_naam \}\} \{\{ totem_dier \}\}">
\s+</div>
\s+<div class="leiding-info">
\s+<h3><strong>\{\{ totem_naam \}\}</strong> \{\{ totem_dier \}\}</h3>
\s+\{\{ if functie && functie != "Leiding" \}\}<p class="functie">\{\{ functie \}\}</p>\{\{ /if \}\}
\s+\{\{ if email \}\}<a href="mailto:\{\{ email \}\}" class="email">\{\{ email \}\}</a>\{\{ /if \}\}
\s+\{\{ if telefoon \}\}<p class="telefoon">\{\{ telefoon \}\}</p>\{\{ /if \}\}
\s+</div>
\s+</div>
\s+\{\{ /leiding \}\}
\s+\{\{ /if \}\}
\s+\{\{ /leidingsploegen:leidingsploegen \}\}
\s+</div>'''

def new_template(tak_naam, indent="            "):
    return f'''{indent}<div class="leiding-grid">
{indent}    {{ leidingsploegen:leidingsploegen }}
{indent}        {{ if tak_naam == "{tak_naam}" && actief }}
{indent}            {{ leiding }}
{indent}                <div class="leiding-card {{ if functie == 'takleider' }}takleider{{ /if }}">
{indent}                    <div class="leiding-foto">
{indent}                        {{ totem_parts = totem | explode: ' ' }}
{indent}                        {{ animal_name = totem_parts | last }}
{indent}                        <img src="/assets/images/leidingfotos/{{ animal_name }}.jpg" alt="{{ totem }}" onerror="this.src='/assets/images/leidingfotos/placeholder.jpg'">
{indent}                    </div>
{indent}                    <div class="leiding-info">
{indent}                        <h3><strong>{{ totem }}</strong></h3>
{indent}                        {{ if functie == 'takleider' }}
{indent}                            <p class="echte-naam">{{ voornaam }} {{ achternaam }} – Takleider</p>
{indent}                        {{ else }}
{indent}                            <p class="echte-naam">{{ voornaam }} {{ achternaam }}</p>
{indent}                        {{ /if }}
{indent}                        <a href="mailto:{{ email }}" class="email">{{ email }}</a>
{indent}                        {{ if functie == 'takleider' }}
{indent}                            <p class="telefoon">Telefoonnummer in de blauwe gids</p>
{indent}                        {{ /if }}
{indent}                    </div>
{indent}                </div>
{indent}            {{ /leiding }}
{indent}        {{ /if }}
{indent}    {{ /leidingsploegen:leidingsploegen }}
{indent}</div>'''

def update_file(filepath):
    """Update een specifiek bestand"""
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        print(f"Updating {filepath}...")
        
        # Find all matches and replace them
        def replace_match(match):
            indent = match.group(1)
            tak_naam = match.group(2)
            return new_template(tak_naam, indent)
        
        updated_content = re.sub(OLD_PATTERN, replace_match, content, flags=re.MULTILINE | re.DOTALL)
        
        if updated_content != content:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(updated_content)
            print(f"✅ {filepath} updated successfully")
            return True
        else:
            print(f"⚠️ No changes needed for {filepath}")
            return False
            
    except Exception as e:
        print(f"❌ Error updating {filepath}: {e}")
        return False

def main():
    updated_count = 0
    
    for filepath in TAKKEN_FILES:
        if os.path.exists(filepath):
            if update_file(filepath):
                updated_count += 1
        else:
            print(f"❌ File not found: {filepath}")
    
    print(f"\n✨ Update complete! {updated_count} files updated.")

if __name__ == "__main__":
    main()
