<?php
// vygenerováno AI a funguje ;-)
/**
 * 
 * PHP skript pro generování souboru .htpasswd (bcrypt) bez přístupu ke shellu.
 *
 * POZOR: Po úspěšném vytvoření souboru je dobré skript smazat nebo
 *       přístup k němu omezit (např. .htaccess deny from all).
 */

#########################
#  KONFIGURACE SKRIPTU  #
#########################
$htpasswdFile = '.htpasswd';   // umístění soboru .htpasswd
//$allowedIps   = ['127.0.0.1'];                // IP, ze kterých je skript povolen (volitelně)
$useHttpsOnly = true;                        // povinně HTTPS?

#########################
#  OCHRANA PŘÍSTUPU     #
#########################
if ($useHttpsOnly && empty($_SERVER['HTTPS'])) {
    http_response_code(403);
    exit('HTTPS is required.');
}
if (!empty($allowedIps) && !in_array($_SERVER['REMOTE_ADDR'], $allowedIps)) {
    http_response_code(403);
    exit('Access denied.');
}

/**
 * Vytvoří (nebo doplní) řádek pro .htpasswd.
 *
 * @param string $user  Uživatelské jméno (ASCII, bez dvojtečky)
 * @param string $pass  Heslo (plain‑text)
 * @return string       Řádek ve formátu "user:hash\n"
 */
function makeHtpasswdLine(string $user, string $pass): string
{
    // Bcrypt – automaticky vygeneruje náhodný salt
    $hash = password_hash($pass, PASSWORD_BCRYPT);
    // password_hash() vrací řetězec už připravený pro .htpasswd (začíná $2y$)
    return $user . ':' . $hash . PHP_EOL;
}

/**
 * Přidá nebo aktualizuje uživatele v .htpasswd souboru.
 *
 * @param string $file  Cesta k .htpasswd
 * @param string $user  Uživatelské jméno
 * @param string $pass  Heslo
 * @return bool         true = úspěch, false = chyba
 */
function updateHtpasswd(string $file, string $user, string $pass): bool
{
    // Načteme existující obsah (pokud soubor existuje)
    $lines = [];
    if (is_file($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    // Odstraňujeme starý záznam (pokud existuje)
    $newLines = [];
    foreach ($lines as $ln) {
        if (strpos($ln, $user . ':') !== 0) {
            $newLines[] = $ln;
        }
    }

    // Přidáme novou (nebo první) verzi
    $newLines[] = trim(makeHtpasswdLine($user, $pass));

    // Zapíšeme zpět atomicky (tmp soubor → přejmenujeme)
    $tmp = $file . '.tmp';
    if (file_put_contents($tmp, implode(PHP_EOL, $newLines) . PHP_EOL, LOCK_EX) === false) {
        return false;
    }

    // Nastavíme oprávnění 0600 (čtení/zápis jen pro vlastníka)
    @chmod($tmp, 0660);

    // Přesuneme na konečnou cestu
    return rename($tmp, $file);
}

/* ----------------------- */
$feedback = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    // Jednoduchá validace
    if ($user === '' || $pass === '') {
        $feedback = 'Uživatelské jméno i heslo jsou povinné.';
    } elseif (strpos($user, ':') !== false) {
        $feedback = 'Uživatelské jméno nesmí obsahovat dvojtečku (:)';
    } else {
        // Zkusíme aktualizovat soubor
        if (updateHtpasswd($htpasswdFile, $user, $pass)) {
            $feedback = "Uživatel <strong>" . htmlspecialchars($user) . "</strong> byl úspěšně uložen.";
        } else {
            $feedback = "Chyba při zápisu do souboru <code>" . htmlspecialchars($htpasswdFile) . "</code>.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Vytvoření .htpasswd</title>
    <style>
        body{font-family:Arial,sans-serif;background:#f8f8f8;margin:2rem}
        .box{background:#fff;padding:1.5rem;border:1px solid #ddd;max-width:400px}
        label{display:block;margin-top:0.8rem}
        input[type=text],input[type=password]{width:100%;padding:0.4rem;margin-top:0.2rem}
        .msg{margin-top:1rem;padding:0.8rem;background:#e2ffe2;border:1px solid #8f8}
    </style>
</head>
<body>
<div class="box">
    <h2>Vytvořit / aktualizovat uživatele v .htpasswd</h2>

    <?php if ($feedback): ?>
        <div class="msg"><?= $feedback ?></div>
    <?php endif; ?>

    <form method="post">
        <label for="username">Uživatelské jméno</label>
        <input type="text" id="username" name="username" required autocomplete="off">

        <label for="password">Heslo</label>
        <input type="password" id="password" name="password" required autocomplete="new-password">

        <button type="submit" style="margin-top:1rem;padding:0.5rem 1rem;">Uložit</button>
    </form>

    <p style="margin-top:1rem;font-size:0.9rem;color:#555;">
        Soubor bude uložen na <code><?= htmlspecialchars($htpasswdFile) ?></code>.<br>
        Po dokončení odstraňte nebo zabezpečte tento skript.
    </p>
</div>
</body>
</html>
