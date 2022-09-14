<?php get_header();?>
<?php 
if (have_posts()) :
	$args = array('post_type'=>'portofolio', 'posts_per_page' => 10);                    
	$the_query = new WP_Query( $args );
	if ( $the_query->have_posts() ) {
		?>
		<div class="content-portofolio content content-gray">
			<?php 
				$arr_tax = array();
				$arr = array();		    
			    while ( $the_query->have_posts() ) {
			        $the_query->the_post();
			        $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID ),'medium');	        
				    // Get post type by post.
				    $post_type = $post->post_type;			 
				    // Get post type taxonomies.
				    $taxonomies = get_object_taxonomies( $post_type,'objects');			 	
				 	$arr_category =  array();			    	 
				    foreach ( $taxonomies as $taxonomy_slug => $taxonomy ){			 
				        // Get the terms related to post.
				        $terms = get_the_terms( $post->ID, $taxonomy_slug );			 
				        if ( ! empty( $terms ) ) {
				            foreach ( $terms as $term ) {
				            	$categories = array('name'=>$term->name,'slug'=>$term->slug);
				            	$arr_tax[$term->term_order] = $categories;
				            	array_push($arr_category, $categories);

				            }			            
				        }
				    }
				    array_push($arr,array('title'=>$post->post_title,'sef'=>$post->post_name,'image'=>$image[0], 'category'=>$arr_category));
			    }
			?>
			<div class="middle">
				<div class="container-title-portofolio clearfix">
					<h1 class="fleft"><strong>Portofolio.</strong></h1>	
					<?php
						if(!empty($arr_tax)){?>
							<ul id="filter-portofolio" class="filter-portofolio fright">
								<li><button class="button current-state" data-filter=".item-portofolio" onclick="filter_portofolio(event)">All</button></li>
								<?php 
									foreach ($arr_tax as $key => $filter) { ?>
										<li> / <button class="button" data-filter="<?php echo '.'.$filter['slug']; ?>"  onclick="filter_portofolio(event)"><?php echo $filter['name']; ?></button></li>
									<?php 
									}
								?>
							</ul>
						<?php
						}
					?>
				</div>
				
				<?php
				    if(!empty($arr)){ ?>
				    	<div id="container-portfolio-block" class="container-portfolio-block">
					    	<?php 
					    	set_query_var('portofolio', $arr);
			    			get_template_part('content', 'portofolio-list');
			    			?>
		    				<div id="overlay" class="overlay"></div>
		    			</div>
		    			<?php
				    }
			    ?>		    	
			</div>


	    </div>

	    <div id="clone" class="hide">
	    	<?php 	
	    		set_query_var('clone', '-clone');
				get_template_part('content', 'portofolio-list');
	    	?>
	    </div>
	    <?php 
	}
endif;
?>

<?php 
	get_template_part('content', 'tertarik-dengan-kerja-kami');
?>

<?php get_footer();?>