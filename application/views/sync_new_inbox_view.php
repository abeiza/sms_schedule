<!DOCTYPE HTML>
<html>
<head></head>
<!--<meta http-equiv="refresh" content="60">-->
<body>
	<div style="position:absolute;width:100%;height:100%;top:0;left:0;background-color:#444;">
		<div STYLE="display:flex;align-items:center;color:#fff;font-family:calibri;height:100%;width:100%;text-align:center;">
			<div style="display:block;width:100%;">
				<div style="width:400px;text-align:left;margin:auto;">
					<h1 style="text-align:center;width:100%;">SYNC DATA</h1>
					<h4 style="text-align:center;width:100%;">Sync Data New Inbox SMS Gateway</h4>
					<style>
						.tab{
							width:100%;
							float:left;
						}
						
						.tab .label{
							width:50%;
							float:left;
							padding:10px 0px;
						}
						
						.tab .sym{
							width:1%;
							float:left;
							padding:10px 0px;
						}
						
						.tab .value{
							width:49%;
							float:left;
							padding:10px 0px;
							text-align:center;
						}
					</style>
					<div class="tab">
						<div class='label'>Success Insert DB Server</div>
						<div class='sym'>:</div>
						<div class='value'><?php echo $notif_insert_success;?></div>
					</div>
					<div class="tab">
						<div class='label'>Failure Insert Status Local</div>
						<div class='sym'>:</div>
						<div class='value'><?php echo $notif_insert_fail_success;?></div>
					</div>
					<div class="tab">
						<div class='label'>Success Update DB Server</div>
						<div class='sym'>:</div>
						<div class='value'><?php echo $notif_update_success;?></div>
					</div>
					<div  class="tab" style="border-bottom:1px dashed #fff;">
						<div class='label'>Failure Update Status Local</div>
						<div class='sym'>:</div>
						<div class='value'><?php echo $notif_update_fail_success;?></div>
					</div>
					<div  class="tab">
						<div class='label'>Total Items Sync</div>
						<div class='sym'>:</div>
						<div class='value'><?php echo $notif_insert_success;?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>