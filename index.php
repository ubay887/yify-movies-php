<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
       <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
       <title>Yify Torrents</title>
       <link rel="stylesheet" type="text/css" href="style.css" /> <!--to make language check inline --><style>div.inline { float:right; }.clearBoth { clear:both; }</style>
       <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> <!--youtube in site preview for pc and mobile in app preview in mob devices -->
       <script type="text/javascript" src="auto_suggest_jquery_ui.js"></script> <!-- auto suggest jquery ui  -->
       <script type="text/javascript" src="script1.js"></script> <!-- get data with ajax requests  -->
       <script type="text/javascript" src="youtube.js"></script> <!--youtube in site preview for pc and mobile in app preview in mob devices -->
       <script> $(document).ready(function() {   $("#noJS").hide();  });  </script> <!-- Warning for JS disabled browser -->
</head>
  <?php
        //get input variables from user

        if (isset($_GET['page'])) { $pagea = $_GET["page"]; $page1 = $pagea; 	}     else { $page1 = 1;      }     
        if (isset($_GET['quality'])) { $quality = $_GET["quality"];	     	}     else { $quality = ""; 	 }     
        if (isset($_GET['genre']))   { $genre = $_GET["genre"];			}     else {	$genre = "";     }
        if (isset($_GET['rating']))  { $rating = $_GET["rating"];	 	}     else {	$rating = "";    }
        if (isset($_GET['sort_by'])) {	$sort_by = $_GET["sort_by"];  		}     else {	$sort_by = "";   }
        if (isset($_GET['query_term'])) {  $query_term = $_GET["query_term"]; $query_term = mb_strtolower($query_term); /*convert upper case to lower case*/ $query_term = urlencode($query_term); }   else { $query_term = "";	}
        
        
        
        // main code starts below
          $yts = new YTS();
	  $movies = $yts->listMovies($quality, 20, $page1, $query_term, $rating, $genre, $sort_by); // All quality, limit etc
          //$movies = $yts->listMovies('All', 20, $page1); // All quality, limit 6
              //print_r($movies);
              class YTS
              	{
               	        const BASE_URL = 'https://yts.ag';
	                public
                	function listMovies(
                              $quality = 'All',        // $quality Used to filter by a given quality
                      	      $limit = 20,                                 // $limit The limit of results per page that has been set
                 	      $page = 0,                                   // $page Used to see the next page of movies, eg limit=15 and page=2 will show you movies 15-30
			      $query_term = 0,                             // $query_term Used for movie search, matching on: Movie Title/IMDb Code, Actor Name/IMDb Code, Director Name/IMDb Code
                 	      $minimum_rating = 0,                         // $minimum_rating Used to filter movie by a given minimum IMDb rating
	                      $genre = '',                                 // $genre Used to filter by a given genre (See http://www.imdb.com/genre/ for full list)
                       	      $sort_by = 'date-added',                     // $sort_by Sorts the results by chosen value
	                      $order_by = 'desc',                          // $order_by Orders the results by either Ascending or Descending order
                       	      $with_rt_ratings = false                     // $with_rt_ratings Returns the list with the Rotten Tomatoes rating included
	                            )
		{
		              $baseUrl = self::BASE_URL . '/api/v2/list_movies.json';
		              $parameters = '?limit=' . $limit . '&page=' . $page . '&quality=' . $quality . '&minimum_rating=' . $minimum_rating . '&query_term=' . $query_term . '&genre=' . $genre . '&sort_by=' . $sort_by . '&order_by=' . $order_by . '&with_rt_ratings=' . $with_rt_ratings;
		              $data = $this->getFromApi($baseUrl . $parameters);
		              if ($data->movie_count == 0)
			              {
			              return false;
			              }
              		return isset($data->movies) ? $data->movies : [];
		 }
	                                          /* MovieDetail
	                                           * Returns the information about a specific movie
                                               	   * @param int $movie_id
                                          	   * @param bool $with_images
	                                           * @param bool $with_cast
	                                           * @return Object | bool false if no results
	                                           * @throws Exception thrown when HTTP request or API request fails */
	     public
        	function movieDetail($movie_id, $with_images = false, $with_cast = false)
		{
		              $baseUrl = self::BASE_URL . '/api/v2/movie_details.json';
		              $parameters = '?movie_id=' . $movie_id . '&with_images' . $with_images . '&with_cast=' . $with_cast;
                 	      $movieObj = $this->getFromApi($baseUrl . $parameters);
		              if (property_exists($movieObj, 'movie')) return $movieObj->movie;
		              return false;
		}
						/* MovieSuggestions
						 * Returns 4 related movies as suggestions for the user
						 * @param int $movie_id The ID of the movie
	 					 * @return array|bool array with movie objects
						 * @throws Exception thrown when HTTP request or API request fails */
            public
        	        	function movieSuggestions($movie_id)
		        	{
		        	     $baseUrl = self::BASE_URL . '/api/v2/movie_suggestions.json?movie_id=' . $movie_id;
		        	     $data = $this->getFromApi($baseUrl);
		        	     if ($data->movie_suggestions_count == 0){ return false; }
                                        return isset($data->movie_suggestions) ? $data->movie_suggestions : [];
		                 }
	        	        	        /* MovieComments
        	        	        	 * Returns all the comments for the specified movie
        	        	        	 * @param $movie_id
	         	        	         * @return array|bool array with comments objects
        	        	        	 * @throws Exception thrown when HTTP request or API request fails */
	   public
          	        	function movieComments($movie_id)
		        	{
		        	        	$baseUrl = self::BASE_URL . '/api/v2/movie_comments.json?movie_id=' . $movie_id;
        	        			$data = $this->getFromApi($baseUrl);
        	        			if ($data->comment_count == 0)	{ return false; }
        	        			return isset($data->comments) ? $data->comments : [];
        			}

	
	        	        	/* MovieReviews
	        	        	 * Returns all the parental guide ratings for the specified movie
	        	        	 * @param $movie_id
	        	        	 * @return array|bool array with review objects
        	        		 * @throws Exception thrown when HTTP request or API request fails */
           public
        	        	function movieReviews($movie_id)
		        	{
        	        			$baseUrl = self::BASE_URL . '/api/v2/movie_reviews.json?movie_id=' . $movie_id;
		        	        	$data = $this->getFromApi($baseUrl);
		        	        	if ($data->review_count == 0)	{ return false;	}
        	        			return isset($data->reviews) ? $data->reviews : [];
		        	}
	        	        	        	/* MovieParentalGuides
               	        	        		 * Returns all the parental guide ratings for the specified movie
        	        	        		 * @param $movie_id
	        	        	        	 * @return array|bool array with parental guide objects
        	        	        		 * @throws Exception thrown when HTTP request or API request fails */
           public
        	        	function movieParentalGuides($movie_id)
        			{
        	        			$baseUrl = self::BASE_URL . '/api/v2/movie_parental_guides.json?movie_id=' . $movie_id;
        	        			$data = $this->getFromApi($baseUrl);
        	        			if ($data->parental_guide_count == 0)	{ 	return false; 		}
		        	        	return isset($data->parental_guides) ? $data->parental_guides : [];
		        	}
          	        	        		/* List Upcoming
	         	        	        	 * Returns the 4 latest upcoming movies
        	        	        		 * @return array|bool array with movie objects
        	        	        		 * @throws Exception thrown when HTTP request or API request fails */
	  public
        	        	function listUpcoming()
        			{
        	        			$baseUrl = self::BASE_URL . '/api/v2/list_upcoming.json';
        	        			$data = $this->getFromApi($baseUrl);
        	        			if ($data->upcoming_movies_count == 0)	{ 	return false; 	}
        	        			return isset($data->upcoming_movies) ? $data->upcoming_movies : [];
        			}
	
							/* GetFromApi
							 * Does the requests to the yts api
       							 * @param string $url the url that will be called
						         * @return mixed $data object with the data from the API
					         	 * @throws Exception thrown when HTTP request or API request fails */
	private
	    			function getFromApi($url)
				{
						if (!$data = file_get_contents($url)){
									$error = error_get_last();
									throw new Exception("HTTP request failed. Error was: " . $error['message']);
										      }
	                     			else {
							$data = json_decode($data);
							if ($data->status != 'ok') {  throw new Exception("API request failed. Error was: " . $data->status_message); 	}
		  			                return $data->data;
				}
		}
	}
