<?php
$title = __('Credit logs', 'wmc');
$order_list = new WMR_Credit_log();
?>
<h3>
<?php
//echo esc_html( $title );
?>
</h3>
                
    <form method="get" id="point_logs_id">
        <?php
        $order_list->prepare_items();
        $order_list->display(); ?>
    </form>
</div>
