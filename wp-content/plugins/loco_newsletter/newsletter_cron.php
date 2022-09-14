<?php 
define( 'SHORTINIT', true );
$path = $_SERVER['DOCUMENT_ROOT'];
//include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
/*include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';*/
//include_once $path . '/wp-includes/plugin.php';
global $wpdb;

date_default_timezone_set('Asia/Makassar');
$time = date('y-m-d h:i:s',time());	
//echo $time;



$q = $wpdb->prepare("select a.id_newsletter, a.id_schedule, b.subject, b.content, a.type_member from {$wpdb->prefix}newsletter_schedule a 
					inner join {$wpdb->prefix}newsletter b on a.id_newsletter=b.id_newsletter
					where (a.status=%s or a.status=%s) and start <= %s",'publish','onprogress',$time);//echo $q;
$dts = $wpdb->get_results( $q, 'ARRAY_A' );
//print_r($dts);
$n = 0;
$max = 50;
$msg = 'failed';
$error = 0;
if(!empty($dts)){	
	foreach ($dts as $key => $dt) {
		if($n<$max){
			$type_member = $dt['type_member'];
			$subject = $dt['subject'];
			$temp = $dt['content'];	
			
			//get member available
			if($type_member=='all'){//all
				$qm =$wpdb->prepare("select a.id_member, a.first_name, a.last_name, a.email, a.sex, a.country, a.type_member 
								from {$wpdb->prefix}newsletter_member a 
								where a.id_member
								NOT IN (select id_member from {$wpdb->prefix}newsletter_record where id_schedule=%d)
								and a.status=%s
								order by a.menu_order", $dt['id_schedule'],'verified');
			}else{
				$qm =$wpdb->prepare("select a.id_member, a.first_name, a.last_name, a.email, a.sex, a.country, a.type_member 
								from {$wpdb->prefix}newsletter_member a 
								where 
								a.id_member
								NOT IN (select id_member from {$wpdb->prefix}newsletter_record where id_schedule=%d)
								and a.status=%s and a.type_member=%s
								order by a.menu_order", $dt['id_schedule'],'verified', $type_member);
			}	
			//echo $qm;
			$rm = $wpdb->get_results($qm,'ARRAY_A');
			
			if(!empty($rm)){
				update_status_schedule('onprogress',$dt['id_schedule']);

					foreach ($rm as $key => $dtm) {
					$subject = generate_dynamic_content($subject,$dtm);				
					$content = generate_dynamic_content(stripslashes($temp),$dtm);				
					$name_from = get_option('blogname');
					$email_from = get_option('admin_email');
					$name_to = $dtm['first_name'].' '.$dtm['last_name'];
					$email_to = $dtm['email'];

					if(send_email_mailer($name_from,$email_from,$name_to,$email_to,$content,$subject)){
						$id = get_max_id() + 1;
						$created = date('Y-m-d H:i:s',time());
						$qi = $wpdb->prepare("insert into {$wpdb->prefix}newsletter_record (id_record, id_schedule, id_member, created) values (%d, %d, %d, %s)", 
									$id, $dt['id_schedule'], $dtm['id_member'], $created );//echo $qi;
						$ri = $wpdb->query($qi);
						if($ri) $n++;	
					}else{
						$error++;
					}
				}	
			}else{
				$error++;
				update_status_schedule('sent',$dt['id_schedule']);
				$msg = 'all have sent';
			}
		}		
	}

	//if($error==0) echo 'success';
	//else echo "failed";
}else{
	$msg = "no schedule exist";
	$error++;
}
if($error==0)$msg = 'success';
echo $msg;


function update_status_schedule($status='',$id){
	global $wpdb;
	$qu = $wpdb->prepare("update {$wpdb->prefix}newsletter_schedule set status=%s where id_schedule=%d",$status,$id);//echo $qu; // status sent
	$wpdb->query($qu);
}


function get_max_id(){
	global $wpdb;
	$return = 0;		
	$q = "select max(id_record) from {$wpdb->prefix}newsletter_record";
	$return = $wpdb->get_var($q);
	return $return;
}

function generate_dynamic_content($content="",$dt){
	$arr_available = array('first_name','last_name','email','sex','country','type_member');
	$arr_sex = array('Women','Male');	
	$arr_type = array('All','Entrepreneur','Profesional','Employee');
	
	foreach ($arr_available as $key => $value) {		
		$str_value = $dt[$value];
		if($value=='sex') $str_value = $arr_sex[$dt[$value]];
		if($value=='type_member') $str_value = $arr_type[$dt[$value]];
		$content = str_replace('['.$value.']', $str_value, $content);
	}
	return $content;
}


function send_email_mailer($name_from,$email_from,$name_to,$email_to,$content,$subject){	
	require 'phpMailer/class.phpmailer.php';
	$mail = new PHPMailer(true);
	$opt = get_option('my_option_name');
	
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
		return false;
	}catch (Exception $e) {
		return false;
	}
}



?>