?>
<!-- Above code is just for API nothing will be displayed by above api code
below code is used to display what you want -->
<?php
  if ($movies)
	{
	foreach($movies as $movie)
		{
		$title1[] = $movie->title ;                   // Movie title from api
                $rating1[] = $movie->rating;
                /*$genres1[] = implode(",",$movie->genres);*/$check_geners_existance =@count($movie->genres);   if ( $check_geners_existance !== 0 ) { $genres1[] = implode(",",$movie->genres); } else { $genres1[] = "Empty"; }
                $image_url[] = $movie->medium_cover_image;
                $orignal_link_url[] = $movie->url;
                $synopsis1[] = $movie->synopsis;
                $imdb_code[] = $movie->imdb_code;
                $imdb_rating[] = $movie->rating;
                $year[] = $movie->year;
                $language1[] = $movie->language;
                $yt_trailer_code1[] = $movie->yt_trailer_code;
                $torrents_counts = count($movie->torrents);

                /*        $url= "https://www.imdb.com/title/".$imdb_code."/mediaindex"; $ch = curl_init ($url);curl_setopt($ch, CURLOPT_HEADER, 0); curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla'); curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); curl_setopt($ch, CURLOPT_BINARYTRANSFER,1); $rawdata=curl_exec($ch); curl_close ($ch); $re = '/ <link rel=\'image_src\' href="(.*?)">/m'; preg_match_all($re, $rawdata, $matches, PREG_SET_ORDER, 0);
                echo '<img src="'. $matches[0][1]. '" alt="Broken-image" style="width:150px;height:200px;"><br>'; */
	        	$torrent[] = $movie->torrents[0];
                $torrenta = $movie->torrents[0];                             // First torrent
                $torrents_counts11 = $torrents_counts - 1; //echo $torrents_counts11;
                for ($x = 0; $x <= $torrents_counts11; $x++) {
	        	    $torrent1111[$x] = 'magnet:?xt=urn:btih:'.$movie->torrents[$x]->hash;
	        	    $quality1111[$x] = $movie->torrents[$x]->quality;
	        	    $size1111[$x]    = $movie->torrents[$x]->size;
	        	    $complete_torrent_info_test[$x] ='<br><b> Quality - </b>'.$quality1111[$x].'<br><b>Size -</b>'.$size1111[$x].'<br><b> Magnet Link = </b><a href="'.$torrent1111[$x].'">'.$torrent1111[$x].'</a>';
	        	}
	        	$complete_torrent_info1[] = implode(",",$complete_torrent_info_test);
 	        	$magnet_link1[] = 'magnet:?xt=urn:btih:' . $torrenta->hash;
                $size1[] = $torrenta->size;
 		//echo '<a href="' . $torrent->url . '">' . $torrent->url . '</a> (' . $torrent->size . ')<br/>'; // Torrent url and size
 }
	}
