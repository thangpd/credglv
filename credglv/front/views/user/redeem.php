<?php

?>
<h2>Balance: USD <?php echo $data['total_cash']->total ?></h2>

<form action="" class="form-control form-redeem">

    <label for="redeem_cash">
		<?php echo __( 'Cash redeem', 'credglv' ) ?>
        <input type="text" name="amount" id="amount">
    </label>
    <br>
    <br>
    <input type="submit">
</form>


<h3>Recent History</h3>

<section class="the-css-at-table">
    <header style="display: none;">
        <p class="tr">
            <span class="th">#</span>
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
<?php


?>

