<!DOCTYPE HTML>
<html>
<head></head>
<meta http-equiv="refresh" content="30">
<body>
	<div id="body">
        <?php 
		$phpgrid -> enable_edit('FORM', 'R');
		$phpgrid -> set_caption('SMS GATEWAY');
		//$phpgrid -> enable_advanced_search(true);
		$phpgrid->set_theme('smoothness');

		$phpgrid->set_dimension(1330, false); 
		$phpgrid ->set_sortname('ReceivingDateTime', 'DESC');
		$phpgrid->set_col_hidden('Text');
		$phpgrid->set_col_hidden('Coding');
		$phpgrid->set_col_hidden('object_id');
		$phpgrid->set_col_hidden('RecipientID');
		$phpgrid->set_col_hidden('Class');
		$phpgrid->set_col_hidden('SMSCNumber');
		$phpgrid->set_col_width('Processed',10);
		$phpgrid->set_col_width('status',11);
		$phpgrid->set_col_width('UDH',20);
		$phpgrid->set_col_width('SenderNumber',30);
		$phpgrid->set_col_width('ReceivingDateTime',30);
		$phpgrid->set_col_width('ReplySMS',50);
		$phpgrid->enable_search(true);
		$phpgrid->display(); 
		?>
    </div>

</div>
</body>
</html>