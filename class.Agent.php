<?php
/*
  About:
  Autor: Raul Perusquia <rulovico@gmail.com>
  Created: 27 April 2016 at 18:00:39 GMT+8, Lovina, Bali , Indonesioa

  Copyright (c) 2009, Inikoo

  Version 2.0
*/
include_once 'class.SubjectSupplier.php';


class Agent extends SubjectSupplier {



	var $new=false;
	public $locale='en_GB';
	function Agent($arg1=false, $arg2=false, $arg3=false) {


		global $db;
		$this->db=$db;

		$this->table_name='Agent';
		$this->ignore_fields=array('Agent Key');


		if (is_numeric($arg1)) {
			$this->get_data('id', $arg1);
			return ;
		}

		if ($arg1=='new') {
			$this->find($arg2, $arg3 , 'create');
			return;
		}



		$this->get_data($arg1, $arg2);

	}


	function get_data($tipo, $id) {
		$this->data=$this->base_data();




		if ($tipo=='id' or $tipo=='key')
			$sql=sprintf("select * from `Agent Dimension` where `Agent Key`=%d", $id);
		elseif ($tipo=='code') {

			$sql=sprintf("select * from `Agent Dimension` where `Agent Code`=%s ", prepare_mysql($id));


		}else {
			return;
		}
		if ($this->data = $this->db->query($sql)->fetch()) {
			$this->id=$this->data['Agent Key'];
		}

	}



	function find($raw_data, $address_raw_data, $options) {
		// print "$options\n";
		//print_r($raw_data);

		if (isset($raw_data['editor'])) {
			foreach ($raw_data['editor'] as $key=>$value) {

				if (array_key_exists($key, $this->editor))
					$this->editor[$key]=$value;

			}
		}




		$create='';

		if (preg_match('/create/i', $options)) {
			$create='create';
		}

		if (isset($raw_data['name']))
			$raw_data['Agent Name']=$raw_data['name'];
		if (isset($raw_data['code']))
			$raw_data['Agent Code']=$raw_data['code'];
		if (isset($raw_data['Agent Code']) and $raw_data['Agent Code']=='') {
			$this->get_data('id', 1);
			return;
		}


		$data=$this->base_data();

		foreach ($raw_data as $key=>$value) {
			if (array_key_exists($key, $data)) {
				$data[$key]=_trim($value);
			}
			elseif (preg_match('/^Agent Address/', $key)) {
				$data[$key]=_trim($value);
			}
		}

		$data['Agent Code']=mb_substr($data['Agent Code'], 0, 16);


		if ($data['Agent Code']!='') {
			$sql=sprintf("select `Agent Key` from `Agent Dimension` where `Agent Code`=%s ", prepare_mysql($data['Agent Code']));
			$result=mysql_query($sql);
			if ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {
				$this->found=true;
				$this->found_key=$row['Agent Key'];

			}
		}

		if ($this->found) {
			$this->get_data('id', $this->found_key);
		}


		if ($create) {

			if (!$this->found)
				$this->create($data, $address_raw_data);
		}






	}






	function get($key) {


		if (!$this->id)return false;

		list($got, $result)=$this->get_subject_common($key);
		if ($got)return $result;




		switch ($key) {

		case('Valid From'):
		case('Valid To'):
			if ($this->data['Agent '.$key]=='') {
				return '';
			}else {
				return strftime("%a, %e %b %y", strtotime($this->data['Agent '.$key].' +0:00'));
			}
			break;


		case 'Average Delivery Days':
			if ($this->data['Agent Average Delivery Days']=='')return '';
			return number($this->data['Agent Average Delivery Days']);
			break;
		case 'Delivery Time':
			include_once 'utils/natural_language.php';
			if ($this->get('Agent Average Delivery Days')=='') {
				return '<span class="italic very_discreet">'._('Unknown').'</span>';
			}else {
				return seconds_to_natural_string(24*3600*$this->get('Agent Average Delivery Days'));
			}
			break;



		default;

			if (array_key_exists($key, $this->data))
				return $this->data[$key];

			if (array_key_exists('Agent '.$key, $this->data))
				return $this->data['Agent '.$key];

		}

		return '';

	}




