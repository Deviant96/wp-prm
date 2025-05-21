<?php
/**
 * The template for displaying the footer
 */
?>
	<!-- Toast -->
	<script src="<?php echo get_template_directory_uri() . '/assets/js/toast.js' ?>"></script>
	<div class="toast-container" id="toast-container"></div>
	<!-- End Toast -->

	<footer id="colophon" class="site-footer">
		<div class="site-info">
            <div class="dashboard-footer">
                &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.
            </div>
		</div>
	</footer>
</div>

<?php wp_footer(); ?>
</body>
</html>