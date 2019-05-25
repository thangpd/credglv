<?php
if ( ! empty( $data ) ) {
	$total_cash = isset( $data['total_cash']->total ) ? number_format( $data['total_cash']->total, 2, '.', '' ) : 0;
	?>
    <h2>Cash Balance:
        USD <?php echo $total_cash ?></h2>

    <form action="" class="form-control form-redeem" method="POST">

        <label for="redeem_cash">
			<?php echo __( 'Cash redeem', 'credglv' ) ?>
            <input type="text" name="amount" id="amount">
        </label>
        <br>
        <br>
        <button class="btn btn-default ld-ext-right" type="submit"
                name="cash_redeem"><?php echo __('Submit','credglv') ?>
            <div class="ld ld-spinner ld-spin"></div>
        </button>

    </form>


    <h3>Recent History</h3>

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
	<?php
}

?>

