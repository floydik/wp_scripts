<?php
/**
 * Skript pro vytvoření administrátora WP
 *
 * POZOR: Po použití tento soubor OKAMŽITĚ SMAŽTE ze serveru!
 */

// Načte prostředí WordPressu
require_once( 'wp-load.php' );

// --- ZDE UPRAVTE ÚDAJE NOVÉHO UŽIVATELE ---
$nove_jmeno   = 'nekdo'; // Zvolte si přihlašovací jméno
$nove_heslo   = 'CheevBonOosckyinkelrott7';    // Zadejte silné heslo
$novy_email   = 'nekdo@nejaka.tld'; // Zadejte váš e-mail
// --- KONEC ÚPRAV ---


// Zkontrolujeme, zda uživatel nebo e-mail již neexistuje
if ( username_exists( $nove_jmeno ) || email_exists( $novy_email ) ) {
    die( 'CHYBA: Uživatel s tímto jménem nebo e-mailem již existuje. Skript neprovedl žádnou akci.' );
}

// Sestavení dat pro nového uživatele
$user_data = array(
    'user_login' => $nove_jmeno,
    'user_pass'  => $nove_heslo,
    'user_email' => $novy_email,
    'role'       => 'administrator' // Přiřadíme rovnou roli administrátora
);

// Vložíme uživatele do databáze pomocí WordPress funkce
$user_id = wp_insert_user( $user_data );

// Zkontrolujeme výsledek
if ( is_wp_error( $user_id ) ) {
    echo 'CHYBA: Nepodařilo se vytvořit uživatele: ' . $user_id->get_error_message();
} else {
    echo 'HOTOVO: Uživatel "' . esc_html( $nove_jmeno ) . '" byl úspěšně vytvořen s rolí administrátora.';
    echo '<br><br><strong style="color:red; font-size: 20px;">VAROVÁNÍ: Nyní tento soubor (' . __FILE__ . ') OKAMŽITĚ SMAŽTE z FTP!</strong>';
}

?>
