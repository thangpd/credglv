<?php
/**
 * @copyright © 2019 by GLV 
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 * @var \credglv\core\RuntimeException $exception
 */
?>

<?php wp_head(); ?>
<body class="credglv-single-page">
<div class="credglv-content-wrapper">
    <style>
        .credglv-message.error{
            color: red;
            width: 65%;
            min-width: 500px;
            margin: 50px auto;
            padding: 30px;
            border: 1px #ddd solid;
            text-align: center;
            -webkit-box-shadow: 1px 3px 5px 0px rgba(0,0,0,0.54);
            -moz-box-shadow: 1px 3px 5px 0px rgba(0,0,0,0.54);
            box-shadow: 1px 3px 5px 0px rgba(0,0,0,0.54);
        }
    </style>
    <div class="credglv-message error" style="">
        <?php echo $exception->getMessage()?>
    </div>
</div>
</body>
<?php wp_footer()?>

