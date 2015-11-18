<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 22 September 2015 13:54:37 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/


function get_table_parameters() {

	return  prepare_values($_REQUEST, array(
			'parameters'=>array('type'=>'json array'),
			'nr'=>array('type'=>'number'),
			'page'=>array('type'=>'number'),
			'o'=>array('type'=>'string', 'optional'=>true),
			'od'=>array('type'=>'string', 'optional'=>true),
			'f_value'=>array('type'=>'string', 'optional'=>true),

		));
}


function get_table_totals($sql_totals, $wheref='', $record_label='') {


	global $db;


	if ($sql_totals) {
		$sql=trim($sql_totals." $wheref");

		if ($row = $db->query($sql)->fetch()) {
			$total=$row['num'];
		}


		if ($wheref!='') {
			$sql=$sql_totals;
			if ($row = $db->query($sql)->fetch()) {
				$total_records=$row['num'];
				$filtered=$row['num']-$total;
			}

		} else {
			$filtered=0;
			$filter_total=0;
			$total_records=$total;
		}

	}else {
		$filtered=0;
		$filter_total=0;
		$total_records=0;
		$total=0;
	}



	if ($filtered==0) {
		$rtext=get_rtext($record_label, $total_records);
	}else {
		$rtext='<i class="fa fa-filter fa-fw"></i> '. get_rtext_with_filter($record_label, $total, $total_records);
	}

	return array($rtext, $total, $filtered);

}


function get_rtext($record_label, $total_records) {
	if ($record_label=='customer') {
		return sprintf(ngettext('%s customer', '%s customers', $total_records), number($total_records));
	}elseif ($record_label=='order') {
		return sprintf(ngettext('%s order', '%s orders', $total_records), number($total_records));
	}elseif ($record_label=='store') {
		return sprintf(ngettext('%s store', '%s stores', $total_records), number($total_records));
	}elseif ($record_label=='department') {
		return sprintf(ngettext('%s department', '%s departments', $total_records), number($total_records));
	}elseif ($record_label=='family') {
		return sprintf(ngettext('%s family', '%s families', $total_records), number($total_records));
	}elseif ($record_label=='product') {
		return sprintf(ngettext('%s product', '%s products', $total_records), number($total_records));
	}elseif ($record_label=='category') {
		return sprintf(ngettext('%s category', '%s categories', $total_records), number($total_records));
	}elseif ($record_label=='order') {
		return sprintf(ngettext('%s order', '%s orders', $total_records), number($total_records));
	}elseif ($record_label=='item') {
		return sprintf(ngettext('%s item', '%s items', $total_records), number($total_records));
	}elseif ($record_label=='invoice') {
		return sprintf(ngettext('%s invoice', '%s invoices', $total_records), number($total_records));
	}elseif ($record_label=='delivery_note') {
		return sprintf(ngettext('%s delivery note', '%s delivery notes', $total_records), number($total_records));
	}elseif ($record_label=='part') {
		return sprintf(ngettext('%s part', '%s parts', $total_records), number($total_records));
	}elseif ($record_label=='website') {
		return sprintf(ngettext('%s website', '%s websites', $total_records), number($total_records));
	}elseif ($record_label=='warehouse') {
		return sprintf(ngettext('%s warehouse', '%s warehouses', $total_records), number($total_records));
	}elseif ($record_label=='supplier') {
		return sprintf(ngettext('%s supplier', '%s suppliers', $total_records), number($total_records));
	}elseif ($record_label=='employee') {
		return sprintf(ngettext('%s employee', '%s employees', $total_records), number($total_records));
	}elseif ($record_label=='ex employee') {
		return sprintf(ngettext('%s ex employee', '%s ex employees', $total_records), number($total_records));
	}elseif ($record_label=='user') {
		return sprintf(ngettext('%s user', '%s users', $total_records), number($total_records));
	}elseif ($record_label=='report') {
		return sprintf(ngettext('%s report', '%s reports', $total_records), number($total_records));
	}elseif ($record_label=='session') {
		return sprintf(ngettext('%s session', '%s sessions', $total_records), number($total_records));
	}elseif ($record_label=='list') {
		return sprintf(ngettext('%s list', '%s lists', $total_records), number($total_records));
	}elseif ($record_label=='customer with favourites') {
		return sprintf(ngettext('%s customer with favourites', '%s customers with favourites', $total_records), number($total_records));
	}elseif ($record_label=='product favourited') {
		return sprintf(ngettext('%s product favourited', '%s products favourited', $total_records), number($total_records));
	}elseif ($record_label=='query') {
		return sprintf(ngettext('%s query', '%s queries', $total_records), number($total_records));
	}elseif ($record_label=='search') {
		return sprintf(ngettext('%s search', '%s searches', $total_records), number($total_records));
	}elseif ($record_label=='transaction') {
		return sprintf(ngettext('%s transaction', '%s transactions', $total_records), number($total_records));
	}elseif ($record_label=='payment_account') {
		return sprintf(ngettext('%s payment account', '%s payment accounts', $total_records), number($total_records));
	}else {
		return sprintf(ngettext('%s record', '%s records', $total_records), number($total_records));
	}
}


