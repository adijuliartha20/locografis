<div class="content-portofolio-single content">
	<div class="middle">
		<h1 class="title-portofolio-single"><?php the_title(); ?></h1>
		<ul class="list-category list-category-single">
			<?php 
				$all = count($dt['category']);$n = 1;
				foreach ($dt['category'] as $key => $cat) { ?>
					<li>
						<a href="<?php echo get_site_url().'/portofolio/#'.$cat['slug']; ?>">
							<?php echo $cat['name'];if($n<$all) echo ',';?>
						</a>
					</li>	
					<?php 
					$n++;
				}
			?>
		</ul>
		<div class="brief-portofolio"><?php the_content(); ?></div>


		<div class="year-link clearfix">
			<?php 
				if(isset($dt['year']) && !empty($dt['year'])){?>
					<div class="year fleft">Year: <?php echo $dt['year']; ?></div>
				<?php 
				}

				if(isset($dt['link_website']) && !empty($dt['link_website'])){?>
					<div class="link_website fright"><a href="<?php echo $dt['link_website']; ?>" target="_blank">Visit: <?php echo $dt['link_website']; ?></a></div>
				<?php 
				}
			?>
		</div>
	</div>

	<div class="list-image-portofolio">
		<?php 
			add_filter( 'use_default_gallery_style', '__return_false' );
			echo do_shortcode($dt['images']); 
		?>
	</div>

	<?php
			global $wp;
			$current_url = home_url(add_query_arg(array(),$wp->request));
	?>
	<div class="nav-n-share clearfix">
		<div class="prev-next fleft">
			<ul>
				<li class="prev clearfix"><?php previous_post_link('%link', 'Previous post »'); ?></li>
				<li class="next clearfix"><?php next_post_link('%link', 'Next post »'); ?></li>
			</ul>		
		</div>
		<div class="share-post fright">
			<b>Share:</b>
			<a id="share-fb" class="share-fb" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $current_url; ?>" onclick="return fbs_click('share-fb')" target="_blank" title="Share This on Facebook" rel="nofollow" data-url="https://www.facebook.com/sharer/sharer.php?u=">Facebook</a>
		</div>
	</div>
</div>

<?php get_template_part('content', 'tertarik-dengan-kerja-kami');?>

 