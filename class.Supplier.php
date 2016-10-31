<?php
/*
  File: Supplier.php

  This file contains the Supplier Class

  About:
  Autor: Raul Perusquia <rulovico@gmail.com>

  Copyright (c) 2009, Inikoo

  Version 2.0
*/
include_once 'class.SubjectSupplier.php';


class Supplier extends SubjectSupplier {



	var $new=false;
	public $locale='en_GB';

	function Supplier($arg1=false, $arg2=false, $arg3=false) {


		global $db;
		$this->db=$db;

		$this->table_name='Supplier';
		$this->ignore_fields=array('Supplier Key');


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

		if ($tipo=='id' or $tipo=='key') {
			$sql=sprintf("select * from `Supplier Dimension` where `Supplier Key`=%d", $id);
		}elseif ($tipo=='code') {


			$sql=sprintf("select * from `Supplier Dimension` where `Supplier Code`=%s ", prepare_mysql($id));


		}elseif ($tipo=='deleted') {
			$this->get_deleted_data($id);
			return;
		}else {
			return;
		}
		if ($this->data = $this->db->query($sql)->fetch()) {
			$this->id=$this->data['Supplier Key'];
		}

	}


	function get_deleted_data( $tag) {

		$this->deleted=true;
		$sql=sprintf("select * from `Supplier Deleted Dimension` where `Supplier Deleted Key`=%d", $tag);
		if ($this->data = $this->db->query($sql)->fetch()) {
			$this->id=$this->data['Supplier Deleted Key'];
			foreach (json_decode(gzuncompress($this->data['Supplier Deleted Metadata']), true) as $key=>$value) {
				$this->data[$key]=$value;
			}
		}
	}


