<?php
/**
 * Created by PhpStorm.
 * User: thang
 * Date: 4/2/19
 * Time: 6:49 PM
 */
?>
<h1><?php echo __( 'Redeem local bank', 'credglv' ); ?></h1>
<section class="the-css-at-table">
    <header style="display: none;">
        <p class="tr">
            <span class="th">#</span>
            <span class="th">User</span>
            <span class="th">Log</span>
            <span class="th">Status</span>
            <span class="th">Amount</span>
            <span class="th">Fee</span>
            <span class="th">Create Date</span>
        </p>
    </header>
    <div class="tbody">
		<?php
		echo $data['html'];
		?>

    </div>
</section>















