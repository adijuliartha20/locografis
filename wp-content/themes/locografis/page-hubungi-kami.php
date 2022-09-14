<?php get_header();?>
<?php if ( have_posts() ) : ?>

<?php 
	while ( have_posts() ) : the_post();
		contact_form($post);
	endwhile; ?>
<?php else :
	get_template_part( 'content', 'none' );		
endif;?>
<?php get_footer();?>