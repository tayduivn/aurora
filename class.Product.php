<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Based in 2009 class.Product.php
 Created: 16 February 2016 at 22:35:16 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2016, Inikoo

 Version 3

*/

include_once 'class.Asset.php';

class Product extends Asset{

	function __construct($arg1=false, $arg2=false, $arg3=false) {

		global $db;
		$this->db=$db;


		$this->table_name='Product';
		$this->ignore_fields=array('Product ID');
		$this->webpage=false;
		if (is_numeric($arg1)) {
			$this->get_data('id', $arg1);
			return ;
		}
		if (preg_match('/^find/i', $arg1)) {

			$this->find($arg2, $arg3);
			return;
		}

		if (preg_match('/create|new/i', $arg1) and is_array($arg2) ) {

			$this->find($arg2, 'create');
			return;
		}
		$this->get_data($arg1, $arg2, $arg3);



	}


	function get_data($key, $id, $aux_id=false) {

		if ($key=='id') {
			$sql=sprintf("select * from `Product Dimension` where `Product ID`=%d", $id);
			if ($this->data = $this->db->query($sql)->fetch()) {
				$this->id=$this->data['Product ID'];
				$this->historic_id=$this->data['Product Current Key'];
			}
		}elseif ($key=='store_code') {
			$sql=sprintf("select * from `Product Dimension` where `Product Store Key`=%s  and `Product Code`=%s", $id, prepare_mysql($aux_id));
			if ($this->data = $this->db->query($sql)->fetch()) {
				$this->id=$this->data['Product ID'];
				$this->historic_id=$this->data['Product Current Key'];
			}
		}elseif ($key=='historic_key') {
			$sql=sprintf("select * from `Product History Dimension` where `Product Key`=%s", $id);
			if ($this->data = $this->db->query($sql)->fetch()) {
				$this->historic_id=$this->data['Product Key'];
				$this->id=$this->data['Product ID'];


				$sql=sprintf("select * from `Product Dimension` where `Product ID`=%d", $this->data['Product ID']);
				if ($row = $this->db->query($sql)->fetch()) {

					foreach ($row as $key=>$value) {
						$this->data[$key]=$value;
					}
				}



			}
		}
		else {
		    
			sdasdas();
			exit ("wrong id in class.product get_data :$key  \n");
			return;
		}


		$this->get_store_data();
	}


	function get_store_data() {

		$sql=sprintf('select * from `Store Dimension` where `Store Key`=%d ', $this->data['Product Store Key']);
		if ($row = $this->db->query($sql)->fetch()) {

			foreach ($row as $key=>$value) {
				$this->data[$key]=$value;
			}
		}
	}


