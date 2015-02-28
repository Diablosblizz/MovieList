<?php
include ("configuration.php");

try {
	$pdo = new PDO($dsn, $db_username, $db_password);
	$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$selectGenres = $pdo -> prepare("SELECT maingenres FROM configuration");
	$selectGenres -> execute();
	$fetchGenres = $selectGenres -> fetch();

	$genreArray = explode(',', $fetchGenres[0]);

	$advChecked = "";
	// Adventure
	$docChecked = "";
	// Documentary
	$actChecked = "";
	// Action
	$famChecked = "";
	// Family
	$clasChecked = "";
	// Classic
	$horChecked = "";
	// Horror
	$comChecked = "";
	// Comedy
	$musChecked = "";
	// Music
	$draChecked = "";
	// Drama
	$romChecked = "";
	// Romance
	$animChecked = "";
	// Animation
	$bioChecked = "";
	// Biography
	$spoChecked = "";
	// Sport
	$criChecked = "";
	// Crime
	$warChecked = "";
	// War
	$mysChecked = "";
	// Mystery
	$fanChecked = "";
	// Fantasy
	$sciChecked = "";
	// Sci-Fi
	$histChecked = "";
	// History
	$westChecked = "";
	// Western
	$thrChecked = "";
	// Thriller

	foreach ($genreArray as $genre) {
		if ($genre == "adventure") {
			$advChecked = "checked";
		}

		if ($genre == "documentary") {
			$docChecked = "checked";
		}

		if ($genre == "action") {
			$actChecked = "checked";
		}

		if ($genre == "family") {
			$famChecked = "checked";
		}

		if ($genre == "classic") {
			$clasChecked = "checked";
		}

		if ($genre == "horror") {
			$horChecked = "checked";
		}

		if ($genre == "comedy") {
			$comChecked = "checked";
		}

		if ($genre == "music") {
			$musChecked = "checked";
		}

		if ($genre == "drama") {
			$draChecked = "checked";
		}

		if ($genre == "romance") {
			$romChecked = "checked";
		}

		if ($genre == "animation") {
			$animChecked = "checked";
		}

		if ($genre == "biography") {
			$bioChecked = "checked";
		}

		if ($genre == "sport") {
			$spoChecked = "checked";
		}

		if ($genre == "crime") {
			$criChecked = "checked";
		}

		if ($genre == "war") {
			$warChecked = "checked";
		}

		if ($genre == "mystery") {
			$mysChecked = "checked";
		}

		if ($genre == "fantasy") {
			$fanChecked = "checked";
		}

		if ($genre == "scifi") {
			$sciChecked = "checked";
		}

		if ($genre == "history") {
			$histChecked = "checked";
		}

		if ($genre == "western") {
			$westChecked = "checked";
		}

		if ($genre == "thriller") {
			$thrChecked = "checked";
		}
	}

	$selectClients = $pdo -> prepare("SELECT * FROM clients");
	$selectClients -> execute();
	
	$selectLayout = $pdo -> prepare("SELECT displaytype FROM configuration");
	$selectLayout -> execute();
	$fetchLayout = $selectLayout -> fetch();
	
	$genreEdit = "";
	$genreText = "";
	
	if($fetchLayout[0] != 1) {
		$genreEdit = "disabled";
		$genreText = "This is currently disabled as you do not have the \"Genre Based\" layout selected. Please select that layout if you wish to make changes.";
	}
	
	$pdo = null;
} catch (PDOException $pdo) {
	echo "An error has occured.";
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Settings</title>
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link href="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.css" rel="stylesheet" type="text/css" />
		<script src="http://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
		<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.js" type="text/javascript"></script>
		<script src="javascript/settings.js"></script>
		<link rel="apple-touch-icon" href="images/iPhone-Icon.png" />

	</head>

	<body>
		<div data-role="page" id="settings" data-overlay-theme="b" data-theme="b" data-dom-cache="false" data-close-btn="none">
			<div data-role="header">
				<a href="#" data-role="button" data-icon="delete" data-iconpos="notext" onclick="javascript: closeSettings();"></a>
				<a href="#" id="settingsSave" data-role="button" data-icon="check" class="ui-btn-right" onclick="javascript: submitForm();">Save</a>
				<h1>Settings</h1>
			</div>
            <div id="settings-all">
            <form id="settings-form">
			<div data-role="collapsible" style="padding-right: 10px; padding-left: 10px;">
				<h3>Main Genres</h3>
					<span style="font-size: 12px;">Note: This option sets which genres are shown on the main page by default. <?=$genreText;?></span>
					<div data-role="content" id="genre">						
							<div class="ui-grid-a">
								<div class="ui-block-a">
									<label>
										<input type="checkbox" name="adventure" <?=$advChecked; ?> <?=$genreEdit; ?>/>
										Adventure</label>
									<label>
										<input type="checkbox" name="action"  <?=$actChecked; ?> <?=$genreEdit; ?>/>
										Action</label>
									<label>
										<input type="checkbox" name="classic" <?=$clasChecked; ?> <?=$genreEdit; ?>/>
										Classic</label>
									<label>
										<input type="checkbox" name="comedy" <?=$comChecked; ?> <?=$genreEdit; ?>/>
										Comedy</label>
									<label>
										<input type="checkbox" name="drama" <?=$draChecked; ?> <?=$genreEdit; ?>/>
										Drama</label>
									<label>
										<input type="checkbox" name="animation" <?=$animChecked; ?> <?=$genreEdit; ?>/>
										Animation</label>
									<label>
										<input type="checkbox" name="sport" <?=$spoChecked; ?> <?=$genreEdit; ?>/>
										Sport</label>
									<label>
										<input type="checkbox" name="war" <?=$warChecked; ?> <?=$genreEdit; ?>/>
										War</label>
									<label>
										<input type="checkbox" name="fantasy" <?=$fanChecked; ?> <?=$genreEdit; ?>/>
										Fantasy</label>
									<label>
										<input type="checkbox" name="history" <?=$histChecked; ?> <?=$genreEdit; ?>/>
										History</label>
									<label>
										<input type="checkbox" name="thriller" <?=$thrChecked; ?> <?=$genreEdit; ?>/>
										Thriller</label>
								</div>
								<div class="ui-block-b">
									<label>
										<input type="checkbox" name="documentary" <?=$docChecked; ?>  <?=$genreEdit; ?>/>
										Documentary</label>
									<label>
										<input type="checkbox" name="family" <?=$famChecked; ?> <?=$genreEdit; ?>/>
										Family</label>
									<label>
										<input type="checkbox" name="horror" <?=$horChecked; ?> <?=$genreEdit; ?>/>
										Horror</label>
									<label>
										<input type="checkbox" name="music" <?=$musChecked; ?> <?=$genreEdit; ?>/>
										Music</label>
									<label>
										<input type="checkbox" name="romance" <?=$romChecked; ?> <?=$genreEdit; ?>/>
										Romance</label>
									<label>
										<input type="checkbox" name="biography" <?=$bioChecked; ?> <?=$genreEdit; ?>/>
										Biography</label>
									<label>
										<input type="checkbox" name="crime" <?=$criChecked; ?> <?=$genreEdit; ?>/>
										Crime</label>
									<label>
										<input type="checkbox" name="mystery" <?=$mysChecked; ?> <?=$genreEdit; ?>/>
										Mystery</label>
									<label>
										<input type="checkbox" name="scifi" <?=$sciChecked; ?> <?=$genreEdit; ?>/>
										Sci-Fi</label>
									<label>
										<input type="checkbox" name="western" <?=$westChecked; ?> <?=$genreEdit; ?>/>
										Western</label>
								</div>
							</div>

						
					</div>
			</div>
			<div data-role="collapsible" style="padding-right: 10px; padding-left: 10px;">
				<span style="font-size: 12px;">Note: This option chooses which client to send the play command to.</span>
				<h3>Default Client</h3>
						<?php
						while ($fetchClients = $selectClients -> fetch()) {
							if ($fetchClients["selected"] == 1) {
								echo "<label>";
								echo "<input type=\"radio\" name=\"client\" value=\"$fetchClients[id]\" checked>";
								echo "$fetchClients[name]";
								echo "</label>";
							} else {
								echo "<label>";
								echo "<input type=\"radio\" name=\"client\" value=\"$fetchClients[id]\">";
								echo "$fetchClients[name]";
								echo "</label>";
							}
						}
						?>
			</div>
			<div data-role="collapsible" style="padding-bottom: 20px; padding-right: 10px; padding-left: 10px;">
				<span style="font-size: 12px;">Note: This option chooses what Layout to output the movies in.</span>
				<h3>Layout</h3>
					<label>
						<input type="radio" name="layout" value="1" <?php if($fetchLayout[0] == 1) { echo "checked"; } ?>>Genre Based
					</label>
					<label>
						<input type="radio" name="layout" value="2" <?php if($fetchLayout[0] == 2) { echo "checked"; } ?>>Name Based
					</label>
					<label>
						<input type="radio" name="layout" value="3" <?php if($fetchLayout[0] == 3) { echo "checked"; } ?>>Classic
					</label>	
			</div>
			</form>
        </div>
		</div>
	</body>
</html>