<?php 
/*$args = array('post_title_like' => 'Tertarik dengan hasil pekerjaan desain kami?');
$res = new WP_Query($arg);
print_r($res);*/
$page = get_page_by_title( 'Tertarik dengan hasil pekerjaan desain kami?' );

if(isset($page) && !empty($page)){ ?>
	<div class="content-intersting content-yellow">
		<div class="middle">
			<?php echo $page->post_content; ?>	
		</div>
	</div>
<?php 
}
?>