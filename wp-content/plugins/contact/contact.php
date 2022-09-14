<?php 
/*
Plugin Name: Contact
Plugin URI: http://www.locografis.com
Description: Simple Contact from Loco Grafis
Author: Adi Juliartha
Author URI: http://www.locografis.com 
Version: 1.0.0/
*/

function inquiry_now(){	
	$return = array();
	$valid_capcha_value = valid_capcha_value($_POST['recapcha']);
	//$valid_capcha_value =  true;
	
	if($valid_capcha_value){
		$message = temp_email($_POST);	
		//echo $message;return;

		$name_from = $_POST['name'];
		$email_from = $_POST['email'];
		$name_to = get_option('blogname');
		$email_to = get_option('admin_email');

		if(send_email_mailer($name_from,$email_from,$name_to,$email_to,$message)){
			$return['status']='success';
			$return['message']= 'Thank you for your inquiry we will get back to you as soon as possible.';
		}else{
			$return['status']='failed';
			$return['to']=$to;
			$return['message']= 'Failed send email. Please try again later.';
		}
	}else{
		$return['status'] = 'failed';
		$return['message'] = 'Error capcha validation. Failed to send inquiry.';
	}

	//if(valid_capcha_value('')) {
		/**/
	//else{}


	
	echo json_encode($return);
	die();
}

function valid_capcha_value($response){
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
}

function temp_email($dt){
	$arr = array('jasa'=>'Jasa','name'=>'Nama','company'=>'Perusahaan','email'=>'Email','mobile'=>'Telphone','date'=>'Deadline','info_detail'=>'Info Detail');

	$temp = '';
	foreach ($arr as $key => $label) {
		$temp .= '	<tr>
						<td style="width:100px;">'.$label.'</td>
						<td style="width:10px;">:</td>
						<td>'.nl2br($dt[$key]).'</td>
					</tr>';
	}

	$temp = '<table>'.$temp.'</table>';
	return $temp;
}


function temp_email_old($name,$package,$email,$mobile,$country,$date,$location,$additional_info){
	$temp = '<table>
				<tr>
					<td style="width:100px;">Name</td>
					<td style="width:10px;">:</td>
					<td>'.$name.'</td>
				</tr>
				<tr>
					<td style="width:100px;">Package</td>
					<td style="width:10px;">:</td>
					<td>'.$package.'</td>
				</tr>
				<tr>
					<td style="width:100px;">Email</td>
					<td style="width:10px;">:</td>
					<td>'.$email.'</td>
				</tr>
				<tr>
					<td style="width:100px;">Phone</td>
					<td style="width:10px;">:</td>
					<td>'.$mobile.'</td>
				</tr>
				<tr>
					<td style="width:100px;">Country</td>
					<td style="width:10px;">:</td>
					<td>'.$country.'</td>
				</tr>
				<tr>
					<td style="width:100px;">Date</td>
					<td style="width:10px;">:</td>
					<td>'.$date.'</td>
				</tr>
				<tr>
					<td style="width:100px;">Wedding Venue</td>
					<td style="width:10px;">:</td>
					<td>'.$location.'</td>
				</tr>
				<tr>
					<td style="width:100px;">Additional Info</td>
					<td style="width:10px;">:</td>
					<td>'.nl2br($additional_info).'</td>
				</tr>
			</table>';
	return $temp;		
}


add_action( 'wp_ajax_nopriv_inquiry-now', 'inquiry_now' );
add_action( 'wp_ajax_inquiry-now', 'inquiry_now' );



function send_email_mailer($name_from,$email_from,$name_to,$email_to,$content){
	require 'phpMailer/class.phpmailer.php';
	$mail = new PHPMailer(true);
	$opt = get_option('my_option_name');
	$subject = $opt['subject_email'];
	$email_smtp = $opt['email_smtp_custom'];
	$pass_smtp = $opt['pass_smtp_custom'];
	$SMTP_PORT =  $opt['port_smtp_server'];
	$host = $opt['smtp_server'];
	


	try {
		//Create a new PHPMailer instance
		//$mail->SMTPDebug  = 1;
		$mail->IsSMTP();
		$mail->Host = $host;
		$mail->SMTPAuth = true;
		$mail->Port       = $SMTP_PORT; 
		$mail->Username   = $email_smtp;  // GMAIL username
		$mail->Password   = $pass_smtp; // GMAIL password

		$mail->AddReplyTo($email_from,$name_from);
		$mail->AddAddress($email_to,$name_to); //karena kirim ke diri sendiri
		$mail->SetFrom($email_smtp, $name_from);
		$mail->Subject = $subject;
		$mail->MsgHTML($content);
		$mail->Send();
		return true;
	}catch (phpmailerException $e) {
		//echo $e;
		return false;
	}catch (Exception $e) {
		return false;
	}

	
}




