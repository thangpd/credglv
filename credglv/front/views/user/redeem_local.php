<?php

$total_cash  = isset( $data['total_cash']->total ) ? number_format( $data['total_cash']->total, 2, '.', '' ) : 0;
$total_local = isset( $data['total_local']->total ) ? number_format( $data['total_local']->total, 2, '.', '' ) : 0;
?>
<h2>Cash Balance: <?php
	if ( ! empty( $data ) ) {
		echo $total_cash.' '. __( 'USD', 'credglv' );
	} ?></h2>
<form method="POST" action="" class="form-control form-redeem local-redeem">

    <label for="redeem_cash">
		<?php echo __( 'Withdrawal amount', 'credglv' ) ?>
        <input type="text" name="amount" id="amount" style="width: 100%">
    </label>
    <input type="hidden" name="type" value="local" id="type">

    <p>
    <?php echo __('Withdrawal fee is 20,000 VND per request.','credglv'); ?>
    </p>
    <br>
    <br>
    <button class="btn btn-default ld-ext-right" type="submit"
            name="cach_redeem"><?php echo __( 'Submit', 'credglv' ) ?>
        <div class="ld ld-spinner ld-spin"></div>
    </button>
</form>

<?php
if ( ! empty( $data ) ) {
	?>

    <h3>Recent History</h3>
    <?php if($data['html']) { ?>
    <section class="the-css-at-table">
        <header style="display: none;">
            <p class="tr">
                <span class="th">#</span>
                <span class="th">Log</span>
                <span class="th">Status</span>
                <span class="th">Amount</span>
                <span class="th">Create Date</span>
            </p>
        </header>
        <div class="tbody">
			<?php
			echo $data['html'];
			?>

        </div>
    </section>
	<?php } else {
        echo '<label>No transaction was made yet</label>';    
    }
}

?>