	function load_acc_data() {
		$sql=sprintf("select * from `Supplier Data` where `Supplier Key`=%d", $this->id);

		if ($result=$this->db->query($sql)) {
			if ($row = $result->fetch()) {
				foreach ($row as $key=>$value) {
					$this->data[$key]=$value;
				}
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
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
			$raw_data['Supplier Name']=$raw_data['name'];
		if (isset($raw_data['code']))
			$raw_data['Supplier Code']=$raw_data['code'];
		if (isset($raw_data['Supplier Code']) and $raw_data['Supplier Code']=='') {
			$this->get_data('id', 1);
			return;
		}


		$data=$this->base_data();

		foreach ($raw_data as $key=>$value) {
			if (array_key_exists($key, $data)) {
				$data[$key]=_trim($value);
			}
			elseif (preg_match('/^Supplier Address/', $key)) {
				$data[$key]=_trim($value);
			}
		}

		$data['Supplier Code']=mb_substr($data['Supplier Code'], 0, 16);


		if ($data['Supplier Code']!='') {
			$sql=sprintf("select `Supplier Key` from `Supplier Dimension` where `Supplier Code`=%s ", prepare_mysql($data['Supplier Code']));

			if ($result=$this->db->query($sql)) {
				if ($row = $result->fetch()) {

					$this->found=true;
					$this->found_key=$row['Supplier Key'];


				}
			}else {
				print_r($error_info=$this->db->errorInfo());
				exit;
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


	function get_categories($scope='keys') {

		if (   $scope=='objects') {
			include_once 'class.Category.php';
		}


		$categories=array();


		$sql=sprintf("select B.`Category Key` from `Category Dimension` C left join `Category Bridge` B on (B.`Category Key`=C.`Category Key`) where `Subject`='Supplier' and `Subject Key`=%d and `Category Branch Type`!='Root'",
			$this->id);

		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {

				if ($scope=='objects') {
					$categories[$row['Category Key']]=new Category($row['Category Key']);
				}else {
					$categories[$row['Category Key']]=$row['Category Key'];
				}


			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}

		return $categories;


	}


	function get_category_data() {
		$sql=sprintf("select B.`Category Key`,`Category Root Key`,`Other Note`,`Category Label`,`Category Code`,`Is Category Field Other` from `Category Bridge` B left join `Category Dimension` C on (C.`Category Key`=B.`Category Key`) where  `Category Branch Type`='Head'  and B.`Subject Key`=%d and B.`Subject`='Supplier'",
			$this->id);

		$category_data=array();



		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {




				$sql=sprintf("select `Category Label`,`Category Code` from `Category Dimension` where `Category Key`=%d", $row['Category Root Key']);


				if ($result2=$this->db->query($sql)) {
					if ($row2 = $result2->fetch()) {
						$root_label=$row2['Category Label'];
						$root_code=$row2['Category Code'];
					}
				}else {
					print_r($error_info=$this->db->errorInfo());
					exit;
				}



				if ($row['Is Category Field Other']=='Yes' and $row['Other Note']!='') {
					$value=$row['Other Note'];
				}
				else {
					$value=$row['Category Label'];
				}
				$category_data[]=array('root_label'=>$root_label, 'root_code'=>$root_code, 'label'=>$row['Category Label'], 'label'=>$row['Category Code'], 'value'=>$value, 'category_key'=>$row['Category Key']);

			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}




		return $category_data;
	}


	function get($key) {

		global $account;
		if (!$this->id)return false;
		list($got, $result)=$this->get_subject_supplier_common($key);

		if ($got)return $result;

		switch ($key) {
		default;



			if (array_key_exists($key, $this->data))
				return $this->data[$key];
			if (array_key_exists('Supplier '.$key, $this->data))
				return $this->data['Supplier '.$key];
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



		if ($this->data['Supplier Main Plain Mobile']!='') {
			list($this->data['Supplier Main Plain Mobile'], $this->data['Supplier Main XHTML Mobile'])=$this->get_formatted_number($this->data['Supplier Main Plain Mobile']);
		}
		if ($this->data['Supplier Main Plain Telephone']!='') {
			list($this->data['Supplier Main Plain Telephone'], $this->data['Supplier Main XHTML Telephone'])=$this->get_formatted_number($this->data['Supplier Main Plain Telephone']);
		}
		if ($this->data['Supplier Main Plain FAX']!='') {
			list($this->data['Supplier Main Plain FAX'], $this->data['Supplier Main XHTML FAX'])=$this->get_formatted_number($this->data['Supplier Main Plain FAX']);
		}





		$this->data['Supplier Valid From']=gmdate('Y-m-d H:i:s');



		$keys='';
		$values='';
		foreach ($this->data as $key=>$value) {
			$keys.=",`".$key."`";

			if (in_array($key, array('Supplier Average Delivery Days', 'Supplier Default Incoterm', 'Supplier Default Port of Export', 'Supplier Default Port of Import', 'Supplier Valid To'))) {
				$values.=','.prepare_mysql($value, true);

			}else {
				$values.=','.prepare_mysql($value, false);

			}

		}
		$values=preg_replace('/^,/', '', $values);
		$keys=preg_replace('/^,/', '', $keys);

		$sql="insert into `Supplier Dimension` ($keys) values ($values)";

		if ($this->db->exec($sql)) {
			$this->id=$this->db->lastInsertId();


			$this->get_data('id', $this->id);


			$sql="insert into `Supplier Data` (`Supplier Key`) values(".$this->id.");";
			$this->db->exec($sql);

			if ($this->data['Supplier Company Name']!='') {
				$supplier_name=$this->data['Supplier Company Name'];
			}else {
				$supplier_name=$this->data['Supplier Main Contact Name'];
			}
			$this->update_field('Supplier Name', $supplier_name, 'no_history');

			$this->update_address('Contact', $address_raw_data);

			$history_data=array(
				'History Abstract'=>_('Supplier created'),
				'History Details'=>'',
				'Action'=>'created'
			);
			$this->add_subject_history($history_data, true, 'No', 'Changes', $this->get_object_name(), $this->get_main_id());
			$this->new=true;

		} else {
			// print "Error can not create supplier $sql\n";
		}






	}


	function update_supplier_parts() {


		$parts_skus=$this->get_part_skus();


		$supplier_number_parts=0;
		$supplier_number_active_parts=0;
		$supplier_number_surplus_parts=0;
		$supplier_number_optimal_parts=0;
		$supplier_number_low_parts=0;
		$supplier_number_critical_parts=0;
		$supplier_number_out_of_stock_parts=0;


		if ($parts_skus!='') {


			$supplier_number_parts=count(preg_split('/,/', $parts_skus));


			/*
			$sql=sprintf('select count(*) as num
		from `Supplier Part Dimension` SP  where `Supplier Part Supplier Key`=%d  ',
				$this->id
			);


			if ($result=$this->db->query($sql)) {
				if ($row = $result->fetch()) {
					//print_r($row);

					$supplier_number_parts=$row['num'];

				}
			}else {
				print_r($error_info=$this->db->errorInfo());
				exit;
			}
*/

			$parts_skus=$this->get_part_skus('in_use');

			if ($parts_skus!='') {

				$sql=sprintf('select count(*) as num ,
		sum(if(`Part Stock Status`="Surplus",1,0)) as surplus,
		sum(if(`Part Stock Status`="Optimal",1,0)) as optimal,
		sum(if(`Part Stock Status`="Low",1,0)) as low,
		sum(if(`Part Stock Status`="Critical",1,0)) as critical,
		sum(if(`Part Stock Status`="Out_Of_Stock",1,0)) as out_of_stock

		from `Supplier Part Dimension` SP  left join `Part Dimension` P on (P.`Part SKU`=SP.`Supplier Part Part SKU`)  where `Supplier Part Supplier Key`=%d and `Supplier Part Part SKU` in (%s) ',
					$this->id,
					addslashes($parts_skus)
				);




			//	print $sql;
				if ($result=$this->db->query($sql)) {
					if ($row = $result->fetch()) {
						//print_r($row);
						$supplier_number_active_parts=$row['num'];
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

			}



		}



		$this->update(array(
				'Supplier Number Parts'=>$supplier_number_parts,
				'Supplier Number Active Parts'=>$supplier_number_active_parts,
				'Supplier Number Surplus Parts'=>$supplier_number_surplus_parts,
				'Supplier Number Optimal Parts'=>$supplier_number_optimal_parts,
				'Supplier Number Low Parts'=>$supplier_number_low_parts,
				'Supplier Number Critical Parts'=>$supplier_number_critical_parts,
				'Supplier Number Out Of Stock Parts'=>$supplier_number_out_of_stock_parts,

			), 'no_history');


		foreach ($this->get_categories('objects') as $category) {
			$category->update_supplier_category_parts();
		}


	}


	function get_part_skus($type='all') {


		$part_skus='';

		if ($type=='in_use') {
			$sql=sprintf('select `Supplier Part Part SKU` from `Supplier Part Dimension` SP left join `Part Dimension` P on (P.`Part SKU`=SP.`Supplier Part Part SKU`) where `Supplier Part Supplier Key`=%d and `Part Status` in ("In Use","Discontinuing") and `Supplier Part Status`!="Discontinued" group by `Supplier Part Part SKU`', $this->id);
		}else {
			$sql=sprintf('select `Supplier Part Part SKU` from `Supplier Part Dimension` where `Supplier Part Supplier Key`=%d  group by `Supplier Part Part SKU`', $this->id);
		}

		$part_skus='';
		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {
				$part_skus.=$row['Supplier Part Part SKU'].',';
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}
		$part_skus=preg_replace('/\,$/', '', $part_skus);

		return $part_skus;

	}






	function update_field_switcher($field, $value, $options='', $metadata='') {



		if (is_string($value))
			$value=_trim($value);


		if ($this->update_subject_field_switcher($field, $value, $options, $metadata)) {
			return;
		}


		switch ($field) {




		case('Supplier ID'):
		case('Supplier Valid From'):
		case('Supplier Stock Value'):
		case('Supplier Company Key'):
		case('Supplier Accounts Payable Contact Key'):
			break;
		case 'Supplier On Demand':

			if (! in_array($value, array('No', 'Yes'))) {
				$this->error=true;
				$this->msg=sprintf(_('Invalid value, valid values: %s'), '"Yes", "No"');
				return;
			}

			$this->update_field($field, $value, $options);
			if ($this->updated and $value=='No') {

				$sql=sprintf("select `Supplier Part Key` from `Supplier Part Dimension` where `Supplier Part Supplier Key`=%d  and  `Supplier Part On Demand`='Yes' ",
					$this->id);
				if ($result=$this->db->query($sql)) {
					include_once 'class.SupplierPart.php';
					foreach ($result as $row) {
						$supplier_part=new SupplierPart( $row['Supplier Part Key']);

						$supplier_part->update(array('Supplier Part On Demand'=>'No'), $options);
					}
				}else {
					print_r($error_info=$db->errorInfo());
					exit;
				}

			}

			break;
		case('Supplier Sticky Note'):
			$this->update_field_switcher('Sticky Note', $value);
			break;
		case('Sticky Note'):
			$this->update_field('Supplier '.$field, $value, 'no_null');
			$this->new_value=html_entity_decode($this->new_value);
			break;
		case('Note'):
			$this->add_note($value);
			break;
		case('Attach'):
			$this->add_attach($value);
			break;
		case('Supplier Average Delivery Days'):
			$this->update_field($field, $value, $options);
			$this->update_metadata=array(
				'class_html'=>array(
					'Delivery_Time'=>$this->get('Delivery Time'),
				)

			);

			if ($value!='') {

				include_once 'class.SupplierPart.php';

				$sql=sprintf("select `Supplier Part Key` from `Supplier Part Dimension` where `Supplier Part Supplier Key`=%d  and  `Supplier Part Average Delivery Days` is NULL ", $this->id);
				if ($result=$this->db->query($sql)) {
					foreach ($result as $row) {
						$supplier_part=new SupplierPart( $row['Supplier Part Key']);

						$supplier_part->update(array('Supplier Part Average Delivery Days'=>$this->get('Supplier Average Delivery Days')), $options);
					}
				}else {
					print_r($error_info=$db->errorInfo());
					exit;
				}




			}

			break;
		case('Supplier Products Origin Country Code'):
			$this->update_field($field, $value, $options);

			include_once 'class.Part.php';

			$sql=sprintf("select  `Part SKU`  from `Supplier Part Dimension` left join `Part Dimension` on (`Part SKU`=`Supplier Part Part SKU`)  where `Supplier Part Supplier Key`=%d and  `Part Origin Country Code` is NULL", $this->id);


			if ($result=$this->db->query($sql)) {
				foreach ($result as $row) {
					$part=new Part($row['Part SKU']);

					$part->update(array('Part Origin Country Code'=>$value));
				}
			}else {
				print_r($error_info=$this->db->errorInfo());
				exit;
			}
			break;

		case 'Supplier Default Currency Code':

			$this->update_field($field, $value, $options);

			include_once 'class.SupplierPart.php';
			$sql=sprintf('select `Supplier Part Key` from `Supplier Part Dimension` where `Supplier Part Supplier Key`=%d ', $this->id);

			if ($result=$this->db->query($sql)) {
				foreach ($result as $row) {
					$supplier_part=new SupplierPart($row['Supplier Part Key']);

					$supplier_part->update(array('Supplier Part Currency Code'=>$this->get('Supplier Default Currency Code')), $options);
				}
			}else {
				print_r($error_info=$this->db->errorInfo());
				exit;
			}


			break;
		case 'unlink agent':


			include_once 'class.Agent.php';
			$agent=new Agent($value);

			$sql=sprintf('delete from `Agent Supplier Bridge` where `Agent Supplier Agent Key`=%d and `Agent Supplier Supplier Key`=%d',
				$value,
				$this->id
			);
			$this->db->exec($sql);

			$this->update_type('Free', 'no_history');
			$agent->update_supplier_parts() ;

			$history_data=array(
				'History Abstract'=>sprintf(_("Supplier %s inlinked from agent %s"), $this->data['Supplier Code'], $agent->get('Code')),
				'History Details'=>'',
				'Action'=>'edited'
			);

			$this->add_subject_history($history_data, true, 'No', 'Changes', $this->get_object_name(), $this->get_main_id());

			break;

		default:


			if (array_key_exists($field, $this->base_data('Supplier Data'))   ) {
				//print "$field $value \n";
				$this->update_table_field($field, $value, $options, 'Supplier', 'Supplier Data', $this->id);
			}else {

				$this->update_field($field, $value, $options);
			}
		}


	}


	function create_supplier_part_record($data) {




		$data['editor']=$this->editor;

		unset($data['Supplier Part Supplier Code']);


		if (isset($data['Supplier Part Package Description']) and !isset($data['Part Package Description'])) {
			$data['Part Package Description']=$data['Supplier Part Package Description'];
			unset($data['Supplier Part Package Description']);
		}


		if (isset($data['Supplier Part Unit Description']) and !isset($data['Part Unit Description'])) {
			$data['Part Unit Description']=$data['Supplier Part Unit Description'];
			unset($data['Supplier Part Unit Description']);
		}

		if (isset($data['Supplier Part Unit Label']) and !isset($data['Part Unit Label'])) {
			$data['Part Unit Label']=$data['Supplier Part Unit Label'];
			unset($data['Supplier Part Unit Label']);
		}






		if ( !isset($data['Supplier Part Reference']) or $data['Supplier Part Reference']=='') {
			$this->error=true;
			$this->msg=_("Supplier's part reference missing");
			$this->error_code='supplier_part_reference_missing';
			$this->metadata='';
			return;
		}

		$sql=sprintf('select count(*) as num from `Supplier Part Dimension` where `Supplier Part Reference`=%s and `Supplier Part Supplier Key`=%d  ',
			prepare_mysql($data['Supplier Part Reference']),
			$this->id

		);


		if ($result=$this->db->query($sql)) {
			if ($row = $result->fetch()) {
				if ($row['num']>0) {
					$this->error=true;
					$this->msg=sprintf(_('Duplicated reference (%s)'), $data['Supplier Part Reference']);
					$this->error_code='duplicate_supplier_part_reference';
					$this->metadata=$data['Supplier Part Reference'];
					return;
				}
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}


		if ( !isset($data['Part Reference']) or $data['Part Reference']=='') {
			$this->error=true;
			$this->msg=_("Part reference missing");
			$this->error_code='part_reference_missing';
			$this->metadata='';
			return;
		}


		$sql=sprintf('select count(*) as num from `Part Dimension` where `Part Reference`=%s ',
			prepare_mysql($data['Part Reference'])
		);


		if ($result=$this->db->query($sql)) {
			if ($row = $result->fetch()) {
				if ($row['num']>0) {
					$this->error=true;
					$this->msg=sprintf(_('Duplicated reference (%s)'), $data['Part Reference']);
					$this->error_code='duplicate_part_reference';
					$this->metadata=$data['Part Reference'];
					return;
				}
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}

		if ( !isset($data['Part Unit Label']) or $data['Part Unit Label']=='') {


			$this->error=true;
			$this->msg=_('Unit label missing');
			$this->error_code='part_unit_label_missing';
			return;
		}

		if ( !isset($data['Part Unit Description']) or $data['Part Unit Description']=='') {


			$this->error=true;
			$this->msg=_('Unit description missing');
			$this->error_code='part_unit_description_missing';
			return;
		}

		if ( !isset($data['Part Package Description']) or $data['Part Package Description']=='') {


			$this->error=true;
			$this->msg=_('Outers (SKO) description missing');
			$this->error_code='part_package_description_missing';
			return;
		}

		if (  !isset($data['Supplier Part Packages Per Carton']) or $data['Supplier Part Packages Per Carton']==''   ) {
			$this->error=true;
			$this->msg=_('Outers (SKO) per carton missing');
			$this->error_code='supplier_part_packages_per_carton_missing';
			return;
		}

		if (!is_numeric($data['Supplier Part Packages Per Carton']) or $data['Supplier Part Packages Per Carton']<0  ) {
			$this->error=true;
			$this->msg=sprintf(_('Invalid outers (SKO) per carton (%s)'), $data['Supplier Part Packages Per Carton']);
			$this->error_code='invalid_supplier_part_packages_per_carton';
			$this->metadata=$data['Supplier Part Packages Per Carton'];
			return;
		}



		if (  !isset($data['Part Units Per Package']) or $data['Part Units Per Package']==''   ) {
			$this->error=true;
			$this->msg=_('Units per SKO missing');
			$this->error_code='part_unit_per_package_missing';

			return;
		}

		if (!is_numeric($data['Part Units Per Package']) or $data['Part Units Per Package']<0  ) {
			$this->error=true;
			$this->msg=sprintf(_('Invalid units per SKO (%s)'), $data['Part Units Per Package']);
			$this->error_code='invalid_part_unit_per_package';
			$this->metadata=$data['Part Units Per Package'];
			return;
		}



		if (  !isset($data['Supplier Part Minimum Carton Order']) or $data['Supplier Part Minimum Carton Order']==''   ) {
			$this->error=true;
			$this->msg=_('Minimum order missing');
			$this->error_code='supplier_part_minimum_carton_order_missing';

			return;
		}

		if (!is_numeric($data['Supplier Part Minimum Carton Order']) or $data['Supplier Part Minimum Carton Order']<0  ) {
			$this->error=true;
			$this->msg=sprintf(_('Invalid minimum order (%s)'), $data['Supplier Part Minimum Carton Order']);
			$this->error_code='invalid_supplier_part_minimum_carton_order';
			$this->metadata=$data['Supplier Part Minimum Carton Order'];
			return;
		}


		if (  !isset($data['Supplier Part Unit Cost']) or $data['Supplier Part Unit Cost']==''   ) {
			$this->error=true;
			$this->msg=_('Cost missing');
			$this->error_code='supplier_part_unit_cost_missing';

			return;
		}

		if (!is_numeric($data['Supplier Part Unit Cost']) or $data['Supplier Part Unit Cost']<0  ) {
			$this->error=true;
			$this->msg=sprintf(_('Invalid cost (%s)'), $data['Supplier Part Unit Cost']);
			$this->error_code='invalid_supplier_part_unit_cost';
			$this->metadata=$data['Supplier Part Unit Cost'];
			return;
		}



		if (  !isset($data['Supplier Part Unit Extra Cost']) or $data['Supplier Part Unit Extra Cost']==''   ) {
			$data['Supplier Part Unit Extra Cost']=0;
		}

		if (!is_numeric($data['Supplier Part Unit Extra Cost']) or $data['Supplier Part Unit Extra Cost']<0  ) {
			$this->error=true;
			$this->msg=sprintf(_('Invalid extra cost (%s)'), $data['Supplier Part Unit Extra Cost']);
			$this->error_code='invalid_supplier_part_unit_extra_cost';
			$this->metadata=$data['Supplier Part Unit Extra Cost'];
			return;
		}

		if (isset($data['Part Unit Price']) and $data['Part Unit Price']!='' ) {
			if (!is_numeric($data['Part Unit Price']) or $data['Part Unit Price']<0  ) {
				$this->error=true;
				$this->msg=sprintf(_('Invalid unit recommended price (%s)'), $data['Part Unit Price']);
				$this->error_code='invalid_part_unit_price';
				$this->metadata=$data['Part Unit Price'];
				return;
			}
		}
		if (isset($data['Part Unit RRP']) and $data['Part Unit RRP']!='' ) {
			if (!is_numeric($data['Part Unit RRP']) or $data['Part Unit RRP']<0  ) {
				$this->error=true;
				$this->msg=sprintf(_('Invalid unit recommended RRP (%s)'), $data['Part Unit RRP']);
				$this->error_code='invalid_part_unit_rrp';
				$this->metadata=$data['Part Unit RRP'];
				return;
			}
		}
		if (isset($data['Supplier Part Carton CBM']) and $data['Supplier Part Carton CBM']!='' ) {
			if (!is_numeric($data['Supplier Part Carton CBM']) or $data['Supplier Part Carton CBM']<0  ) {
				$this->error=true;
				$this->msg=sprintf(_('Invalid carton CBM (%s)'), $data['Supplier Part Carton CBM']);
				$this->error_code='invalid_supplier_part_carton_cbm';
				$this->metadata=$data['Supplier Part Carton CBM'];
				return;
			}
		}

		if (  !isset($data['Supplier Part Average Delivery Days']) or $data['Supplier Part Average Delivery Days']==''   ) {
			$data['Supplier Part Average Delivery Days']=$this->get('Supplier Average Delivery Days');
		}else {
			if (!is_numeric($data['Supplier Part Average Delivery Days']) or $data['Supplier Part Average Delivery Days']<0  ) {
				$this->error=true;
				$this->msg=sprintf(_('Invalid delivery time (%s)'), $data['Supplier Part Average Delivery Days']);
				$this->error_code='invalid_supplier_delivery_days';
				$this->metadata=$data['Supplier Part Average Delivery Days'];
				return;
			}

		}


		$data['Supplier Part Supplier Key']=$this->id;

		$data['Supplier Part Minimum Carton Order']=ceil($data['Supplier Part Minimum Carton Order']);



		$data['Supplier Part Currency Code']=$this->data['Supplier Default Currency Code'];






		$data['Supplier Part Status']='Available';

		$supplier_part= new SupplierPart('find', $data, 'create');



		if ($supplier_part->id) {
			$this->new_object_msg=$supplier_part->msg;

			if ($supplier_part->new) {
				$this->new_object=true;
				$this->update_supplier_parts();





				if (isset($data['Part Part Materials'])) {
					$materials=$data['Part Part Materials'];
					unset($data['Part Part Materials']);

				}else {
					$materials='';
				}

				if (isset($data['Part Part Package Dimensions'])) {
					$package_dimensions=$data['Part Part Package Dimensions'];
					unset($data['Part Part Package Dimensions']);

				}else {
					$package_dimensions='';
				}

				if (isset($data['Part Part Unit Dimensions'])) {
					$unit_dimensions=$data['Part Part Unit Dimensions'];
					unset($data['Part Part Unit Dimensions']);

				}else {
					$unit_dimensions='';
				}


				foreach ($data as $key=>$value) {
					$_key=preg_replace('/^Part Part /', 'Part ', $key);
					$data[$_key]=$value;


				}


				$part=new Part('find', $data, 'create');


				if ($part->new) {

					$part->update(
						array(
							'Part Materials'=>$materials,
							'Part Package Dimensions'=>$package_dimensions,
							'Part Unit Dimensions'=>$unit_dimensions,

						)
						, 'no_history'
					);

					$supplier_part->update(array('Supplier Part Part SKU'=>$part->sku));
					$supplier_part->get_data('id', $supplier_part->id);

					$supplier_part->update_historic_object();
					$part->update_cost();
				}else {

					$this->error=true;
					if ($part->found) {

						$this->error_code='duplicated_field';
						$this->error_metadata=json_encode(array($part->duplicated_field));

						if ($part->duplicated_field=='Part Reference') {
							$this->msg=_("Duplicated part reference");
						}else {
							$this->msg='Duplicated '.$part->duplicated_field;
						}


					}else {
						$this->msg=$part->msg;
					}

					$sql=sprintf('delete from `Supplier Part Dimension` where `Supplier Part Key`=%d', $supplier_part->id);
					$this->db->exec($sql);
					$sql=sprintf('select `History Key` from `Supplier Part History Bridge` where `Supplier Part Key`=%d', $supplier_part->id);
					if ($result=$this->db->query($sql)) {
						foreach ($result as $row) {
							$sql=sprintf('delete from `History Dimension` where `History Key`=%d  ', $row['History Key']);
							$this->db->exec($sql);
						}
					}else {
						print_r($error_info=$this->db->errorInfo());
						exit;
					}

					$sql=sprintf('delete from `Supplier Part Dimension` where `Supplier Part Key`=%d', $supplier_part->id);
					$this->db->exec($sql);
					$supplier_part=new SupplierPart(0);

				}




			}
			else {

				$this->error=true;
				if ($supplier_part->found) {

					$this->error_code='duplicated_field';
					$this->error_metadata=json_encode(array($supplier_part->duplicated_field));

					if ($supplier_part->duplicated_field=='Supplier Part Reference') {
						$this->msg=_("Duplicated supplier's part reference");
					}else {
						$this->msg='Duplicated '.$supplier_part->duplicated_field;
					}


				}else {
					$this->msg=$supplier_part->msg;
				}
			}
			return $supplier_part;
		}
		else {
			$this->error=true;

			if ($supplier_part->found) {
				$this->error_code='duplicated_field';
				$this->error_metadata=json_encode(array($supplier_part->duplicated_field));

				if ($supplier_part->duplicated_field=='Part Reference') {
					$this->msg=_("Duplicated part reference");
				}else {
					$this->msg='Duplicated '.$supplier_part->duplicated_field;
				}

			}else {



				$this->msg=$supplier_part->msg;
			}
		}

	}


	function get_field_label($field) {
		global $account;

		switch ($field) {

		case 'Supplier Code':
			$label=_('code');
			break;
		case 'Supplier Name':
			$label=_('name');
			break;
		case 'Supplier Location':
			$label=_('location');
			break;
		case 'Supplier Company Name':
			$label=_('company name');
			break;
		case 'Supplier Main Contact Name':
			$label=_('contact name');
			break;
		case 'Supplier Main Plain Email':
			$label=_('email');
			break;
		case 'Supplier Main Email':
			$label=_('main email');
			break;
		case 'Supplier Other Email':
			$label=_('other email');
			break;
		case 'Supplier Main Plain Telephone':
		case 'Supplier Main XHTML Telephone':
			$label=_('telephone');
			break;
		case 'Supplier Main Plain Mobile':
		case 'Supplier Main XHTML Mobile':
			$label=_('mobile');
			break;
		case 'Supplier Main Plain FAX':
		case 'Supplier Main XHTML Fax':
			$label=_('fax');
			break;
		case 'Supplier Other Telephone':
			$label=_('other telephone');
			break;
		case 'Supplier Preferred Contact Number':
			$label=_('main contact number');
			break;
		case 'Supplier Fiscal Name':
			$label=_('fiscal name');
			break;

		case 'Supplier Contact Address':
			$label=_('contact address');
			break;
		case 'Supplier Average Delivery Days':
			$label=_('delivery time (days)');
			break;
		case 'Supplier Default Currency Code':
			$label=_('currency');
			break;
		case 'Part Origin Country Code':
			$label=_('country of origin');
			break;
		case 'Supplier Default Incoterm':
			$label=_('Incoterm');
			break;
		case 'Supplier Default Port of Export':
			$label=_('Port of export');
			break;
		case 'Supplier Default Port of Import':
			$label=_('port of import');
			break;
		case 'Supplier Default PO Terms and Conditions':
			$label=_('T&C');
			break;
		case 'Supplier Show Warehouse TC in PO':
			$label=_('Include general T&C');
			break;
		case 'Supplier User Active':
			$label=_('active');
			break;
		case 'Supplier User Handle':
			$label=_('login');
			break;
		case 'Supplier User Password':
			$label=_('password');
			break;
		case 'Supplier User PIN':
			$label=_('PIN');
			break;
		case 'Supplier On Demand':
			$label=_('Allow on demand');
			break;
		case 'Supplier Account Number':
			$label=_("Account number");
			break;
		case 'Supplier Skip Inputting':
			$label=_("Skip inputting");
			break;
		case 'Supplier Skip Mark as Dispatched':
			$label=_("Skip mark as dispatched");
			break;
		case 'Supplier Skip Mark as Received':
			$label=_("Skip mark as received");
			break;
		case 'Supplier Skip Checking':
			$label=_("Skip checking");
			break;
		case 'Supplier Automatic Placement Location':
			$label=_("Try automatic placement location");
			break;













		default:
			$label=$field;

		}

		return $label;

	}




	function get_agents_data() {
		$agents_data=array();
		$sql=sprintf('select `Agent Code`,`Agent Key`,`Agent Name`  from `Agent Supplier Bridge` left join `Agent Dimension` on (`Agent Supplier Agent Key`=`Agent Key`)  where `Agent Supplier Supplier Key`=%d',
			$this->id
		);
		if ($result=$this->db->query($sql)) {
			if ($row = $result->fetch()) {
				$agents_data[]=array(
					'Agent Key'=>$row['Agent Key'],
					'Agent Code'=>$row['Agent Code'],
					'Agent Name'=>$row['Agent Name'],

				);
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}
		return $agents_data;

	}


	function archive() {

		$this->update_type('Archived', 'no_history');


		$history_data=array(
			'History Abstract'=>sprintf(_("Supplier %s archived"), $this->data['Supplier Code']),
			'History Details'=>'',
			'Action'=>'edited'
		);

		$this->add_subject_history($history_data, true, 'No', 'Changes', $this->get_object_name(), $this->get_main_id());



	}


	function unarchive() {

		$this->update_type('Free', 'no_history');


		$history_data=array(
			'History Abstract'=>sprintf(_("Supplier %s unarchived"), $this->data['Supplier Code']),
			'History Details'=>'',
			'Action'=>'edited'
		);

		$this->add_subject_history($history_data, true, 'No', 'Changes', $this->get_object_name(), $this->get_main_id());



	}


	function update_type($value, $options='') {

		$has_agent='No';
		$sql=sprintf('select count(*) as num from `Agent Supplier Bridge` where `Agent Supplier Supplier Key`=%d',
			$this->id
		);
		if ($result=$this->db->query($sql)) {
			if ($row = $result->fetch()) {
				if ($row['num']>0) {
					$has_agent='Yes';
				}
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}


		if ($value!='Archived') {
			if ($has_agent=='Yes') {
				$value='Agent';
			}else {
				$value='Free';

			}

		}



		switch ($value) {
		case 'Free':
			$this->update(array(
					'Supplier Type'=>'Free',
					'Supplier Has Agent'=>$has_agent,
					'Supplier Valid To'=>''

				), 'no_history');
			break;
		case 'Agent':
			$this->update(array(
					'Supplier Type'=>'Agent',
					'Supplier Has Agent'=>$has_agent,
					'Supplier Valid To'=>''
				), 'no_history');

			break;
		case 'Archived':




			$this->update(array(
					'Supplier Type'=>'Archived',
					'Supplier Has Agent'=>$has_agent,
					'Supplier Valid To'=>gmdate('Y-m-d H:i:s')

				), 'no_history');

			break;
		default:
			$this->error=true;
			$this->msg='Not valid supplirt type value '.$value;
			break;
		}

	}


	function delete($metadata=false) {

		$this->load_acc_data();


		$sql=sprintf('insert into `Supplier Deleted Dimension`  (`Supplier Deleted Key`,`Supplier Deleted Code`,`Supplier Deleted Name`,`Supplier Deleted From`,`Supplier Deleted To`,`Supplier Deleted Metadata`) values (%d,%s,%s,%s,%s,%s) ',
			$this->id,
			prepare_mysql($this->get('Supplier Code')),
			prepare_mysql($this->get('Supplier Name')),
			prepare_mysql($this->get('Supplier Valid From')),
			prepare_mysql(gmdate('Y-m-d H:i:s')),
			prepare_mysql(gzcompress(json_encode($this->data), 9))

		);
		$this->db->exec($sql);

		//print $sql;


		$sql=sprintf('delete from `Supplier Dimension`  where `Supplier Key`=%d ',
			$this->id
		);
		$this->db->exec($sql);


		$history_data=array(
			'History Abstract'=>sprintf(_("Supplier record %s deleted"), $this->data['Supplier Name']),
			'History Details'=>'',
			'Action'=>'deleted'
		);

		$this->add_subject_history($history_data, true, 'No', 'Changes', $this->get_object_name(), $this->get_main_id());




		$this->deleted=true;


		$sql=sprintf('select `Supplier Part Key` from `Supplier Part Dimension` where `Supplier Part Supplier Key`=%d  ', $this->id);

		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {
				$supplier_part=get_object('Supplier Part', $row['Supplier Part Key']);
				$supplier_part->delete();
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}


	}



	function update_timseries_date($date) {
		include_once 'class.Timeserie.php';
		$sql=sprintf('select `Timeseries Key` from `Timeseries Dimension` where `Timeseries Parent`="Supplier" and `Timeseries Parent Key`=%d ',
			$this->id);


		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {
				$timeseries=new Timeseries($row['Timeseries Key']);
				$this->update_timeseries_record($timeseries, $date, $date );
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}


	}


	function create_timeseries($data) {


		include_once 'class.Timeserie.php';

		$data['Timeseries Parent']='Supplier';
		$data['Timeseries Parent Key']=$this->id;



		$timeseries=new Timeseries('find', $data, 'create');

		if ($timeseries->id ) {
			require_once 'utils/date_functions.php';

			if ($this->data['Supplier Valid From']!='') {
				$from=date('Y-m-d', strtotime($this->get('Valid From')));

			}else {
				$from='';
			}

			if ($this->get('Supplier Type')=='Archived') {
				$to=$this->get('Valid To');
			}else {
				$to=date('Y-m-d');
			}



			$sql=sprintf('delete from `Timeseries Record Dimension` where `Timeseries Record Timeseries Key`=%d and `Timeseries Record Date`<%s ',
				$timeseries->id,
				prepare_mysql($from)
			);

			$update_sql = $this->db->prepare($sql);
			$update_sql->execute();
			if ($update_sql->rowCount()) {
				$timeseries->update(array('Timeseries Updated'=>gmdate('Y-m-d H:i:s')), 'no_history');

			}

			$sql=sprintf('delete from `Timeseries Record Dimension` where `Timeseries Record Timeseries Key`=%d and `Timeseries Record Date`>%s ',
				$timeseries->id,
				prepare_mysql($to)
			);

			$update_sql = $this->db->prepare($sql);
			$update_sql->execute();
			if ($update_sql->rowCount()) {
				$timeseries->update(array('Timeseries Updated'=>gmdate('Y-m-d H:i:s')), 'no_history');

			}


			if ($from and $to) {


				$this->update_timeseries_record($timeseries, $from, $to);






			}
		}

	}


	function update_timeseries_record($timeseries, $from, $to) {



		$dates=date_frequency_range($this->db, $timeseries->get('Timeseries Frequency'), $from, $to);



		foreach ($dates as $date_frequency_period) {
			$sales_data=$this->get_sales_data($date_frequency_period['from'], $date_frequency_period['to']);
			$_date=gmdate('Y-m-d', strtotime($date_frequency_period['from'].' +0:00')) ;


			if ($sales_data['deliveries']>0 or $sales_data['dispatched']>0 or $sales_data['invoiced_amount']!=0 or $sales_data['required']!=0 or $sales_data['profit']!=0) {

				list($timeseries_record_key, $date)=$timeseries->create_record(array('Timeseries Record Date'=> $_date ));



				$sql=sprintf('update `Timeseries Record Dimension` set `Timeseries Record Integer A`=%d ,`Timeseries Record Integer B`=%d ,`Timeseries Record Float A`=%.2f ,  `Timeseries Record Float B`=%f ,`Timeseries Record Float C`=%f ,`Timeseries Record Type`=%s where `Timeseries Record Key`=%d',
					$sales_data['dispatched'],
					$sales_data['deliveries'],
					$sales_data['invoiced_amount'],
					$sales_data['required'],
					$sales_data['profit'],
					prepare_mysql('Data'),
					$timeseries_record_key

				);



				//  print "$sql\n";

				$update_sql=$this->db->prepare($sql);
				$update_sql->execute();
				if ($update_sql->rowCount() or $date==date('Y-m-d')) {
					$timeseries->update(array('Timeseries Updated'=>gmdate('Y-m-d H:i:s')), 'no_history');
				}


			}
			else {
				$sql=sprintf('delete from `Timeseries Record Dimension` where `Timeseries Record Timeseries Key`=%d and `Timeseries Record Date`=%s ',
					$timeseries->id,
					prepare_mysql($_date)
				);

				$update_sql = $this->db->prepare($sql);
				$update_sql->execute();
				if ($update_sql->rowCount()) {
					$timeseries->update(array('Timeseries Updated'=>gmdate('Y-m-d H:i:s')), 'no_history');

				}

			}
			$timeseries->update_stats();

		}


	}




}


?>
