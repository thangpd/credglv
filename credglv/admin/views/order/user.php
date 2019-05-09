<?php
/**
 * @copyright © 2017 by Solazu Co.,LTD
 * @project Learn Master Plugin
 *
 * @since 1.0
 *
 *
 * @var \credglv\models\OrderModel $order
 */
?>

<div class="la-form-group" id="credglv-order-user">
    <fieldset class="credglv <?php echo empty($order->credglv_order_user_id) == false ? ' credglv-hide ' : ''?>" id="credglv-order-user-edit">
        <legend>Select an user :</legend>
        <select data-select2-ajax name="LemaOrder[credglv_order_user_id]" data-order_user class="select2" data-url="<?php echo admin_url('amin-ajax.php')?>" data-action="credglv_search_user">
            <option selected value="<?php echo $order->credglv_order_user_id?>"></option>
        </select>
        <br/><hr/>
        <div class="aling-left">
            <button data-order_id="<?php echo $order->post->ID?>" data-target="#credglv-order-user" data-items="data-order_user" data-action="credglv_order_add_user" type="button" class="button button-primary button-large ajax-button">Save</button>
            &nbsp;
            <?php if (!empty($order->credglv_order_user_id)) :?>
                <a href="javascript:void(0);" data-show="#credglv-order-user-info" data-hide="#credglv-order-user-edit">cancel</a>
            <?php endif;?>
        </div>
    </fieldset>
    <?php if (empty($order->credglv_order_user_id)) :?>
        <div class="credglv-message warning is-dismissible">
            <p><?php echo __( 'No student selected', 'credglv' ); ?></p>
        </div>
    <?php else:?>
        <?php
            /** @var WP_User $user */
            $user = $order->getUser();
        ?>
        <div id="credglv-order-user-info">
            <fieldset class="credglv" >
                <legend>Student information</legend>
                <strong><?php echo $user->display_name?></strong> <a href="javascript:void(0);" data-hide="#credglv-order-user-info" data-show="#credglv-order-user-edit"><i class="fa fa-pencil"></i> </a>
            </fieldset>
            <fieldset class="credglv">
                <legend>Billing address <a href="javascript:void(0);" data-hide="#credglv-orfer-billing-info" data-show="#credglv-order-billding-edit"><i class="fa fa-pencil"></i> </a> </legend>
                <?php echo $context->render('_billing_address', ['form' => $form, 'order' => $order])?>
            </fieldset>
        </div>
    <?php endif;?>
</div>


<script language="javascript">
    credglv.ui.select2();
</script>