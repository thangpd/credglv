<?php
/**
 * @copyright © 2017 by Solazu Co.,LTD
 * @project Learn Master Plugin
 *
 * @since 1.0
 *
 */
 ?>
<?php credglv()->helpers->general->registerPjax('save-setting-general', 'div')?>
<div class="credglv-setting">
    <?php if(isset($message)):?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( $message, 'credglv' ); ?></p>
        </div>
    <?php endif;?>

    <h2><?php echo __('General settings')?></h2>
    <div class="credglv-col-50">
        <form data-container="#save-setting-general" data-pjax action="<?php echo admin_url('admin-ajax.php')?>" method="post">
            <input type="hidden" name="action" value="setting-general-save" />
            
            <?php foreach ($fields as $field) :?>
                <div class="la-form-group">
                    <?php echo $field?>
                </div>
            <?php endforeach;?>
            
            <div class="la-form-group">
                <button class="button button-primary" type="submit"><?php echo __('Save changes', 'credglv')?></button>
            </div>
        </form>
    </div>
    <div class="credglv-col-50"></div>
    <script language="javascript">
        credglv.ui.select2();
    </script>
</div>
<?php credglv()->helpers->general->endPjax('div')?>

