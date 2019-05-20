<?php

$total_cash  = isset( $data['total_cash']->total ) ? number_format( $data['total_cash']->total, 2, '.', '' ) : 0;
$total_local = isset( $data['total_local']->total ) ? number_format( $data['total_local']->total, 2, '.', '' ) : 0;
?>
<h2>Balance Cash: <?php
	if ( ! empty( $data ) ) {
		echo $total_cash . __( 'USD', 'credglv' );
	} ?></h2>
<h2>Balance Local: <?php
	if ( ! empty( $data ) ) {
		echo $total_local . __( 'USD', 'credglv' );
	} ?></h2>
<form method="POST" action="" class="form-control form-redeem">

    <label for="redeem_cash">
		<?php echo __( 'Local Bank Redeem', 'credglv' ) ?>
        <input type="text" name="amount" id="amount">
    </label>
    <input type="hidden" name="type" value="local" id="type">

    <br>
    <br>
    <input type="submit">
</form>

<?php
if ( ! empty( $data ) ) {
	?>

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
