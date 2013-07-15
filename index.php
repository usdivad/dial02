<!DOCTYPE html>
<html>
<!--
  <head>
	</head>
	<body>
	
	<div id="mirage"></div>
-->
<head>
<link rel='stylesheet' type='text/css' href='style.css'>
<title>dial 02</title>
<!--<h1>dial 02</h1>-->
</head>

<body>
<div id="logodiv">
<a href="index.php"><img src="logo.jpg" alt="dial 02" align="middle"></a>
</div>

<div id="main">
	<div id="submission">
		<form name="sf" action="index.php" method="get">
				Enter a WKCR program URL here: <input type="text" name="theUrl" value=" or refresh for another random playlist">
				<input type="submit" value="Submit">
		</form>
		<br>
	</div>

<?php
	//GETTING URL
	
	//echo "<b>wow! i'm pulling from the site!</b><br>";
	//$url = "http://www.studentaffairs.columbia.edu/wkcr/program/jazz-alternatives/jazz-alternatives-playlist-03232011";
	
	$handpicked = array("http://www.studentaffairs.columbia.edu/wkcr/program/jazz-alternatives/jazz-alternatives-playlist-03232011",
	"http://www.studentaffairs.columbia.edu/wkcr/program/daybreak-express/daybreak-express-playlist-04182011",
	"http://www.studentaffairs.columbia.edu/wkcr/program/jazz-alternatives/jazz-alternatives-playlist-07182012",
	"http://www.studentaffairs.columbia.edu/wkcr/program/jazz-profiles/jazz-profiles-playlist-11202011",
	"http://www.studentaffairs.columbia.edu/wkcr/program/jazz-alternatives/jazz-alternatives-playlist-05252010",
	"http://www.studentaffairs.columbia.edu/wkcr/program/hip-hop-show/hip-hop-show-playlist-09022011",
	"http://www.studentaffairs.columbia.edu/wkcr/program/out-lunch/out-lunch-playlist-05262011",
	"http://www.studentaffairs.columbia.edu/wkcr/program/across-110th-street/across-110th-street-playlist-09112010",
	"http://www.studentaffairs.columbia.edu/wkcr/program/bach-festival-2011/bach-festival-2011-playlist-12282011-0",
	"http://www.studentaffairs.columbia.edu/wkcr/program/honky-tonkin/honky-tonkin-playlist-06122012",
	"http://www.studentaffairs.columbia.edu/wkcr/program/mambo-machine/mambo-machine-playlist-04022010-0"
	);
	
	$url = $_GET["theUrl"];
	if (is_null($url)) {
		$url = $handpicked[rand(0, sizeof($handpicked)-1)];
	}
	$content = file_get_contents($url);
	//echo($content);
	//echo "as";
	
	
	//grabbing set title
	/*
	$title_start = strpos($content, "<title>") + 7;
	$title_end = strpos($content, "</title>");
	$title_length = $title_end - $title_start;
	
	$title = substr($content, $title_start, $title_length);
	*/
	
	$title = array(array("Karl Malone Happy Jazz Broadcast for 12/21/2012 | WKCR 89.9FM NY"));
	$dj = array(array("DJ Evan Williams"));
	$desc = array(array("Today we celebrated with a Basket of Rubies."));
	preg_match_all('#(?<=<title>).*(?=</title>)#s', $content, $title);
	preg_match_all('#(?<=DJs:&nbsp;</div>).*(?=</div>)#sU', $content, $dj);
	preg_match_all('#(?<=</div> <p>).*(?=</p>)#sU', $content, $desc);
	
	//GETTING INDIVIDUAL SONG DATA
	$songs = array();
	//echo "<div id = 'main'>";
	echo "<h3>".$title[0][0]."</h3><br>";
	echo "by ".$dj[0][0]."<br><br><br>";
	//print_r($dj);
	//songclass
	class Song {
		public $artist = "Karl Malone";
		public $title = "The Mailman Pt. II";
		public $album = "Utajazz";

		public function __construct($ar, $ti, $al) {
			$this->artist = $ar;
			$this->title = $ti;
			$this->album = $al;
		}
	
	}
	
	//parse
	
	//preg_match_all("#<tbody>.*</tbody>#s", $content, $matches);
	//preg_match_all('#<tr class="odd">.*</tr>', $matches[0], $odds);
	preg_match_all('#(?<=<td class="views-field views-field-artist">)\n[^<]*(?=</td>)#s', $content, $artists);
	preg_match_all('#(?<=<td class="views-field views-field-title">)\n[^<]*(?=</td>)#s', $content, $titles);
	preg_match_all('#(?<=<td class="views-field views-field-album">)\n[^<]*(?=</td>)#s', $content, $albums);
	
	//store as Songs
	foreach ($artists[0] as $key => $value) {
		$ar = $value;
		$ti = $titles[0][$key];
		$al = $albums[0][$key];
		$songs[$key] = new Song($ar, $ti, $al);
		/*echo($key);
		echo($value);*/
	}
	
	
	//SPOTIFY SEARCH
	$searchURL = "http://ws.spotify.com/search/1/track.json?q=";
	$urlArray = array();
	
	echo '<div id="links">';
	
	//generate search URL for each artist/title pair in the playlist
	foreach ($songs as $key => $value) {
		$a = $value->artist;
		$a = preg_replace("#\s*(?!\w)#", "", $a);
		$a = str_replace(" ", "%20", $a);
		$a = str_replace(",", "", $a);
		
		$t = $value->title;
		$t = preg_replace("#\s*(?!\w)#", "", $t);
		$t = str_replace(" ", "%20", $t);
		//$t = str_replace(",", "", $t);
		
		//if we have ";" or "," then split it and put into array...
		$t_ls = explode(";", $t);
		if (sizeof($t_ls) == 1) {
			$t_ls = explode(",", $t);
		}
		
		//... then loop over it and output as many searchURLs as necessary
		foreach ($t_ls as $theTitle) {
			$str = $searchURL.$a.$theTitle;
			
			//$urlArray[$key] = $str;
			array_push($urlArray, $str);
			print_r($str."<br>");
		}
	}
	echo '</div>';
	
	//print_r($songs);
	//print_r('<div id="songz">'.json_encode($urlArray).'</div>');
	
	echo '<div id="playlist"><br><br><br><br><br><br><br><br>Please wait while the playlist is loading...</div>';
	
	//desc
	//echo '<div id="description">'.$desc[0][0].'</div>';
	
	//echo "</div>";
	?>
	
