<?php
/**
 * @copyright © 2017 by Solazu Co.,LTD
 * @project Learn Master Plugin
 *
 * @since 1.0
 *
 */
?>
<?php credglv()->helpers->general->registerPjax('save-setting-payment', 'div')?>
<div class="credglv-setting">
    <?php if(isset($message)):?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( $message, 'credglv' ); ?></p>
        </div>
    <?php endif;?>
    <?php if(!isset($_GET['section'])):?>
        <fieldset class="credglv-setting-block">
            <legend><?php echo __('Payment settings', 'credglv')?></legend>
            <div class="credglv-setting-bar">
                <?php echo __('Configuration for payment gateways :', 'credglv')?>
                <?php foreach ($gateways as $gateway):?>
                    <?php /** @var \credglv\core\interfaces\PaymentGatewayInterface $gateway */?>
                    <a href="?page=credglv-setting&tab=payment&section=<?php echo $gateway->getPaymentGatewayId()?>"><?php echo $gateway->getGatewayName()?></a>
                <?php endforeach;?>
            </div>
            <form data-container="#save-setting-payment" data-pjax action="<?php echo admin_url('admin-ajax.php')?>" method="post">
                <input type="hidden" name="action" value="setting-payment-save" />
                <h3><?php echo __('Enabled payment gateways :', 'credglv')?></h3>
                <input type="hidden" name="Payment[gateways][]" value=""/>
                <?php foreach ($gateways as $gateway):?>
                    <div class="la-form-group">
                        <label>
                            <input <?php echo in_array($gateway->getPaymentGatewayId(), $enabledGateways) ? ' checked ' : ''?> type="checkbox" name="Payment[gateways][]" value="<?php echo $gateway->getPaymentGatewayId()?>" />
                            <?php echo $gateway->getGatewayName()?>
                        </label>
                    </div>
                <?php endforeach;?>
                <div class="la-form-group">
                    <button class="button button-primary" name="selected_caches" type="submit" value="">
                        <?php echo __('Save your changes', 'credglv')?>
                    </button>
                </div>
            </form>
        </fieldset>
    <?php else:?>
        <a href="?page=credglv-setting&tab=payment"><i class="fa fa-arrow-left"></i> <?php echo __('Return payment gateways', 'credglv')?></a>
        <?php /** @var \credglv\core\interfaces\PaymentGatewayInterface $gateway */?>
        <?php echo $gateway->settings()?>
    <?php endif;?>
</div>
<?php credglv()->helpers->general->endPjax('div')?>