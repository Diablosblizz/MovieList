<?php
error_reporting(E_ALL);
 * Note: This file is called when the default poster (movieid_Poster.png) cannot be
 * found. This file serves two purposes as a result:
 *
 * 1. It will double check if that file exists, if not it will try to capture it from OMDb.
 * 2. If number 1 fails, it will result to showing a default image with the movie title
 * as a place holder, so that it does not look out of place.
 *

	$height = (count($lines) * $line_height);
		$height = 150;
	}
}

$dtitle = $_GET['title'];
$atitle = $_GET['atitle'];
$year = $_GET['year'];
$id = $_GET['id'];
$text = <<<END
$dtitle - $year
END;

// double checking that the poster does not exist:

$checkPoster = file_exists("images/posters/" . $id . "_Poster.png");
		$img = "images/posters/" . $id . "_Poster.png";
		file_put_contents($img, file_get_contents($poster));
		header("Content-type: image/jpeg");
		readfile("images/posters/" . $id . "_Poster.png");
	} else {
	}
} else {
?>