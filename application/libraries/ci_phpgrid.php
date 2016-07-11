<?php
require_once('phpgrid/conf.php');

class CI_phpgrid {

    public function example_method($val = '')
    {
        $dg = new C_DataGrid("SELECT * FROM Orders", $val, "Orders");
        return $dg;
    }
	
	public function final_sms(){
		$final = new C_DataGrid("SELECT * FROM new_inbox","object_id","new_inbox");
		return $final;
	}
}