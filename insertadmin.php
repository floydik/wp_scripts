<?php
/* insert admin account itno WP database */

// channge to real values:
// user login
$user_login = "floydik";
// user nice name
$user_nicename = "Floydik from Brno";
// user password
$user_pass = "IbHaHer\drynluWumeedvib8";
// user e-mail
$user_email = "someone@somewhere.tld";

include ("wp_config.php");

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if (mysqli_connect_error()) {
        die('Connect Error (' . mysqli_connect_errno() . ') '
           . mysqli_connect_error());
        }
$q = "INSERT INTO `'.$table_prefix.'_users` (`user_login`, `user_pass`, `user_nicename`, `user_email`, `user_status`)
VALUES ('$user_login', MD5('$user_pass'), '$user_nicename', '$user_email', '0');";
echo $q."<br /n>";

$q = "INSERT INTO `'.$table_prefix.'_usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`) 
VALUES (NULL, (Select max(id) FROM '.$table_prefix.'_users), 'wp_capabilities', 'a:1:{s:13:\"administrator\";s:1:\"1\";}');";
echo $q."<br /n>";

$q = "INSERT INTO `'.$table_prefix.'_usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`) 
VALUES (NULL, (Select max(id) FROM '.$table_prefix.'_users), 'wp_user_level', '10');";
echo $q."<br /n>";
/*
if ($mysqli->query($q) === TRUE) {
                printf("INSERT OK\n");
}
*/
?>
