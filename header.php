<?php
// Create a session to keep track of the score
session_start();

// Contains functions used in the game
include 'engine/engine.php';

/* 
The index page has a redirect to the game page after a choice is made about which part of the world the
game will be played with. This needs to be here so that the jump works.
http://stackoverflow.com/questions/353803/redirect-to-specified-url-on-php-script-completion
*/
ob_start(); // ensures anything dumped out will be caught
?>
<!DOCTYPE>
<html>
<head>
	<title>Country Hopping</title>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/screen.min.css">
</head>
<body>
<header role="banner">
	<div>
		<h1>Country hopping</h1>
		<p>linking bordering countries</p>
	</div>
</header>
<main role="main">
