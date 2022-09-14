<?php get_header();?>
<?php 
if (have_posts()) :
	while ( have_posts() ) : the_post();
		$dt = array();
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
	    $dt['category'] = $arr_category;
	    //get_meta_data
	    $arr_metadata = array('year','link_website','images');
	    foreach ($arr_metadata as $idx => $key) {
	    	$dt[$key] = get_post_meta($post->ID,$key,true);
	    }

		set_query_var('dt', $dt);
		get_template_part('content', 'portofolio-single');
	endwhile;
endif;
?>
<?php get_footer();?>