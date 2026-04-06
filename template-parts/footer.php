<?php

/**
 * The footer of the site.
 */

global $meta;

?>


</main> <!-- /.site-main -->

	<footer class="site-footer" role="contentinfo">
		<div class="container">
			<div class="site-footer__inner animated-up">

				<div class="site-footer__brand">
					<a href="/" aria-label="WP Mailblox home">
						<?php include_asset('static/logo.svg'); ?>
					</a>
					<p class="site-footer__tagline">Email builder for WordPress.</p>
				</div>

				<nav class="site-footer__nav" aria-label="Footer navigation">
					<ul class="flex flex-wrap gap-x-6 gap-y-2">
						<li><a href="/">Home</a></li>
						<li><a href="/docs">Docs</a></li>
						<li><a href="/changelog">Changelog</a></li>
						<li><a href="/terms-privacy">Terms &amp; Privacy</a></li>
						<li><a href="https://wordpress.org/plugins/wp-mailblox/" target="_blank" rel="noopener">WordPress.org</a></li>
					</ul>
				</nav>

				<p class="site-footer__copy">&copy; <?php echo date('Y'); ?> WP Mailblox. Built for WordPress.</p>

			</div>
		</div>
	</footer> <!-- /.site-footer -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script src="/<?php echo get_revision('footer.js'); ?>"></script>
</body>

</html>