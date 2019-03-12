
set mypath=%cd%
start "PHP Unit" %mypath%\vendor\bin\phpunit --bootstrap %mypath%\vendor\autoload.php test
