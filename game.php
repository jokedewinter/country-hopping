<?php
include('header.php');

// Info about the current game
?>
<section class="ini">
	<div>
		<h2>Connect countries <?php echo $_SESSION['choice']; ?> of the Atlantic</h2>
		<?php echo file_get_contents('svg/' . $_SESSION['choice'] . '.svg'); ?>
	</div>
</section>
<div class="dia"></div>
<?php

// List countries available to choose
global $continue;
$continue = true;

choose_country();

// Only show the country buttons when the game is still going
if ( $continue ) :
	?>
<section class="co">
	<h3>Choose a country</h3>
	<form action="#score" method="post">
<?php
// Split up the countries into first letter sections
$current_letter = 'A';
?>
		<article>
			<a id="<?php echo $current_letter; ?>" href="#<?php echo $current_letter; ?>"><h4><?php echo $current_letter; ?></h4></a>
			<div>
			<?php
			foreach ( $_SESSION['countries_left'] as $key => $left ) :
				if ( $left['n'][0] != $current_letter ) :
					?>
			</div>
		</article>
		<article>
			<a id="<?php echo $left['n'][0]; ?>" href="#<?php echo $left['n'][0]; ?>"><h4><?php echo $left['n'][0]; ?></h4></a>
			<div>
			<?php
					$current_letter = $left['n'][0];
				endif;
				?>
				<button name="<?php echo $key; ?>" value="<?php echo $key; ?>"><?php echo $left['n']; ?></button>
				<?php
			endforeach;
			?>
			</div>
		</article>
	</form>
</section>
<?php 
endif;

include('footer.php'); ?>