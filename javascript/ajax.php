<?php
include ("../configuration.php");
switch ($_GET['page']) {
	default :
		die("You cannot be here.");
		break;

	case 'settings' :
		$genres = "";

		if (isset($_POST['adventure'])) {
			$genres = $genres . 'adventure,';
		}

		if (isset($_POST['documentary'])) {
			$genres = $genres . 'documentary,';
		}

		if (isset($_POST['action'])) {
			$genres = $genres . 'action,';
		}

		if (isset($_POST['family'])) {
			$genres = $genres . 'family,';
		}

		if (isset($_POST['classic'])) {
			$genres = $genres . 'classic,';
		}

		if (isset($_POST['horror'])) {
			$genres = $genres . 'horror,';
		}

		if (isset($_POST['comedy'])) {
			$genres = $genres . 'comedy,';
		}

		if (isset($_POST['music'])) {
			$genres = $genres . 'music,';
		}

		if (isset($_POST['drama'])) {
			$genres = $genres . 'drama,';
		}

		if (isset($_POST['romance'])) {
			$genres = $genres . 'romance,';
		}

		if (isset($_POST['animation'])) {
			$genres = $genres . 'animation,';
		}

		if (isset($_POST['biography'])) {
			$genres = $genres . 'biography,';
		}

		if (isset($_POST['sport'])) {
			$genres = $genres . 'sport,';
		}

		if (isset($_POST['crime'])) {
			$genres = $genres . 'crime,';
		}

		if (isset($_POST['war'])) {
			$genres = $genres . 'war,';
		}

		if (isset($_POST['mystery'])) {
			$genres = $genres . 'mystery,';
		}

		if (isset($_POST['fantasy'])) {
			$genres = $genres . 'fantasy,';
		}

		if (isset($_POST['scifi'])) {
			$genres = $genres . 'scifi,';
		}

		if (isset($_POST['history'])) {
			$genres = $genres . 'history,';
		}

		if (isset($_POST['western'])) {
			$genres = $genres . 'western,';
		}

		if (isset($_POST['thriller'])) {
			$genres = $genres . 'thriller,';
		}

		if (isset($_POST['client'])) {
			$client = $_POST['client'];
		}
		
		if(isset($_POST['layout'])) {
			$layout = $_POST['layout'];
		}

		try {
			$pdo = new PDO($dsn, $db_username, $db_password);
			$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$insertGenres = $pdo -> prepare("UPDATE configuration SET maingenres = ?");
			$insertGenres -> bindParam(1, $genres);
			$insertGenres -> execute();

			$updateClients = $pdo -> prepare("UPDATE clients SET selected = 0 WHERE selected = 1");
			$updateClients -> execute();

			$updateClients = $pdo -> prepare("UPDATE clients SET selected = 1 WHERE id = ?");
			$updateClients -> bindParam(1, $client);
			$updateClients -> execute();
			
			$updateLayout = $pdo -> prepare("UPDATE configuration SET displaytype = ?");
			$updateLayout -> bindParam(1, $layout);
			$updateLayout -> execute();
			$pdo = null;

			echo "<div style=\"padding: 8px;\" id=\"settingsSuccess\">The settings were saved successfully.</div>";
		} catch (PDOException $error) {
			echo "There was an issue saving the settings.";
		}
		break;

	case 'edit' :
		if (isset($_GET['id'])) {
			try {
				$pdo = new PDO($dsn, $db_username, $db_password);
				$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$selectMovie = $pdo -> prepare("SELECT displaytitle, year, genre, actors, media, plexSummary FROM movies WHERE id = ?");
				$selectMovie -> bindParam(1, $_GET['id']);
				$selectMovie -> execute();
				$fetchMovie = $selectMovie -> fetch();
			} catch (PDOException $error) {
				die("There was a problem grabbing movie information");
			}
			$pdo = null;
			header("Content-Type: text/xml");
			$summary = htmlspecialchars($fetchMovie[5]);
			echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
			echo "<movie title=\"$fetchMovie[0]\" year=\"$fetchMovie[1]\" genre=\"$fetchMovie[2]\" actors=\"$fetchMovie[3]\" media=\"$fetchMovie[4]\" summary=\"$summary\"></movie>";
		} else {
			die("Possible hijacking attempt");
		}
		break;

	case 'editsave' :
		if (isset($_GET['movieeditid'])) {
			$movieid = $_GET['movieeditid'];
		} else {
			die("Possible hijacking attempt.");
		}
		$movietitle = $_POST['movietitle'];
		$year = $_POST['year'];
		$genre = $_POST['genre'];
		$actors = $_POST['actors'];
		$media = $_POST['media'];
		
		$summary = htmlspecialchars($_POST['summary']);
		try {
			$pdo = new PDO($dsn, $db_username, $db_password);
			$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$updateMovie = $pdo -> prepare("UPDATE movies SET displaytitle = ?, year = ?, genre = ?, actors = ?, media = ?, plexSummary = ? WHERE id = ?");
			$updateMovie -> bindParam(1, $movietitle, PDO::PARAM_STR);
			$updateMovie -> bindParam(2, $year, PDO::PARAM_INT);
			$updateMovie -> bindParam(3, $genre, PDO::PARAM_STR);
			$updateMovie -> bindParam(4, $actors, PDO::PARAM_STR);
			$updateMovie -> bindParam(5, $media, PDO::PARAM_STR);
			$updateMovie -> bindParam(6, $summary, PDO::PARAM_STR);
			$updateMovie -> bindParam(7, $movieid, PDO::PARAM_INT);
			$updateMovie -> execute();
			
			$selectMovie = $pdo -> prepare("SELECT directors, writers, runtime, tmdbid FROM movies WHERE id = ?");
			$selectMovie -> bindParam(1, $movieid, PDO::PARAM_INT);
			$selectMovie -> execute();
			$fetchMovie = $selectMovie -> fetch();
			
			$directors = htmlentities($fetchMovie[0]);
			$writers = $fetchMovie[1];
			$runtime = $fetchMovie[2];
			$tmdbid = $fetchMovie[3];
			
			$pdo = null;
			header("Content-Type: text/xml");
			echo "<?xml version=\"1.0\"  encoding=\"UTF-8\"?>";
			echo "<movie title=\"" . $movietitle . "\" year=\"" . $year . "\" genre=\"" . $genre . "\" actors=\"" . $actors . "\" media=\"" . $media . "\" summary = \"" . $summary . "\" directors=\"" . $directors . "\" writers=\"" . $writers . "\" runtime=\"" . $runtime . "\" movieid=\"" . $movieid . "\" tmdbid=\"" . $tmdbid . "\"></movie>";
		} catch (PDOException $error) {
			die("There was a problem saving the details. Please try again.");
		}
		break;

	case 'playMovie' :
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
		}
		// get currently selected client
		try {
			$pdo = new PDO($dsn, $db_username, $db_password);
			$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$getClient = $pdo -> prepare("SELECT * FROM clients WHERE selected = 1");
			$getClient -> execute();
			$fetchClient = $getClient -> fetch();
			$pdo = null;
		} catch (PDOException $pdo) {
			die("There was a problem getting the client details. Please try again.");
		}

		$clientIP = $fetchClient["ipAddr"];
		$clientPort = $fetchClient["port"];
		$clientID = $fetchClient["clientID"];

		$ch = curl_init("http://$clientIP:$clientPort/player/playback/playMedia?key=%2Flibrary%2Fmetadata%2F$id&offset=0&X-Plex-Client-Identifier=$clientID&machineIdentifier=$plexServID&address=$plexIP&port=$plexPort&protocol=http&path=http%3A%2F%2F$plexIP%3A$plexPort%2Flibrary%2Fmetadata%F$id");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);
		header("Content-Type: text/xml");
		if (curl_errno($ch)) {
			echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
			echo "<status code=\"0\"></status>";
		} else {
			echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
			echo "<status code=\"1\"></status>";
		}
		curl_close($ch);
		break;

	case "playPauseMovie" :
		$currentMode = $_GET['mode'];
		include_once ("../Client.php");
		$client = new Client();
		// get the current client
		$fetchClient = $client -> GetSelectedClient();

		$client -> SetIPAddress($fetchClient["ipAddr"]);
		$client -> setPort($fetchClient["port"]);

		$client -> PlayPause($currentMode);

		break;

	case "stopMovie" :
		include_once ("../Client.php");
		$client = new Client();
		// get the current client
		$fetchClient = $client -> GetSelectedClient();

		$client -> SetIPAddress($fetchClient["ipAddr"]);
		$client -> setPort($fetchClient["port"]);

		$client -> Stop();

		break;

	case "stepBack" :
		include_once ("../Client.php");
		$client = new Client();
		// get the current client
		$fetchClient = $client -> GetSelectedClient();

		$client -> SetIPAddress($fetchClient["ipAddr"]);
		$client -> setPort($fetchClient["port"]);

		$client -> StepBack();

		break;

	case "stepForward" :
		include_once ("../Client.php");
		$client = new Client();
		// get the current client
		$fetchClient = $client -> GetSelectedClient();

		$client -> SetIPAddress($fetchClient["ipAddr"]);
		$client -> setPort($fetchClient["port"]);

		$client -> StepForward();

		break;
	case "search" :
		if (isset($_GET['query'])) {
			$query = $_GET['query'];
			$pdo = new PDO($dsn, $db_username, $db_password);
			$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$searchQuery = $pdo -> prepare("SELECT id, year, displaytitle, actors FROM movies WHERE LOWER(displaytitle) LIKE CONCAT('%', LOWER(:query), '%') OR LOWER(movietitle) LIKE CONCAT('%', LOWER(:query), '%') OR LOWER(actors) LIKE CONCAT ('%', LOWER(:query), '%') ORDER BY movietitle");
			$searchQuery -> bindParam(':query', $query);
			$searchQuery -> execute();
			$getTemplate = $pdo -> prepare("SELECT displaytype FROM configuration");
			$getTemplate -> execute();
			$fetchTemplate = $getTemplate -> fetch();
			header("Content-Type: text/xml");
			echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
			echo "<movies displaytype=\"" . $fetchTemplate[0] . "\">";
			while ($fetchQuery = $searchQuery -> fetch()) {
				echo "<film id=\"" . $fetchQuery[0] . "\" movietitle=\"" . $fetchQuery[2] . "\" year=\"" . $fetchQuery[1] . "\" actors=\"" . htmlspecialchars($fetchQuery[3]) . "\"></film>";
			}
			echo "</movies>";
		} else {
			die("You should not be here");
		}
		$pdo = null;
		break;

	case "addmovie" :
		$movietitle = $_POST['movietitle'];
		$displaytitle = $_POST['displaytitle'];
		$year = $_POST['year'];
		$genre = $_POST['genre'];
		$actors = $_POST['actors'];
		$summary = $_POST['summary'];
		$media = $_POST['media'];
		$writers = $_POST['writers'];
		$runtime = $_POST['runtime'];
		$directors = $_POST['directors'];
		$imagepath = $_POST['imagepath'];
		$tmdbid = $_POST['tmdbid'];
		
		// Thank you to "Ing" at http://stackoverflow.com/questions/10054818/convert-accented-characters-to-their-plain-ascii-equivalents for this below
		$normalizeChars = array(
			'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
			'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
			'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
			'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
			'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
			'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
			'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f', "ć"=>'c',
			'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
		);
		
		$actors = strtr($actors, $normalizeChars);
		$directors = strtr($directors, $normalizeChars);
		$writers = strtr($writers, $normalizeChars);

		try {
			$pdo = new PDO($dsn, $db_username, $db_password);
			$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// check for duplicates first
			$checkQuery = $pdo->prepare("SELECT id FROM movies WHERE movietitle = :query OR displaytitle = :query1;");
			$checkQuery->bindParam(':query', $movietitle);
			$checkQuery->bindParam(':query1', $displaytitle);
			$checkQuery->execute();
			$fetchQuery = $checkQuery->fetch();
			
			if($fetchQuery[0] > 0) {
				header("Content-Type: text/xml");
				echo "<?xml version=\"1.0\"  encoding=\"UTF-8\"?>";
				echo "<result code=\"0\"></result>";
			} else {			
				$insertQuery = $pdo->prepare("INSERT INTO movies (genre, movietitle, displaytitle, year, actors, media, plexSummary, runtime, writers, directors, tmdbid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
				$insertQuery->bindParam(1, $genre); // bind genre
				$insertQuery->bindParam(2, $movietitle); // bind movietitle
				$insertQuery->bindParam(3, $displaytitle); // bind displaytitle
				$insertQuery->bindParam(4, $year); // bind year
				$insertQuery->bindParam(5, $actors, PDO::PARAM_STR); // bind actors
				$insertQuery->bindParam(6, $media); // bind media
				$insertQuery->bindParam(7, $summary); // bind summary
				$insertQuery->bindParam(8, $runtime); // bind runtime
				$insertQuery->bindParam(9, $writers); // bind writers
				$insertQuery->bindParam(10, $directors); // bind directors
				$insertQuery->bindParam(11, $tmdbid);
				$insertQuery->execute();
				$lastId = $pdo -> lastInsertId();
				
				// save poster
				$img = "../images/posters/" . $lastId . "_Poster.png";
				file_put_contents($img, file_get_contents($imagepath));
				
				header("Content-Type: text/xml");
				echo "<?xml version=\"1.0\"  encoding=\"UTF-8\"?>";
				echo "<result code=\"1\"></result>";
			}
			$pdo = null;
		} catch (PDOException $ex) {
			echo "PDOException occured. $ex";
		}
		break;
}
?>