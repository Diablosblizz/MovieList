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
			if (@simplexml_load_file("http://" . $server . "/status/sessions")) {
				$xml = simplexml_load_file("http://" . $server . "/status/sessions");
			} else {
				$curl = curl_init();
				$authToken = $this -> GetAuthToken();
				curl_setopt_array($curl, array(
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_URL => "http://" . $server . "/status/sessions",
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

			foreach ($xml->Video as $movie) {
				$currentID = $movie -> Player["machineIdentifier"];
				if ($currentID == $currentClientID) {
					if ($movie["type"] == "movie") {
						return true;
					}
				}
			}
		} catch (PDOException $pdo) {
			die("There was a problem getting the client details. Please try again.");
		}
	}

	function GetCurrentPlayback($server) {
		$currentClientID = $this -> GetSelectedClient()[2];
		if (@simplexml_load_file("http://" . $server . "/status/sessions")) {
			$xml = simplexml_load_file("http://" . $server . "/status/sessions");
		} else {
			$curl = curl_init();
			$authToken = $this -> GetAuthToken();
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_URL => "http://" . $server . "/status/sessions",
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
		$playingArray = array();

		foreach ($xml->Video as $movie) {
			$currentID = $movie -> Player["machineIdentifier"];
			if ($currentID == $currentClientID) {
				if ($movie["type"] == "movie") {
					$title = $movie["title"];
					$plexId = $movie["ratingKey"];
					array_push($playingArray, $title, $plexId);
				}
			}
		}
		return $playingArray;
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

	function PlayPause($mode) {
		if ($mode == 0) {
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