/*function send_email_mailer($name_from,$email_from,$name_to,$email_to,$content,$subject){
	//require_once(ROOT_PATH.'/phpMailer/class.phpmailer.php');
	require_once('PHPMailer/class.phpmailer.php' );
	$mail = new PHPMailer(true);
	$SMTP_SERVER = get_meta_data('smtp');
	$SMTP_PORT 	= 2525;
	$email_smtp			= 'adi@lumonatalabs.com';
	$pass_smtp 			= 'adijuli789';
	
	try {
		$mail->SMTPDebug  = 1;
		$mail->IsSMTP();
		$mail->Host = $SMTP_SERVER;
		$mail->SMTPAuth = false;
		$mail->Port       = $SMTP_PORT; 
		$mail->Username   = $email_smtp;  // GMAIL username
		$mail->Password   = $pass_smtp; // GMAIL password

		$mail->AddReplyTo($email_from,$name_from);
		$mail->AddAddress($email_to,$name_to); //karena kirim ke diri sendiri
		$mail->SetFrom($email_smtp, $name_from);
		$mail->Subject = $subject;
		$mail->MsgHTML($content);
		$mail->Send();
		return true;
	}catch (phpmailerException $e) {
		echo $e;
		return false;
	}catch (Exception $e) {
		return false;
	}
}*/

function contact_form($post){
	wp_enqueue_script('contact-form-js-1',plugin_dir_url( __FILE__ ) . 'script.js',false,'1.1',true);
	wp_localize_script('contact-form-js-1','d',array('url'=>admin_url('admin-ajax.php'),));
	
	$recapcha = get_option('recapcha');
	wp_enqueue_script('google-capcha','https://www.google.com/recaptcha/api.js',false,'1.1',true);
	$option_package = '';

	$categories = get_terms( 'category', array(
					    'orderby'    => 'count',
					    'hide_empty' => 0
					) );

	$option = '';
	foreach ($categories as $key => $cat) {
		if($cat->name!= 'Uncategorized') $option .= '<option value="'.$cat->name.'">'.$cat->name.'</option>';
	}

	
	?>
	<div class="content content-contact content-yellow">
		<h1><?php echo $post->post_title; ?></h1>	

		<table class="container-form-contact content-white middle clearfix">
			<tr>
				<td id="form-contact" class="form-contact">
					<div class="field">
		        		<select name="jasa" id="jasa" class="_select require"  onkeyup="validate_error(event)" onchange="validate_error(event)">
							<option value="">Pilih Jasa</option>
		        			<?php  echo $option; ?>
		        		</select>
		        	</div>
		        	<div class="field">
		        		<input class="_input require" placeholder="Nama" type="text" name="name" id="name"  onkeyup="validate_error(event)" onchange="validate_error(event)">
		        	</div>
		        	<div class="field">
		        		<input class="_input" placeholder="Name Perusahaan" type="text" name="company" id="company"  onkeyup="validate_error(event)" onchange="validate_error(event)">
		        	</div>
		        	<div class="field">
		        		<input class="_input require" placeholder="Email" type="text" name="email" id="email"  onkeyup="validate_error(event)" onchange="validate_error(event)">
		        	</div>
		        	<div class="field">
		        		<input class="_input" placeholder="No. Telphone" type="text" name="mobile" id="mobile"  onkeyup="validate_error(event)" onchange="validate_error(event)">
		        	</div>
		        	<div class="field">
		        		<input class="_input" placeholder="Tanggal Deadline" type="text" name="date" id="date"  onkeyup="validate_error(event)" onchange="validate_error(event)">
		        	</div>
		        	<div class="field">
		        		<textarea class="_textarea require" name="info_detail" id="info_detail" placeholder="Tentang / Detail projek" onkeyup="validate_error(event)" onchange="validate_error(event)"></textarea>
		        	</div>

	        		<?php echo html_entity_decode($recapcha['snipset']);?>       
			        <input type="button" class="btn-black" onclick="send_inquiry(event)" value="Kirim Inquiry" data-onprocess="Silahkan Menunggu..." data-onfinish="Kirim Inquiry" />
			        <div id="notify" class="notify">
			        	<div id="notify-text"></div>
			        </div>

				</td>	
				<td class="info-contact">
					<div class="detail-info-contact">
						<label class="label-jam">Jam Kerja:<br>Senin - Jumat: 09.00am - 06.00pm</label>	
						<p><em>Lewat dari jam kerja, email akan dibalas dihari berikutnya.</em></p>

						<ul class="ul-contact-info">
						<?php
							$opt = get_option('my_option_name');
							$email = get_option( 'admin_email' );
							if(isset($opt['phone']) && !empty($opt['phone'])) {?>
								<li class="clearfix li-phone">
									<img class="fleft" src="<?php echo get_template_directory_uri().'/images/Icon_kontactus-phone.svg';?>">
									<span class="fleft"><?php echo $opt['phone']; ?></span>
								</li>
							<?php
							}
							if(!empty($email)){?>
								<li class="clearfix li-email">
									<img class="fleft" src="<?php echo get_template_directory_uri().'/images/Icon_kontactus-email.svg';?>">
									<span class="fleft"><?php echo $email; ?></span>
								</li>
							<?php }
							//print_r($opt);
						 ?>
						</ul>
						<label>Hanya melalui janji:</label>
						<p><?php echo $opt['address']; ?></p>
					</div>
					<div id="map" class="map"></div>
					<?php set_map(); ?>
				</td>

			</tr>	
			
		</table>
	</div>
	<?php
}



