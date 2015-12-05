<?php
$KKL = new KKL();
/*
Template Name: Liga Übersicht (Teams)

*/
?>
<?php get_header(); ?>

<section class="kkl-content">
		<div class="container">
			<div class="row">
				
				<div class="col-md-9">
					<div class="row">
						<div class="col-xs-12">
							<nav class="isotope-nav">
								<ul class="nav nav-pills">
		              <li><a href="#" rel="isotope" data-filter="*">Alle</a></li>
									<li><a href="#" rel="isotope" data-filter=".koeln1">1. Liga</a></li>
									<li><a href="#" rel="isotope" data-filter=".koeln2a">2. Liga A</a></li>
									<li><a href="#" rel="isotope" data-filter=".koeln2b">2. Liga B</a></li>
									<li><a href="#" rel="isotope" data-filter=".koeln3a">3. Liga A</a></li>
									<li><a href="#" rel="isotope" data-filter=".koeln3b">3. Liga B</a></li>
									<li><a href="#" rel="isotope" data-filter=".koeln3c">3. Liga C</a></li>
									<li><a href="#" rel="isotope" data-filter=".koeln3d">3. Liga D</a></li>
								</ul>
							</nav>
						</div>
					</div>
					<div class="row">
						<?php
							/* Run the loop to output the page.
							 * If you want to overload this in a child theme then include a file
							 * called loop-page.php and that will be used instead.
							 */
							get_template_part( 'loop', 'page' );
						?>
					</div>
				</div>
				
				<aside class="col-md-3 hidden-xs hidden-sm">
	        <ul><?php dynamic_sidebar( 'kkl_leagues_sidebar' ); ?></ul>
  				<ul><?php dynamic_sidebar( 'kkl_global_sidebar' ); ?></ul>
	      </aside><!-- rechter content end. -->

			</div>
		</div>
	</section>

<?php get_footer(); ?>
