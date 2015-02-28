<?php
$dir = getcwd();
if(file_exists($dir . "\setup.php")) {
	echo "Please visit the setup script first!";
	die();
} else {
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title> Add Movie </title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link href="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.css" rel="stylesheet" type="text/css" />
		<script src="http://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
		<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.js" type="text/javascript"></script>
		<script src="javascript/movieData.js" type="text/javascript"></script>
		<link rel="apple-touch-icon" href="images/iPhone-Icon.png" />
	</head>
	<body>
		<div data-role="page" id="page" data-overlay-theme="b" data-theme="b" data-dom-cache="false" data-history="false">
			<div data-role="header">
				<h1> Add Movie </h1>
				<a data-direction="reverse" data-transition="pop" role="button" href="#" data-role="button" data-icon="check" id="saveBtn" class="ui-btn-right ui-link ui-btn ui-icon-check ui-btn-icon-left ui-shadow ui-corner-all" onclick="javascript: addMovie();">Save</a>
			</div>
			<div data-role="content" id="addMoviePage">
				<form method="POST" id="addMovieForm">
				<input type="hidden" name="runtime" id="runtime" />
				<input type="hidden" name="writers" id="writers" />
				<input type="hidden" name="directors" id="directors" />
				<input type="hidden" name="imagepath" id="imagepath" />
				<input type="hidden" name="tmdbid" id="tmdbid" />
					<div data-role="fieldcontain">
						<div data-role="fieldcontain">
							<label for="movietitle"> Movie Title: </label>
							<input type="text" id="movietitle" name="movietitle" placeholder="Exact Movie Title" />
						</div>
						<div data-role="fieldcontain">
							<label for="displaytitle"> Display Title: </label>
							<input type="text" id="displaytitle" name="displaytitle" placeholder="Display Title" />
						</div>
						<div data-role="fieldcontain">
							<input type="button" onclick="javascript: grabMovie(document.getElementById('movietitle').value, document.getElementById('year').value);" value="Grab Movie Info" />
						</div>
						<div data-role="fieldcontain">
							<label for="year"> Year of Release: </label>
							<input type="text" name="year" id="year" placeholder="Year of Release" />
						</div>
						<div data-role="fieldcontain">
							<label for="genre"> Genre: </label>
							<input type="text" name="genre" id="genre" placeholder="Genre" />
						</div>
						<div data-role="fieldcontain">
							<label for="actors"> Actor(s): </label>
							<input type="text" name="actors" id="actors" placeholder="Actor(s)" />
						</div>
						<div data-role="fieldcontain">
							<label for="summary">Summary:</label>
							<textarea id="summary" name="summary" rows="30"></textarea>
						</div>
						<div data-role="fieldcontain">
							<label for="media"> Media: </label>
							<select name="media" id="media">
								<option value="DVD"> DVD </option>
								<option value="Blu-ray"> Blu-ray </option>
								<option value="DVD / Blu-ray"> DVD / Blu-ray </option>
							</select>
						</div>
					</div>
				</form>
			</div>
		</div>
	</body>

</html>
<?php } ?>