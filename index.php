<?php
session_unset();
include('header.php');

// Introduce the game
?>
<section class="ini">
	<div>
		<h2>Game rules</h2>
		<p>Link as many bordering countries together without going to the same country twice.</p>
		<p>You get three lives before you die, but if you pick a country without any neighbours left to pick, you will die immediately. Even if you still have three lives left.</p>
	</div>
</section>
<div class="dia"></div>
<?php
// Ask East or West?
global $choice, $countries;

if ( choose_side() ) :

	// Load East or West
	$_SESSION['choice'] = $choice;
	$_SESSION['countries'] = json_decode($countries, true);

	// Create variables needed for the game
	$_SESSION['lives'] = 3;
	$_SESSION['countries_picked_codes'] = array();
	$_SESSION['countries_picked_names'] = array();
	$_SESSION['countries_left'] = $_SESSION['countries'];

	$_SESSION['previous']['code'] = '';
	$_SESSION['previous']['name'] = '';
	$_SESSION['previous']['nb'] = array();

	// clear out the output buffer
	//while (ob_get_status()) { ob_end_clean(); }

	// Redirect to the game page
	header( "Location: game.php" );

else :
?>
<section class="ch">
	<h2>Which part of the world do you want to play?</h2>
	<form action="" method="post">
		<button type="submit" name="west"><div>West<span>The new world</span></div></button>
		<div class="wo"></div>
		<button type="submit" name="east"><div>East<span>The old world</span></div></button>
	</form>
</section>
<?php
endif;

include('footer.php'); ?>
