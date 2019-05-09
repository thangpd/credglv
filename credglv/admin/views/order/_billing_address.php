<?php
/**
 * @copyright © 2017 by Solazu Co.,LTD
 * @project Learn Master Plugin
 *
 * @since 1.0
 *
 * @var \credglv\models\OrderModel $order
 * @var \credglv\core\components\Billing  $address
 */
$address = $order->getBillingAddress();
 ?>
<div id="credglv-order-billing">
    <div id="credglv-orfer-billing-info">
        <?php if( !$address->empty) :?>
            <?php echo $address?>

        <?php else:?>
            <div class="credglv-message warning is-dismissible" id="credglv-order-billing-warning">
                <p><?php echo __( 'No billing address found.', 'credglv' ); ?> &nbsp; <a href="javascript:void(0);" data-hide="#credglv-order-billing-warning" data-show="#credglv-order-billding-edit"> <?php echo __('Add new', 'credglv')?> <sup><i class="fa fa-pencil"></i> </sup></a> </p>
            </div>
        <?php endif;?>
    </div>
    <div id="credglv-order-billding-edit" class="credglv-hide">
        <div class="la-form-group credglv-form">
            <div class="credglv-col-50">
                <label><?php echo __('First name', 'credglv')?></label>
                <input value="<?php echo $address->first_name?>" name="Billing[first_name]" data-order_billing type="text" class="la-form-control" />
            </div>
            <div class="credglv-col-50">
                <label><?php echo __('Last name', 'credglv')?></label>
                <input value="<?php echo $address->last_name?>" name="Billing[last_name]" data-order_billing type="text" class="la-form-control" />
            </div>
        </div>
        <div class="la-form-group">
            <label><?php echo __('Company', 'credglv')?></label>
            <input name="Billing[company]" value="<?php echo $address->company?>" data-order_billing type="text" class="la-form-control" />
        </div>
        <div class="la-form-group credglv-form">
            <div class="credglv-col-50">
                <label><?php echo __('Address line 1', 'credglv')?></label>
                <input name="Billing[address_line1]"  value="<?php echo $address->address_line1?>"data-order_billing type="text" class="la-form-control" />
            </div>
            <div class="credglv-col-50">
                <label><?php echo __('Address line 2', 'credglv')?></label>
                <input name="Billing[address_line2]" value="<?php echo $address->address_line2?>" data-order_billing type="text" class="la-form-control" />
            </div>
        </div>
        <div class="la-form-group credglv-form">
            <div class="credglv-col-50">
                <label><?php echo __('City', 'credglv')?></label>
                <input  name="Billing[city]" value="<?php echo $address->city?>" data-order_billing type="text" class="la-form-control" />
            </div>
            <div class="credglv-col-50">
                <label><?php echo __('Postcode/ZIP', 'credglv')?></label>
                <input name="Billing[postcode]" value="<?php echo $address->postcode?>" data-order_billing type="text" class="la-form-control" />
            </div>
        </div>
        <div class="la-form-group credglv-form">
            <div class="credglv-col-50">
                <label><?php echo __('Country', 'credglv')?></label>
                <select name="Billing[country]"  data-order_billing class="la-form-control">
                    <option>No</option>
                </select>
            </div>
            <div class="credglv-col-50">
                <label><?php echo __('State/County', 'credglv')?></label>
                <input name="Billing[state]" value="<?php echo $address->state?>" data-order_billing type="text" class="la-form-control" />
            </div>
        </div>
        <div class="la-form-group credglv-form">
            <div class="credglv-col-50">
                <label><?php echo __('Email', 'credglv')?></label>
                <input name="Billing[email]" value="<?php echo $address->email?>" data-order_billing type="text" class="la-form-control" />
            </div>
            <div class="credglv-col-50">
                <label><?php echo __('Phone', 'credglv')?></label>
                <input  name="Billing[phone]" value="<?php echo $address->phone?>" data-order_billing type="text" class="la-form-control" />
            </div>
        </div>
        <div class="la-form-group credglv-form">
            <div class="credglv-col-50">
                <label><?php echo __('Payment method', 'credglv')?></label>
                <select name="Billing[payment_method]" data-order_billing class="la-form-control">
                    <option>No</option>
                </select>
            </div>
            <div class="credglv-col-50">
                <label><?php echo __('Transaction ID', 'credglv')?></label>
                <input name="Billing[transaction_id]" value="<?php echo $address->transaction_id?>" data-order_billing type="text" class="la-form-control" />
            </div>
        </div>
        <div class="la-form-group button-block button-block-left">
            <button data-order_id="<?php echo $order->post->ID?>" data-target="#credglv-order-billing" data-items="data-order_billing" data-action="credglv_order_add_billing" type="button" class="button button-primary button-large ajax-button">
                <?php echo __('Update billing', 'credglv')?>
            </button>
            &nbsp;
            <a href="javascript:void(0);" data-show="#credglv-orfer-billing-info" data-hide="#credglv-order-billding-edit"><i class="fa fa-close"></i> Cancel</a>
        </div>
    </div>
</div>