<div class="referral_program_details">
	<div class="referral_program_overview second_section">

		<div class="referral_program_stats total_earn_credit">
			<span class="total_credit_icon"></span>
			<span><?php _e('Total Credits Earned','woocommerce-extention'); ?></span>
			<span class="show_output"><?php echo  number_format($data['total_earn_point'],2) ; ?></span>
		</div>
		<div class="referral_program_stats total_avilable_credit">
			<span class="total_credit_icon"></span>
			<span><?php _e('Total Credits Available','wmc'); ?></span>
			<span class="show_output"><?php echo  number_format($data['total_points'],2) ; ?></span>
		</div>
		<div class="referral_program_stats">
			<span class="total_credit_icon total_withdraw_credit"></span>
			<span><?php echo _e('Total Withdrawn','woocommerce-extention'); ?></span>
			<span class="show_output"><?php echo  number_format($data['total_withdraw'],2) ; ?></span>
		</div>
	</div>
	<div class="referral_program_overview referral_top_section">
		<div class="referral_program_stats">
			<span class="referral_icon"></span>
			<span><?php _e('Referral Code', 'wmc'); ?></span>
			<span class="show_output"><?php echo $data['referral_code']; ?></span>
		</div>
		<div class="referral_program_stats total_avilable_credit">
			<span class="total_credit_icon"></span>
			<span><?php _e('Total Credits Available','wmc'); ?></span>
			<span class="show_output"><?php echo  number_format($data['total_points'],2) ; ?></span>
		</div>
		<div class="referral_program_stats">
			<span class="total_referral"></span>
			<span><?php _e('Total Referrals', 'wmc'); ?></span>
			<span class="show_output"><?php echo $data['total_followers']; ?></span>
		</div>
	</div>
	<div class="referral_program_sections" style="padding-top: 30px;">

		<div class="referral_program_content">
			<?php echo $data['content']; ?>
		</div>
	</div>
</div>
