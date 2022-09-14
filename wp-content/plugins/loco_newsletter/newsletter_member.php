<?php 
class Newsletter_Member_List extends WP_List_Table {

	//Class constructor
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Newsletter Member', 'sp' ), //singular name of the listed records
			'plural'   => __( 'newsletter_member', 'sp' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

	}


	
	// Retrieve newsletter data from the database
	//
	// @param int $per_page
	// @param int $page_number
	//
	// @return mixed
	
	public static function get_data($per_page = 5, $page_number = 1) {
		global $wpdb;


		$sql = "SELECT a.*, b.display_name FROM {$wpdb->prefix}newsletter_member a inner join wp_users b on a.author_update=b.ID";

		if (!empty( $_REQUEST['status'] ) && $_REQUEST['status']!='all' ) {
			$sql .= " where a.status='".esc_sql( $_REQUEST['status'])."'";
		}else{
			$sql .= " where a.status <> 'trash'";
		}

		if ( ! empty( $_REQUEST['s'] ) ) {
			$sql .= " and a.email like '%".esc_sql( $_REQUEST['s'])."%' or a.first_name like '%".esc_sql( $_REQUEST['s'])."%' or 
					 a.last_name like '%".esc_sql( $_REQUEST['s'])."%' or a.country like '%".esc_sql( $_REQUEST['s'])."%' ";
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}else {
			$sql .= ' ORDER BY a.menu_order';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}


	
	// Delete a customer record.	 
	// @param int $id customer ID	 
	public static function delete_data( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}newsletter_member",
			[ 'id_member' => $id ],
			[ '%d' ]
		);

