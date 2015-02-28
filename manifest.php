<?php
header("Content-Type: text/cache-manifest");
include("configuration.php");
echo "CACHE MANIFEST
";
$pdo = new pdo($dsn, $db_username, $db_password);

$selectMovies = $pdo->prepare("SELECT id FROM movies");
$selectMovies->execute();


while ($fetch = $selectMovies->fetch()) {
	echo "images/posters/" . ($fetch[0]) . '_Poster.png
';
}
?>
