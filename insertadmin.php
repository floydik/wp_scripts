<?php
/* insert admin account itno WP database */

// channge flowing to real values:
// user login
$userlogin = "floydik";
// user nice name
$usernicename = "Floydik from Brno";
// user password
$userpass = "IbHaHer\drynluWumeedvib8";
// user e-mail
$useremail = "someone@somewhere.tld";
//

require ("wp-config.php");

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if (mysqli_connect_error()) {
        die('Connect Error (' . mysqli_connect_errno() . ') '
           . mysqli_connect_error());
        }
$q = "INSERT INTO `".$table_prefix."users` (`user_login`, `user_pass`, `user_nicename`, `user_email`, `user_status`)
VALUES ('".$userlogin."', MD5('".$userpass."'), '".$usernicename."', '".$useremail."', '0');";
if ($mysqli->query($q) === TRUE) {
                printf("INSERT OK<br />");
}

$q = "INSERT INTO `".$table_prefix."usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`)
VALUES (NULL, (Select max(id) FROM ".$table_prefix."users), 'wp_capabilities', 'a:1:{s:13:\"administrator\";s:1:\"1\";}');";
if ($mysqli->query($q) === TRUE) {
                printf("INSERT OK<br />);
}

$q = "INSERT INTO `".$table_prefix."usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`)
VALUES (NULL, (Select max(id) FROM ".$table_prefix."users), 'wp_user_level', '10');";
if ($mysqli->query($q) === TRUE) {
                printf("INSERT <br />");
}

?>
