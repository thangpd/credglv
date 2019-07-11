<?php
date_default_timezone_set("Asia/Bangkok");
?>
<ul style="list-style-type: none; margin: 0">
<?php 
foreach ($data['test'] as $key => $value) { 
	$border_top = '';
	if($key == 0)
		$border_top = 'border-top: 0.5px solid black;';
	$color = '';
	$min_past_time = '0';
	$now = strtotime(date("Y-m-d H:i:s"));
	$create_date = strtotime(date("Y-m-d H:i:s",strtotime($value->created_date)));

	$past_time = $now - $create_date;
	  
	$years = floor($past_time / (365*60*60*24));  
	  
	$months = floor(($past_time - $years * 365*60*60*24) / (30*60*60*24));  
	  
	$days = floor(($past_time - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)); 

	$hours = floor(($past_time - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60));  
	  
	$minutes = floor(($past_time - $years * 365*60*60*24  - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);

	$text_past_time = '';
	if($days > 7){
		$weeks = (int) ($days/7);
		$text_past_time = $weeks.' weeks ago';
	}
	else {
		if($days > 0)
			$text_past_time = $days. ' days ago';
		else{
			if($hours > 0)
				$text_past_time = $hours.' hours ago';
			else{
				if($minutes > 0)
					$text_past_time = $minutes.' minutes ago';
				else
					$text_past_time = 'Just now';
			}
		}
	}
	if($value->active == 0) 
		$color = 'background-color: #edf2fa';
	$content = $value->content;
	if(strlen($content) > 60){
		$content = substr($content, 0, 60).'...';
	} ?>
	<li style="position: relative; overflow: hidden; display: block;padding: 10px;<?php echo $color ?>">
		<a href="<?php echo $value->link ?>" style="display: block; position: relative; color: black">
			<div style="float: left; margin-right: 10px">
				<img src="http://localhost/Outsource/GLV/wp-content/uploads/credglv/img/20_ava_2019-06-3-18-51-33_61725047_1079796815553593_8550129646849490944_n.jpg" style="width: 65px; height: 65px; display: block; border-radius: 50%">
			</div>
			<div>
				<span><?php echo $content ?></span>
				<br>
				<span><?php echo $text_past_time ?></span>
			</div>
		</a>
	</li>
	<hr style="margin: 0">
<?php }
?>
</ul>



