<?php
/**
 * Date: 5/2/19
 * Time: 11:01 AM
 */
if ( is_user_logged_in() ) {
	$user    = new \credglv\models\UserModel();
	$user_id = get_current_user_id();
	$list    = [];

	$list = $user->recursive_tree_referral_user( $user_id );
//	$user->update_active_status( $user_id );
//	print_r( $user->check_actived_referral( $user_id ) );

	?>
    <div class="total-referal" style="margin-bottom: 8px">
        <div class="title"><?php echo __( 'Total member of your team (downlines): ', 'credglv' ); ?><?php echo $user->count_referral_user( $user_id ) ?></div>

    </div>
    <div id="collapsable-example" data-data='<?php echo json_encode( $list ) ?>'
         style="width:100%; height: 460px; border: none; background-color: #2f4058"></div>
<?php } ?>