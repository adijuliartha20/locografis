<?php 
/**
 * Plugin Name: Google Map
 * Plugin URI: http://www.lumonata.com/
 * Description: Google Map Plugin
 * Version: 0.1
 * Author URI: http://www.lumonata.com/
 * Author : Adi Juliartha
 */
 
class GoogleMapPage{
	private $options;
	var $field_array = array('latitude'=>'Latitude','langitude'=>'Langitude','zoom'=>'Zoom','key'=>'Key');
	var $setting_name = 'google_map';
	var $setting_label = 'Google Map';

    public function __construct(){
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    public function add_plugin_page(){
        // This page will be under "Settings"
        add_options_page('Settings Admin', $this->setting_label, 'manage_options', $this->setting_name.'-admin', array( $this, 'create_admin_page' ));
    }
	
    public function create_admin_page(){
        // Set class property
        $this->options = get_option($this->setting_name);
        ?>
        <div class="wrap">
            <h2><?php echo $this->setting_label; ?></h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group_'.$this->setting_name);   
                do_settings_sections( $this->setting_name.'-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

 
    public function page_init(){        
        register_setting(
            'my_option_group_'.$this->setting_name, // Option group
            $this->setting_name, // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

		add_settings_section(
            'setting_section_id', // ID
            'My Custom '.$this->setting_label, // Title
            array( $this, 'print_section_info' ), // Callback
            $this->setting_name.'-admin' // Page
        );  
		
		foreach($this->field_array as $key => $val){
			add_settings_field($key, $val, array( $this, 'input_callback' ), $this->setting_name.'-admin', 'setting_section_id' ,$key); 	
		}	
    }

  
    public function sanitize($input){
        $new_input = array();
		foreach($this->field_array as $key => $val){
			$new_input[$key] = sanitize_text_field( $input[$key] );	
		}
        return $new_input;
    }
	
	function input_callback($key){
		$value = '';
		if($key!='snipset') $value = ( isset( $this->options[$key] ) ? esc_attr( $this->options[$key]) : '');
		else $value = html_entity_decode(get_option('my_option',htmlentities($this->options[$key])));
		
		printf(
            '<input type="text" id="'.$key.'" name="'.$this->setting_name.'['.$key.']" value="%s" class="regular-text" />', $value 
        );
	}	
}
if(is_admin()) $my_settings_page = new GoogleMapPage();

function set_map(){
    $google_map = get_option('google_map');
    $key = $google_map['key'];
    wp_enqueue_script('google-map-js',plugin_dir_url( __FILE__ ) . 'script.js',false,'1.1',true);
    wp_enqueue_script('google-map','https://maps.googleapis.com/maps/api/js?callback=initMap&key='.$key,false,'1.1',true);
    
    wp_localize_script(
            'google-map-js',
            'Dgm',
            array(
                'lat' => $google_map['latitude'],
                'lang'=> $google_map['langitude'],
                'zoom'=>$google_map['zoom'],
            )
    );
}


function set_map_header(){
	wp_enqueue_script('google-map-js',plugin_dir_url( __FILE__ ) . 'script.js',false,'1.1',true);
	wp_enqueue_script('google-map','https://maps.googleapis.com/maps/api/js?callback=initMap',false,'1.1',true);
	$google_map = get_option('google_map');
	//print_r($google_map);	
	wp_localize_script(
			'google-map-js',
			'Dgm',
			array(
				'lat' => $google_map['latitude'],
				'lang'=> $google_map['langitude'],
				'zoom'=>$google_map['zoom'],
			)
	);
	?>
    <div class="container container-25">
    	<div class="container container-27 clearfix">
          <div id="map" class="wrapper"></div>
          <div class="element element-15"></div>
          <h1><?php echo the_title(); ?></h1>
        </div>
    </div>
    <?php
}


?>