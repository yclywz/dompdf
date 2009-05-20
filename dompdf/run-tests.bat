echo off
echo "Running tests..."
for %%f in (test\*.html test\*.htm test\*.php) do (
echo .
echo %%f
php dompdf -v %%f
)