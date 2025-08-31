#!/usr/bin/env python3
"""
Script to convert Twig templates to Antlers templates
"""
import os
import re

def convert_twig_to_antlers(twig_content, template_name):
    """Convert Twig syntax to Antlers syntax"""
    content = twig_content
    
    # Remove extends and block structure, keep only content
    content = re.sub(r'{%\s*extends\s*[^%]*%}', '', content)
    content = re.sub(r'{%\s*block\s+title\s*%}.*?{%\s*endblock\s*%}', '', content, flags=re.DOTALL)
    content = re.sub(r'{%\s*block\s+description\s*%}.*?{%\s*endblock\s*%}', '', content, flags=re.DOTALL)
    content = re.sub(r'{%\s*block\s+head\s*%}.*?{%\s*endblock\s*%}', '', content, flags=re.DOTALL)
    
    # Extract content between {% block content %} and {% endblock %}
    content_match = re.search(r'{%\s*block\s+content\s*%}(.*?){%\s*endblock\s*%}', content, flags=re.DOTALL)
    if content_match:
        content = content_match.group(1).strip()
    
    # Extract scripts between {% block scripts %} and {% endblock %}
    scripts_match = re.search(r'{%\s*block\s+scripts\s*%}(.*?){%\s*endblock\s*%}', twig_content, flags=re.DOTALL)
    scripts = scripts_match.group(1).strip() if scripts_match else ""
    
    # Convert Twig variables to Antlers
    content = re.sub(r'{{\s*alias\(\'@web\'\)\s*}}', '', content)
    content = re.sub(r'{{\s*([^}]+)\s*}}', r'{{ \1 }}', content)
    
    # Convert Twig loops (basic conversion) 
    content = re.sub(r'{%\s*for\s+(\w+)\s+in\s+([^%]+)\s*%}', r'{{ \2 scope="\1" }}', content)
    content = re.sub(r'{%\s*endfor\s*%}', r'{{ /\2 }}', content)
    
    # Convert Twig conditionals (basic conversion)
    content = re.sub(r'{%\s*if\s+([^%]+)\s*%}', r'{{ if \1 }}', content)
    content = re.sub(r'{%\s*endif\s*%}', r'{{ /if }}', content)
    
    # Clean up extra whitespace
    content = re.sub(r'\n\s*\n\s*\n', '\n\n', content)
    content = content.strip()
    
    # Add scripts if they exist
    if scripts:
        # Convert script block
        scripts = re.sub(r'{{\s*alias\(\'@web\'\)\s*}}', '', scripts)
        content += "\n\n" + scripts
    
    return content

def main():
    source_dir = "resources/views/temp"
    target_dir = "resources/views"
    
    # List of templates to convert
    templates = [
        "takken.twig",
        "kalender.twig", 
        "stamhoofd.twig",
        "thilala.twig",
        "verhuur.twig",
        "meer.twig",
        "over.twig"
    ]
    
    for template_file in templates:
        source_path = os.path.join(source_dir, template_file)
        if os.path.exists(source_path):
            with open(source_path, 'r', encoding='utf-8') as f:
                twig_content = f.read()
            
            template_name = template_file.replace('.twig', '')
            antlers_content = convert_twig_to_antlers(twig_content, template_name)
            
            target_file = f"{template_name}.antlers.html"
            target_path = os.path.join(target_dir, target_file)
            
            with open(target_path, 'w', encoding='utf-8') as f:
                f.write(antlers_content)
            
            print(f"Converted {template_file} -> {target_file}")

if __name__ == "__main__":
    main()