function get_rtext_with_filter($record_label, $total_with_filter, $total_no_filter) {
	if ($record_label=='customer') {
		return sprintf(ngettext('%s customer of %s', '%s customers of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='order') {
		return sprintf(ngettext('%s order of %s', '%s orders of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='store') {
		return sprintf(ngettext('%s store of %s', '%s stores of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='department') {
		return sprintf(ngettext('%s department of %s', '%s departments of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='family') {
		return sprintf(ngettext('%s family of %s', '%s families of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='product') {
		return sprintf(ngettext('%s product of %s', '%s products of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='category') {
		return sprintf(ngettext('%s category of %s', '%s categories of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='order') {
		return sprintf(ngettext('%s order of %s', '%s orders of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='item') {
		return sprintf(ngettext('%s item of %s', '%s items of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='invoice') {
		return sprintf(ngettext('%s invoice of %s', '%s invoices of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='delivery_note') {
		return sprintf(ngettext('%s delivery note of %s', '%s delivery notes of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='part') {
		return sprintf(ngettext('%s part of %s', '%s parts of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='website') {
		return sprintf(ngettext('%s website of %s', '%s websites of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='warehouse') {
		return sprintf(ngettext('%s warehouse of %s', '%s warehouses of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='supplier') {
		return sprintf(ngettext('%s supplier of %s', '%s suppliers of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='employee') {
		return sprintf(ngettext('%s employee of %s', '%s employees of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='ex employee') {
		return sprintf(ngettext('%s ex employee of %s', '%s ex employees of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='user') {
		return sprintf(ngettext('%s user of %s', '%s users of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='report') {
		return sprintf(ngettext('%s report of %s', '%s reports of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='session') {
		return sprintf(ngettext('%s session of %s', '%s sessions of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='list') {
		return sprintf(ngettext('%s list of %s', '%s lists of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='customer with favourites') {
		return sprintf(ngettext('%s customer with favourites of %s', '%s customers with favourites of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='product favourited') {
		return sprintf(ngettext('%s product favourited of %s', '%s products favourited of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='query') {
		return sprintf(ngettext('%s query of %s', '%s queries of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='search') {
		return sprintf(ngettext('%s search of %s', '%s searches of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='transaction') {
		return sprintf(ngettext('%s transaction of %s', '%s transaction of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}elseif ($record_label=='payment_account') {
		return sprintf(ngettext('%s payment account of %s', '%s payment accounts of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}else {
		return sprintf(ngettext('%s record of %s', '%s records of %s', $total_with_filter), number($total_with_filter) , number($total_no_filter)  );
	}
}


?>
