<?php if ( have_posts() ) :
	$img = "";
	while ( have_posts() ) : the_post();
		//$media = get_attached_media( 'image', $posts[0]->ID );print_r($media);
		$attachments = get_posts( array(
			'post_type' => 'attachment',
			'posts_per_page' => -1,
			'post_parent' => $posts[0]->ID,
			'exclude'     => get_post_thumbnail_id()
		));

		if($attachments) {?>
		<!-- Slide Start -->
		<div class="wrap-slide">
			<!--<a class="logo-slide"><img src="<?php echo get_template_directory_uri().'/images/Icon_logo-white.svg' ?>"></a>
			<?php wp_nav_menu( array('menu' => 'Slide','menu_class'=>'menu-slide clearfix','link_before'=> '<span>','link_after'=> '</span>',) ); ?> -->

			<?php
			if(isset($posts[0]->post_content) && !empty($posts[0]->post_content)){?>
				<div class="brief-slide"><?php the_content();?></div>
			<?php }
			?>
			


			<div id="slide" class="slide">	
			<?php	
			foreach ($attachments as $attachment) {
				$image = wp_get_attachment_image_src( $attachment->ID ,'large');
				if(!empty($image)){
					echo '<input type="hidden" value="'.$image[0].'">';
				}
			}?>
			</div>


			<?php 
				$opt = get_option('my_option_name');
				$arr = array('facebook','instagram');
				$has_sosmed =  false;
				$item_sosmed = '';
				foreach ($arr as $key => $sosmed) {
					if(isset($opt[$sosmed]) && !empty($opt[$sosmed])){
						$item_sosmed .= '<li class="link-'.$sosmed.'"><a href="'.$opt[$sosmed].'" target="_blank"><img src="'.get_template_directory_uri().'/images/'.$sosmed.'.svg"></a></li>';
						$has_sosmed = true;
					}
				}

				if($has_sosmed){?>
				<ul class="sosmed-slide"><?php echo $item_sosmed; ?></ul>
				<?php }
			?>

			<button class="scroll-down-slide" rel="content-portofolio-homes" onClick="scroll_to(event,'content-portofolio-home',70)" ><img src="<?php echo get_template_directory_uri().'/images/Icon_arrow-scrolldown-homepage.svg' ?>"></button>
		</div>
		<!-- Slide End -->
		<?php
		}
	endwhile;
endif;
 ?>