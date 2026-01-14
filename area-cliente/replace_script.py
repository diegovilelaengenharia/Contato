import os

# Define paths
base_dir = r"c:\Users\diego\Meu Drive\02. Trabalho (Engenheiro Civil)\01. Escritório (Vilela Engenharia)\3. Digital\02. Landing Page da Vilela Engenharia\area-cliente"
target_file_path = os.path.join(base_dir, "gestao_admin_99.php")
temp_file_path = os.path.join(base_dir, "temp_profile_tab.php")

# Read target file
with open(target_file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Define markers
start_marker = "<?php if ($active_tab == 'perfil'): ?>"
end_marker = "<?php if ($active_tab == 'cadastro' || $active_tab == 'andamento'): ?>"

# Find markers
start_idx = content.find(start_marker)
end_idx = content.find(end_marker)

if start_idx == -1:
    print(f"Error: Start marker not found: {start_marker}")
    exit(1)
if end_idx == -1:
    print(f"Error: End marker not found: {end_marker}")
    exit(1)

print(f"Start index: {start_idx}")
print(f"End index: {end_idx}")

# Read replacement content
with open(temp_file_path, 'r', encoding='utf-8') as f:
    new_block = f.read()

# Construct new content
# We keep content BEFORE start_marker
# We insert new_block
# We keep content FROM end_marker onwards
new_content = content[:start_idx] + new_block + "\n\n" + (" " * 20) + content[end_idx:]

# Write back to target file
with open(target_file_path, 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Success: File updated.")
