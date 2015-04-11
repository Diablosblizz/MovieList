<?php
include_once "configuration.php";

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
		if(@simplexml_load_file("http://" . $this -> IpAddress . "/library/sections/")) {
			$xml = simplexml_load_file("http://" . $this -> IpAddress . "/library/sections/");
		} else {
			$authToken = $this -> GetAuthToken();
			$curl = curl_init();
			
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_URL => 'http://' . $this -> IpAddress . '/library/sections/',
				CURLOPT_HTTPHEADER => array(
					'Content-Length: 0',
					'X-Plex-Client-Identifier: movielist',
					'X-Plex-Token: ' . $authToken
				)
			));
			
			$response = curl_exec($curl);
			$xml = simplexml_load_string($response);
			curl_close($curl);
		}
		foreach ($xml->Directory as $section) {
			$attributes = $section -> attributes();
			if ($section["title"] == "Movies") {
				return $section["key"];
			}
		}
	}
	
	function GetMovieTitles() {
		if(@simplexml_load_file("http://" . $this -> IpAddress . "/library/sections/" . $this -> GetCorrectSection() . "/all")) {
			$xml = simplexml_load_file("http://" . $this -> IpAddress . "/library/sections/" . $this -> GetCorrectSection() . "/all");
		} else {
			$authToken = $this -> GetAuthToken();
			$curl = curl_init();
			
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_URL => 'http://' . $this -> IpAddress . '/library/sections/' . $this -> GetCorrectSection() . '/all',
				CURLOPT_HTTPHEADER => array(
					'Content-Length: 0',
					'X-Plex-Client-Identifier: movielist',
					'X-Plex-Token: ' . $authToken
				)
			));
			
			$response = curl_exec($curl);
			$xml = simplexml_load_string($response);
			curl_close($curl);
		}
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
			$genres = strtr($genres, $normalizeChars);
			$writers = strtr($writers, $normalizeChars);
			$directors = strtr($directors, $normalizeChars);
			
			array_push($MovieArray, htmlspecialchars($actors), htmlspecialchars($genres));
			array_push($MovieArray, $movie["ratingKey"], htmlspecialchars($writers));
			array_push($MovieArray, $runtime, htmlspecialchars($directors));
			array_push($movies, $MovieArray);
		}
		return $movies;
	}

	function GetTotalMovies() {
		if(@simplexml_load_file("http://" . $this -> IpAddress . "/library/sections/" . $this -> GetCorrectSection() . "/all")) {
			$xml = simplexml_load_file("http://" . $this -> IpAddress . "/library/sections/" . $this -> GetCorrectSection() . "/all");
		} else {
			$authToken = $this -> GetAuthToken();
			$curl = curl_init();
			
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_URL => 'http://' . $this -> IpAddress . '/library/sections/' . $this -> GetCorrectSection() . '/all',
				CURLOPT_HTTPHEADER => array(
					'Content-Length: 0',
					'X-Plex-Client-Identifier: movielist',
					'X-Plex-Token: ' . $authToken
				)
			));
			
			$response = curl_exec($curl);
			$xml = simplexml_load_string($response);
			curl_close($curl);
		}
		$movies = 0;
		foreach ($xml->Video as $movie) {
			$movies++;
		}
		return $movies;
	}

	function GetClients() {
		if(@simplexml_load_file("http://" . $this -> IpAddress . "/clients/")) {
			$xml = simplexml_load_file("http://" . $this -> IpAddress . "/clients/");
		} else {
			$authToken = $this -> GetAuthToken();
			$curl = curl_init();
			
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_URL => 'http://' . $this -> IpAddress . '/clients/',
				CURLOPT_HTTPHEADER => array(
					'Content-Length: 0',
					'X-Plex-Client-Identifier: movielist',
					'X-Plex-Token: ' . $authToken
				)
			));
			
			$response = curl_exec($curl);
			$xml = simplexml_load_string($response);
			curl_close($curl);
		}
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
	
function GetAuthToken() {
		$plexUser;
		$plexPass;
		try {
			global $dsn;
			global $db_username;
			global $db_password;
			$pdo = new PDO($dsn, $db_username, $db_password);
			$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$getAuth = $pdo -> prepare("SELECT plexusername, plexpassword FROM configuration");
			$getAuth -> execute();
			$fetchAuth = $getAuth -> fetch();
			$plexUser = $fetchAuth[0];
			$plexPass = $fetchAuth[1];
			$pdo = null;
		} catch (PDOException $pdo) {
			die("There was a problem getting the client details. Please try again.");
		}

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_URL => 'https://my.plexapp.com/users/sign_in.xml',
			CURLOPT_CAINFO => getcwd() . '/cacert.pem',
			CURLOPT_POST => true,
			CURLOPT_USERPWD => $plexUser . ':' . $plexPass,
			CURLOPT_HTTPHEADER => array(
				'Content-Length: 0',
				'X-Plex-Client-Identifier: movielist'
			)
		));

		$response = curl_exec($curl);
		$xmlR = new SimpleXMLElement($response);
		if (isset($xmlR->error)) {
			echo "<script>warnUser()</script>";
		} else {
			$authToken = $xmlR["authenticationToken"];
			return $authToken;
		}
		curl_close($curl);
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
