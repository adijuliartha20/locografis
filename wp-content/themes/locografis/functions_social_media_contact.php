<?php 
class MySettingsPage{
	private $options;

	public function __construct(){
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page(){
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Website Info', 
            'manage_options', 
            'my-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page(){
        // Set class property
        $this->options = get_option( 'my_option_name' );
        ?>
        <div class="wrap">
            <h1>Website Info</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'my-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    public function page_init(){        
        register_setting(
            'my_option_group', // Option group
            'my_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Social Media', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );  

        add_settings_field('facebook', 'Facebook', array( $this, 'facebook_callback' ), 'my-setting-admin', 'setting_section_id');      
        add_settings_field('instagram', 'Instagram', array( $this, 'instagram_callback' ), 'my-setting-admin', 'setting_section_id'); 

        add_settings_section('setting_section_id_2','Contact Info',array( $this, 'print_section_info_2' ),'my-setting-admin');
        add_settings_field('phone', 'Phone', array( $this, 'phone_callback' ), 'my-setting-admin', 'setting_section_id_2');      
        add_settings_field('phone_2', 'Alternative Phone', array( $this, 'phone_2_callback' ), 'my-setting-admin', 'setting_section_id_2');      
        add_settings_field('address', 'Address', array( $this, 'address_callback' ), 'my-setting-admin', 'setting_section_id_2');      

        add_settings_section('setting_section_id_3','Mailer Setting',array( $this, 'print_section_info_2' ),'my-setting-admin');
        add_settings_field('email_smtp_custom', 'Email SMTP', array( $this, 'email_smtp_callback' ), 'my-setting-admin', 'setting_section_id_3');      
        add_settings_field('pass_smtp_custom', 'Password', array( $this, 'pass_smtp_callback' ), 'my-setting-admin', 'setting_section_id_3'); 

        add_settings_field('smtp_server', 'SMTP Server', array( $this, 'smtp_server_callback' ), 'my-setting-admin', 'setting_section_id_3');      
        add_settings_field('port_smtp_server', 'Port SMTP Server', array( $this, 'port_smtp_server_callback' ), 'my-setting-admin', 'setting_section_id_3');      

        add_settings_field('subject_email', 'Subject Email', array( $this, 'subject_email_callback' ), 'my-setting-admin', 'setting_section_id_3'); 

    }

    public function sanitize( $input ){
        $new_input = array();
        //if( isset( $input['id_number'] ) )$new_input['id_number'] = absint( $input['id_number'] );
        //if( isset( $input['title'] )) $new_input['title'] = sanitize_text_field( $input['title'] );

        if( isset( $input['instagram'] )) $new_input['instagram'] = sanitize_text_field( $input['instagram'] );
        if( isset( $input['facebook'] )) $new_input['facebook'] = sanitize_text_field( $input['facebook'] );

        if( isset( $input['phone'] )) $new_input['phone'] = sanitize_text_field( $input['phone'] );
        if( isset( $input['phone_2'] )) $new_input['phone_2'] = sanitize_text_field( $input['phone_2'] );
        if( isset( $input['address'] )) $new_input['address'] = sanitize_text_field( $input['address'] );

        if( isset( $input['email_smtp_custom'] )) $new_input['email_smtp_custom'] = sanitize_text_field( $input['email_smtp_custom'] );
        if( isset( $input['pass_smtp_custom'] )) $new_input['pass_smtp_custom'] = sanitize_text_field( $input['pass_smtp_custom'] );

        if( isset( $input['smtp_server'] )) $new_input['smtp_server'] = sanitize_text_field( $input['smtp_server'] );
        if( isset( $input['port_smtp_server'] )) $new_input['port_smtp_server'] = sanitize_text_field( $input['port_smtp_server'] );

        if( isset( $input['subject_email'] )) $new_input['subject_email'] = sanitize_text_field( $input['subject_email'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    public function print_section_info_2()
    {
        print 'Enter your settings below:';
    }


    /** 
     * Get the settings option array and print one of its values
     */
    public function title_callback(){
        printf(
            '<input type="text" id="title" name="my_option_name[title]" value="%s" />',
            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
        );
    }

    public function instagram_callback(){
        printf(
            '<input type="text" id="instagram" name="my_option_name[instagram]" value="%s" />',
            isset( $this->options['instagram'] ) ? esc_attr( $this->options['instagram']) : ''
        );
    }
    public function facebook_callback(){
        printf(
            '<input type="text" id="facebook" name="my_option_name[facebook]" value="%s" />',
            isset( $this->options['facebook'] ) ? esc_attr( $this->options['facebook']) : ''
        );
    }

    //Contact Info Callback

    public function phone_callback(){
        printf(
            '<input type="text" id="phone" name="my_option_name[phone]" value="%s" />',
            isset( $this->options['phone'] ) ? esc_attr( $this->options['phone']) : ''
        );
    }

    public function phone_2_callback(){
        printf(
            '<input type="text" id="phone_2" name="my_option_name[phone_2]" value="%s" />',
            isset( $this->options['phone_2'] ) ? esc_attr( $this->options['phone_2']) : ''
        );
    }

    public function address_callback(){
        printf(
            '<input type="text" id="address" name="my_option_name[address]" value="%s" class="regular-text" />',
            isset( $this->options['address'] ) ? esc_attr( $this->options['address']) : ''
        );
    }

    //SMTP Callback

    public function email_smtp_callback(){
        printf(
            '<input type="text" id="email_smtp_custom" name="my_option_name[email_smtp_custom]" value="%s" />',
            isset( $this->options['email_smtp_custom'] ) ? esc_attr( $this->options['email_smtp_custom']) : ''
        );
    }

    public function pass_smtp_callback(){
        printf(
            '<input type="password" id="pass_smtp_custom" name="my_option_name[pass_smtp_custom]" value="%s" />',
            isset( $this->options['pass_smtp_custom'] ) ? esc_attr( $this->options['pass_smtp_custom']) : ''
        );
    }

    public function subject_email_callback(){
        printf(
            '<input type="text" id="subject_email" name="my_option_name[subject_email]" value="%s" class="large-text" />',
            isset( $this->options['subject_email'] ) ? esc_attr( $this->options['subject_email']) : ''
        );
    }

    public function smtp_server_callback(){
        printf(
            '<input type="text" id="smtp_server" name="my_option_name[smtp_server]" value="%s" />',
            isset( $this->options['smtp_server'] ) ? esc_attr( $this->options['smtp_server']) : ''
        );
    }
    public function port_smtp_server_callback(){
        printf(
            '<input type="text" id="port_smtp_server" name="my_option_name[port_smtp_server]" value="%s" />',
            isset( $this->options['port_smtp_server'] ) ? esc_attr( $this->options['port_smtp_server']) : ''
        );
    }

}


if( is_admin()) $my_settings_page = new MySettingsPage();

?>