<?php
// přidá do .htaccess pravidla pro blokování přístupu k wp-login.php jménem a heslem
// heslo (a soubor .htpasswd) vygeeruje skript htpassword.php

// cesta k souboru .htaccess (relativní k tomuto skriptu)
$htaccessFile = __DIR__ . '/.htaccess';

// vložíme na začátek .htaccess příslušný kus kódu
$line = '#Zablokování přístupu k .htaccess a .htpasswd (pouze pro jistotu)
RewriteEngine On
RewriteRule ^\.ht - [F,L]
#Zamezení přístupu k wp-login.php
<Files "wp-login.php">
    AuthType Basic
    AuthName "Nutné přihlášení"
    AuthUserFile '. __DIR__ .'/.htpasswd
    Require valid-user
</Files>

';

// načteme celý obsah (pokud soubor neexistuje, $old bude prázdný řetězec)
$old = file_exists($htaccessFile) ? file_get_contents($htaccessFile) : '';

// otevřeme soubor pro zápis (přepíšeme celý soubor)
$fh = fopen($htaccessFile, 'w');
fwrite($fh, $line . $old);
fclose($fh);
echo "Soubor .htaccess byl upraven. Smažte tento skript.";
?>
