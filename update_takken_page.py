#!/usr/bin/env python3
"""
Script to update takken page with real takleider names from globals
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

def find_takleider(leiding_list):
    """Find the takleider from a list of leaders"""
    for leader in leiding_list:
        if leader.get('functie') == 'takleider':
            return leader
    return None

def update_takken_page():
    """Update the takken page with real takleider names"""
    page_path = "content/collections/pages/takken.md"
    
    if not os.path.exists(page_path):
        print(f"Takken page not found: {page_path}")
        return False
    
    # Load globals data
    leidingsploegen = load_globals()
    if not leidingsploegen:
        return False
    
    # Create mapping of tak groups to their takleiders
    takleiders_mapping = {}
    for ploeg in leidingsploegen:
        tak_groep = ploeg.get('tak_groep')
        if tak_groep and tak_groep != 'groepsleiding':
            takleider = find_takleider(ploeg.get('leiding', []))
            if takleider:
                name = f"{takleider.get('voornaam', '')} {takleider.get('achternaam', '')}".strip()
                email = takleider.get('email', '')
                takleiders_mapping[tak_groep] = {
                    'name': name,
                    'email': email
                }
    
    print("Found takleiders:")
    for tak, data in takleiders_mapping.items():
        print(f"  {tak}: {data['name']} ({data['email']})")
    
    # Read the existing page
    with open(page_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Split frontmatter and content
    if content.startswith('---\n'):
        parts = content.split('---\n', 2)
        if len(parts) >= 3:
            frontmatter = parts[1]
            body_content = parts[2] if len(parts) > 2 else ""
        else:
            print(f"Invalid frontmatter format in {page_path}")
            return False
    else:
        print(f"No frontmatter found in {page_path}")
        return False
    
    # Parse existing frontmatter
    try:
        data = yaml.safe_load(frontmatter)
        if data is None:
            data = {}
    except yaml.YAMLError as e:
        print(f"Error parsing YAML in {page_path}: {e}")
        return False
    
    # Update takleiders in takken_groepen
    if 'takken_groepen' in data:
        updates_made = 0
        for tak_groep in data['takken_groepen']:
            naam = tak_groep.get('naam', '').lower()
            
            # Map the page names to our globals keys
            if naam == 'kapoenen' and 'kapoenen' in takleiders_mapping:
                takleider_data = takleiders_mapping['kapoenen']
                if 'takleiders' in tak_groep and len(tak_groep['takleiders']) > 0:
                    tak_groep['takleiders'][0]['naam'] = takleider_data['name']
                    tak_groep['takleiders'][0]['email'] = takleider_data['email']
                    updates_made += 1
                    
            elif naam == 'welpen' and 'welpen' in takleiders_mapping:
                takleider_data = takleiders_mapping['welpen']
                if 'takleiders' in tak_groep and len(tak_groep['takleiders']) > 0:
                    tak_groep['takleiders'][0]['naam'] = takleider_data['name']
                    tak_groep['takleiders'][0]['email'] = takleider_data['email']
                    updates_made += 1
                    
            elif naam == 'tictak (akabe)' and 'tictak' in takleiders_mapping:
                takleider_data = takleiders_mapping['tictak']
                if 'takleiders' in tak_groep and len(tak_groep['takleiders']) > 0:
                    tak_groep['takleiders'][0]['naam'] = takleider_data['name']
                    tak_groep['takleiders'][0]['email'] = takleider_data['email']
                    updates_made += 1
                    
            elif naam == 'jongverkenners' and 'jongverkenners' in takleiders_mapping:
                takleider_data = takleiders_mapping['jongverkenners']
                if 'takleiders' in tak_groep and len(tak_groep['takleiders']) > 0:
                    tak_groep['takleiders'][0]['naam'] = takleider_data['name']
                    tak_groep['takleiders'][0]['email'] = takleider_data['email']
                    updates_made += 1
                    
            elif naam == 'verkenners' and 'verkenners' in takleiders_mapping:
                takleider_data = takleiders_mapping['verkenners']
                if 'takleiders' in tak_groep and len(tak_groep['takleiders']) > 0:
                    tak_groep['takleiders'][0]['naam'] = takleider_data['name']
                    tak_groep['takleiders'][0]['email'] = takleider_data['email']
                    updates_made += 1
                    
            elif naam == 'voortrekkers' and 'voortrekkers' in takleiders_mapping:
                takleider_data = takleiders_mapping['voortrekkers']
                if 'takleiders' in tak_groep and len(tak_groep['takleiders']) > 0:
                    tak_groep['takleiders'][0]['naam'] = takleider_data['name']
                    tak_groep['takleiders'][0]['email'] = takleider_data['email']
                    updates_made += 1
        
        print(f"Made {updates_made} takleider updates")
    
    # Write back to file
    try:
        with open(page_path, 'w', encoding='utf-8') as f:
            f.write('---\n')
            yaml.dump(data, f, default_flow_style=False, allow_unicode=True, sort_keys=False)
            f.write('---\n')
            f.write(body_content)
        
        print(f"✅ Updated {page_path}")
        return True
    except Exception as e:
        print(f"Error writing to {page_path}: {e}")
        return False

def main():
    """Main function"""
    print("🔄 Updating takken page with real takleider names...")
    
    if update_takken_page():
        print("✅ Takken page updated successfully!")
    else:
        print("❌ Failed to update takken page")

if __name__ == "__main__":
    main()
