#!/usr/bin/env python3
"""
Script to update collection entries with leadership data from globals
"""

import yaml
import os
from pathlib import Path

def load_globals():
    """Load leadership data from globals"""
    globals_path = "content/globals/leidingsploegen.yaml"
    
    if not os.path.exists(globals_path):
        print(f"Globals file not found: {globals_path}")
        return None
    
    with open(globals_path, 'r', encoding='utf-8') as f:
        data = yaml.safe_load(f)
    
    # Extract the leidingsploegen array from the data structure
    if 'data' in data and 'leidingsploegen' in data['data']:
        return data['data']['leidingsploegen']
    
    return None

def update_collection_entry(tak_name, leaders_data):
    """Update a collection entry with leaders data"""
    collection_path = f"content/collections/takken/{tak_name}.md"
    
    if not os.path.exists(collection_path):
        print(f"Collection entry not found: {collection_path}")
        return False
    
    # Read the existing file
    with open(collection_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Split frontmatter and content
    if content.startswith('---\n'):
        parts = content.split('---\n', 2)
        if len(parts) >= 3:
            frontmatter = parts[1]
            body_content = parts[2] if len(parts) > 2 else ""
        else:
            print(f"Invalid frontmatter format in {collection_path}")
            return False
    else:
        print(f"No frontmatter found in {collection_path}")
        return False
    
    # Parse existing frontmatter
    try:
        data = yaml.safe_load(frontmatter)
        if data is None:
            data = {}
    except yaml.YAMLError as e:
        print(f"Error parsing YAML in {collection_path}: {e}")
        return False
    
    # Update with leaders data
    leaders_list = []
    for leader in leaders_data:
        leader_entry = {
            'type': 'leader',
            'enabled': True,
            'name': f"{leader.get('voornaam', '')} {leader.get('achternaam', '')}".strip(),
            'role': leader.get('functie', ''),
        }
        
        # Add photo if available
        totem = leader.get('totem', '').lower().replace(' ', '_')
        if totem:
            leader_entry['photo'] = f"leidingfotos/{totem}.jpg"
        
        leaders_list.append(leader_entry)
    
    data['leaders'] = leaders_list
    
    # Write back to file
    try:
        with open(collection_path, 'w', encoding='utf-8') as f:
            f.write('---\n')
            yaml.dump(data, f, default_flow_style=False, allow_unicode=True, sort_keys=False)
            f.write('---\n')
            f.write(body_content)
        
        print(f"✅ Updated {collection_path} with {len(leaders_list)} leaders")
        return True
    except Exception as e:
        print(f"Error writing to {collection_path}: {e}")
        return False

def main():
    """Main function"""
    print("🔄 Updating collection entries with globals data...")
    
    # Load globals data
    leidingsploegen = load_globals()
    if not leidingsploegen:
        return
    
    # Mapping between collection filenames and tak_groep values
    tak_mapping = {
        'kapoenen': 'kapoenen',
        'welpen': 'welpen', 
        'jongverkenners': 'jongverkenners',
        'verkenners': 'verkenners',
        'voortrekkers': 'voortrekkers',
        'tictak': 'tictak'
    }
    
    # Update each collection entry
    success_count = 0
    for collection_name, tak_groep in tak_mapping.items():
        # Find the tak in the leidingsploegen array
        tak_data = None
        for ploeg in leidingsploegen:
            if ploeg.get('tak_groep') == tak_groep:
                tak_data = ploeg
                break
        
        if tak_data and 'leiding' in tak_data:
            leaders = tak_data['leiding']
            if update_collection_entry(collection_name, leaders):
                success_count += 1
        else:
            print(f"⚠️  No data found for {tak_groep} in globals")
    
    print(f"\n✅ Successfully updated {success_count}/{len(tak_mapping)} collection entries")

if __name__ == "__main__":
    main()
