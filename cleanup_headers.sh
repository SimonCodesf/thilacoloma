#!/bin/bash

# Script om alle lokale header styling uit template bestanden te verwijderen

FILES=(
    "resources/views/kalender.antlers.html"
    "resources/views/voortrekkers.antlers.html" 
    "resources/views/jongverkenners.antlers.html"
    "resources/views/tictak.antlers.html"
)

for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "Cleaning header styling from $file..."
        
        # Backup het originele bestand
        cp "$file" "$file.backup"
        
        # Verwijder header CSS blokken
        perl -i -pe '
            BEGIN { undef $/; }
            s@/\*[^*]*Header[^*]*\*/.*?\.header-actions[^}]*}[^}]*}@@gms;
            s@\.simple-header-content\s*\{[^}]*\}@@gms;
            s@\.simple-nav\s*\{[^}]*\}@@gms;
            s@\.nav-icon[^{]*\{[^}]*\}@@gms;
            s@\.header-actions[^{]*\{[^}]*\}@@gms;
            s@\.search-container[^{]*\{[^}]*\}@@gms;
            s@\.search-input[^{]*\{[^}]*\}@@gms;
            s@\.search-button[^{]*\{[^}]*\}@@gms;
        ' "$file"
        
        echo "Completed $file"
    else
        echo "File $file not found"
    fi
done

echo "Header cleanup completed!"
