<?php 
add_action( 'init', 'create_post_type_portofolio' );

function create_post_type_portofolio() {
	$name = 'Portofolio';
	$labels = array(
		'name'                => _x( $name, 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( $name, 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( $name, 'text_domain' ),
		'parent_item_colon'   => __( 'Parent '.$name.':', 'text_domain' ),
		'all_items'           => __( 'All '.$name, 'text_domain' ),
		'view_item'           => __( 'View '.$name, 'text_domain' ),
		'add_new_item'        => __( 'Add New '.$name, 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'edit_item'           => __( 'Edit '.$name, 'text_domain' ),
		'update_item'         => __( 'Update '.$name, 'text_domain' ),
		'search_items'        => __( 'Search '.$name, 'text_domain' ),
		'not_found'           => __( 'No '.$name.' found', 'text_domain' ),
		'not_found_in_trash'  => __( 'No '.$name.' found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'portofolio', 'text_domain' ),
		'description'         => __( $name.' information pages', 'text_domain' ),
		'labels'              => $labels,
		'supports' => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_icon'           => 'dashicons-admin-page',
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'taxonomies'          => array( 'category' )
	);
	add_theme_support( 'post-thumbnails' );
	//add_theme_support( 'post-thumbnails', array( 'post','gallery') );
	
	register_post_type('portofolio', $args);	
}

function title_portofolio( $title ){
     $screen = get_current_screen();
     if  ( 'portofolio' == $screen->post_type ) {
          $title = 'Enter Portofolio name here';
     } 
     return $title;
}
add_filter( 'enter_title_here', 'title_portofolio' );

add_filter('pll_get_post_types', 'my_pll_get_post_portofolio');
function my_pll_get_post_portofolio($types) {
	return array_merge($types, array('portofolio' => 'portofolio'));	
}


function setting_field_portofolio(){
	/**
	 * Class for adding a new field to the options-reading.php page
	 */
	class Add_Settings_Field {

		/**
		 * Class constructor
		 */
		public function __construct() {
			add_action( 'admin_init' , array( $this , 'register_fields' ) );
		}

		/**
		 * Add new fields to wp-admin/options-reading.php page
		 */
		public function register_fields() {
			register_setting( 'reading', 'portofolio_view_val', 'esc_attr' );
			add_settings_field(
				'portofolio_view',
				'<label for="portofolio_view">' . __( 'Portofolio pages show at most' , 'portofolio_view_val' ) . '</label>',
				array( $this, 'fields_html' ),
				'reading'
			);
		}

		/**
		 * HTML for extra settings
		 */
		public function fields_html() {
			$value = get_option( 'portofolio_view_val', '' );
			echo '<input type="number" id="portofolio_view" name="portofolio_view_val" value="' . esc_attr( $value ) . '" class="small-text"/> items';
		}

	}
	new Add_Settings_Field();	
	
}
add_action( 'init', 'setting_field_portofolio' );



/**
 * Register meta box(es).
 */
function wpdocs_register_meta_boxes() {
	$screens = array('portofolio');
    foreach ( $screens as $screen ) {
    	add_meta_box( 'meta-box-id-'.$screen, __( 'Additional Field', 'textdomain' ), 'wpdocs_my_display_callback', $screen );	
    }
    
}
add_action( 'add_meta_boxes', 'wpdocs_register_meta_boxes' );
 
/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function wpdocs_my_display_callback( $post ) {
    // Display code/markup goes here. Don't forget to include nonces!
    // Add an nonce field so we can check for it later.
  	wp_nonce_field( 'myplugin_inner_custom_box_price_list', 'myplugin_inner_custom_box_price_list_nonce' );
    
    $arr_meta = arr_adf_price_list() ;   

	foreach ($arr_meta as $key => $dt_field) {
		$type = $dt_field['type'];
		$title = $dt_field['title'];
		$value_meta = get_post_meta( $post->ID, $key, true );
		$unit_lable = (isset($dt_field['unit_lable'])? '&nbsp;'.$dt_field['unit_lable']:'');

		echo '<div style="margin-bottom:10px;"><label for="'.$key.'" style="width:100px; float:left; margin-top:5px">';
		   _e( $title, 'myplugin_textdomain' );
		echo '</label> ';

		if($type=='select'){
			$options = $dt_field['option'];
			echo '<select id="'.$key.'" name="'.$key.'" >';
			foreach ($options as $key_opt => $label) {
				echo '<option value="'.$key_opt.'" '.($key_opt==$value_meta?'selected':'').'>'.$label.'</option>';
			}
			echo '</select>'.$unit_lable;
		}else if($type=='textarea'){
			echo '<textarea class="" name="'.$key.'" style="width:50%; min-width:500px;min-height:100px;">' . esc_attr( $value_meta ) . '</textarea>';
		}else if($type=='number'){
			
			echo '<input type="number" id="'.$key.'" name="'.$key.'" value="' . esc_attr( $value_meta ) . '" size="70" />'.$unit_lable;
		}else if($type=='wp_editor'){
			wp_editor( $value_meta, $key );
		}else{	
			echo '<input type="text" id="'.$key.'" name="'.$key.'" value="' . esc_attr( $value_meta ) . '" size="70" />';
		}
		echo '<br/></div>';
		
	}

}
 
/**
 * Save meta box content.
 *
 * @param int $post_id Post ID
 */
function wpdocs_save_meta_box( $post_id ) {
    // Save logic goes here. Don't forget to include nonce checks!
    if ( ! isset( $_POST['myplugin_inner_custom_box_price_list_nonce'] ) )return $post_id;  
	$nonce = $_POST['myplugin_inner_custom_box_price_list_nonce'];  
	if (! wp_verify_nonce( $nonce, 'myplugin_inner_custom_box_price_list' ))return $post_id;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )  return $post_id;

	// Check the user's permissions.
	if ( 'price-list' == $_POST['post_type'] ) {
	if ( ! current_user_can( 'edit_page', $post_id ) ) return $post_id;
	} else { 
		if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
	}

	$arr_meta = arr_adf_price_list() ;
	foreach ($arr_meta as $key => $title) {
		$value_meta = sanitize_text_field( $_POST[$key] );
		update_post_meta( $post_id, $key, $value_meta);
	}
}
add_action( 'save_post', 'wpdocs_save_meta_box' );

function arr_adf_price_list(){
	/*$arr_meta = array('time'=>
								array(	'type'=>'select',
										'title' => 'Time',
										'option'=>array(1,2,3,4,5,6,7,8,9,10,11,12),
										'unit_lable'=>'hours'
								),
						'price'=> array('type'=>'number','title'=>'Price','unit_lable'=>'USD'),
						'videographers'=> array('type'=>'number','title'=>'Videographers'),
						'camera'=> array('type'=>'text','title'=>'Camera'),$arr_meta = array(
						'tools'=> array('type'=>'textarea','title'=>'Tools'),
						'final_result'=> array('type'=>'textarea','title'=>'Final Result'),);*/
	$arr_meta = array(
						'year'=> array('type'=>'text','title'=>'Year'),
						'link_website'=> array('type'=>'text','title'=>'Link Website'),
						//'images'=> array('type'=>'wp_editor','title'=>'Images'),
						'time'=>
								array(	'type'=>'select',
										'title' => 'Time',
										'option'=>array(1,2,3,4,5,6,7,8,9,10,11,12),
										'unit_lable'=>'hours'
								)
					);					
	return $arr_meta;
}






?>