	function get_webpage() {

		$page_key=0;
		include_once 'class.Page.php';
		$sql=sprintf('select `Page Key` from `Page Store Dimension` where `Page Store Section Type`="Product"  and  `Page Parent Key`=%d ', $this->id);

		if ($result=$this->db->query($sql)) {
			if ($row = $result->fetch()) {
				$page_key=$row['Page Key'];
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}
		$this->webpage=new Page($row['Page Key']);
		$this->webpage->editor=$this->editor;

		

	}


	function get_pages($scope='keys') {

		if ($scope=='objects') {
			include_once 'class.Page.php';
		}

		$sql=sprintf("Select `Page Key` from `Page Store Dimension` where `Page Store Section Type`='Product' and  `Page Parent Key`=%d", $this->id);

		$pages=array();

		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {

				if ($scope=='objects') {
					$pages[$row['Page Key']]=new Page($row['Page Key']);
				}else {
					$pages[$row['Page Key']]=$row['Page Key'];
				}


			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}

		return $pages;



	}


	function get_parts($scope='keys') {


		if ($scope=='objects') {
			include_once 'class.Part.php';
		}

		$sql=sprintf('select `Product Part Part SKU` as `Part SKU` from `Product Part Bridge` where `Product Part Product ID`=%d ', $this->id);

		$parts=array();

		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {

				if ($scope=='objects') {
					$parts[$row['Part SKU']]=new Part($row['Part SKU']);
				}else {
					$parts[$row['Part SKU']]=$row['Part SKU'];
				}


			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}

		return $parts;
	}


	function get_see_also_data() {
		return $this->webpage->get_see_also_data();
	}


	function get_related_products_data() {
		return $this->webpage->get_related_products_data();

	}


	function get_parts_data($with_objects=false) {

		include_once 'class.Part.php';

		$sql=sprintf("select `Product Part Key`,`Product Part Linked Fields`,`Product Part Part SKU`,`Product Part Ratio`,`Product Part Note` from `Product Part Bridge` where `Product Part Product ID`=%d ",
			$this->id
		);
		$parts_data=array();
		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {
				$part_data=$row;

				$part_data=array(
					'Key'=>$row['Product Part Key'],
					'Ratio'=>$row['Product Part Ratio'],
					'Note'=>$row['Product Part Note'],
					'Part SKU'=>$row['Product Part Part SKU'],
				);


				if ($row['Product Part Linked Fields']=='') {
					$part_data['Linked Fields']=array();
					$part_data['Number Linked Fields']=0;
				}else {
					$part_data['Linked Fields']=json_decode($row['Product Part Linked Fields'], true);
					$part_data['Number Linked Fields']=count($part_data['Linked Fields']);
				}
				if ($with_objects) {
					$part_data['Part']=new Part($row['Product Part Part SKU']);
				}


				$parts_data[]=$part_data;
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}

		return $parts_data;
	}


	function get($key, $arg1='') {


		include_once 'utils/natural_language.php';

		list($got, $result)=$this->get_asset_common($key, $arg1);
		if ($got)return $result;

		if (!$this->id)
			return;

		switch ($key) {

		case 'Webpage Related Products':

			$related_products_data=$this->webpage->get_related_products_data();
			$related_products='';


			foreach ($related_products_data['links'] as $link) {
				$related_products.=$link['code'].', ';
			}

			$related_products=preg_replace('/, $/', '', $related_products);

			return $related_products;



			break;
		case 'Webpage See Also':

			$see_also_data=$this->webpage->get_see_also_data();
			$see_also='';
			if ($see_also_data['type']=='Auto') {
				$see_also=_('Automatic').': ';
			}

			if (count($see_also_data['links'])==0) {
				$see_also.=', '._('none');
			}else {
				foreach ($see_also_data['links'] as $link) {
					$see_also.=$link['code'].', ';
				}
			}
			$see_also=preg_replace('/, $/', '', $see_also);

			return $see_also;



			break;

		case 'Product Webpage Name':
		case 'Webpage Name':


			return $this->webpage->get('Page Store Title');

			break;
		case 'Website Node Parent Key':

			return $this->webpage->get('Found In Page Key');

			break;
		case 'Product Website Node Parent Key':

			return $this->webpage->get('Page Found In Page Key');

			break;
		case 'Price':

			$price= money($this->data['Product Price'], $this->data['Store Currency Code']);

			if ($this->data['Product Units Per Case']!=1) {

				$price.=' ('.money($this->data['Product Price']/$this->data['Product Units Per Case'], $this->data['Store Currency Code']).'/'.$this->data['Product Unit Label'].')';


				//$price.=' ('.sprintf(_('%s per %s'), money($this->data['Product Price']/$this->data['Product Units Per Case'], $this->data['Store Currency Code']), $this->data['Product Unit Label']).')';
			}

			$unit_margin=$this->data['Product Price']-$this->data['Product Cost'];
			$price_other_info=sprintf(_('margin %s'), percentage($unit_margin, $this->data['Product Price']));



			$price_other_info=preg_replace('/^, /', '', $price_other_info);
			if ($price_other_info!='') {
				$price.=' <span class="'.($unit_margin<0?'error':'').'  discreet padding_left_10">'.$price_other_info.'</span>';
			}


			return $price;
			break;
		case 'Unit Price':
			return money($this->data['Product Price']/$this->data['Product Units Per Case'], $this->data['Store Currency Code']);
			break;
		case 'Formatted Per Outer':
			return _('per outer');
			break;
		case 'RRP':
			if ($this->data['Product RRP']=='')return '';
			return money($this->data['Product RRP'], $this->data['Store Currency Code']);
			break;
		case 'Unit RRP':

			if ($this->data['Product RRP']=='')return '';

			$rrp= money($this->data['Product RRP']/$this->data['Product Units Per Case'], $this->data['Store Currency Code']);
			if ($this->get('Product Units Per Case')!=1) {
				$rrp.='/'.$this->get('Product Unit Label');
			}



			$unit_margin=$this->data['Product RRP']-$this->data['Product Price'];
			$rrp_other_info=sprintf(_('margin %s'), percentage($unit_margin, $this->data['Product RRP']));



			$rrp_other_info=preg_replace('/^, /', '', $rrp_other_info);
			if ($rrp_other_info!='') {
				$rrp.=' <span class="'.($unit_margin<0?'error':'').'  discreet padding_left_10">'.$rrp_other_info.'</span>';
			}
			return $rrp;
			break;



			return money($this->data['Product RRP']/$this->data['Product Units Per Case'], $this->data['Store Currency Code']);
			break;
		case 'Product Unit RRP':
			return $this->data['Product RRP']/$this->data['Product Units Per Case'];
			break;

		case 'Unit Type':
			if ($this->data['Product Unit Type']=='')return '';
			return _($this->data['Product Unit Type']);

			/*
			if ($this->data['Product Unit Type']=='')return '';
			$unit_type_data=json_decode($this->data['Product Unit Type'], true);
			$unit_type_key=key($unit_type_data);

			$unit_type_value=$unit_type_data[$unit_type_key];
			$unit_type_key=_($unit_type_key);
			if ($unit_type_key!=$unit_type_value) {
				return "$unit_type_value ($unit_type_key)";
			}else {
				return $unit_type_key;
			}
*/
			break;
		case 'Parts':
			$parts='';



			$parts_data=$this->get_parts_data(true);


			foreach ($parts_data as $part_data) {

				$parts.=', '.number($part_data['Ratio']).'x <span class="link" onClick="change_view(\'part/'.$part_data['Part']->id.'\')">'.$part_data['Part']->get('Reference').'</span>';
				//if ($part_data['Note']!='') {
				// $parts.=' <span class="very_discreet">('.$part_data['Note'].')</span>';
				//}

			}

			if ($parts=='') {
				$parts='<span class="discret">'._('No parts assigned').'</span>';
			}
			$parts=preg_replace('/^, /', '', $parts);
			return $parts;

			break;

		case 'Product Price':
			$str = number_format($this->data['Product Price'], 4);

			return preg_replace('/(?<=\d{2})0+$/', '', $str);
			break;
		case 'Price':
			return money($this->data['Product Price'], $this->data['Store Currency Code']);
			break;

		case 'Origin Country Code':
			if ($this->data['Product Origin Country Code']) {
				include_once 'class.Country.php';
				$country=new Country('code', $this->data['Product Origin Country Code']);
				return '<img src="/art/flags/'.strtolower($country->get('Country 2 Alpha Code')).'.gif" title="'.$country->get('Country Code').'"> '._($country->get('Country Name'));
			}else {
				return '';
			}

			break;
		case 'Origin Country':
			if ($this->data['Product Origin Country Code']) {
				include_once 'class.Country.php';
				$country=new Country('code', $this->data['Product Origin Country Code']);
				return $country->get('Country Name');
			}else {
				return '';
			}

			break;



		case 'Status':


			switch ($this->data['Product Status']) {
			case 'Active':
				$status= _('Active');
				break;
			case 'Suspended':
				$status= _('Suspended');
				break;
			case 'Discontinued':
				$status= _('Discontinued');
				break;
			default:
				$status=$this->data['Product Status'];
				break;
			}
			return $status;

			break;

		case 'Web Configuration':


			switch ($this->data['Product Web Configuration']) {
			case 'Online Auto':
				$web_configuration= _('Automatic');
				break;
			case 'Online Force For Sale':
				$web_configuration= _('For sale').' <i class="fa fa-thumb-tack padding_left_5" aria-hidden="true"></i>';
				break;
			case 'Online Force Out of Stock':
				$web_configuration= _('Out of Stock').' <i class="fa fa-thumb-tack padding_left_5" aria-hidden="true"></i>';
				break;
			case 'Offline':
				$web_configuration= _('Offline');
				break;
			default:
				$web_configuration=$this->data['Product Web Configuration'];
				break;
			}
			return $web_configuration;
			break;

		case 'Web State':

			switch ($this->data['Product Web State']) {
			case 'For Sale':
				$web_state= '<span class="'.(($this->get('Product Availability')<=0 and  $this->data['Product Number of Parts']>0  )?'error':'').'">'._('Online').'</span>'.($this->data['Product Web Configuration']=='Online Force For Sale'?' <i class="fa fa-thumb-tack padding_left_5" aria-hidden="true"></i>':'');
				break;
			case 'Out of Stock':
				$web_state= '<span  class="'.(($this->get('Product Availability')>0 and $this->data['Product Number of Parts']>0  ) ?'error':'').'">'._('Out of Stock').'</span>'.($this->data['Product Web Configuration']=='Online Force Out of Stock'?' <i class="fa fa-thumb-tack padding_left_5" aria-hidden="true"></i>':'');
				break;
			case 'Discontinued':
				$web_state= _('Discontinued');
				break;
			case 'Offline':

				if ($this->data['Product Status']!='Active') {
					$web_state= _('Offline');
				}else {

					$web_state= '<span class="'.(($this->get('Product Availability')>0 and $this->data['Product Number of Parts']>0 ) ?'error':'').'">'._('Offline').'</span>'.($this->data['Product Status']=='Active'?' <i class="fa fa-thumb-tack padding_left_5" aria-hidden="true"></i>':'');
				}
				break;
			default:
				$web_state=$this->data['Product Web State'];
				break;
			}
			return $web_state;
			break;


		default:
			if (array_key_exists($key, $this->data))
				return $this->data[$key];

			if (array_key_exists('Product '.$key, $this->data))
				return $this->data['Product '.$key];

		}


	}


	function get_field_label($field) {
		global $account;

		switch ($field) {

		case 'Product ID':
			$label=_('id');
			break;


		case 'Product Cost':
			$label=_('Outer cost');
			break;

		case 'Product Description':
			$label=_('Product description');
			break;
		case 'Product Webpage Name':
			$label=_('Webpage title');
			break;
		case 'Product Code':
			$label=_('code');
			break;
		case 'Product Outer Description':
			$label=_('description');
			break;
		case 'Product Unit Description':
			$label=_('unit description');
			break;
		case 'Product Price':
			$label=_('Outer price');
			break;
		case 'Product Outer Weight':
			$label=_('weight');
			break;
		case 'Product Outer Dimensions':
			$label=_('dimensions');
			break;
		case 'Product Units Per Outer':
			$label=_('retail units per outer');
			break;
		case 'Product Outer Tariff Code':
			$label=_('tariff code');
			break;
		case 'Product Outer Duty Rate':
			$label=_('duty rate');
			break;
		case 'Product Unit Type':
			$label=_('unit type');
			break;
		case 'Product Label in Family':
			$label=_('label in family');
			break;

		case 'Product Unit Weight':
			$label=_('unit weight');
			break;
		case 'Product Unit Dimensions':
			$label=_('unit dimensions');
			break;
		case 'Product Units Per Case':
			$label=_('units per outer');
			break;
		case 'Product Unit Label':
			$label=_('unit label');
			break;
		case 'Product Parts':
			$label=_('parts');
			break;
		case 'Product Name':
			$label=_('unit name');
			break;

		case 'Product Unit RRP':
			$label=_('unit RRP');
			break;

		case 'Product Tariff Code':
			$label=_('tariff code');
			break;

		case 'Product Duty Rate':
			$label=_('duty rate');
			break;

		case 'Product UN Number':
			$label=_('UN number');
			break;

		case 'Product UN Class':
			$label=_('UN class');
			break;
		case 'Product Packing Group':
			$label=_('packing group');
			break;
		case 'Product Proper Shipping Name':
			$label=_('proper shipping name');
			break;
		case 'Product Hazard Indentification Number':
			$label=_('hazard indentification number');
			break;
		case 'Product Materials':
			$label=_('Materials/Ingredients');
			break;
		case 'Product Origin Country Code':
			$label=_('country of origin');
			break;
		case 'Product Units Per Package':
			$label=_('units per SKO');
			break;
		case 'Product Barcode Number':
			$label=_('barcode');
			break;

		default:
			$label=$field;

		}

		return $label;

	}


	function find($raw_data, $options) {



		if (isset($raw_data['editor'])) {
			foreach ($raw_data['editor'] as $key=>$value) {

				if (array_key_exists($key, $this->editor))
					$this->editor[$key]=$value;

			}
		}


		$create='';
		$update='';
		if (preg_match('/create/i', $options)) {
			$create='create';
		}



		$data=$this->base_data();
		foreach ($raw_data as $key=>$value) {
			if (array_key_exists($key, $data)) {
				$data[$key]=_trim($value);
			}
		}


		$sql=sprintf("select `Product ID` from `Product Dimension` where  `Product Store Key`=%s and `Product Code`=%s",
			$data['Product Store Key'],
			prepare_mysql($data['Product Code'])
		);


		if ($result=$this->db->query($sql)) {
			if ($row = $result->fetch()) {
				$this->found=true;
				$this->found_key=$row['Product ID'];
				$this->get_data('id', $this->found_key);
				$this->duplicated_field='Product Code';
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}


		if ($create and !$this->found) {




			$this->create($raw_data);

		}



	}


	function create($data) {

		include_once 'utils/natural_language.php';


		$this->data=$this->base_data();
		foreach ($data as $key=>$value) {
			if (array_key_exists($key, $this->data)) {
				$this->data[$key]=_trim($value);
			}
		}
		$this->editor=$data['editor'];

		if ($this->data['Product Valid From']=='') {
			$this->data['Product Valid From']=gmdate('Y-m-d H:i:s');
		}


		$this->data['Product Code File As']=get_file_as($this->data['Product Code']);




		$keys='';
		$values='';
		foreach ($this->data as $key=>$value) {
			$keys.=",`".$key."`";
			if (in_array($key, array('Product Valid To', 'Product Unit Weight', 'Product Outer Weight', 'Product RRP'))) {
				$values.=','.prepare_mysql($value, true);

			}else {
				$values.=','.prepare_mysql($value, false);
			}
		}
		$values=preg_replace('/^,/', '', $values);
		$keys=preg_replace('/^,/', '', $keys);

		$sql="insert into `Product Dimension` ($keys) values ($values)";



		if ($this->db->exec($sql)) {
			$this->id=$this->db->lastInsertId();
			$this->get_data('id', $this->id);

			$sql=sprintf("insert into  `Product DC Data`  (`Product ID`) values (%d) ", $this->id);
			$this->db->exec($sql);

			$sql=sprintf("insert into  `Product Data`  (`Product ID`) values (%d) ", $this->id);
			$this->db->exec($sql);




			$history_data=array(
				'History Abstract'=>sprintf(_('%s product created'), $this->data['Product Name']),
				'History Details'=>'',
				'Action'=>'created'
			);

			$this->add_subject_history($history_data, true, 'No', 'Changes', $this->get_object_name(), $this->get_main_id());

			$this->new=true;


			$this->update_historic_object();
			$this->get_data('id', $this->id);


		}else {
			$this->error=true;
			$this->msg='Error inserting Product record';
		}



	}



	function update_field_switcher($field, $value, $options='', $metadata='') {
		if (is_string($value))
			$value=_trim($value);



		switch ($field) {
		case 'Webpage See Also':

			$this->webpage->update(array(
					'See Also'=>$value
				), $options);

			$this->updated=$this->webpage->updated;

			break;
		case 'Webpage Related Products':

			$this->webpage->update(array(
					'Related Products'=>$value
				), $options);

			$this->updated=$this->webpage->updated;

			break;
		case 'Product Webpage Name':

			$this->webpage->update(array(
					'Page Store Title'=>$value,
					'Page Short Title'=>$value,
					'Page Title'=>$value
				), $options);

			$this->updated=$this->webpage->updated;

			break;
		case 'Product Website Node Parent Key':

			$this->get_webpage();
			$this->webpage->update(array('Found In'=>array($value)), $options);

			$this->updated=true;

			break;
		case('Product Status'):

			if (! in_array($value, array('Active', 'Suspended' , 'Discontinued', 'Discontinuing'))) {
				$this->error=true;
				$this->msg=_('Invalid status').' ('.$value.')';
				return;
			}





			$this->update_field('Product Status', $value, $options);


			if ($value=='Suspended' or $value=='Discontinued') {
				$this->update_field('Product Valid To', gmdate('Y-m-d H:i:s'), 'no_history');
			}
			if ($value=='Discontinuing') {
				$this->update_field('Product Web Configuration', 'Online Auto', 'no_history');

			}


			$this->update_web_state();


			$this->update_metadata=array(
				'class_html'=>array(
					'Product_Web_State'=>$this->get('Web State'),
				)

			);



			$this->other_fields_updated=array(
				'Product_Web_Configuration'=>array(
					'field'=>'Product_Web_Configuration',
					'render'=>($value=='Active'?true:false),
					'value'=>$this->get('Product Web Configuration'),
					'formatted_value'=>$this->get('Web Configuration'),
				),


			);



			break;

		case('Product Web Configuration'):

			if (! in_array($value, array('Online Force Out of Stock', 'Online Auto', 'Offline', 'Online Force For Sale'))) {
				$this->error=true;
				$this->msg=_('Invalid web configuration').' ('.$value.')';
				return;
			}

			$this->update_field($field, $value, $options);


			if (preg_match('/no_fork/', $options)) {
				$this->update_web_state($use_fork=false);
			}else {
				$this->update_web_state();
			}



			$this->update_metadata=array(
				'class_html'=>array(
					'Product_Web_State'=>$this->get('Web State'),
				)

			);

			break;
		case('Product Tariff Code'):

			if ( !preg_match('/from_part/', $options) and   count($this->get_parts())==1) {


				$part=array_values($this->get_parts('objects'))[0];
				$part->update(array(
						preg_replace('/^Product/', 'Part', $field)=>$value
					), $options);

				$this->get_data('id', $this->id);
				$this->updated=$part->updated;
				return;

			}


			if ($value=='') {
				$tariff_code_valid='';
			}else {
				include_once 'utils/validate_tariff_code.php';
				$tariff_code_valid=validate_tariff_code($value, $this->db);
			}




			$this->update_field($field, $value, $options);

			$this->update_field('Product Tariff Code Valid', $tariff_code_valid, 'no_history');




			break;

		case 'Product Unit Weight':

			if (  $value!=''and (    !is_numeric($value) or $value<0  )) {
				$this->error=true;
				$this->msg=sprintf(_('Invalid weight (%s)'), $value);
				return;
			}

			if ( !preg_match('/from_part/', $options) and   count($this->get_parts())==1) {


				$part=array_values($this->get_parts('objects'))[0];
				$part->update(array(
						preg_replace('/^Product/', 'Part', $field)=>$value
					), $options);

				$this->get_data('id', $this->id);
				$this->updated=$part->updated;
				return;

			}



			$this->update_field($field, $value, $options);

			break;


		case 'Product Unit Dimensions':





			include_once 'utils/parse_natural_language.php';

			$tag=preg_replace('/ Dimensions$/', '', $field);

			if ($value=='') {
				$dim='';
				$vol='';
			}else {
				$dim=parse_dimensions($value);
				if ($dim=='') {
					$this->error=true;
					$this->msg=sprintf(_("Dimensions can't be parsed (%s)"), $value);
					return;
				}
				$_tmp=json_decode($dim, true);
				$vol=$_tmp['vol'];
			}

			if ( !preg_match('/from_part/', $options) and   count($this->get_parts())==1) {


				$part=array_values($this->get_parts('objects'))[0];
				$part->update(array(
						preg_replace('/^Product/', 'Part', $field)=>$value
					), $options);

				$this->get_data('id', $this->id);
				$this->updated=$part->updated;
				return;

			}



			$this->update_field($tag.' Dimensions', $dim, $options);


			break;

		case 'Product Materials':


			if ( !preg_match('/from_part/', $options) and   count($this->get_parts())==1) {


				$part=array_values($this->get_parts('objects'))[0];
				$part->update(array(
						preg_replace('/^Product/', 'Part', $field)=>$value
					), $options);

				$this->get_data('id', $this->id);
				$this->updated=$part->updated;
				return;

			}






			include_once 'utils/parse_materials.php';
			include_once 'class.Material.php';

			$materials_to_update=array();
			$sql=sprintf('select `Material Key` from `Product Material Bridge` where `Product ID`=%d', $this->id);
			if ($result=$this->db->query($sql)) {
				foreach ($result as $row) {
					$materials_to_update[$row['Material Key']]=true;
				}
			}else {
				print_r($error_info=$this->db->errorInfo());
				exit;
			}


			if ($value=='') {
				$materials='';




				$sql=sprintf("delete from `Product Material Bridge` where `Product ID`=%d ", $this->id);
				$this->db->exec($sql);

			}else {

				$materials_data=parse_materials($value, $this->editor);

				$sql=sprintf("delete from `Product Material Bridge` where `Product ID`=%d ", $this->id);

				$this->db->exec($sql);

				foreach ($materials_data as $material_data) {

					if ($material_data['id']>0) {
						$sql=sprintf("insert into `Product Material Bridge` (`Product ID`, `Material Key`, `Ratio`, `May Contain`) values (%d, %d, %s, %s) ",
							$this->id,
							$material_data['id'],
							prepare_mysql($material_data['ratio']),
							prepare_mysql($material_data['may_contain'])

						);
						$this->db->exec($sql);

						if (isset($materials_to_update[$material_data['id']])) {
							$materials_to_update[$material_data['id']]=false;
						}else {
							$materials_to_update[$material_data['id']]=true;
						}

					}


				}


				$materials=json_encode($materials_data);
			}


			foreach ($materials_to_update as  $material_key=>$update) {
				if ($update) {
					$material=new Material($material_key);
					$material->update_stats();

				}
			}


			$this->update_field('Product Materials', $materials, $options);
			$updated=$this->updated;



			$this->updated=$updated;
			break;


		case 'Product Code':
			$value=_trim($value);

			if ($value==''   ) {
				$this->error=true;
				$this->msg=_('Code missing');
				return;
			}

			if (preg_match('/\s/', $value)  ) {
				$this->error=true;
				$this->msg=_("Code can't have spaces");
				return;
			}

			if (preg_match('/\,/', $value)  ) {
				$this->error=true;
				$this->msg=_("Code can't have commas");
				return;
			}

			$sql=sprintf('select count(*) as num from `Product Dimension` where `Product Code`=%s and `Product Store Key`=%d and  `Product Status`!="Discontinued"  and `Product ID`!=%d ',
				prepare_mysql($value),
				$this->get('Product Store Key'),
				$this->id
			);


			if ($result=$this->db->query($sql)) {
				if ($row = $result->fetch()) {
					if ($row['num']>0) {
						$this->error=true;
						$this->msg=sprintf(_("Another product has this code (%s)"), $value);
						return;
					}
				}
			}else {
				print_r($error_info=$this->db->errorInfo());
				exit;
			}




			$this->update_field($field, $value, $options);
			$updated=$this->updated;
			$this->update_historic_object();
			$this->updated=$updated;

			break;

		case 'Product Name':
			if ($value==''   ) {
				$this->error=true;
				$this->msg=_('Unit name missing');
				return;
			}

			$this->update_field($field, $value, $options);
			$updated=$this->updated;
			$this->update_historic_object();
			$this->updated=$updated;

			break;
		case 'Product Unit Label':
			if ($value==''   ) {
				$this->error=true;
				$this->msg=_('Unit label missing');
				return;
			}

			$this->update_field($field, $value, $options);

			$this->other_fields_updated=array(
				'Product_Price'=>array(
					'field'=>'Product_Price',
					'render'=>true,
					'value'=>$this->get('Product Price'),
					'formatted_value'=>$this->get('Price'),
				),
				'Product_Unit_RRP'=>array(
					'field'=>'Product_Unit_RRP',
					'render'=>true,
					'value'=>$this->get('Product Unit RRP'),
					'formatted_value'=>$this->get('Unit RRP'),
				),

			);

			break;
		case 'Product Label in Family':

			$this->update_field('Product Special Characteristic', $value, $options);// Migration

			$this->update_field($field, $value, $options);

			break;


		case 'Product Price':


			if ($value==''   ) {
				$this->error=true;
				$this->msg=_('Price missing');
				return;
			}

			if (  $value!=''and (    !is_numeric($value) or $value<0  )) {
				$this->error=true;
				$this->msg=sprintf(_('Invalid price (%s)'), $value);
				return;
			}



			$this->update_field($field, $value, $options);

			$this->other_fields_updated=array(

				'Product_Unit_RRP'=>array(
					'field'=>'Product_Unit_RRP',
					'render'=>true,
					'value'=>$this->get('Product Unit RRP'),
					'formatted_value'=>$this->get('Unit RRP'),
				),

			);

			$updated=$this->updated;
			$this->update_historic_object();
			$this->updated=$updated;

			break;


		case 'Product Unit RRP':


			if (  $value!=''and (    !is_numeric($value) or $value<0  )) {
				$this->error=true;
				$this->msg=sprintf(_('Invalid unit RRP (%s)'), $value);
				return;
			}

			if ($value=='') {
				$this->update_field('Product RRP', '', $options);

			}else {
				$this->update_field('Product RRP', $value*$this->data['Product Units Per Case'], $options);

			}






			break;

		case 'Product Units Per Case':
			if ($value==''   ) {
				$this->error=true;
				$this->msg=_('Units per outer missing');
				return;
			}

			if (!is_numeric($value) or $value<0  ) {
				$this->error=true;
				$this->msg=sprintf(_('Invalid units per outer (%s)'), $value);
				return;
			}

			$old_value=$this->get('Product Units Per Case');

			$this->update_field('Product Units Per Case', $value, $options);
			$updated=$this->updated;
			if (is_numeric($old_value) and $old_value>0) {
				$rrp_per_unit=$this->get('Product RRP')/$old_value;
				$this->update_field('Product RRP', $rrp_per_unit*$this->get('Product Units Per Case'), $options);

			}




			$this->other_fields_updated=array(
				'Product_Price'=>array(
					'field'=>'Product_Price',
					'render'=>true,
					'value'=>$this->get('Product Price'),
					'formatted_value'=>$this->get('Price'),
				),
				'Product_Unit_RRP'=>array(
					'field'=>'Product_Unit_RRP',
					'render'=>true,
					'value'=>$this->get('Product Unit RRP'),
					'formatted_value'=>$this->get('Unit RRP'),
				),

			);


			$this->update_historic_object();
			$this->updated=$updated;

			break;



		case 'Product Parts':

			$this->update_part_list($value, $options);

			break;
		case 'Product Public':
			if ($value=='Yes' and in_array($this->get('Product Status'), array('Suspended', 'Discontinued')  )) {
				return ;
			}
			$this->update_field($field, $value, $options);
			break;


		case 'Product Family Code':

			if ($value=='') {
				$this->error=true;
				$this->msg=_("Family's code missing");
				return;
			}

			include_once 'class.Category.php';


			$root_category=new Category($this->get('Store Family Category Key'));
			if ($root_category->id) {
				$root_category->editor=$this->editor;
				$family=$root_category->create_category(array('Category Code'=>$value));
				if ($family->id) {

					$this->update_field_switcher('Product Family Category Key', $family->id, $options);


				}else {
					$this->error=true;
					$this->msg=_("Can't create family");
					return;
				}
			}else {
				$this->error=true;
				$this->msg=_("Product families not configured");
				return;
			}


			break;

		case 'Direct Product Family Category Key':

			$this->update_field('Product Family Category Key', $value, 'no_history');

			break;
		case 'Direct Product Department Category Key':

			$this->update_field('Product Department Category Key', $value, 'no_history');

			break;
		case 'Product Family Category Key':


			if ($value) {

				include_once 'class.Category.php';
				$family=new Category($value);
				$family->associate_subject($this->id, false, '', 'skip_direct_update');

				$sql=sprintf("select C.`Category Key` from `Category Dimension` C left join `Category Bridge` B on (C.`Category Key`=B.`Category Key`) where `Category Root Key`=%d and `Subject Key`=%d and `Subject`='Category' and `Category Branch Type`='Head'",

					$this->data['Store Department Category Key'],
					$family->id
				);
				//print $sql;
				$department_key='';
				if ($result=$this->db->query($sql)) {
					if ($row = $result->fetch()) {
						$department_key=$row['Category Key'];
					}
				}else {
					print_r($error_info=$this->db->errorInfo());
					exit;
				}
				$this->update_field('Product Department Category Key', $department_key, 'no_history');


			}else {
				if ($this->data['Product Family Category Key']!='') {


					$category=new Category($this->data['Product Family Category Key']);

					if ($category->id) {
						$category->disassociate_subject($this->id);
					}

				}

			}

			$this->update_field($field, $value, 'no_history');

			$categories='';
			foreach ($this->get_category_data() as $item) {
				$categories.=sprintf('<li><span class="button" onclick="change_view(\'category/%d\')" title="%s">%s</span></li>',
					$item['category_key'],
					$item['label'],
					$item['code']

				);

			}
			$this->update_metadata=array(
				'class_html'=>array(
					'Categories'=>$categories,

				)
			);


			break;
		case 'Product UN Number':
		case 'Product UN Class':
		case 'Product Packing Group':
		case 'Product Proper Shipping Name':
		case 'Product Hazard Indentification Number':
		case('Product Duty Rate'):


			if ( !preg_match('/from_part/', $options) and   count($this->get_parts())==1) {


				$part=array_values($this->get_parts('objects'))[0];
				$part->update(array(
						preg_replace('/^Product/', 'Part', $field)=>$value
					), $options);

				$this->get_data('id', $this->id);
				$this->updated=$part->updated;
				return;

			}


			$this->update_field($field, $value, $options);

			break;
		case 'Product Origin Country Code':


			if ($value=='') {
				$this->error=true;
				$this->msg=_("Country of origin missing");
				return;
			}

			include_once 'class.Country.php';
			$country=new Country('find', $value);
			if ($country->get('Country Code')=='UNK') {
				$this->error=true;
				$this->msg=sprintf(_("Country not found (%s)"), $value);
				return;

			}

			$value=$country->get('Country Code');

			if ( !preg_match('/from_part/', $options) and   count($this->get_parts())==1) {


				$part=array_values($this->get_parts('objects'))[0];
				$part->update(array(
						preg_replace('/^Product/', 'Part', $field)=>$value
					), $options);

				$this->get_data('id', $this->id);
				$this->updated=$part->updated;
				return;

			}


			$this->update_field($field, $value, $options);
			break;
		default:
			$base_data=$this->base_data();
			if (array_key_exists($field, $base_data)) {
				$this->update_field($field, $value, $options);
			}
		}
		$this->reread();

	}


	function update_part_list($value, $options='') {


		$value=json_decode($value, true);




		$part_list=$this->get_parts_data();

		$old_part_list_keys=array();
		foreach ($part_list as $product_part) {
			$old_part_list_keys[$product_part['Key']]=$product_part['Key'];
		}


		$new_part_list_keys=array();
		foreach ($value as $product_part) {
			if (isset($product_part['Key'])) {
				$new_part_list_keys[$product_part['Key']]=$product_part['Key'];
			}
		}

		if (count(array_diff($old_part_list_keys, $new_part_list_keys))!=0) {

			//print_r($old_part_list_keys);
			//print_r($new_part_list_keys);
			$this->error=true;
			$this->msg=_('Another user updated current part list, refresh and try again');
			return;
		}

		foreach ($value as $product_part) {

			//print_r($product_part);
			if (isset($product_part['Key']) and $product_part['Key']>0) {

				$sql=sprintf('update `Product Part Bridge` set `Product Part Note`=%s where `Product Part Key`=%d and `Product Part Product ID`=%d ',
					prepare_mysql($product_part['Note']),
					$product_part['Key'],
					$this->id
				);

				$updt = $this->db->prepare($sql);
				$updt->execute();
				if ($updt->rowCount()) {
					$this->updated=true;
				}


				if ($product_part['Ratio']==0) {
					$sql=sprintf('delete from `Product Part Bridge` where `Product Part Key`=%d and `Product Part Product ID`=%d ',
						$product_part['Key'],
						$this->id
					);

					$updt = $this->db->prepare($sql);
					$updt->execute();
					if ($updt->rowCount()) {
						$this->updated=true;
					}

				}else {

					$sql=sprintf('update `Product Part Bridge` set `Product Part Ratio`=%f where `Product Part Key`=%d and `Product Part Product ID`=%d ',
						$product_part['Ratio'],
						$product_part['Key'],
						$this->id
					);

					$updt = $this->db->prepare($sql);
					$updt->execute();
					if ($updt->rowCount()) {
						$this->updated=true;
					}
				}

			}
			else {

				if ($product_part['Part SKU']>0) {

					$sql=sprintf('insert into `Product Part Bridge` (`Product Part Product ID`,`Product Part Part SKU`,`Product Part Ratio`,`Product Part Note`) values (%d,%d,%f,%s)',
						$this->id,
						$product_part['Part SKU'],
						$product_part['Ratio'],
						prepare_mysql($product_part['Note'], false)
					);
					//print $sql;
					$this->db->exec($sql);
					$this->updated=true;
				}
			}
		}

		$this->get_data('id', $this->id);
		$this->update_part_numbers();

		$this->update_availability();
		$this->update_cost();


	}


    function update_cost(){
        $cost=0;
        
        foreach ($this->get_parts_data($with_objects=true) as $part_data) {
            $cost+=$part_data['Part']->get('Part Cost')*$part_data['Ratio'];
            
        }
        
        $this->update(array('Product Cost'=>$cost),'no_history');
    }


	function update_availability($use_fork=true) {



		if ($this->get('Product Number of Parts')>0) {

			$sql=sprintf(" select `Part Stock State`,`Part Current On Hand Stock`-`Part Current Stock In Process` as stock,`Part Current Stock In Process`,`Part Current On Hand Stock`,`Product Part Ratio` from     `Product Part Bridge` B left join   `Part Dimension` P   on (P.`Part SKU`=B.`Product Part Part SKU`)   where B.`Product Part Product ID`=%d   ",
				$this->id
			);




			$result=mysql_query($sql);
			$stock=99999999999;
			$tipo='Excess';
			$change=false;
			$stock_error=false;



			if ($result=$this->db->query($sql)) {
				foreach ($result as $row) {



					if ($row['Part Stock State']=='Error')
						$tipo='Error';
					elseif ($row['Part Stock State']=='OutofStock' and $tipo!='Error')
						$tipo='OutofStock';
					elseif ($row['Part Stock State']=='VeryLow' and $tipo!='Error' and $tipo!='OutofStock' )
						$tipo='VeryLow';
					else if ($row['Part Stock State']=='Low' and $tipo!='Error' and $tipo!='OutofStock' and $tipo!='VeryLow')
						$tipo='Low';
					elseif ($row['Part Stock State']=='Normal' and $tipo=='Excess' )
						$tipo='Normal';

					if (is_numeric($row['stock']) and is_numeric($row['Product Part Ratio'])  and $row['Product Part Ratio']>0 ) {

						$_part_stock=$row['stock'];
						if ($row['Part Current On Hand Stock']==0  and $row['Part Current Stock In Process']>0 ) {
							$_part_stock=0;
						}

						$_stock=$_part_stock/$row['Product Part Ratio'];
						if ($stock>$_stock) {
							$stock=$_stock;
							$change=true;
						}
					}
					else {

						$stock=0;
						$stock_error=true;
					}


				}
			}else {
				print_r($error_info=$this->db->errorInfo());
				exit;
			}



			if ( $stock<0) {

				$stock=0;

			}elseif (!$change or $stock_error) {
				$stock=0;
			}else if (is_numeric($stock) and $stock<0) {
				$stock=0;
			}
		}
		else {
			$stock=0;
			$tipo='Normal';
		}




		$this->update(array(
				'Product Availability'=>$stock,
				'Product Availability State'=>$tipo,

			), 'no_history');






		$this->update_web_state($use_fork);










		$this->other_fields_updated=array(
			'Product_Availability'=>array(
				'field'=>'Product_Availability',
				'value'=>$this->get('Product Availability'),
				'formatted_value'=>$this->get('Availability'),


			),
			'Product_Web_State'=>array(
				'field'=>'Product_Web_State',
				'value'=>$this->get('Product Web State'),
				'formatted_value'=>$this->get('Web State'),


			)
		);











	}


	function update_web_state($use_fork=true) {




		$old_web_state=$this->get('Product Web State');


		if ($old_web_state=='For Sale')
			$old_web_availability='Yes';
		else
			$old_web_availability='No';

		$web_state=$this->get_web_state();


		$this->update_field('Product Web State', $web_state, 'no_history');



		if ($web_state=='For Sale')
			$web_availability='Yes';
		else
			$web_availability='No';






		$web_availability_updated=($old_web_availability!=$web_availability?true:false);


		if ($web_availability_updated) {


			//print $this->data['Product Store Key'].' '.$this->data['Product Code']." $old_web_availability  $web_availability \n";

			if (isset($this->editor['User Key'])and is_numeric($this->editor['User Key'])  )
				$user_key=$this->editor['User Key'];
			else
				$user_key=0;


			$sql=sprintf("select UNIX_TIMESTAMP(`Date`) as date,`Product Availability Key` from `Product Availability Timeline` where `Product ID`=%d  order by `Date`  desc limit 1",
				$this->id
			);

			if ($result=$this->db->query($sql)) {
				if ($row = $result->fetch()) {
					$last_record_key=$row['Product Availability Key'];
					$last_record_date=$row['date'];
				}else {
					$last_record_key=false;
					$last_record_date=false;
				}
			}else {
				print_r($error_info=$this->db->errorInfo());
				exit;
			}





			$new_date_formated=gmdate('Y-m-d H:i:s');
			$new_date=gmdate('U');

			$sql=sprintf("insert into `Product Availability Timeline`  (`Product ID`,`Store Key`,`Department Key`,`Family Key`,`User Key`,`Date`,`Availability`,`Web State`) values (%d,%d,%d,%d,%d,%s,%s,%s) ",
				$this->id,
				$this->data['Product Store Key'],
				$this->data['Product Main Department Key'],
				$this->data['Product Family Key'],
				$user_key,
				prepare_mysql($new_date_formated),
				prepare_mysql($web_availability),
				prepare_mysql($web_state)

			);
			$this->db->exec($sql);

			if ($last_record_key) {
				$sql=sprintf("update `Product Availability Timeline` set `Duration`=%d where `Product Availability Key`=%d",
					$new_date-$last_record_date,
					$last_record_key

				);
				$this->db->exec($sql);

			}


			if ($web_availability=='Yes') {
				$sql=sprintf("update `Email Site Reminder Dimension` set `Email Site Reminder State`='Ready' where `Email Site Reminder State`='Waiting' and `Trigger Scope`='Back in Stock' and `Trigger Scope Key`=%d ",
					$this->id
				);

			}else {
				$sql=sprintf("update `Email Site Reminder Dimension` set `Email Site Reminder State`='Waiting' where `Email Site Reminder State`='Ready' and `Trigger Scope`='Back in Stock' and `Trigger Scope Key`=%d ",
					$this->id
				);

			}
			$this->db->exec($sql);



		}



		if ($use_fork) {
			include_once 'utils/new_fork.php';
			 $account=new Account($this->db);

			list($fork_key, $msg)=new_fork('au_housekeeping', array('type'=>'update_web_state_slow_forks', 'web_availability_updated'=>$web_availability_updated, 'product_id'=>$this->id), $account->get('Account Code'), $this->db);

		}else {
			$this->update_web_state_slow_forks($web_availability_updated);
		}





	}


	function update_web_state_slow_forks($web_availability_updated) {


		if ($web_availability_updated) {





			include_once 'class.Page.php';
			include_once 'class.Site.php';
			include_once 'class.Order.php';

			$sql=sprintf("select `Page Key` from `Page Product Dimension` where `Product ID`=%d ",
				$this->id);


			if ($result=$this->db->query($sql)) {
				foreach ($result as $row) {

					$page=new Page($row['Page Key']);

            
					$site=new Site($page->get('Page Site Key'));
					if ($site->data['Site SSL']=='Yes') {
						$site_protocol='https';
					}else {
						$site_protocol='http';
					}

					if ($site->id and $site->data['Site URL']!='') {

						 $template_response=file_get_contents($site_protocol.'://'.$site->data['Site URL']."/maintenance/write_templates.php?parent=page_clean_cache&parent_key=".$page->id."&sk=x");

					}


				}
			}else {
				print_r($error_info=$this->db->errorInfo());
				exit;
			}







			$sql=sprintf("select `Order Key` from `Order Transaction Fact` where `Current Dispatching State`='In Process by Customer' and `Product ID`=%d ",
				$this->id
			);


			if ($result=$this->db->query($sql)) {
				foreach ($result as $row) {

					$web_availability=($this->get_web_state()=='For Sale'?'Yes':'No');
					if ($web_availability=='No' ) {
						$order=new Order($row['Order Key']);
						$order->remove_out_of_stocks_from_basket($this->id);
					}
				}
			}else {
				print_r($error_info=$this->db->errorInfo());
				exit;
			}


			$sql=sprintf("select `Order Key` from `Order Transaction Fact` where `Current Dispatching State`='Out of Stock in Basket' and `Product ID`=%d ",
				$this->id
			);

			if ($result=$this->db->query($sql)) {
				foreach ($result as $row) {
					$web_availability=($this->get_web_state()=='For Sale'?'Yes':'No');
					if ($web_availability=='Yes' ) {
						$order=new Order($row['Order Key']);
						$order->restore_back_to_stock_to_basket($this->id);
					}
				}
			}else {
				print_r($error_info=$this->db->errorInfo());
				exit;
			}






		}




		$this->get_data('id', $this->id);


		if ( !($this->get('Product Status')=='Active' or $this->get('Product Status')=='Discontinuing') or $this->get('Product Web Configuration')=='Offline' ) {
			$_state='Offline';
		}else {
			$_state='Online';
		}


		foreach ($this->get_pages('objects') as $page) {
			$page->update(array('Page State'=>$_state), 'no_history');
		}

	}





	function get_web_state() {


		if ( !( $this->data['Product Status']=='Active' or $this->data['Product Status']=='Discontinuing')  or ($this->data['Product Number of Parts']==0) ) {

			return 'Offline';
		}
		switch ($this->data['Product Web Configuration']) {



		case 'Offline':
			return 'Offline';
			break;
		case 'Online Force Out of Stock':
			return 'Out of Stock';
			break;
		case 'Online Force For Sale':
			return 'For Sale';
			break;
		case 'Online Auto':

			if ($this->data['Product Number of Parts']==0) {
				return 'For Sale';
			}else {

				if ($this->data['Product Availability']>0) {
					return 'For Sale';
				}else {
					return 'Out of Stock';
				}
			}
			break;
		default:
			return 'Offline';
			break;
		}

	}





	function get_linked_fields_data() {

		$sql=sprintf("select `Product Part Part SKU`,`Product Part Linked Fields` from `Product Part Bridge` where `Product Part Product ID`=%d", $this->id);

		$linked_fields_data=array();
		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {
				if ($row['Product Part Linked Fields']!='') {
					$linked_fields=json_decode($row['Product Part Linked Fields'], true);

					foreach ($linked_fields as $key=>$value) {
						$value=preg_replace('/\s/', '_', $value);
						$linked_fields_data[$value]=$row['Product Part Part SKU'];
					}

				}
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}

		return $linked_fields_data;

	}


	function create_time_series($date=false) {
		if (!$date) {
			$date=gmdate("Y-m-d");
		}
		$sql=sprintf("select sum(`Invoice Quantity`) as outers,sum(`Invoice Transaction Gross Amount`-`Invoice Transaction Total Discount Amount`) as sales,  sum(`Invoice Currency Exchange Rate`*(`Invoice Transaction Gross Amount`-`Invoice Transaction Total Discount Amount`)) as dc_sales, count(Distinct `Customer Key`) as customers , count(Distinct `Invoice Key`) as invoices from `Order Transaction Fact` where `Product ID`=%d and `Current Dispatching State`='Dispatched' and `Invoice Date`>=%s  and `Invoice Date`<=%s   ",
			$this->id,
			prepare_mysql($date.' 00:00:00'),
			prepare_mysql($date.' 23:59:59')

		);
		$outers=0;
		$sales=0;
		$dc_sales=0;
		$customers=0;
		$invoices=0;


		if ($result=$this->db->query($sql)) {
			if ($row = $result->fetch()) {

				$sales=$row['sales'];
				$dc_sales=$row['dc_sales'];
				$customers=$row['customers'];
				$invoices=$row['invoices'];
				$outers=$row['outers'];




			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}




		if ($invoices!=0 and $customers!=0 and $sales!=0 and $outers!=0) {


			$sql=sprintf("insert into `Order Spanshot Fact`(`Date`, `Product ID`, `Availability`, `Outers Out`, `Sales`, `Sales DC`, `Customers`, `Invoices`) values (%s,%d   ,%f,%f, %.2f,%.2f,  %d,%d) ON DUPLICATE KEY UPDATE `Outers Out`=%f,`Sales`=%.2f,`Sales DC`=%.2f,`Customers`=%d,`Invoices`=%d ",
				prepare_mysql($date),

				$this->id,
				1,
				$outers,
				$sales,
				$dc_sales,
				$customers,
				$invoices,

				$outers,
				$sales,
				$dc_sales,
				$customers,
				$invoices


			);
			$this->db->exec($sql);

			//$this->update_sales_averages();
		}

	}


	function update_historic_object() {

		if (!$this->id)return;

		$old_value=$this->get('Product Current Key');
		$changed=false;



		$desc=$this->get('Product Units Per Case').'x '.$this->get('Product Name').' ('.$this->get('Price').')';

		$sql=sprintf('select `Product Key` from `Product History Dimension` where
		`Product History Code`=%s and `Product History Units Per Case`=%d and `Product History Price`=%.2f and
		`Product History Name`=%s and `Product ID`=%d',

			prepare_mysql($this->data['Product Code']),
			$this->data['Product Units Per Case'],
			$this->data['Product Price'],
			prepare_mysql($this->data['Product Name']),
			$this->id
		);

		//print "$sql\n";

		if ($result=$this->db->query($sql)) {
			if ($row = $result->fetch()) {


				$this->update(array('Product Current Key'=>$row['Product Key']), 'no_history');
				$changed=true;

			}else {




				$sql=sprintf('insert into `Product History Dimension` (`Product ID`,`Product History Code`,`Product History Units Per Case`,
						`Product History Price`, `Product History Name`,`Product History Valid From`,`Product History Short Description`,`Product History XHTML Short Description`,`Product History Special Characteristic`

				) values (%d,%s,%d,%.2f,%s,%s,%s,%s,%s) ',
					$this->id,
					prepare_mysql($this->data['Product Code']),
					$this->data['Product Units Per Case'],
					$this->data['Product Price'],
					prepare_mysql($this->data['Product Name']),
					prepare_mysql(gmdate('Y-m-d H:i:s')),
					prepare_mysql($desc),
					prepare_mysql($desc),
					prepare_mysql($this->get('Product Special Characteristic'))
				);
				//print "$sql\n";
				// exit;
				if ($this->db->exec($sql)) {
					$this->update(array('Product Current Key'=>$this->db->lastInsertId()), 'no_history');
					$changed=true;
				}
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			print $sql;
			exit;
		}




		$change_orders_in_basket=true;
		$change_orders_in_process=false;

		$states_to_change='';
		if ($change_orders_in_basket) {
			$states_to_change="'In Process by Customer','In Process','Out of Stock in Basket',";
		}
		if ($change_orders_in_process) {
			$states_to_change.="'Submitted by Customer','Ready to Pick','Picking','Ready to Pack','Ready to Ship','Packing','Packed','Packed Done','No Picked Due Out of Stock','No Picked Due No Authorised','No Picked Due Not Found','No Picked Due Other'";
		}

		$states_to_change=preg_replace('/\,$/', '', $states_to_change);
		if ($changed and  $old_value>0 and $states_to_change!='' ) {




			include_once 'class.Order.php';

			$orders=array();
			$sql=sprintf("select `Order Key`,`Delivery Note Key`,`Order Quantity`,`Order Transaction Fact Key` from `Order Transaction Fact` OTF  where `Product ID`=%d   and `Product Key`!=%d and  `Current Dispatching State` in (%s) and `Invoice Key` is NULL ",
				$this->id,
				$this->get('Product Current Key'),
				$states_to_change

			);

			if ($result=$this->db->query($sql)) {
				foreach ($result as $row) {




					$sql=sprintf('update `Order Transaction Fact` set  `Product Key`=%d, `Product Code`=%s, `Order Transaction Gross Amount`=%.2f, `Order Transaction Total Discount Amount`=0	, `Order Transaction Amount`=%.2f  where `Order Transaction Fact Key`=%d',
						$this->get('Product Current Key'),
						prepare_mysql($this->get('Product Code')),
						$this->get('Product Price')*$row['Order Quantity'],
						$this->get('Product Price')*$row['Order Quantity'],

						$row['Order Transaction Fact Key']
					);

					//    print "$sql\n";
					$this->db->exec($sql);

					$order=new Order($row['Order Key']);



					$order->update_number_products();
					$order->update_insurance();

					$order->update_discounts_items();
					$order->update_totals();
					$order->update_shipping($row['Delivery Note Key'], false);
					$order->update_charges($row['Delivery Note Key'], false);
					$order->update_discounts_no_items($row['Delivery Note Key']);
					$order->update_deal_bridge();
					$order->update_totals();
					$order->update_number_products();
					$order->apply_payment_from_customer_account();
					$order->update_payment_state();
				}



			}else {
				print_r($error_info=$this->db->errorInfo());
				exit;
			}
		}






	}









	function update_pages_numbers() {

		$number_pages=0;

		$sql=sprintf('select count(Distinct `Page Key`) as num from `Page Product Dimension`  where `Product ID`=%d',
			$this->id
		);

		if ($result=$this->db->query($sql)) {
			if ($row = $result->fetch()) {
				$number_pages=$row['num'];
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}

		$this->update(array(
				'Product Number Web Pages'=>$number_pages,


			), 'no_history');

	}


	function update_part_numbers() {

		$number_parts=0;

		$sql=sprintf('select count(`Product Part Part SKU`) as num from `Product Part Bridge`  where `Product Part Product ID`=%d',
			$this->id
		);

		if ($result=$this->db->query($sql)) {
			if ($row = $result->fetch()) {
				$number_parts=$row['num'];
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}


		$this->update(array(
				'Product Number of Parts'=>$number_parts,


			), 'no_history');



	}


	function update_status_from_parts() {

		$status='Active';



		foreach ($this->get_parts('objects') as $part) {
			if ($part->get('Part Status')=='Discontinuing') {
				$status='Discontinuing';
			}elseif ($part->get('Part Status')=='Not In Use') {
				$status='Discontiued';
				break;
			}


		}


		if ( $status=='Active') {
			if ($this->get('Product Status')=='Discontinuing' ) {
				$this->update(array('Product Status'=>'Active'), 'no_history');

			}
		}elseif ( $status=='Discontinuing') {
			if ($this->get('Product Status')=='Active' ) {
				$this->update(array('Product Status'=>'Discontinuing'), 'no_history');

			}
		}elseif ( $status=='Discontiued') {

			$this->update(array('Product Status'=>'Discontiued'), 'no_history');


		}

		$this->update_availability();


	}


	function get_category_data() {


		$type='Product';

		$sql=sprintf("select B.`Category Key`,`Category Root Key`,`Other Note`,`Category Label`,`Category Code`,`Is Category Field Other` from `Category Bridge` B left join `Category Dimension` C on (C.`Category Key`=B.`Category Key`) where  `Category Branch Type`='Head'  and B.`Subject Key`=%d and B.`Subject`=%s",
			$this->id,
			prepare_mysql($type)
		);

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
				$category_data[]=array(
					'root_label'=>$root_label,
					'root_code'=>$root_code,
					'label'=>$row['Category Label'],
					'code'=>$row['Category Code'],
					'value'=>$value,
					'category_key'=>$row['Category Key']
				);

			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}




		return $category_data;
	}


}




?>
