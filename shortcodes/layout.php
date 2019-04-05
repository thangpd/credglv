<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */
 ?>

<div class="shortcode-blocks" data-id="<?php echo $context->getId()?>" data-priority="<?php echo $context->getPriority()?>" <?php echo $context->generateAttrbuteHtml($data)?> >
    <?php echo isset($content) ? $content : ''?>
</div>

