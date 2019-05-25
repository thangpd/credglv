<?php
/**
 * @copyright © 2017 by Solazu Co.,LTD
 * @project Learn Master Plugin
 *
 * @since 1.0
 *
 */
$tabContent = '';
?>
<nav class="nav-tab-wrapper">
    <?php foreach ($tabs as $name => $tab) :?>
        <?php
        if ($tab['active']) {
            $tabContent = $tab['tabContent'];
        }
        ?>
        <a href="?page=credglv-setting&tab=<?php echo $name?>" class="nav-tab <?php echo $tab['active'] ? 'nav-tab-active' : ''?>"><?php echo $tab['label']?></a>
    <?php endforeach;?>
</nav>

<?php echo $tabContent?>