	function create($raw_data, $address_raw_data) {




		$this->data=$this->base_data();
		foreach ($raw_data as $key=>$value) {
			if (array_key_exists($key, $this->data)) {
				$this->data[$key]=_trim($value);
			}
		}



		if ($this->data['Agent Main Plain Mobile']!='') {
			list($this->data['Agent Main Plain Mobile'], $this->data['Agent Main XHTML Mobile'])=$this->get_formatted_number($this->data['Agent Main Plain Mobile']);
		}
		if ($this->data['Agent Main Plain Telephone']!='') {
			list($this->data['Agent Main Plain Telephone'], $this->data['Agent Main XHTML Telephone'])=$this->get_formatted_number($this->data['Agent Main Plain Telephone']);
		}
		if ($this->data['Agent Main Plain FAX']!='') {
			list($this->data['Agent Main Plain FAX'], $this->data['Agent Main XHTML FAX'])=$this->get_formatted_number($this->data['Agent Main Plain FAX']);
		}





		$this->data['Agent Valid From']=gmdate('Y-m-d H:i:s');



		$keys='';
		$values='';
		foreach ($this->data as $key=>$value) {
			$keys.=",`".$key."`";

			if (in_array($key, array('Agent Average Delivery Days', 'Agent Default Incoterm', 'Agent Default Port of Export', 'Agent Default Port of Import', 'Agent Valid To'))) {
				$values.=','.prepare_mysql($value, true);

			}else {
				$values.=','.prepare_mysql($value, false);

			}

		}
		$values=preg_replace('/^,/', '', $values);
		$keys=preg_replace('/^,/', '', $keys);

		$sql="insert into `Agent Dimension` ($keys) values ($values)";

		if ($this->db->exec($sql)) {
			$this->id=$this->db->lastInsertId();


			$this->get_data('id', $this->id);



			if ($this->data['Agent Company Name']!='') {
				$agent_name=$this->data['Agent Company Name'];
			}else {
				$agent_name=$this->data['Agent Main Contact Name'];
			}
			$this->update_field('Agent Name', $agent_name, 'no_history');

			$this->update_address('Contact', $address_raw_data);

			$history_data=array(
				'History Abstract'=>sprintf(_('Agent %s created'), $this->get('Name')),
				'History Details'=>'',
				'Action'=>'created'
			);
			$this->add_history($history_data);
			$this->new=true;

		} else {
			// print "Error can not create agent $sql\n";
		}






	}








	function update_field_switcher($field, $value, $options='', $metadata='') {



		if (is_string($value))
			$value=_trim($value);


		if ($this->update_subject_field_switcher($field, $value, $options, $metadata)) {
			return;
		}



		switch ($field) {
		case('Agent Valid From'):
		case('Agent Valid To'):



			break;

		case('Agent Sticky Note'):
			$this->update_field_switcher('Sticky Note', $value);
			break;
		case('Sticky Note'):
			$this->update_field('Agent '.$field, $value, 'no_null');
			$this->new_value=html_entity_decode($this->new_value);
			break;
		case('Note'):
			$this->add_note($value);
			break;
		case('Attach'):
			$this->add_attach($value);
			break;
		case('Agent Average Delivery Days'):
			$this->update_field($field, $value, $options);
			$this->update_metadata=array(
				'class_html'=>array(
					'Delivery_Time'=>$this->get('Delivery Time'),
				)

			);
			break;
		default:

			$this->update_field($field, $value, $options);
		}


	}



	function post_add_history($history_key, $type=false) {

		if (!$type) {
			$type='Changes';
		}

		$sql=sprintf("insert into  `Agent History Bridge` (`Agent Key`,`History Key`,`Type`) values (%d,%d,%s)",
			$this->id,
			$history_key,
			prepare_mysql($type)
		);
		$this->db->exec($sql);

	}










	function get_field_label($field) {
		global $account;

		switch ($field) {

		case 'Agent Code':
			$label=_('code');
			break;
		case 'Agent Name':
			$label=_('name');
			break;
		case 'Agent Location':
			$label=_('location');
			break;
		case 'Agent Company Name':
			$label=_('company name');
			break;
		case 'Agent Main Contact Name':
			$label=_('contact name');
			break;
		case 'Agent Main Plain Email':
			$label=_('email');
			break;
		case 'Agent Main Email':
			$label=_('main email');
			break;
		case 'Agent Other Email':
			$label=_('other email');
			break;
		case 'Agent Main Plain Telephone':
		case 'Agent Main XHTML Telephone':
			$label=_('telephone');
			break;
		case 'Agent Main Plain Mobile':
		case 'Agent Main XHTML Mobile':
			$label=_('mobile');
			break;
		case 'Agent Main Plain FAX':
		case 'Agent Main XHTML Fax':
			$label=_('fax');
			break;
		case 'Agent Other Telephone':
			$label=_('other telephone');
			break;
		case 'Agent Preferred Contact Number':
			$label=_('main contact number');
			break;
		case 'Agent Fiscal Name':
			$label=_('fiscal name');
			break;

		case 'Agent Contact Address':
			$label=_('contact address');
			break;
		case 'Agent Average Delivery Days':
			$label=_('delivery time (days)');
			break;
		case 'Agent Default Currency Code':
			$label=_('currency');
			break;
		case 'Part Origin Country Code':
			$label=_('country of origin');
			break;
		case 'Agent Default Incoterm':
			$label=_('Incoterm');
			break;
		case 'Agent Default Port of Export':
			$label=_('Port of export');
			break;
		case 'Agent Default Port of Import':
			$label=_('port of import');
			break;
		case 'Agent Default PO Terms and Conditions':
			$label=_('T&C');
			break;
		case 'Agent Show Warehouse TC in PO':
			$label=_('Include general T&C');
			break;
		case 'Agent User Active':
			$label=_('active');
			break;
		case 'Agent User Handle':
			$label=_('login');
			break;
		case 'Agent User Password':
			$label=_('password');
			break;
		case 'Agent User PIN':
			$label=_('PIN');

		default:
			$label=$field;

		}

		return $label;

	}


