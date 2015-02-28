<?php
include_once "configuration.php";

class Client {
	private $IpAddress = 0;
	private $port = 0;

	/**
	 * Instantiates the Client Class
	 *
	 * @param IPAddress $ip
	 */
	function Client($ip = 0) {
		$this -> IpAddress = $ip;
	}

	function SetIpAddress($ip) {
		$this -> IpAddress = $ip;
	}

	function SetPort($port) {
		$this -> port = $port;
	}

	function GetSelectedClient() {
		try {
			global $dsn;
			global $db_username;
			global $db_password;
			$pdo = new PDO($dsn, $db_username, $db_password);
			$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$getClient = $pdo -> prepare("SELECT * FROM clients WHERE selected = 1");
			$getClient -> execute();
			$fetchClient = $getClient -> fetch();
			$pdo = null;
		} catch (PDOException $pdo) {
			die("There was a problem getting the client details. Please try again.");
		}
		return $fetchClient;
	}
	
	function GetIsContentPlaying($server) {
		try {	
			$currentClientID = $this -> GetSelectedClient()[2];
			$xml = simplexml_load_file("http://" . $server . "/status/sessions");
			
			foreach($xml->Video as $movie) {
				$currentID = $movie->Player["machineIdentifier"];
				if($currentID == $currentClientID) {
					if($movie["type"] == "movie") {
						return true;
					}
				}
			}
		} catch (PDOException $pdo) {
			die("There was a problem getting the client details. Please try again.");
		}
	}
	
	function GetCurrentPlayback($server) {
		try {	
			$currentClientID = $this -> GetSelectedClient()[2];
			$xml = simplexml_load_file("http://" . $server . "/status/sessions");
			$playingArray = array();
			
			foreach($xml->Video as $movie) {
				$currentID = $movie->Player["machineIdentifier"];
				if($currentID == $currentClientID) {
					if($movie["type"] == "movie") {
						$title = $movie["title"];
						$plexId = $movie["ratingKey"];
						array_push($playingArray, $title, $plexId);
					}
				}
			}
			return $playingArray;
		} catch (PDOException $pdo) {
			die("There was a problem getting the client details. Please try again.");
		}
	}

	function PlayPause($mode) {
		if($mode == 0) {
			$ch = curl_init("http://" . $this -> IpAddress . ":" . $this -> port . "/player/playback/pause?type=video");
		} else {
			$ch = curl_init("http://" . $this -> IpAddress . ":" . $this -> port . "/player/playback/play?type=video");
		}
		curl_exec($ch);
		curl_close($ch);
	}

	function Stop() {
		$ch = curl_init("http://" . $this -> IpAddress . ":" . $this -> port . "/player/playback/stop?type=video");
		curl_exec($ch);
		curl_close($ch);
	}

	function StepBack() {
		$ch = curl_init("http://" . $this -> IpAddress . ":" . $this -> port . "/player/playback/stepBack?commandID=1&type=video");
		curl_exec($ch);
		curl_close($ch);
	}

	function StepForward() {
		$ch = curl_init("http://" . $this -> IpAddress . ":" . $this -> port . "/player/playback/stepForward?commandID=1&type=video");
		curl_exec($ch);
		curl_close($ch);
	}

}
