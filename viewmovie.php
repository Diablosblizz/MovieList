<?php
/* File: viewmovie.php
 * 
 * Purpose: This file provides the user with the UI to view movie metadata.
 * It also allows the user to control the film with the default set client if
 * the movie is located in Plex's library.
 *
*/
$dir = getcwd();
if(file_exists($dir . "\setup.php")) {
	echo "Please visit the setup script first!";
	die();
} else {
if (isset($_GET['movieid'])) {
    $movieid = $_GET['movieid'];
    include ("configuration.php");
    try {
        $pdo = new PDO($dsn, $db_username, $db_password);
        $stmt = $pdo->prepare('SELECT * FROM `movies` WHERE id = ?');
        $stmt->bindParam(1, $movieid);
        $stmt->execute();
        $row = $stmt->fetch();
		$plot = $row["plexSummary"];
		$director = $row["directors"];
		$actors = $row["actors"];
		$genre = $row["genre"];
		$writer = $row["writers"];
		$runtime = $row["runtime"];
		$tmdbid = $row["tmdbid"];
		
		$jsonUrl = "http://api.themoviedb.org/3/movie/" . $tmdbid . "/videos?api_key=7389b73b4c2e1a9f63dfcda64f2bb1cf";
		$json = file_get_contents($jsonUrl);
		$jsona = json_decode($json, true);
		$showTrailer = false;
		$embedURL = "";
		
		if($jsona["results"][0]["site"] == "YouTube") {
			$showTrailer = true;
			$embedURL = $jsona["results"][0]["key"];
		}
		
		if($runtime > 1000) {
			$runtime = round(($runtime/1000)/60) . ' mins';
		} else {
			$runtime = $runtime . ' mins';
		}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?= $row['displaytitle']; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.css" rel="stylesheet" type="text/css" />
<script src="http://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.js" type="text/javascript"></script>
<link rel="apple-touch-icon" href="images/iPhone-Icon.png" />
</head>

<body>
<div data-role="page" id="dialog-success" data-overlay-theme="b" data-theme="b" data-dom-cache="false">
  <div data-role="header" id="movieheader">
    <h1 id="movieTitle"><?= $row['displaytitle']; ?></h1>
    <a href="javascript: void(0);" id="edit-btn" data-role="button" data-icon="edit" class="ui-btn-right" onclick="javascript: buildEdit(<?=$row['id'];?>);">Edit</a>
 </div>
  <div data-role="content" id="movieinfo">
  	<?php
  	if(strpos($row["media"], "Plex") !== FALSE) { ?>
	<img src="<?= 'images/posters/' . $row["id"] . '_Poster.png'; ?>" style="width: 200px; height: 300px; display: block; margin-left: auto; margin-right: auto;" alt="<?= $row['displaytitle']; ?> Poster"  onerror="this.src='images/img_notfound.png';" onclick="javascript: playMovie(<?=$row['plexMediaID'];?>, <?=$row['id'];?>, '<?=addslashes($row['displaytitle']);?>', '<?=htmlspecialchars($row['actors']);?>');"/><br /><br />
    <?php } else { ?>
    <img src="<?= 'images/posters/' . $row["id"] . '_Poster.png'; ?>" style="width: 200px; height: 300px; display: block; margin-left: auto; margin-right: auto;" alt="<?= $row['displaytitle']; ?> Poster"  onerror="this.src='images/img_notfound.png';"/><br /><br />	
	<?php } ?>
	<div id="media-controls" style="text-align: center;"></div>
	<?php
	if(isset($_GET['displayPlayBack'])) { ?>
		<script>
		showMediaPlayback();
	</script>
	<?php } ?>
   	<div data-role="collapsible">
    	<h4>Plot</h4>
        <p><?=$plot;?></p>
  	</div>
	<?php if($showTrailer == true) { ?>
	<div data-role="collapsible">
		<h4>Trailer</h4>
		<p><iframe style="width: 100%; height: 315px; border: 0px;" height="315" src="//www.youtube.com/embed/<?=$embedURL;?>" allowfullscreen></iframe></p>
	</div>
	<?php } ?>
   	<div data-role="collapsible">
    	<h4>Director(s)</h4>
        <p>
        <?=$director?></p>
  	</div>
    <div data-role="collapsible">
    	<h4>Actors</h4>
        <p><?php
        $actors = $row["actors"];

        $actorsArray = explode(', ', $actors);
        $i = 1;
        $amount = count($actorsArray);
        foreach ($actorsArray as $actor) {
            if ($i != $amount) {
                echo "<a href=\"javascript: submitSearch('url', '" . $actor . "');\">" . $actor . "</a>, ";
            } else {
                echo "<a href=\"javascript: submitSearch('url', '" . $actor . "');\">" . $actor . "</a>";
            }
            $i++;
        }
?></p>
  	</div>
	<div data-role="collapsible">
		<h4>Genre(s)</h4>
		<p><?=$genre;?></p>
	</div>
    <div data-role="collapsible">
    	<h4>Writer(s)</h4>
        <p>
        <?=$writer;?></p>
  	</div>    
    <div data-role="collapsible">
    	<h4>Runtime</h4>
        <p>
        <?=$runtime?></p>
     	</div>
    <div data-role="collapsible">
    	<h4>Media</h4>
        <p><?= $row["media"]; ?></p>
  	</div>
  </div>
  <div data-role="footer">
    <h4>&copy; MovieList 2014</h4>
  </div>
</div>
</body>
</html>
<?php
        $pdo = null;
    }
    catch (PDOException $error) {
        die("There was an error with PDO and MySQL. Please see the error below.<br /><br />" .
            $error->getMessage());
    }
} else {
    header("Location:	index.php");
}
}
?>