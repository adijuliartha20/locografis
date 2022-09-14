<div id="list-portofolio<?php echo $clone; ?>" class="list-portofolio clearfix">
<?php 
	//print_r($portofolio);
	foreach ($portofolio as $key => $dt) { 
		//print_r($dt);
		$class = '';
		foreach ($dt['category'] as $key => $cat) {
			$class .= ' '.$cat['slug'];
		}
		?>
		<div class="item-portofolio<?php echo $class;?> element-item fleft pos-relative">
			<a class="link-detail-portofolio" href="<?php echo get_site_url().'/portofolio/'.$dt['sef'].'/'; ?>">
				<div class="bg" style="background-image: url(<?php echo $dt['image']; ?>)"></div>
				<div class="item-brief">
					<h2><?php echo $dt['title'];?></h2>
					<div class="cont-cat-link clearfix">
						<!--<ul class="list-category fleft">
							<?php 
								$all = count($dt['category']);
								$n = 1;
								foreach ($dt['category'] as $key => $cat) { ?>
									<li>
										<a href="<?php echo get_site_url().'/portofolio/categories/'.$cat['slug'].'/'; ?>">
											<?php echo $cat['name'];if($n<$all) echo ',';?>
										</a>
									</li>	
								<?php 
									$n++;
								}
							?>
						</ul>-->
						<img class="link-detail fright" src="<?php echo get_template_directory_uri().'/images/Icon_arrow-portofolio-right.svg' ?>">
					</div>
				</div>
			</a>

			<ul class="list-category list-category-desktop pos-absolute">
				<?php 
					$all = count($dt['category']);$n = 1;
					foreach ($dt['category'] as $key => $cat) { ?>
						<li>
							<a href="<?php echo get_site_url().'/portofolio/categories/'.$cat['slug'].'/'; ?>">
								<?php echo $cat['name'];if($n<$all) echo ',';?>
							</a>
						</li>	
					<?php 
						$n++;
					}
				?>
			</ul>

			
		</div>		
	<?php 
	}
?>
</div>