		//echo 
	}

	public static function update_status($id,$status){
		global  $wpdb;

		$wpdb->update(
						"{$wpdb->prefix}newsletter_member",
						['status' => $status],
						['id_member'=>$id]
					);
	}


	// Returns the count of records in the database.	
	// @return null|string	
	public static function record_count($status_fix="") {	
		global $wpdb;
		$status = (isset($_REQUEST['status'])? $_REQUEST['status']: '');
		$status_fix = ($status_fix!=""? $status_fix : $status);
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}newsletter_member ".($status_fix!='' && $status_fix!='all'? "where status='".$status_fix."'": "where status<> 'trash'");
		//echo $sql.'#';

		$return = $wpdb->get_var( $sql );
		if(empty($return)) $return = 0;
		return $return;

		//$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}newsletter_member";
		//return $wpdb->get_var( $sql );
	}


	//Text displayed when no customer data is available 
	public function no_items() {
		_e( 'No member avaliable.', 'sp' );
	}


	//Render a column when no column specific method exist.	
	// @param array $item
	// @param string $column_name	
	// @return mixed	
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {			
			case 'email':
				return $item[ $column_name ];
			case 'sex':
				return $this->arr_sex[$item[$column_name]];
			case 'country':
				return $item[ $column_name ];
			case 'type_member':
				return $this->arr_type[$item[$column_name]];
			case 'display_name':
				return $item[ $column_name ];	 				
			case 'status':
				return $this->arr_status[$item[$column_name]]; 					
			case 'created':
				return $item[ $column_name ];			
			case 'menu_order':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	var $arr_sex = array('Women','Male');	
	var $arr_status = array('unverified'=>'Unverified','verified'=>'Verified','trash'=>'Trash');
	var $arr_type = array('all'=>'All','entrepreneur'=>'Entrepreneur','profesional'=>'Profesional','employee'=>'Employee','accommodation'=>'Accommodation');



	public function list_status(){
		global $wpdb;
		$arr_status = $this->arr_status;
		$return ="";
		$status = (isset($_REQUEST['status'])? $_REQUEST['status']: '');

		//if($this->record_count()>0){ ?>
			<ul class="subsubsub">
				<a href="<?php echo "?page=".esc_attr($_REQUEST['page']).""; ?>" class="<?php echo ($status=='all' || $status==''?'current':''); ?>">All<span class="count">(<?php echo $this->record_count('all'); ?>)</span></a>			
				<?php 
				$n = 0;
				foreach ($arr_status as $key => $value) {
					$link = "?page=".esc_attr($_REQUEST['page'])."&status=".$key;
					?>
					<li>
						| <a href="<?php echo $link; ?>" class="<?php echo ($status==$key?'current':''); ?>"><?php echo $value ?><span class="count">(<?php echo $this->record_count($key); ?>)</span></a>
					</li>
					<?php
					$n++;
				}
				?>			
			</ul>
			<?php 
		//}
		echo $return;
	}

	// Render the bulk edit checkbox	
	// @param array $item
	// @return string
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id_member']
		);
	}


	
	// Method for name column
	//
	// @param array $item an array of DB data
	// 
	// @return string
	//
	// repalce column_subject to column_nameofcolum

	function column_first_name( $item ) {
		$edit_nonce = wp_create_nonce( 'sp_edit_data' );
		$delete_nonce = wp_create_nonce( 'sp_delete_data' );
		$title = '<strong>'.$item['first_name'].' '.$item['last_name'].'</strong>';
		
		$status = (isset($_GET['status'])? $_GET['status'] : '');
		$actions = array();

		if(isset($_GET['status']) && $_GET['status']=='trash'){
			$actions['untrash'] = sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s&paged=%s&status=%s">Restore</a>', esc_attr( $_REQUEST['page'] ), 'untrash', absint( $item['id_member'] ), $delete_nonce, $_GET['paged'], $status);
			$actions['delete'] = sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s&paged=%s&status=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id_member'] ), $delete_nonce, $_GET['paged'], $status);
		}else{
			$actions['edit'] =  sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s&paged=%s&status=%s">Edit</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id_member'] ), $edit_nonce, $_GET['paged'], $status );
			$actions['trash'] = sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s&paged=%s&status=%s">Trash</a>', esc_attr( $_REQUEST['page'] ), 'trash', absint( $item['id_member'] ), $trash_nonce, $_GET['paged'], $status );
		}
		return $title . $this->row_actions( $actions );
	}


	//
	//  Associative array of columns
	//
	// @return array
	//
	function get_columns() {
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'first_name'    => __( 'Name', 'sp' ),
			'email' => __( 'Email', 'sp' ),
			'sex'    => __( 'Sex', 'sp' ),
			'country'    => __( 'Country', 'sp' ),
			'type_member'    => __( 'Type', 'sp' ),
			'display_name'    => __( 'Author', 'sp' ),			
			'status'    => __( 'Status', 'sp' ),
			'created'    => __( 'Created', 'sp' ),			
			'menu_order'    => __( 'Order ID', 'sp' )
		];

		return $columns;
	}


	//
	// Columns to make sortable.
	//
	// @return array
	//
	public function get_sortable_columns() {
		$sortable_columns = array(
			'first_name' => array( 'a.first_name', true ),
			'email' => array( 'a.email', false ),
			'type_member' => array( 'a.type_member', false ),
			'country' => array( 'a.country', false ),
			'created' => array( 'a.created', false ),
			'display_name' => array( 'b.display_name', false ),
			'menu_order' => array( 'a.menu_order', true )
		);
		return $sortable_columns;
	}

	//
	// Returns an associative array containing the bulk action
	//
	// @return array
	//
	public function get_bulk_actions() {
		if(isset($_GET['status']) && $_GET['status']=='trash'){
			$actions = [
				'bulk-untrash' => 'Restore',
				'bulk-delete' => 'Delete'
			];
		}else{
			$actions = [
				//'bulk-edit' => 'Edit',
				'bulk-trash' => 'Trash'
			];
		}
		return $actions;
	}


	
	//Handles data query and filter, sorting, and pagination.	
	public function prepare_items() {
		// Process bulk action
		$this->process_bulk_action();

		$this->_column_headers = $this->get_column_info();		

		$per_page     = $this->get_items_per_page( 'newsletter_per_page', 100 );
		
		$current_page = ( isset($_GET['paged']) && !empty($_GET['paged']) ? $_GET['paged'] : $this->get_pagenum()); 

		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_data( $per_page, $current_page );
	}

	public function process_bulk_action() {
	}

	function extra_tablenav( $which ) {
	    global $wpdb, $testiURL, $tablename, $tablet;
	    $move_on_url = '&cat-filter=';
	    $tablename = "{$wpdb->prefix}terms";
	    $term_group = 0;
	    $term_id = 'term_id';
	    
	    if ( $which == "top" ){ //return;      
	    }
	    if ( $which == "bottom" ){
	        //The code that goes after the table is there

	    }
	}

}


class SP_Plugin_Newsletter_Member {
	// class instance
	static $instance;

