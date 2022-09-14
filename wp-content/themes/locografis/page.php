<?php get_header();?>
<?php if ( have_posts() ) : ?>
<div class="content">
<?php 	
	echo 'page';
	//get_template_part( 'content', 'home' );
?>
</div>     
<?php else :
	get_template_part( 'content', 'none' );		
endif;?>
<?php get_footer();?>