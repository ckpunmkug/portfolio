<?php

$input_file = __DIR__.'/input.json';

$return = file_get_contents($input_file);
if(!is_string($return)) {
	trigger_error("Can't open input file", E_USER_ERROR);
	exit(255);
}
$json = $return;

$return = json_decode($json, true);
if(!is_array($return)) {
	user_error(json_last_error_msg());
	trigger_error("Can't json decode", E_USER_ERROR);
	exit(255);
}
$ITEM = $return;

$bookmakers = '';
$count = count($ITEM);
foreach($ITEM as $index => $item) {

	$item["logo"]["src"] = htmlentities($item["logo"]["src"]);
	$item["logo"]["background"] = htmlentities($item["logo"]["background"]);

	$item["info"]["name"] = htmlentities($item["info"]["name"]);
	$item["info"]["text"] = htmlentities($item["info"]["text"]);

	$item["ads"]["title"] = htmlentities($item["ads"]["title"]);
	$item["ads"]["text"] = htmlentities($item["ads"]["text"]);

	if(($index % 2) == 0) {
		$background = "#FFFFFF";
	}
	else {
		$background = "#f5f5f5";
	}
	
	$radius = '';
	if($index == 0) {
		$radius = ' border-top-left-radius: 8px; border-top-right-radius: 8px;';
	}
	if($index == ($count - 1)) {
		$radius = ' border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;';
	}

	$bookmakers .= 
<<<HEREDOC
<div name="bookmaker" style="background: {$background};{$radius}">

	<div name="leftside">
		<div name="logo" style="background: {$item["logo"]["background"]}">
			<img src="{$item["logo"]["src"]}" />
		</div>
		<div name="info">
			<div name="name">{$item["info"]["name"]}</div>
			<div name="text">{$item["info"]["text"]}</div>
		</div>
	</div>
	
	<div name="rightside">
		<div name="ads">
			<div name="container">
				<div name="title">{$item["ads"]["title"]}</div>
				<div name="text">{$item["ads"]["text"]}</div>
			</div>
		</div>
		<div name="buttons">
			<div name="container">
				<button class="no_highlights" name="play">PLAY</button><br/>
				<button class="no_highlights" name="read">READ REVIEW</button>
			</div>
		</div>
	</div>
</div>\n
HEREDOC;
}

?><!DOCTYPE html>
<html lang="en"><!-- https://aviatorgameonline.in/ -->
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Top Bookmakers</title>
		<link rel="stylesheet" href="assets/styles/bookmakers.css" />
	<style>
@font-face {
	font-family: "Montserrat";
	src: url("assets/fonts/montserrat.woff2") format("woff");
}
body {
	background: #FFF;
	padding: 0px;
	margin: 0px;
	div {
		border: solid 0px #C00; 
	}
}
.container {
	display: flex;
	justify-content: center;
	background: #FFF;
}
	    </style>
	</head>
	<body>
		<div class='container'>
		
<!-- Bookmakers  START -->
<div name="bookmakers">
<?= $bookmakers; ?>
</div>
<!-- Bookmakers  STOP -->

		</div>
	</body>
</html>

