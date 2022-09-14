<?php 
class Newsletter_Schedule_List extends WP_List_Table {

	//Class constructor
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Newsletter Schedule', 'sp' ), //singular name of the listed records
			'plural'   => __( 'newsletter_schedule', 'sp' ), //plural name of the listed records
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


		$sql = "select a.*, b.subject, c.display_name  FROM {$wpdb->prefix}newsletter_schedule a inner join {$wpdb->prefix}newsletter b on a.id_newsletter = b.id_newsletter
					inner join wp_users c on a.author_update=c.ID";

		if (!empty( $_REQUEST['status'] ) && $_REQUEST['status']!='all' ) {
			$sql .= " where a.status='".esc_sql( $_REQUEST['status'])."'";
		}else{
			$sql .= " where a.status <> 'trash'";
		}	


		if ( ! empty( $_REQUEST['s'] ) ) {
			$sql .= " and  b.subject like '%".esc_sql( $_REQUEST['s'])."%' or c.display_name like '%".esc_sql( $_REQUEST['s'])."%'  ";
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}else {
			$sql .= ' ORDER BY a.menu_order';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page; //echo $sql;
		
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}


	
	// Delete a customer record.	 
	// @param int $id customer ID	 
	public static function delete_data( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}newsletter_schedule",
			[ 'id_schedule' => $id ],
			[ '%d' ]
		);

		//echo 
	}

	public static function update_status($id,$status){
		global  $wpdb;

		$wpdb->update(
						"{$wpdb->prefix}newsletter_schedule",
						['status' => $status],
						['id_schedule'=>$id]
					);
	}

	// Returns the count of records in the database.	
	// @return null|string	
	public static function record_count($status_fix='') {
		global $wpdb;
		$status = (isset($_REQUEST['status'])? $_REQUEST['status']: '');
		$status_fix = ($status_fix!=""? $status_fix : $status);
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}newsletter_schedule ".($status_fix!='' && $status_fix!='all'? "where status='".$status_fix."'": "where status<> 'trash'");		

		$return = $wpdb->get_var( $sql );
		if(empty($return)) $return = 0;
		return $return;
	}


	//Text displayed when no customer data is available 
	public function no_items() {
		_e( 'No schedule avaliable.', 'sp' );
	}


	//Render a column when no column specific method exist.	
	// @param array $item
	// @param string $column_name	
	// @return mixed	
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {			
			case 'start':
				return $item[ $column_name ];
			case 'type_member':
				return $this->arr_type[$item[$column_name]];
			case 'status':
				return $this->arr_status[$item[$column_name]]; 					
			case 'created':
				return $item[ $column_name ];			
			case 'updated':
				return $item[ $column_name ];	
			case 'menu_order':
				return $item[ $column_name ];
			case 'display_name':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	var $arr_status = array('draft'=>'Draft','publish'=>'Publish','onprogress'=>'Onprogress','sent'=>'Sent','trash'=>'Trash');
	var $arr_type = array('all'=>'All','entrepreneur'=>'Entrepreneur','profesional'=>'Profesional','employee'=>'Employee','accommodation'=>'Accommodation');

	public function list_status(){
		global $wpdb;
		$arr_status = $this->arr_status;
		$return ="";
		$status = (isset($_REQUEST['status'])? $_REQUEST['status']: '');

		//if($this->record_count('all')>0){ ?>
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
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id_schedule']
		);
	}


	
	// Method for name column
	//
	// @param array $item an array of DB data
	// 
	// @return string
	//
	// repalce column_subject to column_nameofcolum

	function column_subject( $item ) {
		$edit_nonce = wp_create_nonce( 'sp_edit_data' );
		$delete_nonce = wp_create_nonce( 'sp_delete_data' );
		$title = '<strong>'.$item['subject'].'</strong>';
		
		/*$actions = [
			'edit' => sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s&paged=%s">Edit</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id_schedule'] ), $edit_nonce, $_GET['paged'] ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s&paged=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id_schedule'] ), $delete_nonce, $_GET['paged'] )
		];*/

		$status = (isset($_GET['status'])? $_GET['status'] : '');
		$actions = array();

		if(isset($_GET['status']) && $_GET['status']=='trash'){
			$actions['untrash'] = sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s&paged=%s&status=%s">Restore</a>', esc_attr( $_REQUEST['page'] ), 'untrash', absint( $item['id_schedule'] ), $delete_nonce, $_GET['paged'], $status);
			$actions['delete'] = sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s&paged=%s&status=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id_schedule'] ), $delete_nonce, $_GET['paged'], $status);
		}else{
			$actions['edit'] =  sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s&paged=%s&status=%s">Edit</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id_schedule'] ), $edit_nonce, $_GET['paged'], $status );
			$actions['trash'] = sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s&paged=%s&status=%s">Trash</a>', esc_attr( $_REQUEST['page'] ), 'trash', absint( $item['id_schedule'] ), $trash_nonce, $_GET['paged'], $status );
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
			'subject'    => __( 'Subject', 'sp' ),
			'start'    => __( 'Start Publish', 'sp' ),
			'type_member'    => __( 'Type', 'sp' ),			
			'status'    => __( 'Status', 'sp' ),
			'created'    => __( 'Created', 'sp' ),
			'updated'    => __( 'Updated', 'sp' ),			
			'display_name'    => __( 'Author', 'sp' ),
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
			'subject' => array( 'b.subject', true ),
			'start' => array( 'a.start', true ),
			'type_member' => array( 'a.type_member', true ),
			'created' => array( 'a.created', true ),
			'updated' => array( 'a.updated', true ),
			'display_name' => array( 'c.display_name', true ),
			'menu_order' => array( 'menu_order', true )			
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

		$per_page     = $this->get_items_per_page( 'newsletter_per_page', 5 );
		
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


class SP_Plugin_newsletter_schedule {
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
		$hook = add_submenu_page( 'wp_list_table_newsletter', 'Schedule', 'Schedule', 'manage_options', 'wp_list_table_newsletter_schedule', [ $this, 'plugin_settings_page' ]);
		add_action( "load-$hook", [ $this, 'screen_option' ] );
		
	}

	//
	// Plugin settings page
	//
	public function plugin_settings_page() {
		$Newsletter_Schedule_List = new Newsletter_Schedule_List();
		$action = $this->newsletter_obj->current_action();
		
		
		if($action=='edit') $this->edit();
		else if($action=='sorting') $this->sorting() ;
		else if($action=='new') $this->add_new();
		else{//view list
			$status = 'publish';
			if($action=='trash' || $action=='bulk-trash') $status = 'trash';
			if(isset($_POST['bulk-delete']) && ($action=='bulk-trash' || $action=='bulk-untrash')){
				$delete_ids = esc_sql( $_POST['bulk-delete'] );
				foreach ( $delete_ids as $id ) {
					$Newsletter_Schedule_List->update_status($id,$status);
				}
			}
			if($action=='trash' || $action=='untrash'){
				$Newsletter_Schedule_List->update_status($_GET['id'],$status);
			}


			if(isset($_POST['bulk-delete']) && $action=='bulk-delete'){
				$delete_ids = esc_sql( $_POST['bulk-delete'] );
				foreach ( $delete_ids as $id ) {
					$Newsletter_Schedule_List->delete_data( $id );
				}
			}

			if($action=='delete'){
				$Newsletter_Schedule_List->delete_data($_GET['id']);
			}
			$this->view_list();
		}
	}

	function view_list(){
		$this->view();
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
		$Newsletter_Schedule_List = new Newsletter_Schedule_List();
		$current_page = $Newsletter_Schedule_List->get_pagenum();
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline">Schedule</h2> <?php echo sprintf( '<a href="?page=%s&action=%s" class="page-title-action">Add New</a>', esc_attr( $_REQUEST['page'] ), 'new') ?>

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
			<input type="hidden" id="sorting_action" value="sorting_newsletter_schedule">
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

		$this->newsletter_obj = new Newsletter_Schedule_List();
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
			$first_notice = '<p>Failed Add New Schedule. </p>';
			$msg = '';
			$type_notice = 'error';
			
			if($_POST['id_newsletter']==''){
				$msg .= '<p>Please select newsletter </p>';
				$error++;
			}
			if($_POST['start']==''){
				$msg .= '<p>Start publish is require</p>';
				$error++;
			}
			if($_POST['type_member']==''){
				$msg .= '<p>Member Type is require</p>';
				$error++;
			}
			if($_POST['status']==''){
				$msg .= '<p>Status is require</p>';
				$error++;
			}

			if($error==0){
				$id = $this->get_max_id() + 1;
				$date = date_create($_POST['start']);
				$start = date_format($date,'Y-m-d H:i:s');
				$created = date('Y-m-d H:i:s',time());
				$author = get_current_user_id();
				$q = $wpdb->prepare("insert into {$wpdb->prefix}newsletter_schedule (id_schedule, id_newsletter, start, type_member, status,
																					created, updated, menu_order, author, author_update) 
																				    values (%d, %d, %s, %s, %s,
																				    		%s, %s, %d, %d, %d)",
																					$id, $_POST['id_newsletter'], $start, $_POST['type_member'], $_POST['status'],
																					$created, $created, 0, $author, $author);
				$r = $wpdb->query($q);
				if($r){
					$type_notice  = 'success';
					$first_notice = '<p>Success Add New Schedule. </p>';
					$this->sort_again();
				}else $error++;
			}

			if($error>0){
				$dt->id_newsletter 	= $_POST['id_newsletter'];
				$dt->start 			= $_POST['start'];
				$dt->type_member 	= $_POST['type_member'];
				$dt->status 		= $_POST['status'];
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
		$q = $wpdb->prepare("update {$wpdb->prefix}newsletter_schedule set menu_order = menu_order + 1 where id_schedule<>%d",0);
		$r = $wpdb->query($q);
	}


	

	function edit(){
		global $wpdb;
		$id = $_GET['id'];
		$dt = (object) array();
		$alert = '';
		$error = 0;
		if(isset($_POST['act']) && $_POST['act']=='edit'){//edit process			
			$error = 0;
			$first_notice = '<p>Failed Add New Schedule. </p>';
			$msg = '';
			$type_notice = 'error';
			
			if($_POST['id_newsletter']==''){
				$msg .= '<p>Please select newsletter </p>';
				$error++;
			}
			if($_POST['start']==''){
				$msg .= '<p>Start publish is require</p>';
				$error++;
			}
			if($_POST['type_member']==''){
				$msg .= '<p>Member Type is require</p>';
				$error++;
			}
			if($_POST['status']==''){
				$msg .= '<p>Status is require</p>';
				$error++;
			}

			if($error==0){
				$author = get_current_user_id();
				$time = date('Y-m-d H:i:s',time());
				$date = date_create($_POST['start']);
				$start = date_format($date,'Y-m-d H:i:s');
				$q = $wpdb->prepare("update {$wpdb->prefix}newsletter_schedule set 
									id_newsletter=%d, start=%s, type_member=%s, status=%s, updated=%s, author_update=%d where id_schedule=%d",
									 $_POST['id_newsletter'], $start, $_POST['type_member'], $_POST['status'], $time, $author, $id); //echo $q;
				$r = $wpdb->query($q);
				if($r){
					$type_notice  = 'success';
					$first_notice = '<p>Success Update Schedule. </p>';
				}else $error++;
			}

			if($error>0){
				$dt->id_newsletter 	= $_POST['id_newsletter'];
				$dt->start 			= $_POST['start'];
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
			$qc = $wpdb->prepare("select * from {$wpdb->prefix}newsletter_schedule where id_schedule=%d",$id);
			$dtc = $wpdb->get_results($qc);	
			$dt = $dtc[0];
		}		
		
		$this->form_action('Edit',$dt,'edit',$alert);
	}

	
	
	

	function opt_generate($type, $selected=''){
		if($type=='sex') $opts = $this->newsletter_obj->arr_sex;
		if($type=='type_member') $opts = $this->newsletter_obj->arr_type;
		if($type=='status') $opts = $this->newsletter_obj->arr_status;
		$return = "";
		echo $type;
		//print_r($opts);
		foreach ($opts as $key => $value) {
			if(($_GET['action']=='new' || $_GET['action']=='edit')  && ($key=='trash' || $key=='onprogress' || $key=='sent' )) $return;
			else 
			$return .= "<option  value=\"$key\"  ".($key==$selected && $selected!=""  ?"selected":"").">$value</option>";
		}
		return $return;
	}

	function opt_newsletter($selected=''){
		global $wpdb;
		$return  = '';
		$sql = "select id_newsletter, subject from {$wpdb->prefix}newsletter where status='publish'";
		
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		if(!empty($result)){
			foreach ($result as $key => $dt) {
				$return .= '<option value="'.$dt['id_newsletter'].'" '.($dt['id_newsletter']==$selected?'selected':'').' >'.$dt['subject'].'</option>';
			}
		}
		//print_r($result);
		return $return;
	}


	function form_action($title="Add New",$dt = array(), $act='new',$alert=''){
		require_once( ABSPATH . 'wp-admin/includes/meta-boxes.php' );

		$id_newsletter =  (isset($dt->id_newsletter) ? $dt->id_newsletter :'');	
		$type_member =  (isset($dt->type_member) ? $dt->type_member :'');
		$status = (isset($dt->status) ? $dt->status :'');
		
		$start = '';
		if(isset($dt->start) && !empty($dt->start)){
			$date = date_create($_POST['start']);
			$start = date_format($date,'d F Y');	
		}
		



		wp_enqueue_style( 'slider', plugins_url( 'loco_newsletter/css/style.css'.$v , dirname(__FILE__) ));		

		wp_enqueue_style( 'jquery-ui-css', plugins_url( 'loco_newsletter/js/jquery-ui-1.12.1.custom/jquery-ui.min.css'.$v , dirname(__FILE__) ));		
		wp_enqueue_script( 'jquery-ui', plugins_url( 'loco_newsletter/js/jquery-ui-1.12.1.custom/jquery-ui.min.js'.$v , dirname(__FILE__) ) );
		wp_enqueue_script( 'custom-js', plugins_url( 'loco_newsletter/js/script.js'.$v , dirname(__FILE__) ) );			
		?>
		<div class="wrap">
				<h2><?php echo $title; ?> Schedule</h2>
				<?php echo $alert;?>
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<div class="meta-box-sortables ui-sortable">
								<form class="form-action-mini" method="post">
									<select name="id_newsletter" class="text-full">
										<option value="">Choose Newsletter</option>
										<?php echo $this->opt_newsletter($id_newsletter); ?>	
									</select>
									<p></p>
									<input type="text" name="start" class="text text-full text-date" value="<?php echo $start ; ?>" placeholder="Start Publish">
									<p></p>	
									<select name="type_member" class="text-half">
										<option value="">Choose Type Member</option>
										<?php echo $this->opt_generate('type_member',$type_member); ?>
									</select>
									<select name="status" class="text-half text-half-last">
										<option value="">Choose Status</option>
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
		$q = "select max(id_schedule) from {$wpdb->prefix}newsletter_schedule";
		$return = $wpdb->get_var($q);
		return $return;
	}

	//Add & Edit END//
}


add_action('wp_ajax_sorting_newsletter_schedule', 'sorting_newsletter_schedule');

function sorting_newsletter_schedule(){
	global $wpdb;
	
	$Newsletter_Schedule_List = new Newsletter_Schedule_List();
	$per_page     = $Newsletter_Schedule_List->get_items_per_page( 'newsletter_per_page', 5 );
	$current_page = $_POST['current_page'];

	$start = (($current_page * $per_page) - $per_page) + 1;
	$end = ($start + $per_page) - 1;
	
	$n = 0;
	for($i=$start; $i<=$end; $i++){
		$q = $wpdb->prepare("update {$wpdb->prefix}newsletter_schedule set menu_order=%d where id_schedule=%d",$i, $_POST['ids'][$n]);
		$r = $wpdb->query($q);
		$n++;
	}
	echo 'success';
	wp_die();
}


add_action( 'plugins_loaded', function () {
	SP_Plugin_newsletter_schedule::get_instance();
} );
?>