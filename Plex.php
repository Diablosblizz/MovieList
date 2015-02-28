<?php
class Plex {

	private $IpAddress = 0;

	function Plex($Ip) {
		$this -> IpAddress = $Ip;
	}

	function GetIpAddress() {
		return $this -> IpAddress;
	}

	function SetIpAddress($Ip) {
		$this -> IpAddress = $Ip;
	}
	
	function IsPlexReachable($ip, $port) {
		$ping = @fsockopen($ip, $port);
		if(!$ping) {
			return 0;
		} else {
			return 1;
		}
	}

	function GetServerID() {
		$xml = simplexml_load_file("http://" . $this -> IpAddress . "/");
		return $xml["machineIdentifier"];
	}

	function GetCorrectSection() {
		$xml = simplexml_load_file("http://" . $this -> IpAddress . "/library/sections/");
		foreach ($xml->Directory as $section) {
			$attributes = $section -> attributes();
			if ($section["title"] == "Movies") {
				return $section["key"];
			}
		}
	}
	
	function GetMovieTitles() {
		$xml = simplexml_load_file("http://" . $this -> IpAddress . "/library/sections/" . $this -> GetCorrectSection() . "/all");
		$movies = array();
		foreach ($xml->Video as $movie) {
			$MovieArray = array();
			array_push($MovieArray, $movie["title"], $movie["summary"]);
			array_push($MovieArray, $movie["year"], $movie["addedAt"]);
			array_push($MovieArray, $movie["thumb"]);
			$actors = "";
			foreach ($movie->Role as $actor) {
				$actors .= $actor["tag"] . ', ';
			}
			$actors = substr($actors, 0, -1);
			$genres = "";
			foreach ($movie->Genre as $genre) {
				$genres .= $genre["tag"] . ', ';
			}
			$genres = substr($genres, 0, -1);
			$writers = "";
			foreach ($movie->Writer as $writer) {
				$writers .= $writer["tag"] . ', ';
			}
			$writers = substr($writers, 0, -1);
			$directors = "";
			foreach($movie->Director as $director) {
				$directors .= $director["tag"] . ', ';
			}
			$directors = substr($directors, 0, -1);
			
			$runtime;

			foreach ($movie->Media as $media) {
				foreach ($media->Part as $part) {
					$runtime = $part['duration'];
				}
			}
			array_push($MovieArray, $actors, $genres);
			array_push($MovieArray, $movie["ratingKey"], $writers);
			array_push($MovieArray, $runtime, $directors);
			array_push($movies, $MovieArray);
		}
		return $movies;
	}

	function GetTotalMovies() {
		$xml = simplexml_load_file("http://" . $this -> IpAddress . "/library/sections/" . $this -> GetCorrectSection() . "/all");
		$movies = 0;
		foreach ($xml->Video as $movie) {
			$movies++;
		}
		return $movies;
	}

	function GetClients() {
		$xml = simplexml_load_file("http://" . $this -> IpAddress . "/clients/");
		if ($xml["size"] > 0) {
			$clients = array();
			foreach ($xml->Server as $client) {
				$clientArray = array();
				array_push($clientArray, $client["name"], $client["machineIdentifier"]);
				array_push($clientArray, $client["address"], $client["port"]);
				array_push($clients, $clientArray);
			}
			return $clients;
		} else {
			return "No Clients";
		}
	}
	
	function GetTMDBId($title, $year) {
		$jsonUrl = "http://api.themoviedb.org/3/search/movie?query=" . urlencode($title) . "&year=" . $year. "&api_key=7389b73b4c2e1a9f63dfcda64f2bb1cf";
		$json = file_get_contents($jsonUrl);
		$jsona = json_decode($json, true);
		$id = $jsona["results"][0]["id"];
		return $id;
	}

	function DownscaleImage($path, $image_name, $new_width, $new_height) {
		$mime = getimagesize($path);

		if ($mime['mime'] == 'image/png') { $src_img = imagecreatefrompng($path);
		}
		if ($mime['mime'] == 'image/jpg') { $src_img = imagecreatefromjpeg($path);
		}
		if ($mime['mime'] == 'image/jpeg') { $src_img = imagecreatefromjpeg($path);
		}
		if ($mime['mime'] == 'image/pjpeg') { $src_img = imagecreatefromjpeg($path);
		}

		$old_x = imageSX($src_img);
		$old_y = imageSY($src_img);

		if ($old_x > $old_y) {
			$thumb_w = $new_width;
			$thumb_h = $old_y / $old_x * $new_width;
		}

		if ($old_x < $old_y) {
			$thumb_w = $old_x / $old_y * $new_height;
			$thumb_h = $new_height;
		}

		if ($old_x == $old_y) {
			$thumb_w = $new_width;
			$thumb_h = $new_height;
		}
		$dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);

		imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);

		// New save location
		$new_thumb_loc = "images/posters/". $image_name;

		if ($mime['mime'] == 'image/png') { $result = imagepng($dst_img, $new_thumb_loc, 8);
		}
		if ($mime['mime'] == 'image/jpg') { $result = imagejpeg($dst_img, $new_thumb_loc, 80);
		}
		if ($mime['mime'] == 'image/jpeg') { $result = imagejpeg($dst_img, $new_thumb_loc, 80);
		}
		if ($mime['mime'] == 'image/pjpeg') { $result = imagejpeg($dst_img, $new_thumb_loc, 80);
		}

		imagedestroy($dst_img);
		imagedestroy($src_img);

		return $result;
	}

}
