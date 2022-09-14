<?php get_header();?>
<?php 
if ( have_posts() ) :
	get_template_part( 'content', 'portofolio-home' );
endif;
?>
<?php get_footer();?>