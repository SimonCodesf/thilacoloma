#!/usr/bin/env python3
import re

# Read the original file
with open('resources/views/temp/takken.twig', 'r') as f:
    content = f.read()

# Extract content between {% block content %} and {% endblock %}
content_match = re.search(r'{%\s*block\s+content\s*%}(.*?){%\s*endblock\s*%}', content, re.DOTALL)
if content_match:
    extracted_content = content_match.group(1)
else:
    extracted_content = content

# Extract scripts between {% block scripts %} and {% endblock %}
scripts_match = re.search(r'{%\s*block\s+scripts\s*%}(.*?){%\s*endblock\s*%}', content, re.DOTALL)
if scripts_match:
    extracted_scripts = scripts_match.group(1)
else:
    extracted_scripts = ""

# Clean up the content
cleaned_content = extracted_content.strip()
cleaned_scripts = extracted_scripts.strip()

# Fix Twig syntax to plain syntax
cleaned_content = re.sub(r"{{ alias\('@web'\) }}/", "", cleaned_content)
cleaned_scripts = re.sub(r"{{ alias\('@web'\) }}/", "", cleaned_scripts)
cleaned_content = re.sub(r"{{ url\('([^']+)'\) }}", r"/\1", cleaned_content)
cleaned_scripts = re.sub(r"{{ url\('([^']+)'\) }}", r"/\1", cleaned_scripts)

# Combine content and scripts
final_content = cleaned_content
if cleaned_scripts:
    final_content += "\n\n" + cleaned_scripts

# Write to the new file
with open('resources/views/takken.antlers.html', 'w') as f:
    f.write(final_content)

print("Takken template extracted and cleaned successfully!")
