<?php
error_reporting(E_ALL);
/* 
 * Note: This file is called when the default poster (movieid_Poster.png) cannot be
 * found. This file serves two purposes as a result:
 *
 * 1. It will double check if that file exists, if not it will try to capture it from OMDb.
 * 2. If number 1 fails, it will result to showing a default image with the movie title
 * as a place holder, so that it does not look out of place.
 *
*/

function text_to_image($text, $image_width, $colour = array(0,244,34), $background = array(0,0,0))
{
    $font = 3;
	//$font = imageloadfont('./FreeSans.ttf');	
    $line_height = 15;
    $padding = 5;
    $text = wordwrap($text, ($image_width/10));
    $lines = explode("\n", $text);
	$height = (count($lines) * $line_height);
	if($height < 150) {
		$height = 150;
	}
    $image = imagecreate($image_width, $height + ($padding * 2));
    $background = imagecolorallocate($image, $background[0], $background[1], $background[2]);
    $colour = imagecolorallocate($image,$colour[0],$colour[1],$colour[2]);
    imagefill($image, 0, 0, $background);
    $i = $padding;
    foreach($lines as $line){
        imagestring($image, $font, $padding, $i, trim($line), $colour);
        $i += $line_height;
    }
    header("Content-type: image/jpeg");
    imagejpeg($image);
    imagedestroy($image);
    exit;
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
if(!$checkPoster) {
	$xml = simplexml_load_file("http://www.omdbapi.com/?t=" . $atitle . "&y=" . $year . "&r=xml");
	if(isset($xml->movie[0])) {
		$attributes = $xml->movie[0]->attributes();
		$poster = $attributes["poster"];
		$img = "images/posters/" . $id . "_Poster.png";
		file_put_contents($img, file_get_contents($poster));
		header("Content-type: image/jpeg");
		readfile("images/posters/" . $id . "_Poster.png");
	} else {
		$image_width = 100; // pixels
		text_to_image($text, $image_width, array(0,0,0), array(255,255,255));
	}
} else {
		header("Content-Type: image/jpeg");
		header("Content-Length: " . filesize("images/posters/" . $id . "_Poster.png"));
		$fp = fopen("images/posters/" . $id . "_Poster.png", "rb");
		fpassthru($fp);
		exit;
	}
?>