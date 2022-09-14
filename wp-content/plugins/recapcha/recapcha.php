<?php 
/**
 * Plugin Name: Recaphca
 * Plugin URI: http://www.locografis.com/
 * Description: Load recapcha form
 * Version: 0.1
 * Author URI: http://www.locografis.com/
 */
 
class RecapchaPage{
	private $options;
	var $field_array = array('site_key'=>'Site key','secret_key'=>'Secret key','snipset'=>'Snipset');
	var $setting_name = 'recapcha';
	var $setting_label = 'Recapcha';

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
			if( isset( $input[$key]) && $key!='snipset')  $new_input[$key] = sanitize_text_field( $input[$key] );	
			if(isset( $input[$key]) && $key=='snipset')  $new_input[$key] = htmlentities(stripslashes($input[$key] ));//sanitize_text_field( $input[$key] );	
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
if(is_admin()) $my_settings_page = new RecapchaPage();
/*
function valid_capcha($response){
	$recapcha = get_option('recapcha');
	$secret = $recapcha['secret_key'];
	$remoteip = $_SERVER["REMOTE_ADDR"];
	
	$postdata = http_build_query(
		array(
			'secret' => $secret,
			'response' => $response,
			'remoteip' => $remoteip
		)
	);
	
	$opts = array('http' =>
		array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/x-www-form-urlencoded',
			'content' => $postdata
		)
	);
	//print_r($postdata);
	$context  = stream_context_create($opts);
	$result = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
	$result = json_decode($result);
	if($result->success) return true;
	else return false;
}*/



?>