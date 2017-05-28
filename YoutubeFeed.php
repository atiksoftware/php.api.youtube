<?php
	/*
	* auth : Mansur ATIK
	* edit : 28.05.2017

	* used : ?id=atiksoftware&type=user&format=json
	*/


	define("GOOGLE_API_KEY", "AIzaSyDtXXXXXXXXXXXXXXXXXXXXRb9Jf6mKTA" );


	class YoutubeFeed{

		public $id    = "";
		public $liste = [];
		public $nest  = 0;

		function convert_toUTF8($veri){
			return iconv(mb_detect_encoding($veri), "UTF-8", $veri);
		}

		function get($t){
			$data = file_get_contents($t);
			if($data == "") return [];
			try{
				$data = json_decode($data,true);
				return $data['items'];
			}
			catch(customException $e){
				return [];
			}
		}
		function scanChannel($token = ""){
			$this->nest++;
			$ek = "";
			if($token != ""){$ek = "&pageToken=".$token;}

			$items = $this->get('https://www.googleapis.com/youtube/v3/search?key='.GOOGLE_API_KEY.'&channelId='.$this->id.'&part=snippet,id&order=date&maxResults=50'.$ek);
			foreach($items as $item){
				if(isset($item['id']['videoId'])){
					$this->liste[] = $item;
				}
			}
			if(isset($arr['nextPageToken']) && $this->nest <= 10){
				$this->scanChannel($arr['nextPageToken']);
			}
		}
		function scanUser($u){
			$items = $this->get('https://www.googleapis.com/youtube/v3/channels?key='.GOOGLE_API_KEY.'&forUsername='.$u.'&part=id,snippet,statistics,contentDetails,topicDetails');
			$liste = [];
			foreach($items as $item){
				$liste[] = $item['id'];
			}
			return $liste;
		}




	}

	/* channel or userid */
	$_id  = @$_GET['id'];

	/* search type : user/channel */
	$type = @$_GET['type'];

	/* output format : json/xml*/
	$format = @$_GET['format'];



	$feed = new YoutubeFeed();

	if($type=="channel"){
		$feed->id = $_id;
		$feed->scanChannel();
	}
	else if($type == "user"){
		$channels = $feed->scanUser($_id);
		foreach($channels as $channel){
			$feed->id = $channel;
			$feed->scanChannel();
		}
	}

	if($format == "json"){
		header("content-type:application/json");
		echo json_encode($feed->liste);
	}
	else if($format == "xml"){


	}







?>