?>
 <body>
       <div id="rounded">
          <img src="img/top_bg.gif" alt="top" /><div id="main" class="container">
              <h1>Yify Torrents</h1>
              <h2>Simpler is better</h2><br> <div id="noJS">Please enable JavaScript. Otherwise site won't work properly.</div>
	      <a href="javascript:" id="return-to-top"><i> &#x25b2;</i></a>    <!-- Return to Top -->
              <?php $full_url = $_SERVER['REQUEST_URI']; $qurey_url = '?'.$_SERVER['QUERY_STRING']; $full_url1 = str_replace($qurey_url,'', $full_url); ?>
              
              <form action=""><select name="quality"><option value="<?php if(empty($quality)) {  echo "all";} else  { echo $quality; } ?>" selected="selected"><?php if(empty($quality)) {  echo "Quality";} else  { echo $quality; } ?> </option><option value="all">All</option> <option value="720p">720p</option> <option value="1080p">1080p</option> <option value="3D">3D</option> </select> <select name="genre"> <option value="<?php if(empty($genre)) {  echo "all";} else  { echo $genre; } ?>" selected="selected"><?php if(empty($genre)) {  echo "Genre";} else  { echo $genre; } ?></option><option value="all">All</option> <option value="action">Action</option> <option value="adventure">Adventure</option> <option value="animation">Animation</option> <option value="biography">Biography</option> <option value="comedy">Comedy</option> <option value="crime">Crime</option> <option value="documentary">Documentary</option> <option value="drama">Drama</option> <option value="family">Family</option> <option value="fantasy">Fantasy</option> <option value="film-noir">Film-Noir</option> <option value="game-show">Game-Show</option> <option value="history">History</option> <option value="horror">Horror</option> <option value="music">Music</option> <option value="musical">Musical</option> <option value="mystery">Mystery</option> <option value="news">News</option> <option value="reality-tv">Reality-TV</option> <option value="romance">Romance</option> <option value="sci-fi">Sci-Fi</option> <option value="sport">Sport</option> <option value="talk-show">Talk-Show</option> <option value="thriller">Thriller</option> <option value="war">War</option> <option value="western">Western</option> </select> <select name="rating"> <option value="<?php if(empty($rating)) {  echo "0";} else  { print_r( $rating); } ?>" selected="selected"><?php if(empty($rating)) {  echo "Rating";} else  { echo $rating; } ?></option> <option value="9">9+</option> <option value="8">8+</option> <option value="7">7+</option> <option value="6">6+</option> <option value="5">5+</option> <option value="4">4+</option> <option value="3">3+</option> <option value="2">2+</option> <option value="1">1+</option> </select> <select name="sort_by"> <option value="<?php if(empty($sort_by)) {  echo "date-added";} else  { echo $sort_by; } ?>" selected="selected"><?php if(empty($sort_by)) {  echo "Sort By";} else  { echo $sort_by; } ?></option><option value="date-added">Date Added</option> <option value="title">Title</option> <option value="year">Year</option> <option value="rating">Ratings</option> <option value="peers">Peers</option> <option value="seeds">Seeds</option> <option value="download_count">Download Count</option> <option value="like_count">Like Count</option> </select> <div class="tooltip"><span role="status" aria-live="polite" class="ui-helper-hidden-accessible">Start Typing here</span> <input type="text" class="text" maxlength="9999999" name="query_term" id="query_term"  placeholder="Search" /> <input type="submit" value="Submit"> </div> <div id="results"></div> </form>
              <ul id="navigation">
                  
                   <le><a href="<?php echo $full_url1; ?>">Reload</a></le><le> <le><a href="<?php $page2 = $page1 + 1; $next_page = $full_url1.'?page='.$page2."&quality=".$quality."&genre=".$genre."&rating=".$rating."&sort_by=".$sort_by."&query_term=".$query_term;  echo $next_page ?>">Next Page</a></le><le><le><a href="<?php $page3 = $page1 - 1; $prev_page = $full_url1.'?page='.$page3."&quality=".$quality."&genre=".$genre."&rating=".$rating."&sort_by=".$sort_by."&query_term=".$query_term; echo $prev_page ?>">Previous Page</a></le><le><le> <!-- auto loading of imgs --> <le><input name="checkbox" type="checkbox" id="checkbox" onClick="save()" >Load Images Automatically<label for="checkbox" /></input></le>
                   <?php  /* for ($x = 0; $x <= 5; $x++) { $imns = $x+1; echo '<le><a href="#'.$image_url[$x].'">Load Image '.$imns.'</a></le>'; } */ ?>
                   <le><img id="loading" src="img/ajax_load.gif" alt="loading" /></le>
              </ul>    
              <div class="clear"></div>

                  <?php   if (!empty($title1)) {
                          $countresults = sizeof($movies); $countresults = $countresults-1; // -1 because if loop is starting from zero 
                          for ($y = 0; $y <= $countresults; $y++) {
                                       echo '<div id="pageContent"><b><font color="blue">'.$title1[$y].'</b></font>&emsp;('.$year[$y].')&emsp;&emsp;<b>IMDB RATING - '.$imdb_rating[$y].'</b>&emsp;<font color="brown">'.$genres1[$y].'</font>';
                                       echo '<br><b> Magnet Link = </b><a href="'.$magnet_link1[$y].'">'.$magnet_link1[$y].'</a>'; echo '&emsp;&emsp;<ln><a id="lefti" href = "webtor_player.php?magnet_uri='.$magnet_link1[$y].'&images_uri='.$image_url[$y].'">Stream online(beta)</a></ln>';
                                       echo '<br><b> Size - </b>'.$size1[$y];   if(empty($yt_trailer_code1[$y])){ echo '&emsp;&emsp;<b> Youtube Trailer - </b><a class="youtube" href="https://www.youtube.com/embed/?listType=search&list='.$title1[$y].' '.$year[$y].' Official trailer">Pc view</a> - <a href="https://www.youtube.com/embed/?listType=search&list='.$title1[$y].' '.$year[$y].' Official trailer">Normal View</a>'; } else{ echo '&emsp;&emsp;<b> Youtube Trailer - </b><a class="youtube" href="https://www.youtube.com/watch?v='.$yt_trailer_code1[$y].'">Pc view</a> - <a href="https://www.youtube.com/watch?v='.$yt_trailer_code1[$y].'">Normal View</a> -- <a href="#ytlikes'.$yt_trailer_code1[$y].'" id="'.$yt_trailer_code1[$y].'" onclick="launch_toast(this.id)"><span id="'.$yt_trailer_code1[$y].'C1">Yt-Likes</span></a>'; } echo '&emsp;&emsp;<b> Language - </b>'/*.$language1[$y]*/;echo '<ln><a href="#LANGUAGE'.$orignal_link_url[$y].'">check language</a></ln>'; echo '<div class="inline" id="#LANGUAGE'.$orignal_link_url[$y].'"></div>';                                       echo '<br><button class="collapsiblea">Synopsis(Click to view)</button><div class="content"><p>'.$synopsis1[$y].'</p></div>';    echo '<button class="collapsiblea">More Magnet Links</button><div class="content"><p>'.$complete_torrent_info1[$y].'</p></div>';
                                       $imns = $y+1; echo '<ln><a id="lefti" class="pqr" href="#'.$image_url[$y].'"> Click to Load Image '.$imns.'</a></ln>'; echo '&emsp;&emsp;';  echo '<ln><a id="lefti" href="#SCREENSHOTS'.$imdb_code[$y].'"> Click to Load screenshots '.$imns.'</a></ln>';echo'&emsp;&emsp;<b>Rips - </b><ln><a href="#RIPs'.$orignal_link_url[$y].'">check RIPs</a></ln>'; echo '<div class="inline" id="#RIPs'.$orignal_link_url[$y].'"></div>';echo'&emsp;&emsp;<b>Subtitles - </b><ln><a href="#SUBs'.$imdb_code[$y].'">check SUBs</a></ln>';
                                       echo '<div id="#'.$image_url[$y].'"></div>';   echo '<div id="#SCREENSHOTS'.$imdb_code[$y].'"></div>'; echo '<div id="#SUBs'.$imdb_code[$y].'"></div>'; echo '<div id=#ytlikes'.$yt_trailer_code1[$y].'></div></div>';
			  } } else { echo '<br><br>NO matching result found<br><br>Either movie name is wrong or movie is not on yify torrents database yet<br><br>Click to search on <a href="https://duckduckgo.com/?q='.$query_term.' torrent link">DuckDuckGO</a>&emsp;&emsp;<a href="https://www.google.com/search?q='.$query_term.' torrent link">Google</a>&emsp;&emsp;<a href="https://search.yahoo.com/search?p='.$query_term.' torrent link">Yahoo!</a>&emsp;&emsp; <br><br>' ; }
                  ?>
                   <le><a href="<?php echo $full_url1; ?>">Reload</a></le><le> <le><a href="<?php $page2 = $page1 + 1; $next_page = $full_url1.'?page='.$page2."&quality=".$quality."&genre=".$genre."&rating=".$rating."&sort_by=".$sort_by."&query_term=".$query_term;  echo $next_page ?>">Next Page</a></le><le><le><a href="<?php $page3 = $page1 - 1; $prev_page = $full_url1.'?page='.$page3."&quality=".$quality."&genre=".$genre."&rating=".$rating."&sort_by=".$sort_by."&query_term=".$query_term; echo $prev_page ?>">Previous Page</a></le><le>
        </div>
  <div class="clear"></div>
  <div id="mPlayer">  <div> </div>  </div> <!-- these divs for youtube popup and can be defined anywhere if there is problem in css then will display the video link in defined location -->
       <img src="img/bottom_bg.gif" alt="bottom" /></div>
       <div align="center" class="demo"><a href="" target="_blank">@2020 </a></div>
       <script type="text/javascript" src="jquery.js"></script>
       <script type="text/javascript" src="auto.js"></script>
       <!-- click img popup/lightbox  -->
        <link rel="stylesheet" type="text/css" href="lightbox.css" />
        <script type="text/javascript" src="lightbox.js"></script>
        <script>
        lightbox.option({
                        'alwaysShowNavOnTouchDevices': true,
                        'albumLabel':   "Image %1 of %2",
                        'disableScrolling': false,
                        'fadeDuration': 500,
                        'fitImagesInViewport': true,
                        'imageFadeDuration': 500,
                        'maxWidth': '',
                        'maxHeight': '',
                        'positionFromTop': 50,
                        'resizeDuration': 600,
                        'showImageNumberLabel': true,
                        'wrapAround': false
                        })
        </script>
 </body>
</html>
