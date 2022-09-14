<?php get_header();?>
<?php if ( have_posts() ) : ?>

<?php 
	while ( have_posts() ) : the_post(); ?>
	<div class="content content-page<?php echo ' content-'.$post->post_name?>">
	<?php 
		//print_r($post);
		the_content();
	?>	
	</div>
	<?php endwhile; ?>
<?php else :
	get_template_part( 'content', 'none' );		
endif;?>
<?php get_footer();?>