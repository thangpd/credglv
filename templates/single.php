<?php
/**
 * @copyright © 2019 by GLV 
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */

?>
<html>
<head>
    <?php wp_head(); ?>
</head>
<body <?php body_class();?>>
<div class="credglv-content-wrapper">
    <?php echo credglv()->page->execute();?>
</div>
</body>
<?php wp_footer()?>
</html>

