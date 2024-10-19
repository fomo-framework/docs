			</div>
		</section>
	</div><!--//content-inner-->
</div><!--//doc-content-->
<div class="doc-sidebar col-md-3 col-12 order-0 d-none d-md-flex">
	<div id="doc-nav" class="doc-nav">
		<nav id="doc-menu" class="nav doc-menu flex-column sticky">
			<?php foreach($headings as $h1_id_tag => $h1_data) { ?>
				<li class="nav-item">
					<a class="nav-link scrollto" href="#<?php echo $h1_id_tag; ?>"><?php echo $h1_data['text']; ?></a>
				</li>
				<?php if(!empty($h1_data['children'])) { ?>
					<nav class="nav doc-sub-menu nav flex-column">
						<?php foreach($h1_data['children'] as $h2_id_tag) { ?>
							<li class="nav-item">
								<a class="nav-link scrollto" href="#<?php echo $h2_id_tag['id']; ?>"><?php echo $h2_id_tag['text']; ?></a>
							</li>
						<?php } ?>
					</nav>
				<?php } ?>
			<?php } ?>
		</nav><!--//doc-menu-->
	</div>
</div><!--//doc-sidebar-->