</div> <!--end "main"-->

<div id="footer"><a href="http://usdivad.com/">David Su</a> 2013</div>
	
	
	<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
	-->
	<script type="text/javascript" src="jquery.js">
	</script>
	
	<script type="text/javascript">
		//setting initial html
		var qs = window.location.search;
		var qs_link = qs.match(/http%.*/);
		
		if (qs_link) {
			var pu = qs_link[0]; //pu = program URL
			pu = pu.replace(/%3A/g, ":");
			pu = pu.replace(/%2F/g, "/");
			$('[name="theUrl"]').val(pu);
			//console.log("asdf");
		}
		else {
			$('[name="theUrl"]').val(" or refresh for another random playlist");
		}
		
		
		//get data from html
		var str = $("#links").html();
		var uA = str.split("<br>");
		//uA.pop(); //get rid of the "" element
		
		//generate spotify playlist
		var out = "";
		var spotifyURI = "spotify:trackset:PlaylistName:";
		
		for (var i=0; i<uA.length; i++) {
			//the "out" stuff needs to be here because we have to wait for the URI to complete loading at all, hence .done();
			if (i == uA.length-2) {
				var searchURL = uA[i];
				var trackID;
				var spotifyData;
				searchURL = searchURL.replace("&amp;", "");
				searchURL = searchURL.replace(";", "");
				searchURL = searchURL + "";
				console.log(searchURL);
				
				$.getJSON(searchURL, null, function(data) {
					spotifyData = data;
				}).done(function() {
					//adding to spotify URI
					if (spotifyData["tracks"][0]) {
						trackID = spotifyData["tracks"][0]["href"];
					}
					if (trackID) {
						trackID = trackID.replace("spotify:track:", "");
						if (!spotifyURI.match(trackID)) {
							spotifyURI += trackID + ",";
						}
					}
					console.log(trackID);
					console.log(i + ": " + spotifyURI);
					
					//output needs to be within the done block
					out = '<iframe src="https://embed.spotify.com/?uri=' + spotifyURI + '" width="300" height="380" frameborder="0" allowtransparency="true"></iframe>';
					console.log("last one!");
					$("#playlist").html(out);
					$("#links").html("");					
				});

			}
			else {
				var searchURL = uA[i];
				var trackID;
				var spotifyData;
				searchURL = searchURL.replace("&amp;", "");
				searchURL = searchURL.replace(";", "");
				searchURL = searchURL + "";
				console.log(searchURL);
				
				$.getJSON(searchURL, null, function(data) {
					spotifyData = data;
				}).done(function() {
					//adding to spotify URI
					if (spotifyData["tracks"][0]) {
						trackID = spotifyData["tracks"][0]["href"];
					}
					if (trackID) {
						trackID = trackID.replace("spotify:track:", "");
						if (!spotifyURI.match(trackID)) {
							spotifyURI += trackID + ",";
						}
					}
					console.log(trackID);
					console.log(i + ": " + spotifyURI);
					
				});
			}
		
		}
		
//<iframe src="https://embed.spotify.com/?uri=spotify:trackset:PlaylistName:6AanQLrt4GtYXutqNiiQJG,4zisg8WtzYZ1SAP6ma3PxI,0nQcYS8m4bTpkAR3caupIP,0jzW1cBEKwysfjAFlTRZBK,6kOstv0gD7Lu8Iyc0WS5BH,6CR0t7ub78pbwuZrBqHUXT,3SP3oXe5TCvkwzZEWK3uNo,6lUoKi6r3AY4ECxPOqVs7F,0EddmfTcZLFcI2aBOOubO8,3JvkRJAqf0nuQkhcWiR9X4,1tcWA2VFVEWhH0Qv8TGf1L,5AMrnF761nziCWUfjBgRUI,7eO6N0qF1NxLuqpEjGIFp2,0kQLoPjOtOrELs0mpyph8m,6DKC5No5ooHOfzN59hMt40," width="300" height="380" frameborder="0" allowtransparency="true"></iframe>
	
		//$("#mirage").html("<?php echo json_encode($songs); ?>");
	
	</script>
	<!--
	<script type="text/javascript" src="main.js"></script>
	-->
<!-- google analytics -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-42212505-1', 'usdivad.com');
  ga('send', 'pageview');

</script>
</body>
</html>
