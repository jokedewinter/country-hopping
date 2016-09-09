<?php
/*
 * Choose Africa, North-East or West validation
 * -------------------------------------------- */

function choose_side() {

	/*
	Can the required file be read and processed here?
	Make the variable array with the countries global so that it can be used
	during the game.

	This would be to avoid having the second if statement once you've
	returned from this function.

	Is $choice needed?
	Probably useful to show when game starts?
	*/

	global $choice, $countries;

	if ( isset($_POST['east']) ) :

		$choice = 'east';
		$countries = file_get_contents('json/east.json');
		return true;

	elseif ( isset($_POST['west']) ) :

		$choice = 'west';
		$countries = file_get_contents('json/west.json');
		return true;

	else : return false;

	endif;

}

/*
 * Score card
 * ----------------------------------------- */

/*
The lives value runs a bit crazy if you for some reason keep refreshing the screen after the game has ended.
Not sure why you would do that, but anyway, it's a bit stupid, so hence the if in the lives value display.
*/

function score_card( $title, $feedback ) {
	?>
	<section class="sc">
		<h3><?php echo $title; ?></h3>
		<ul>
			<li><span><?php echo count($_SESSION['countries_picked_names']); ?></span> countr<?php if ( 1 == count($_SESSION['countries_picked_names']) ) { echo "y"; } else { echo "ies"; } ?> linked</li>
			<li><span><?php if ( 0 < $_SESSION['lives'] ) { echo $_SESSION['lives']; } else { echo "0"; } ?></span> li<?php if ( 1 == $_SESSION['lives'] ) { echo "fe"; } else { echo "ves"; } ?> remaining</li>
			<?php
			if ( 0 < count($_SESSION['countries_picked_names']) ) :
				?>
				<li><span><?php echo implode(', ', $_SESSION['countries_picked_names']); ?>, ...</span> your journey</li>
				<?php
			endif;
			?>
		</ul>
		<?php
		if (( 'dead-end' == $feedback ) || ( 'no-lives' == $feedback )) :
			?>
			<div class="scfb">
				<?php				
				switch ( $feedback ) :

					case 'dead-end' :
					// Find the name of the last country picked
					$last = count($_SESSION['countries_picked_names']) - 1;
					?>
					<h4>Dead end</h4>
					<p>I am sorry. You reached a dead end. <?php echo $_SESSION['countries_picked_names'][$last]; ?> has no more neighbo<?php if ( 'north-east' == $_SESSION['choice'] ) { echo 'u'; } ?>ring countries left to choose.</p>
					<?php
					break;

					case 'no-lives' :
					?>
					<h4>Bad luck</h4>
					<p>I am sorry. You ran out of lives.</p>
					<?php
					break;

				endswitch;
				?>
			</div>
			<?php
			// Was it a decent score?
			if ( 9 < count($_SESSION['countries_picked_names']) ) :
				?>
				<div class="scre">
					<?php
					if (( 22 == count($_SESSION['countries_picked_names']) ) && ( 'west' == $_SESSION['choice'] )) :
						?>
						<h2>Awesome score!</h2>
						<p>You got the maximium amount of countries you can link in this part of the world.</p>
						<?php
					elseif ( 15 > count($_SESSION['countries_picked_names']) ) :
						?>
						<h2>Pretty decent effort</h2>
						<?php
					elseif ( 20 > count($_SESSION['countries_picked_names']) ) :
						?>
						<h2>Impressive journey</h2>
						<?php
					elseif ( 30 < count($_SESSION['countries_picked_names']) ) :
						?>
						<h2>Amazing knowledge of the world</h2>
						<?php
					else :
						?>
						<h2>Just marvelous</h2>
						<p>Did you have Google Maps open on the side, or are you really that clever?</p>
						<?php
					endif;
					?>
				</div>
				<?php
			endif;
			?>
			<a class="ag" href="index.php">Play again?</a>
			<?php
		elseif ( '' != $feedback ) :
			// Show message of disappointment
			?>
			<div class="scfb">
				<h4>You lost a life</h4>
				<p><?php echo $feedback; ?> does not have a border with <?php echo $_SESSION['previous']['n']; ?>. Try a different country.</p>
			</div>
			<?php
		endif;
		?>
	</section>
	<?php
}

/*
 * Choose a country
 * ----------------------------------------- */

function choose_country() {

global $continue;

	// Catch the choice
	// Loop through all the countries left to choose from
	// Then check if that country was selected

	foreach ( $_SESSION['countries_left'] as $key => $left ) :

		if ( isset($_POST[$key]) ) :

			// Collect the country code of the picked country
			$picked = $_POST[$key];

			// Need this to update the previouse variables later on
			$continue = false;
			$lost_life = '';
			$dead_end = false;

			// Check if this country is a correct choice
			if ( check_country($picked) ) :
				// It's a neighbour

				// Add the chosen country to the picked countries array
				// Use $i as the counter for this array
				$i = count($_SESSION['countries_picked_codes']);
				$_SESSION['countries_picked_codes'][$i] = $picked;
				$_SESSION['countries_picked_names'][$i] = $left['n'];

				// Update the previous choice with the current chosen country
				$_SESSION['previous']['code'] = $_POST[$key];
				$_SESSION['previous']['n'] = $left['n'];
				$_SESSION['previous']['nb'] = $left['nb'];

				// Remove the chosen country from countries_left
				unset($_SESSION['countries_left'][$picked]);

				// If all neighbours of the chosen country are present in the picked array
				// you hit a dead end. No more neighbours left to choose.
				$nb_count = count($left['nb']);
				$nb_match = 0;

				foreach ( $left['nb'] as $nb ) :

					if ( in_array( $nb, $_SESSION['countries_picked_codes']) ) : $nb_match++;
					endif;

				endforeach;

				if ( $nb_match == $nb_count ) :
					$continue = false;
					$dead_end = true;
				else : $continue = true;
				endif;

			else :

				// It's not a neighbour
				// Check how many lives are left
				if ( 1 < $_SESSION['lives'] ) :

					// Update score card
					$continue = true;
					$lost_life = $left['n'];

				else : $continue = false;

				endif;

				// Remove a life
				$_SESSION['lives']--;

			endif;

			if ( $continue ) :

				// Update score card
				score_card( 'Score card', $lost_life );

			elseif ( $dead_end ) :

				// Game over: dead end
				// Show the final score
				score_card( 'Game over', 'dead-end' );

			else :

				// Game over: no lives left
				// Show the final score
				score_card( 'Game over', 'no-lives' );

			endif;

		endif;

	endforeach;
}

/*
 * Check the chosen country
 * ----------------------------------------- */

function check_country( $picked ) {

	// Is the current country an neighbour of the previous country?
	if ( "" == $_SESSION['previous']['code'] ) :

		// If countries_picked is empty, then this is the first country picked - skip all the testing
		return true;

	else :

		// Compare $picked with ['previous']['nb'] array
		if ( in_array( $picked, $_SESSION['previous']['nb'] ) ) : return true;
		else : return false;
		endif;

	endif;
}
?>
