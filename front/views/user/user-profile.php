<?php
/**
 * @copyright © 2019 by GLV 
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */
 ?>
 <style>
 	.padding-100 {
 		padding-top: 100px;
 		padding-bottom: 100px;
 	}
 </style>
 <div class="credglv-user-profile-page">
	<div class="credglv-page-content">
		<div class="container">
			<h2 class="credglv-user-title">User Profile</h2>
			<?php
				echo $context->render('profile', ['user' => $user]);
				if(in_array('credglv_student', $user->roles)){
			?>

			<div class="credglv-tabs credglv-user-profile-tabs">
				<div class="tab-list-wrapper">
					<ul class="tab-list">
						<?php foreach($context->showList() as $key=>$list){ ?>
						<li>
							<a data-tab="tab_<?php echo $list['name']?>" class="tab-link <?php if($key == 0) echo 'active'; ?>">
								<?php echo $list['title']?>
							</a>
						</li>
						<?php } ?>
					</ul>
				</div>
				<div class="tab-content-wrapper">
					<?php foreach($context->showList() as $key=>$list){ ?>
					<div class="tab-panel <?php if($key==0) echo 'active'; ?>" data-content="tab_<?php echo $list['name']?>">
						<div class="credglv-course-list ">
							<?php echo credglv_do_shortcode('[credglv_course_list_'.$list['name'].' cols_on_row=3 course_vc="1" template="template_1" template_1="style_1" ]'); ?>
	   					</div>
					</div>
					<?php } ?>
				</div>
			</div>
			<!-- End tab -->
			<?php } else {?>
	            <div class="credglv-message error">
	                <?php echo __('This page allowed student access only!', 'credglv')?>
	            </div>
	        <?php }?>
		</div>			
	</div>
</div>