function contact_form_old2($post){
	wp_enqueue_script('contact-form-js-1',plugin_dir_url( __FILE__ ) . 'script.js',false,'1.1',true);
	wp_localize_script('contact-form-js-1','d',array('url'=>admin_url('admin-ajax.php'),));
	
	$recapcha = get_option('recapcha');
	wp_enqueue_script('google-capcha','https://www.google.com/recaptcha/api.js',false,'1.1',true);
	$option_package = '';

	$categories = get_terms( 'category', array(
					    'orderby'    => 'count',
					    'hide_empty' => 1
					) );

	$option = '';
	foreach ($categories as $key => $cat) {
		if($cat->name!= 'Uncategorized') $option .= '<option value="'.$cat->name.'">'.$cat->name.'</option>';
	}

	
	?>
	<div class="content content-contact content-yellow">
		<h1><?php echo $post->post_title; ?></h1>	

		<div class="container-form-contact content-white middle clearfix">
			<div class="form-contact fleft">
				<div class="field">
	        		<select name="jasa" id="jasa" class="_select require"  onkeyup="validate_error(event)" onchange="validate_error(event)">
						<option value="">Pilih Jasa</option>
	        			<?php  echo $option; ?>
	        		</select>
	        	</div>
	        	<div class="field">
	        		<input class="_input require" placeholder="Nama" type="text" name="name" id="name"  onkeyup="validate_error(event)" onchange="validate_error(event)">
	        	</div>
	        	<div class="field">
	        		<input class="_input" placeholder="Name Perusahaan" type="text" name="company" id="name"  onkeyup="validate_error(event)" onchange="validate_error(event)">
	        	</div>
	        	<div class="field">
	        		<input class="_input require" placeholder="Email" type="text" name="email" id="email"  onkeyup="validate_error(event)" onchange="validate_error(event)">
	        	</div>
	        	<div class="field">
	        		<input class="_input" placeholder="No. Telphone" type="text" name="mobile" id="mobile"  onkeyup="validate_error(event)" onchange="validate_error(event)">
	        	</div>
	        	<div class="field">
	        		<input class="_input" placeholder="Tanggal Deadline" type="text" name="date" id="date"  onkeyup="validate_error(event)" onchange="validate_error(event)">
	        	</div>
	        	<div class="field">
	        		<textarea class="_textarea" name="info_detail" id="info_detail" placeholder="Tentang / Detail projek" onkeyup="validate_error(event)" onchange="validate_error(event)"></textarea>
	        	</div>

        		<?php echo html_entity_decode($recapcha['snipset']);?>       
		        <input type="button" class="btn-black" onclick="send_inquiry(event)" value="Kirim Inquiry" data-onprocess="Silahkan Menunggu..." data-onfinish="Kirim Inquiry" />
		        <div id="notify" class="notify">
		        	<div id="notify-text"></div>
		        </div>

			</div>	
			<div class="info-contact fleft">
				<div class="detail-info-contact">
					<label class="label-jam">Jam Kerja:<br>Senin - Jumat: 09.00am - 06.00pm</label>	
					<p><em>Lewat dari jam kerja, email akan dibalas dihari berikutnya.</em></p>

					<ul class="ul-contact-info">
					<?php
						$opt = get_option('my_option_name');
						$email = get_option( 'admin_email' );
						if(isset($opt['phone']) && !empty($opt['phone'])) {?>
							<li class="clearfix li-phone">
								<img class="fleft" src="<?php echo get_template_directory_uri().'/images/Icon_kontactus-phone.svg';?>">
								<span class="fleft"><?php echo $opt['phone']; ?></span>
							</li>
						<?php
						}
						if(!empty($email)){?>
							<li class="clearfix li-email">
								<img class="fleft" src="<?php echo get_template_directory_uri().'/images//Icon_kontactus-email.svg';?>">
								<span class="fleft"><?php echo $opt['phone']; ?></span>
							</li>
						<?php }
						//print_r($opt);
					 ?>
					</ul>
					<label>Hanya melalui janji:</label>
					<p><?php echo $opt['address']; ?></p>
				</div>
				<div class="map"></div>
			</div>
		</div>
	</div>
	<?php
}