	// customer WP_List_Table object
	public $newsletter_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
	}


	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function plugin_menu() {
		//$hook = add_menu_page('Newsletter','Newsletter','manage_options','wp_list_table_newsletter',[ $this, 'plugin_settings_page' ]);
		$hook = add_submenu_page( 'wp_list_table_newsletter', 'Member', 'Member', 'manage_options', 'wp_list_table_newsletter_member', [ $this, 'plugin_settings_page' ]);
		add_action( "load-$hook", [ $this, 'screen_option' ] );
		//add_submenu_page( 'wp_list_table_newsletter', 'Member', 'Member', 'manage_options', 'wp_list_table_newsletter_member', 'wp_list_table_newsletter_member');
	}

	//
	// Plugin settings page
	//
	public function plugin_settings_page() {
		$Newsletter_Member_List = new Newsletter_Member_List();
		$action = $this->newsletter_obj->current_action();
		
		
		if($action=='edit') $this->edit();
		else if($action=='sorting') $this->sorting() ;
		else if($action=='new') $this->add_new();
		else{//view list
			$status = 'verified';
			if($action=='trash' || $action=='bulk-trash') $status = 'trash';
			if(isset($_POST['bulk-delete']) && ($action=='bulk-trash' || $action=='bulk-untrash')){
				$delete_ids = esc_sql( $_POST['bulk-delete'] );
				foreach ( $delete_ids as $id ) {
					$Newsletter_Member_List->update_status($id,$status);
				}
			}

			if($action=='trash' || $action=='untrash'){
				$Newsletter_Member_List->update_status($_GET['id'],$status);
			}

			if(isset($_POST['bulk-delete']) && $action=='bulk-delete'){
				$delete_ids = esc_sql( $_POST['bulk-delete'] );
				foreach ( $delete_ids as $id ) {
					$Newsletter_Member_List->delete_data( $id );
				}
			}

			if($action=='delete'){
				$Newsletter_Member_List->delete_data($_GET['id']);
			}
			$this->view_list();
		}
	}

	function view_list(){
		$this->view($paged);
		$v = '?v=1.0.0.'.time();
		wp_enqueue_style( 'slider', plugins_url( 'loco_newsletter/css/style.css'.$v , dirname(__FILE__) ));
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'custom-js', plugins_url( 'loco_newsletter/js/script.js'.$v , dirname(__FILE__) ) );	
	}


	function reset_menu_order($table_name){
		global $wpdb;
		$q = $wpdb->prepare("update $table_name set menu_order=menu_order+%d",1);
		$r = $wpdb->query($q);
	}
	
	function view(){
		$Newsletter_Member_List = new Newsletter_Member_List();
		$current_page = $Newsletter_Member_List->get_pagenum();
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline">Member</h2> <?php echo sprintf( '<a href="?page=%s&action=%s" class="page-title-action">Add New</a>', esc_attr( $_REQUEST['page'] ), 'new') ?>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder metabox-holder-full columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								$this->newsletter_obj->prepare_items();
								$this->newsletter_obj->search_box('Search', 'search_id');
								$this->newsletter_obj->list_status();
								$this->newsletter_obj->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
			<input type="hidden" id="sorting_action" value="sorting_newsletter_member">
			<input type="hidden" id="current_page" value="<?php echo $current_page; ?>">
			<input type="hidden" id="ajax-url" value="<?php echo admin_url('admin-ajax.php'); ?>">
		</div>
		<?php
	}

	//
	// Screen options
	//
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'newsletter',
			'default' => 5,
			'option'  => 'newsletter_per_page'
		];

		add_screen_option( $option, $args );

		$this->newsletter_obj = new Newsletter_Member_List();
	}


	/// Singleton instance
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	//Add & Edit Start//
	function add_new(){
		global $wpdb;
		$alert =  '';
		$dt = (object) array();
		if(isset($_POST['act']) && $_POST['act']=='new'){//edit process
			$error = 0;
			$first_notice = '<p>Failed Add New Member. </p>';
			$msg = '';
			$type_notice = 'error';
			
			if($_POST['first_name']==''){
				$msg .= '<p>First Name is require</p>';
				$error++;
			}
			if($_POST['last_name']==''){
				$msg .= '<p>Last Name is require</p>';
				$error++;
			}
			if($_POST['email']==''){
				$msg .= '<p>Email is require</p>';
				$error++;
			}

			if ($_POST['email'] !='' && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
				$msg .= '<p>Invalid format email</p>';
				$error++;
			}

			if ($_POST['email'] !='' && $this->exist_email($_POST['email']) > 0) {
				$msg .= '<p>Email already registerd</p>';
				$error++;
			}


			if($_POST['sex']==''){
				$msg .= '<p>Sex is require</p>';
				$error++;
			}
			if($_POST['country']==''){
				$msg .= '<p>Country is require</p>';
				$error++;
			}

			if($_POST['type_member']==''){
				$msg .= '<p>Member Type is require</p>';
				$error++;
			}

			if($error==0){
				$author = get_current_user_id();
				$id = $this->get_max_id() + 1;
				$created = date('Y-m-d H:i:s',time());
				$q = $wpdb->prepare("insert into {$wpdb->prefix}newsletter_member (id_member, email, first_name, last_name, sex, 
																				   status, country, type_member, created, menu_order,
																				   author,author_update) 
																				   values (%d,%s,%s,%s,%s,
																				   			%s,%s,%s,%s,%d,
																				   			%d,%d)",
																					$id, $_POST['email'], $_POST['first_name'], $_POST['last_name'], $_POST['sex'],
																					$_POST['status'], $_POST['country'], $_POST['type_member'], $created, 0,
																					$author, $author);//echo $q;
				$r = $wpdb->query($q);
				if($r){
					$type_notice  = 'success';
					$first_notice = '<p>Success Add New Member. </p>';
					$this->sort_again();
				}else $error++;
			}

			if($error>0){
				$dt->first_name 	= $_POST['first_name'];
				$dt->last_name 		= $_POST['last_name'];
				$dt->email 			= $_POST['email'];
				$dt->sex 			= $_POST['sex'];
				$dt->country 		= $_POST['country'];
				$dt->type_member 	= $_POST['type_member'];
				$dt->status 	= $_POST['status'];
			}

			$alert =  '	<div id="message" class="notice notice-'.$type_notice.' is-dismissible">
							'.$first_notice.'
							'.$msg.'
							<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
						</div>';
		}
		$this->form_action('Add New',$dt,'new',$alert);
	}

	function sort_again(){
		global $wpdb;
		$q = $wpdb->prepare("update {$wpdb->prefix}newsletter_member set menu_order = menu_order + 1 where id_member<>%d",0);
		$r = $wpdb->query($q);
	}

	function exist_email($email,$id=0){
		global $wpdb;

		if(empty($id)) $q = $wpdb->prepare("select COUNT(*) from {$wpdb->prefix}newsletter_member where email=%s",$email);
		else $q = $wpdb->prepare("select COUNT(*) from {$wpdb->prefix}newsletter_member where email=%s and id_member<>%d",$email,$id);
		return  $wpdb->get_var($q);
	}

	

	function edit(){
		global $wpdb;
		$id = $_GET['id'];
		$dt = (object) array();
		$alert = '';
		$error = 0;
		if(isset($_POST['act']) && $_POST['act']=='edit'){//edit process
			
			$first_notice = '<p>Failed Update Member. </p>';
			$msg = '';
			$type_notice = 'error';
			
			if($_POST['first_name']==''){
				$msg .= '<p>First Name is require</p>';
				$error++;
			}
			if($_POST['last_name']==''){
				$msg .= '<p>Last Name is require</p>';
				$error++;
			}
			if($_POST['email']==''){
				$msg .= '<p>Email is require</p>';
				$error++;
			}

			if ($_POST['email'] !='' && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
				$msg .= '<p>Invalid format email</p>';
				$error++;
			}

			if ($_POST['email'] !='' && $this->exist_email($_POST['email'],$id) > 0) {
				$msg .= '<p>Email already registerd</p>';
				$error++;
			}


			if($_POST['sex']==''){
				$msg .= '<p>Sex is require</p>';
				$error++;
			}
			if($_POST['country']==''){
				$msg .= '<p>Country is require</p>';
				$error++;
			}

			if($_POST['type_member']==''){
				$msg .= '<p>Member Type is require</p>';
				$error++;
			}

			if($error==0){
				$author = get_current_user_id();
				$q = $wpdb->prepare("update {$wpdb->prefix}newsletter_member set email=%s, first_name=%s, last_name=%s, sex=%s, 
																				   status=%s, country=%s, type_member=%s, author_update=%d where id_member=%d",
																				   $_POST['email'], $_POST['first_name'], $_POST['last_name'], $_POST['sex'],
																				   $_POST['status'], $_POST['country'], $_POST['type_member'], $author, $id);
				$r = $wpdb->query($q);
				$type_notice  = 'success';
				$first_notice = '<p>Success Update Member. </p>';				
			}

			if($error>0){
				$dt->first_name 	= $_POST['first_name'];
				$dt->last_name 		= $_POST['last_name'];
				$dt->email 			= $_POST['email'];
				$dt->sex 			= $_POST['sex'];
				$dt->country 		= $_POST['country'];
				$dt->type_member 	= $_POST['type_member'];
				$dt->status 		= $_POST['status'];
			}

			$alert =  '	<div id="message" class="notice notice-'.$type_notice.' is-dismissible">
							'.$first_notice.'
							'.$msg.'
							<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
						</div>';
		}
		if($error==0){
			$qc = $wpdb->prepare("select * from {$wpdb->prefix}newsletter_member where id_member=%d",$id);
			$dtc = $wpdb->get_results($qc);	
			$dt = $dtc[0];
		}		
		
		$this->form_action('Edit',$dt,'edit',$alert);
	}


	function opt_generate($type, $selected){
		if($type=='sex') $opts = $this->newsletter_obj->arr_sex;
		if($type=='type_member') $opts = $this->newsletter_obj->arr_type;
		if($type=='status') $opts = $this->newsletter_obj->arr_status;
		$return = "";

		foreach ($opts as $key => $value) {
			if($_GET['action']=='new' && $key=='trash' && $type=='status') $return;
			else 
			$return .= "<option  value=\"$key\"  ".($key==$selected && $selected!=""  ?"selected":"").">$value</option>";
		}
		return $return;
	}


	function form_action($title="Add New",$dt = array(), $act='new',$alert=''){
		require_once( ABSPATH . 'wp-admin/includes/meta-boxes.php' );
		$sex =  (isset($dt->sex) ? $dt->sex :'');	
		$type_member =  (isset($dt->type_member) ? $dt->type_member :'');
		$status = (isset($dt->status) ? $dt->status :'');
		wp_enqueue_style( 'slider', plugins_url( 'loco_newsletter/css/style.css'.$v , dirname(__FILE__) ));	
		
		?>
		<div class="wrap">
				<h2><?php echo $title; ?> Member</h2>
				<?php echo $alert;?>
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<div class="meta-box-sortables ui-sortable">
								<form class="form-action-mini" method="post">
									<input type="text" name="first_name" class="text text-half" value="<?php echo (isset($dt->first_name) ? $dt->first_name :''); ?>" placeholder="First Name">
									<input type="text" name="last_name" class="text text-half text-half-last" value="<?php echo (isset($dt->last_name) ? $dt->last_name :''); ?>" placeholder="Last Name">
									<p></p>
									<input type="text" name="email" class="text text-full" value="<?php echo (isset($dt->email) ? $dt->email :''); ?>" placeholder="Email">
									<p></p>
									<select name="sex" class="text-half">
										<option value="sex">Sex</option>
										<?php echo $this->opt_generate('sex',$sex); ?>	
									</select>
									<p></p>
									<input type="text" name="country" class="text text-full" value="<?php echo (isset($dt->country) ? $dt->country :''); ?>" placeholder="Country">
									<p></p>	
									<select name="type_member" class="text-half">
										<option value="">Type</option>
										<?php echo $this->opt_generate('type_member',$type_member); ?>
									</select>
									
									<select name="status" class="text-half text-half-last">
										<option value="">Status</option>
										<?php echo $this->opt_generate('status',$status); ?>
									</select>
									<br/>
									<br/>									
									<p><button class="button button-primary button-large" name="act" value="<?php echo $act; ?>">Save</button></p>	
								</form>
							</div>
						</div>
					</div>
					<br class="clear">
				</div>
			</div>
		<?php
	}


	function get_max_id(){
		global $wpdb;
		$return = 0;		
		$q = "select max(id_member) from {$wpdb->prefix}newsletter_member";
		$return = $wpdb->get_var($q);
		return $return;
	}

	//Add & Edit END//
}


add_action('wp_ajax_sorting_newsletter_member', 'sorting_newsletter_member');

function sorting_newsletter_member(){
	global $wpdb;
	
	$Newsletter_Member_List = new Newsletter_Member_List();
	$per_page     = $Newsletter_Member_List->get_items_per_page( 'newsletter_per_page', 5 );
	$current_page = $_POST['current_page'];

	$start = (($current_page * $per_page) - $per_page) + 1;
	$end = ($start + $per_page) - 1;
	
	$n = 0;
	for($i=$start; $i<=$end; $i++){
		$q = $wpdb->prepare("update {$wpdb->prefix}newsletter_member set menu_order=%d where id_member=%d",$i, $_POST['ids'][$n]);
		$r = $wpdb->query($q);
		$n++;
	}
	echo 'success';
	wp_die();
}


add_action( 'plugins_loaded', function () {
	SP_Plugin_Newsletter_Member::get_instance();
} );
?>