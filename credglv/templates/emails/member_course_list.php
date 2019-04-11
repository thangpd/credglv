<?php
/**
 * @var \credglv\models\OrderModel $order
 */
$items = $order->getItems();
add_image_size( 'square-150', 150, 150, true )
?>
<hr />
<table border="0" cellpadding="0" style="width: 600px" width="600px" cellspacing="0">
    <tbody>
        <?php foreach($items as $item) :?>
            <?php
            /** @var \credglv\models\CourseModel $course */
            $course = \credglv\models\CourseModel::findOne($item->course_id);
            ?>
        <tr>
            <td style="padding:10px 0px 10px 0px;" valign="top">
                <table align="left" width="40%" cellpadding="0" cellspacing="0" border="0">
                    <tbody>
                        <tr>
                            <td valign="top" align="center" style="padding:0px 0px 0px 0px;">
                                <a target="_blank" href="<?php echo get_permalink($course->post->ID)?>">
                                    <?php echo get_the_post_thumbnail($course->post->ID, 'square-150')?>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table align="right" width="58%" cellpadding="0" cellspacing="0" border="0">
                    <tbody>
                        <tr>
                            <td style="font-size:18px;color:#1e2c35;font-weight:bold;text-align:left;font-family:sans-serif;line-height:22px;vertical-align:top;padding:0px 8px 10px 0px;">
                                <a target="_blank" href="<?php echo get_permalink($course->post->ID)?>">
                                    <p style="margin:0;"><?php echo $course->post->post_title?></p>
                                </a>
                                <small>
                                    <?php echo $course->course_subtitle?>
                                </small>
                            </td>
                        </tr>
                        <tr>
                            <td width="58%" style="padding:0px 0px 0px 0px; font-size: 24px; color: darkred; text-align: right">
                                <?php echo credglv()->helpers->general->currencyFormat($course->getPrice())?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="2" align="right" style="font-size: 24px; color: darkred; text-align: right">
                <?php echo __('Total', 'credglv')?> : <?php echo credglv()->helpers->general->currencyFormat($order->total)?>
            </td>
        </tr>
    </tbody>
</table>
<hr />