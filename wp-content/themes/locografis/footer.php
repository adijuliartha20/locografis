<!-- Footer Start -->
<div class="footer clearfix">
	<div class="footer-1 fleft">
		<h3>&copy; Locografis</h3>	
		<p>Digital Agency <?php echo date('Y',time()); ?></p>
	</div>
	<div class="footer-2 fleft">
		<h3>Hubungi Kami</h3>	
		<p>
			<?php 
				$opt = get_option('my_option_name');
				if(isset($opt['phone']) && !empty($opt['phone'])) echo $opt['phone'].'<br>';
				echo get_option( 'admin_email' );
				
			?>
		</p>
	</div>
	<div class="footer-3 fright">
		<p>Kami adalah profesional digital agency yang berbasis di Bali. Jasa kami meliputi dari <b class="orange">pembuatan website & desain grafis secara profesional.</b></p>	
		<a class="link_orange" href="<?php echo get_site_url().'/tentang-kami/' ?>">Selengkapnya...</a>
	</div>
</div>
<!-- Footer End -->  
<?php wp_footer(); ?>
</body>
</html>