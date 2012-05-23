# Turn on URL rewriting
RewriteEngine On

# Put your installation directory here:
# If your URL is www.example.com/, use /
# If your URL is www.example.com/kohana/, use /kohana/
RewriteBase /

# Do not enable rewriting for files or directories that exist
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# For reuests that are not actual files or directories,
# Rewrite to index.php/URL
RewriteRule ^(.*)$ index.php/$1 [PT,L]