function contact_form_old($post_id,$section_css='',$arr=array()){	
	wp_enqueue_script('contact-form-js-1',plugin_dir_url( __FILE__ ) . 'script.js',false,'1.1',true);
	wp_localize_script('contact-form-js-1','d',
											array(
												'url'=>admin_url('admin-ajax.php'),
												
												)
									);
	
	$recapcha = get_option('recapcha');
	wp_enqueue_script('google-capcha','https://www.google.com/recaptcha/api.js',false,'1.1',true);
	
	//get package
	krsort($arr);//print_r($arr);
	$option_package = '<option value="">Choose Package</option>';
	foreach ($arr as $key => $dtp) {
		$time = $dtp['time'] + 1;
		$option_package .= '<option value="'.$time.'">'.$time.' Hours</option>';
	}

	$opt = get_option('my_option_name');
	$email = get_option( 'admin_email' );
	$phone = (isset($opt['phone']) && !empty($opt['phone']) ? $opt['phone'] : '').(isset($opt['phone_2']) && !empty($opt['phone_2']) ? ' / '.$opt['phone_2'] : '');


?>
	<div id="section-contact-us" class="section section-contact  <?php echo 'section-'.$section_css;?>">
		<?php echo get_triangle_post($post_id);?>
		<div class="middle">
			<h1>Contact Us</h1>
	        <form id="contact" name="contact" class="form clearfix">
	        	<div class="field">
	        		<input class="_input require" placeholder="Name (required)" type="text" name="name" id="name"  onkeyup="validate_error(event)" onchange="validate_error(event)">
	        	</div>

	        	<div class="half_field half_field_left">
	        		<select name="package" id="package" class="_select require"  onkeyup="validate_error(event)" onchange="validate_error(event)">
	        			<?php echo $option_package; ?>
	        		</select>
	        	</div>
	        	<div class="half_field half_field_right">
	        		<input class="_input require" placeholder="Email (required)" type="text" name="email" id="email"  onkeyup="validate_error(event)" onchange="validate_error(event)">
	        	</div>
	        	<div class="half_field half_field_left">
	        		<input class="_input" placeholder="Mobile Phone" type="text" name="mobile" id="mobile"  onkeyup="validate_error(event)" onchange="validate_error(event)">
	        	</div>
	        	<div class="half_field half_field_right">
	        		<input class="_input" placeholder="Country of Origin" type="text" name="country" id="country"  onkeyup="validate_error(event)" onchange="validate_error(event)">
	        	</div>
	        	<div class="half_field half_field_left">
	        		<input class="_input" placeholder="Wedding Date" type="text" name="date" id="date"  onkeyup="validate_error(event)" onchange="validate_error(event)">
	        	</div>
	        	<div class="half_field half_field_right">
	        		<input class="_input" placeholder="Wedding Venue" type="text" name="location" id="location"  onkeyup="validate_error(event)" onchange="validate_error(event)">
	        	</div>
	        	<div class="field">
	        		<textarea class="_textarea" name="additional_info" id="additional_info" placeholder="Additional Info (required)" onkeyup="validate_error(event)" onchange="validate_error(event)"></textarea>
	        	</div>

	            <div class="field">
	            <?php echo html_entity_decode($recapcha['snipset']);?>
	            </div>
	        </form>        
	        <input type="button" class="btn-green" onclick="send_inquiry(event)" value="Send Message" data-onprocess="Please wait..." data-onfinish="Send message" />
	        <div id="notify" class="notify">
	        	<div id="notify-text"></div>
	        </div>

	        <div class="address-detail">
	        	<label>Meeting by appointment</label>	
	        	<ul class="clearfix">
	        		<?php if(isset($opt['address']) && !empty($opt['address'])){ ?>
						<li class="address"><?php echo $opt['address'];?></li>	
					<?php }?>
					<?php if(isset($phone) && !empty($phone)){ ?>
						<li class="phone"><?php echo $phone;?></li>	
					<?php }?>
					<?php if(isset($email) && !empty($email)){ ?>
						<li class="email"><?php echo $email;?></li>	
					<?php }?>
					
	        	</ul>
	        </div>	

		</div>
        
    </div>
<?php }

?>