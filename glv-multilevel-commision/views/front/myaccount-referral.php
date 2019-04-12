<div class="referral_program_details">
    <span class="referral_icon"></span>
    <span><?php _e( 'Referral Link', 'wmc' ); ?></span>
	<?php

	$link_referral = '';

	if ( $myaccount_id = get_option( 'woocommerce_myaccount_page_id' ) ) {
		$link_referral = get_permalink( $myaccount_id ) . '?ru=' . $data['referral_code'];
	} else {
		$link_referral = home_url() . '?ru=' . $data['referral_code'];
	}
	?>
    <input type="text" value="<?php echo $link_referral; ?>">
    <span class="total_referral"></span>
    <span><?php _e( 'Total Referrals: ', 'wmc' ); ?></span>
    <span class="show_output"><?php echo $data['total_followers']; ?></span>
    <div class="referral_program_sections" style="padding-top: 30px;">

        <div class="referral_program_content">
			<?php echo $data['content']; ?>
        </div>
    </div>
</div>
