<?php

//script to pull rotten tomatoes critic and audience rating for a film, based on film name plus year (optional)

// to do: 	add number of ratings, for better filtering
// 			prevent films with commas making new fields
//			add IMDB
//			improve matching by release date
//			get better list of current amazon instant films

function get_json($URL){
  $ch = curl_init($URL);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER, 0);

  $obj = json_decode(curl_exec($ch), true);

  curl_close($ch);

  return $obj;
}

function create_url($QUERY){

	$apikey = "3hcpsmekaytaqcb3b3qu8azf";

	$QUERY = str_replace(' ', '+', $QUERY);

   	$QUERY = preg_replace('/[^A-Za-z0-9+\-]/', '', $QUERY);

   	//print("Clean query: " . $QUERY . "\r\n");

	//$QUERY = urlencode($QUERY);

	$url = "http://api.rottentomatoes.com/api/public/v1.0/movies.json?apikey=" . $apikey . "&q=" . $QUERY . "&page_limit=1";

	return $url;
}

$time = time();

$file = "films.csv";

$rows = file($file, FILE_SKIP_EMPTY_LINES);

print("Found " . count($rows) . " rows\r\n");

$output = "Name,Year,Critics score,User score,Query" . PHP_EOL;

file_put_contents("output-" . $time . ".csv", $output, FILE_APPEND);

foreach($rows as $row){

	print("Processing movie: " . $row);

	$URL = create_url($row);

	$data = get_json($URL);

	$critics_score	= $data['movies'][0]['ratings']['critics_score'];
	$user_score		= $data['movies'][0]['ratings']['audience_score'];
	$movie_name 	= $data['movies'][0]['title'];
	$movie_year 	= $data['movies'][0]['release_dates']['theater'];

	$output = $movie_name . "," . $movie_year . "," . $critics_score . "," . $user_score . "," . $row;

	file_put_contents("output-" . $time . ".csv", $output, FILE_APPEND);

	sleep(0.2);

}



//$output = get_json("http://api.rottentomatoes.com/api/public/v1.0/movies.json?apikey=3hcpsmekaytaqcb3b3qu8azf&q=house+of+cards+1992&page_limit=1");

//print_r($output);

//print($output['movies'][0]['ratings']['critics_score']);





?>