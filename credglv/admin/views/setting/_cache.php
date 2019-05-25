<?php
/**
 * @copyright © 2017 by Solazu Co.,LTD
 * @project Learn Master Plugin
 *
 * @since 1.0
 *
 */
 ?>
<?php credglv()->helpers->general->registerPjax('save-setting-cache', 'div')?>
    <div class="credglv-setting">
        <?php if(isset($message)):?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e( $message, 'credglv' ); ?></p>
            </div>
        <?php endif;?>
        <?php if (!empty($options)) :?>
            <h2>Cache Options</h2>
            <form data-container="#save-setting-cache" data-pjax action="<?php echo admin_url('admin-ajax.php')?>" method="post">
                <input type="hidden" name="action" value="options-cache-save" />
                <?php foreach ($options as $optionName => $label) :?>
                    <div class="la-form-group">
                        <div class="title">
                            <?php echo $label?>
                        </div>
                        <label>
                            <input type="radio"  name="Options[<?php echo $optionName?>]" <?php echo (get_option($optionName, '') == '1' ? ' checked ' : '') ?> value="1" />
                            Enable
                        </label>
                        <label>
                            <input type="radio"  name="Options[<?php echo $optionName?>]" <?php echo (get_option($optionName, '') == '0' ? ' checked ' : '') ?> value="0" />
                            Disable
                        </label>
                    </div>
                <?php endforeach;?>
                <div class="la-form-group">
                    <button class="button button-primary" name="selected_caches" type="submit" value="">
                        <?php echo __('Save settings', 'credglv')?>
                    </button>
                </div>
            </form>
        <?php endif;?>
        <h2>Cache managements</h2>
        <form data-container="#save-setting-cache" data-pjax action="<?php echo admin_url('admin-ajax.php')?>" method="post">
            <input type="hidden" name="action" value="setting-cache-save" />
            <?php foreach ($caches as $cache => $label) :?>
                <div class="la-form-group">
                    <label>
                        <input type="checkbox" checked name="Cache[<?php echo $cache?>]" value="1" />
                        <?php echo $label?>
                    </label>
                </div>
            <?php endforeach;?>
            <div class="la-form-group">
                <button class="button button-primary" name="selected_caches" type="submit" value="">
                    <?php echo __('Clear caches', 'credglv')?>
                </button>
            </div>
        </form>
    </div>
<?php credglv()->helpers->general->endPjax('div')?>