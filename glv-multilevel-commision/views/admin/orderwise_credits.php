<?php
$title = __('Orderwise credits', 'wmc');
$order_list = new WMR_Orcer_Credit_List();
?>
<h3>
<?php
//echo esc_html( $title );
?>
</h3>
    <form method="get" id="otherwise_credits">
        <?php
        $order_list->prepare_items();
        $order_list->display(); ?>
    </form>
</div>
