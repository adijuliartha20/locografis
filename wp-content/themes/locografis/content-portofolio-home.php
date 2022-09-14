<?php
	$args = array('post_type'=>'portofolio', 'posts_per_page' => 4);                    
	$the_query = new WP_Query( $args );
	if ( $the_query->have_posts() ) {
		?>
		<div id="content-portofolio-home" class="content-portofolio content-portofolio-home content content-yellow">
			<div class="middle">
				<div class="container-title-portofolio clearfix">
					<h1><strong>Portofolio.</strong></h1>
				</div>
			<?php
			$arr = array();		    
		    while ( $the_query->have_posts() ) {
		        $the_query->the_post();//print_r($post);
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
			            	array_push($arr_category, $categories);			            
			            }			            
			        }
			    }
			    array_push($arr,array('title'=>$post->post_title,'sef'=>$post->post_name,'image'=>$image[0], 'category'=>$arr_category));
		    }

		    if(!empty($arr)){ 
		    	set_query_var('portofolio', $arr);
    			get_template_part('content', 'portofolio-list');
		    }

		    ?>

		    	<a class="show-all-portofolio btn-black" href="<?php echo get_site_url().'/portofolio/'; ?>">Lihat Semua Desain</a>
		    </div>

	    </div>
	    <?php 
	}
?>