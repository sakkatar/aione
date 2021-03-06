<?php get_header(); ?>
	<div id="content" <?php Aione()->layout->add_class( 'content_class' ); ?> <?php Aione()->layout->add_style( 'content_style' ); ?>>
		<?php if( category_description() ): ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class( 'oxo-archive-description' ); ?>>
			<div class="post-content">
				<?php echo category_description(); ?>
			</div>
		</div>
		<?php endif; 
		?>

		<?php get_template_part( 'templates/blog', 'layout' ); ?>
	</div>
	<?php do_action( 'oxo_after_content' ); ?>
<?php get_footer();

// Omit closing PHP tag to avoid "Headers already sent" issues.
