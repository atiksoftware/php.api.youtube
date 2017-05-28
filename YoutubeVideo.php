 <?php



	class YoutubeVideo
	{

		function getVideoInfo($id) {
			return file_get_contents("https://www.youtube.com/get_video_info?video_id=".$id."&cpn=CouQulsSRICzWn5E&eurl&el=adunit");
		}

		function Info($id) {
			//parse the string separated by '&' to array
			parse_str($this->getVideoInfo($id), $data);

			//set video title
			$this->video_title = $data["title"];

			//Get the youtube root link that contains video information
			$stream_map_arr = $this->getStreamArray($id);
			$final_stream_map_arr = array();

			//Create array containing the detail of video
			foreach($stream_map_arr as $stream)
			{
				parse_str($stream, $stream_data);
				$stream_data["title"] = $this->video_title;
				$stream_data["mime"] = $stream_data["type"];
				$mime_type = explode(";", $stream_data["mime"]);
				$stream_data["mime"] = $mime_type[0];
				$start = stripos($mime_type[0], "/");
				$format = ltrim(substr($mime_type[0], $start), "/");
				$stream_data["format"] = $format;
				unset($stream_data["type"]);
				$final_stream_map_arr [] = $stream_data;
			}
			return $final_stream_map_arr;
		}
		function getStreamArray($id)
		{
			parse_str($this->getVideoInfo($id), $data);
			$stream_link = $data["url_encoded_fmt_stream_map"];
			return explode(",", $stream_link);
		}

	}

	$video = new YoutubeVideo();
	$l = $video->Info("Uhx3xUgd-oQ");
	return $l[0]["url"];
