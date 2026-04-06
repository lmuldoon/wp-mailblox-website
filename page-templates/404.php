<?php

/**
 * The 404 page of the site.
 */

$meta->title = '404 Page Not Found';
$meta->description = 'We couldn\'t find the page you were looking for.';

get_header();

?>


	<article>

		<div class="section" id="introduction">
			<div class="container">

				<div class="boxes-row">
					<div class="box single text-center sm:text-left animated">
						<div>
							<h2 class="lined">404 - Page Not Found</h2>
							<div>
								<p>We couldn't find the page you were looking for.</p>
								<p>If you typed the URL, please check it for spelling mistakes. Alternatively, you can try refreshing the page.</p>
								<p><a class="button" href="/">Back to Home</a></p>
							</div>

						</div>
					</div>

				</div>
			</div>
		</div>



	</article>


<?php get_footer(); ?>