	function update_supplier_parts() {

		$supplier_number_suppliers=0;
		$supplier_number_parts=0;
		$supplier_number_surplus_parts=0;
		$supplier_number_optimal_parts=0;
		$supplier_number_low_parts=0;
		$supplier_number_critical_parts=0;
		$supplier_number_out_of_stock_parts=0;

		$sql=sprintf('select
		count(*) as num ,
		count(distinct `Agent Supplier Supplier Key`) as suppliers ,
		sum(if(`Part Stock Status`="Surplus",1,0)) as surplus,
		sum(if(`Part Stock Status`="Optimal",1,0)) as optimal,
		sum(if(`Part Stock Status`="Low",1,0)) as low,
		sum(if(`Part Stock Status`="Critical",1,0)) as critical,
		sum(if(`Part Stock Status`="Out_Of_Stock",1,0)) as out_of_stock

		from `Supplier Part Dimension` SP  left join `Part Dimension` P on (P.`Part SKU`=SP.`Supplier Part Part SKU`) left join `Agent Supplier Bridge` B on (`Agent Supplier Supplier Key`=`Supplier Part Supplier Key`)    where `Agent Supplier Agent Key`=%d  and `Part Status`="In Use" and `Supplier Part Status`!="Discontinued" ',
			$this->id
		);


		if ($result=$this->db->query($sql)) {
			if ($row = $result->fetch()) {

				$supplier_number_suppliers=$row['suppliers'];
				$supplier_number_parts=$row['num'];
				if ($row['num']>0) {

					$supplier_number_surplus_parts=$row['surplus'];
					$supplier_number_optimal_parts=$row['optimal'];
					$supplier_number_low_parts=$row['low'];
					$supplier_number_critical_parts=$row['critical'];
					$supplier_number_out_of_stock_parts=$row['out_of_stock'];
				}

			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}


		$this->update(array(
				'Agent Number Suppliers'=>$supplier_number_suppliers,
				'Agent Number Parts'=>$supplier_number_parts,
				'Agent Number Surplus Parts'=>$supplier_number_surplus_parts,
				'Agent Number Optimal Parts'=>$supplier_number_optimal_parts,
				'Agent Number Low Parts'=>$supplier_number_low_parts,
				'Agent Number Critical Parts'=>$supplier_number_critical_parts,
				'Agent Number Out Of Stock Parts'=>$supplier_number_out_of_stock_parts,

			), 'no_history');

	}


	function associate_subject($supplier_key) {

		if (!$supplier_key) return;

		include_once 'class.Supplier.php';

		$supplier=new Supplier($supplier_key);

		if ($supplier->id) {
			$sql=sprintf("insert into `Agent Supplier Bridge` (`Agent Supplier Agent Key`,`Agent Supplier Supplier Key`) values (%d,%d)",
				$this->id,
				$supplier_key

			);

			$this->db->exec($sql);

			$this->update_supplier_parts() ;
			$supplier->update_has_agent();
		}

		$this->update_metadata['updated_showcase_fields']=array(
			'Agent_Number_Suppliers'=>$this->get('Number Suppliers'),
			'Agent_Number_Parts'=>$this->get('Number Parts'),

		);


	}


	function create_supplier($data) {

		$data['editor']=$this->editor;

		$account=new Account();
		$account->editor=$this->editor;




		$supplier= $account->create_supplier($data);


		if ($supplier->id) {
			$this->associate_subject($supplier->id);
		}
		return $supplier;


	}


	function disassociate_subject($supplier_key) {

		if (!$supplier_key) return;

		include_once 'class.Supplier.php';

		$supplier=new Supplier($supplier_key);

		if ($supplier->id) {
			$sql=sprintf("delete from `Agent Supplier Bridge` where `Agent Supplier Agent Key`=%d and `Agent Supplier Supplier Key`=%d",
				$this->id,
				$supplier_key

			);

			$this->db->exec($sql);

			$this->update_supplier_parts() ;
			$supplier->update_has_agent();
		}

		$this->update_metadata['updated_showcase_fields']=array(
			'Agent_Number_Suppliers'=>$this->get('Number Suppliers'),
			'Agent_Number_Parts'=>$this->get('Number Parts'),

		);

	}



}


?>
