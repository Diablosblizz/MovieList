<?php
include ("configuration.php");
include ("Plex.php");
include ("Client.php");
$dir = getcwd();

$pdo = new PDO($dsn, $db_username, $db_password);
$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$plex = new Plex($plexIP . ':' . $plexPort);

$selectGenres = $pdo -> prepare("SELECT maingenres FROM configuration");
$selectGenres -> execute();
$fetchGenres = $selectGenres -> fetch();
$genreArray = explode(',', $fetchGenres[0]);
array_pop($genreArray);

$amountPlex = $pdo -> prepare("SELECT * FROM configuration");
$amountPlex -> execute();
$fetchPlex = $amountPlex -> fetch();
$dbPlexMovies = $fetchPlex[0];

$dbLastUpdate = $fetchPlex[1];

$displayWarning = false;

/* Update the current list of clients */
$plexOnline = $plex -> IsPlexReachable($plexIP, $plexPort);
if($plexOnline == 1) {
$actPlexMovies = $plex -> GetTotalMovies();
$plexClients = $plex -> GetClients();
if (is_array($plexClients)) {
	for ($i = 0; $i < count($plexClients); $i++) {
		try {
			// First, check for duplicates via the client ID
			$prepAmount = $pdo -> prepare("SELECT COUNT(*) FROM clients WHERE clientID = ?");
			$prepAmount -> bindParam(1, $plexClients[$i][1]);
			$prepAmount -> execute();
			$fetchAmount = $prepAmount -> fetch();
			if ($fetchAmount[0] == 0) {
				// Insert the Client into the Database
				$insertClient = $pdo -> prepare("INSERT INTO clients (name, clientID, ipAddr, port) VALUES (?, ?, ?, ?)");
				$insertClient -> bindParam(1, $plexClients[$i][0]);
				$insertClient -> bindParam(2, $plexClients[$i][1]);
				$insertClient -> bindParam(3, $plexClients[$i][2]);
				$insertClient -> bindParam(4, $plexClients[$i][3]);
				$insertClient -> execute();
				// We now know this client
			} else {
				// Update the Client in the Database

				// There may be a change in information (ip address, port), but let us check first
				$checkClient = $pdo -> prepare("SELECT ipAddr, port FROM clients WHERE clientID = ?");
				$checkClient -> bindParam(1, $plexClients[$i][1]);
				$checkClient -> execute();
				$fetchClient = $checkClient -> fetch();

				if ($fetchClient["ipAddr"] != $plexClients[$i][2]) {
					// IP address has changed
					$updateClient = $pdo -> prepare("UPDATE clients SET ipAddr = ? WHERE clientID = ?");
					$updateClient -> bindParam(1, $plexClients[$i][2]);
					$updateClient -> bindParam(2, $plexClients[$i][1]);
					$updateClient -> execute();
				} else if ($fetchClient["port"] != $plexClients[$i][3]) {
					// Port has changed
					$updateClient = $pdo -> prepare("UPDATE clients SET port = ? WHERE clientID = ?");
					$updateClient -> bindParam(1, $plexClients[$i][3]);
					$updateClient -> bindParam(2, $plexClients[$i][1]);
					$updateClient -> execute();
				}
			}
		} catch (PDOException $pdo) {
			echo "There was a problem with the SQL. $pdo";
		}
	}
}

if ($actPlexMovies > $dbPlexMovies) {
	try {
		$plexMovies = $plex -> GetMovieTitles();
		for ($i = 0; $i < count($plexMovies); $i++) {
			if ($plexMovies[$i][3] > $dbLastUpdate) {
				$checkDuplicate = $pdo -> prepare("SELECT COUNT(id), id FROM movies WHERE movietitle = ? OR displaytitle = ?");
				$checkDuplicate -> bindParam(1, $plexMovies[$i][0]);
				$checkDuplicate -> bindParam(2, $plexMovies[$i][0]);
				$checkDuplicate -> execute();
				$fetchDuplicate = $checkDuplicate -> fetch();
				if ($fetchDuplicate[0] <= 0) {
					$type = "Plex";
					$insert = $pdo -> prepare("INSERT INTO movies (movietitle, displaytitle, year, media, plexPoster, plexSummary, actors, genre, plexMediaID, writers, runtime, directors, tmdbid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
					$insert -> bindParam(1, $plexMovies[$i][0]);
					// movietitle
					$insert -> bindParam(2, $plexMovies[$i][0]);
					// displaytitle
					$insert -> bindParam(3, $plexMovies[$i][2]);
					// year
					$insert -> bindParam(4, $type);
					// media
					$insert -> bindParam(5, $plexMovies[$i][4]);
					// plexPoster
					$insert -> bindParam(6, $plexMovies[$i][1]);
					// summary - it is 1
					$actors = $plexMovies[$i][5];
					$actors = substr($actors, 0, -1);
					$insert -> bindParam(7, $actors);
					// actors
					$genres = $plexMovies[$i][6];
					$genres = substr($genres, 0, -1);
					$insert -> bindParam(8, $genres);
					// genres
					$writers = $plexMovies[$i][8];
					$writers = substr($writers, 0, -1);
					$insert -> bindParam(10, $writers);
                    // writers
					$insert -> bindParam(9, $plexMovies[$i][7]);
					// plexMediaID
                    $insert -> bindParam(11, $plexMovies[$i][9]);
					$directors = $plexMovies[$i][10];
					$directors = substr($directors, 0, -1);
					$insert -> bindParam(12, $directors);
					
					$tmdbid = $plex -> GetTMDBId($plexMovies[$i][0], $plexMovies[$i][2]);
					$insert -> bindParam(13, $tmdbid);
					
					$insert -> execute();
					$lastId = $pdo -> lastInsertId();
					
				} else {
					// an entry already exists in the database, grabbing plex information
					// this will always replace metadata that is pre-existing in the DB
					// I chose this because Plex metadata is more accurate then oMDB.
					$currentDbId = $fetchDuplicate[1];
					
					$grabMedia = $pdo->prepare("SELECT media FROM movies WHERE id = ?");
					$grabMedia->bindParam(1, $currentDbId);
					$grabMedia->execute();
					$fetchMedia = $grabMedia->fetch();
					
					$currentMedia = $fetchMedia[0];
					$newMedia = $currentMedia . " / Plex";
					
					$update = $pdo->prepare("UPDATE `movies` SET plexPoster = ?, plexSummary = ?, plexMediaID = ?, writers = ?, runtime = ?, media = ?, actors = ?, genre = ?, directors = ? WHERE id = ?");
					
					$update->bindParam(1, $plexMovies[$i][4], PDO::PARAM_STR);
					$update->bindParam(2, $plexMovies[$i][1], PDO::PARAM_STR);
					$update->bindParam(3, $plexMovies[$i][7], PDO::PARAM_INT);
					$writers = $plexMovies[$i][8];
					$writers = substr($writers, 0, -1);
					$update -> bindParam(4, $writers, PDO::PARAM_STR);
					$update->bindParam(5, $plexMovies[$i][9]);
					$update->bindParam(6, $newMedia);
					$actors = $plexMovies[$i][5];
					$actors = substr($actors, 0, -1);
					$update -> bindParam(7, $actors);
					$genres = $plexMovies[$i][6];
					$genres = substr($genres, 0, -1);
					$update -> bindParam(8, $genres);
					$directors = $plexMovies[$i][10];
					$directors = substr($directors, 0, -1);
					$update -> bindParam(9, $directors);
					$update->bindParam(10, $currentDbId);
					$update->execute();
				}
			}
		}
		$date = new DateTime("now");
		$epochTime = $date -> format('U');
		$upLastUpdate = $pdo -> prepare("UPDATE configuration SET lastupdate = ?, plexMovies = ?");
		$upLastUpdate -> bindParam(1, $epochTime);
		$upLastUpdate -> bindParam(2, $actPlexMovies);
		$upLastUpdate -> execute();
	} catch (PDOException $pdo) {
		echo "There was an SQL Exception. $pdo";
	}
}
} else { $displayWarning = true; } ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title> My Movies </title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no" />
		<script src="http://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
		<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.js" type="text/javascript"></script>
		<link href="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.css" rel="stylesheet" type="text/css" />
		<link rel="apple-touch-icon" href="images/iPhone-Icon.png" />
		<script type="application/javascript" src="javascript/settings.js" charset="utf-8"></script>
		<script type="application/javascript" src="javascript/movieData.js" charset="utf-8"></script>
		<script type="application/javascript" src="javascript/search.js" charset="utf-8"></script>
		<script type="application/javascript" src="javascript/editMovie.js" charset="utf-8"></script>
		<script type="application/javascript" src="javascript/addMovie.js" charset="utf-8"></script>
		<script type="application/javascript" src="javascript/search.js" charset="utf-8"></script>
		<script type="application/javascript" src="javascript/movieTrailers.js" charset="utf-8"></script>
		<script type="application/javascript" src="javascript/movieControls.js" charset="utf-8"></script>
		<script type="application/javascript" src="javascript/Chart.js" charset="utf-8"></script>
		<style>
			body {
				-webkit-transform: translateZ(0);
			}
		</style>

	</head>
	<body>
		<noscript>
			<b>This website is only capable of functioning with Javascript enabled. Please enable Javascript in your browser settings and refresh the page.</b>
		</noscript>
		<div data-role="page" id="page" data-theme="b" data-dom-cache="false">
			<div data-role="panel" id="sidebar">
				<a href="addmovie.php" data-role="button" data-icon="plus" data-rel="dialog" data-transition="pop">Add Movie</a>
				<form id="searchform" name="searchform" method="POST" data-ajax="false" onsubmit="submitSearch('form'); return false;">
					<div id="search-div">
						<a href="Javascript:void(0);" id="search-input" data-role="button" data-icon="search" data-rel="dialog" data-transition="pop" onclick="javascript: changeSearch();">Search</a>
					</div>
				</form>
				<a href="settings.php" data-role="button" data-icon="gear" data-rel="dialog" data-transition="pop">Settings</a>
				<br />
				<p>
					Statistics (tap chart for details)
					<br />
					<br />
					<?php
					$selectPlex = $pdo -> query("SELECT COUNT(*) FROM movies WHERE media LIKE '%Plex%'");
					$selectPlex -> execute();
					$fetchPlex = $selectPlex -> fetch();
					$amountPlex = $fetchPlex[0];

					$selectDVD = $pdo -> query("SELECT COUNT(*) FROM movies WHERE media LIKE '%DVD%'");
					$selectDVD -> execute();
					$fetchDVD = $selectDVD -> fetch();
					$amountDVD = $fetchDVD[0];

					$selectBlu = $pdo -> query("SELECT COUNT(*) FROM movies WHERE media LIKE '%Blu-Ray%'");
					$selectBlu -> execute();
					$fetchBlu = $selectBlu -> fetch();
					$amountBlu = $fetchBlu[0];

					$selectJDB = $pdo -> query("SELECT COUNT(*) FROM movies WHERE media = 'Blu-ray' OR media = 'DVD' OR media = 'DVD / Plex' OR media = 'Blu-ray / Plex'");
					$selectJDB -> execute();
					$fetchJDB = $selectJDB -> fetch();
					$amountJDB = $fetchJDB[0];

					$selectDDB = $pdo -> query("SELECT count(*)*2 FROM movies WHERE media = 'DVD / Blu-ray' OR media = 'DVD / Blu-ray / Plex'");
					$selectDDB -> execute();
					$fetchDDB = $selectDDB -> fetch();
					$amountDDB = $fetchDDB[0];

					$totalDiscs = $amountJDB + $amountDDB;

					$avgBluRay = $amountBlu * 25;
					$avgDVD = $amountDVD * 4.7;

					$total = $amountPlex + $amountDVD + $amountBlu;
					$totalP = round(($amountPlex / $total) * 100);
					$totalD = round(($amountDVD / $total) * 100);
					$totalB = round(($amountBlu / $total) * 100);

					$query = $pdo -> query("SELECT runtime FROM movies");
					$query -> execute();
					$fetch = $query -> fetchAll();

					$total = 0;

					for ($i = 0; $i < count($fetch); $i++) {
						$currentNumber = $fetch[$i][0];
						if ($currentNumber < 1000) {
							$currentNumber = ($currentNumber * 1000) * 60;
						}
						$total += $currentNumber;
					}

					$queryPSize = $pdo -> query("SELECT size FROM movies WHERE media LIKE '%Plex%'");
					$queryPSize -> execute();
					$sizeArray = array();
					while ($fetch = $queryPSize -> fetch()) {
						array_push($sizeArray, $fetch[0]);
					}

					$totalGB = round(array_sum($sizeArray) / 1024, 2);
					$averageSize = round($totalGB / $amountPlex, 2);
					?>
					<table style="width: 100%; font-size: 12px;">
						<tr>
							<td style="vertical-align: top; width: 40%;">Your Media</td>
							<td style="width: 60%;"><?php echo round(($total / 1000) / 60) . " minutes of media
							<br />
							" . round((($total / 1000) / 60) / 60) . " hours of media
							<br />
							" . round(((($total / 1000) / 60) / 60) / 24) . " days of media";
							?></td>
						</tr>
						<tr>
							<td style="vertical-align: top;">Storage (Plex)</td>
							<td><?=$totalGB; ?>GB Total
							<br />
							<?=$averageSize; ?>GB Avg File Size</td>
						</tr>
						<tr>
							<td style="vertical-align: top;">Storage (Disc)</td>
							<td><?=$totalDiscs; ?> Discs total
							<br />
							<?=$avgBluRay; ?>GB Blu-ray Storage
							<br />
							<?=$avgDVD; ?>GB DVD Storage (est.)</td>
						</tr>
					</table>
					<div id="chartDiv" style="text-align: center">
						<canvas id="mediaChart" width="100" height="200" style="text-align: center"></canvas>
						<table style="width: 100%">
							<tr>
								<td style="background-color: #E8A50B; width: 33%;">Plex</td>
								<td style="background-color: #000000; width: 33%;">DVD</td>
								<td style="background-color: #0095D7; width: 33%;">Blu-Ray</td>
							</tr>
						</table>
					</div>
					<script>
												var data = [{
						value: <?=$totalP; ?>,
						color: "#E8A50B",
						label: "Plex"
						},
						{
						value: <?=$totalB; ?>,
						color: "#0095D7",
						label: "Blu-Ray"
						},
						{
						value: <?=$totalD; ?>
							, color: "#000000",
							label: "DVD"
							}
							]

							var options = {
								animation : false
							};

							//Get the context of the canvas element we want to select
							var c = $('#mediaChart');
							var ct = c.get(0).getContext('2d');
							var ctx = document.getElementById("mediaChart").getContext("2d");
							/*************************************************************************/
							myNewChart = new Chart(ct).Pie(data, options);
					</script>
			</div>
			<div data-role="header" id="header" data-position="fixed">
				<a href="#sidebar" data-icon="bars">Sidebar</a>
				<?php if($displayWarning) { ?>
				<a href="#plexAlert" data-icon="alert" data-rel="popup" class="ui-btn-right">Alert</a>
				<div data-role="popup" id="plexAlert">
					<p>Warning: Plex is not reachable. Please ensure that the configuration is set correctly and that the server is reachable.</p>
				</div>
				<?php } ?>
				<h1> My Movies </h1>
			</div>
			<?php
			if($plexOnline == 1) {
			$client = new Client();
			if($client->GetIsContentPlaying($plexIP . ':' . $plexPort) == true) {
			$currentlyPlaying = $client->GetCurrentPlayback($plexIP . ':' . $plexPort);

			$getDBInfo = $pdo -> prepare("SELECT * FROM movies WHERE movietitle = ? OR plexMediaID = ?");
			$getDBInfo -> bindParam(1, $currentlyPlaying[0]);
			$getDBInfo -> bindParam(2, $currentlyPlaying[1]);
			$getDBInfo -> execute();

			$fetchDBInfo = $getDBInfo -> fetch();
			?>
			<div data-role="content" id="currentPlayback" name="currentPlayback">
				<h2 style="font-size: 15px; margin-top: -5px;">Current Playback:</h2>
				<ul data-role="listview" data-inset="true" style="margin-top: -5px;">
					<li>
						<a href="viewmovie.php?movieid=<?=$fetchDBInfo[0]; ?>&displayPlayBack" data-rel="dialog" data-transition="pop"> <img src="images/posters/<?=$fetchDBInfo[0]; ?>_Poster.png" style="width: 54px; height: 100%;"> <h2><?=$currentlyPlaying[0]; ?></h2>
						<p>
							<?=$fetchDBInfo["actors"]; ?>
						</p> </a>
					</li>
				</ul>
			</div>
			<?php
			}
			}
			$getDisplay = $pdo -> query("SELECT displaytype FROM configuration");
			$getDisplay -> execute();
			$fetchDisplay = $getDisplay -> fetch();

			if($fetchDisplay[0] == 1) {
			// sort by genre
			if(count($genreArray) < 1) {
			echo "
			<div data-role=\"content\">You do not have any default genres selected. Please tap on settings and choose some!</div>";
			} else {
			$i = 1;
			foreach($genreArray as $genre) {
			?>
				<div data-role="content" id="index-movies-<?=$i; ?>">
					<h2 style="font-size: 15px; margin-top: -5px"> <?=ucfirst($genre); ?> </h2>
					<div style="overflow:  scroll; overflow-y: hidden; -webkit-overflow-scrolling: touch;">
						<?php
						try {
							$selectMovies = $pdo -> prepare("SELECT id, displaytitle, plexPoster, year, movietitle FROM movies WHERE LOWER(genre) LIKE CONCAT('%', ?, '%') ORDER BY displaytitle");
							$selectMovies -> bindParam(1, $genre);
							$selectMovies -> execute();
							$getAmount = $pdo -> prepare("SELECT COUNT(*) FROM movies WHERE UPPER(genre) LIKE CONCAT('%', ?, '%')");
							$getAmount -> bindParam(1, $genre);
							$getAmount -> execute();
							$fetchAmount = $getAmount -> fetch();
							$size = $fetchAmount[0] * 111;
							if ($size == 0) {
								$size = 500;
							}
						} catch (PDOException $pdo) {
							echo "There is an error with the SQL. $pdo";
						}
						?>
						<div style="width: <?= $size; ?>px;">
							<?php
							if ($fetchAmount[0] > 0) {
								while ($fetchMovies = $selectMovies -> fetch()) {
									echo "<a href=\"viewmovie.php?movieid=" . $fetchMovies[0] . "\" data-rel=\"dialog\" data-transition=\"pop\"><img src=\"images/posters/" . $fetchMovies[0] . "_Poster.png\" style=\"width: 100px; height: 150px; padding-right: 10px\" alt=\"$fetchMovies[1]\" onerror=\"this.src='image.php?title=" . rawurlencode($fetchMovies[1]) . "&amp;year=" . $fetchMovies[3] . "&amp;id=" . $fetchMovies[0] . "&amp;atitle=" . rawurlencode($fetchMovies[4]) . "'\"></a>";
								}
							} else {
								echo "No movies for this genre.";
							}
							?>
						</div>
					</div>
				</div>
				<?php $i++;
					} }
				?>
			</div>
			<?php } else if($fetchDisplay[0] == 2) { ?>
			<div data-role="content">
				<ul data-role="listview" data-inset="true" data-autodividers="true">
					<?php
					$alphabet = '1,2,3,4,5,6,7,8,9,0,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z';
					$alphaArray = explode(',', $alphabet);
					for ($i = 0; $i < count($alphaArray); $i++) {
						$queryLetter = $pdo -> prepare("SELECT id, displaytitle, actors FROM movies WHERE displaytitle LIKE CONCAT(?, '%') ORDER BY displaytitle");
						// might need to concat
						$queryLetter -> bindParam(1, $alphaArray[$i]);
						$queryLetter -> execute();
						while ($fetchLetter = $queryLetter -> fetch()) {
							echo "<li><a href=\"viewmovie.php?movieid=" . $fetchLetter[0] . "\" data-rel=\"dialog\" data-transition=\"pop\">" . $fetchLetter[1] . "</a></li>";
						}
					}
					?>
				</ul>
			</div>
			<?php
			} else if($fetchDisplay[0] == 3) {
			?>
			<div data-role="content">
				<ul id="movieList" data-role="listview" data-inset="true" data-autodividers="true">
					<?php
					$alphabet = '1,2,3,4,5,6,7,8,9,0,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z';
					$alphaArray = explode(',', $alphabet);
					for ($i = 0; $i < count($alphaArray); $i++) {
						$queryLetter = $pdo -> prepare("SELECT id, displaytitle, actors, year, movietitle FROM movies WHERE displaytitle LIKE CONCAT(?, '%') ORDER BY displaytitle");
						$queryLetter -> bindParam(1, $alphaArray[$i]);
						$queryLetter -> execute();
						while ($fetchLetter = $queryLetter -> fetch()) {
							echo "<li><a href=\"viewmovie.php?movieid=" . $fetchLetter[0] . "\" data-rel=\"dialog\" data-transition=\"pop\"><img src=\"images/posters/" . $fetchLetter[0] . "_Poster.png\" style=\"width: 54px; height: 100%;\" alt=\"" . $fetchLetter[1] . "\"  onerror=\"this.src='image.php?title=" . rawurlencode($fetchLetter[1]) . "&amp;year=" . $fetchLetter[3] . "&amp;id=" . $fetchLetter[0] . "&amp;atitle=" . rawurlencode($fetchLetter[4]) . "'\"><h2>" . $fetchLetter[1] . "</h2><p>" . $fetchLetter[2] . "</p></a></li>";
						}
					}
					?>
				</ul>
			</div>
			<?php } else { die("There is currently no layout selected! Please click on the sidebar for settings."); } ?>
		</div>
	</body>
</html>