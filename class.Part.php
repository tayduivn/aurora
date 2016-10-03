<?php
/*
 File: Part.php

 This file contains the Part Class

 About:
 Autor: Raul Perusquia <rulovico@gmail.com>

 Copyright (c) 2009, Inikoo

 Version 2.0
*/

include_once 'class.Asset.php';

class Part extends Asset{



	private $current_locations_loaded=false;
	public $sku=false;
	public $warehouse_key=1;
	public $locale='en_GB';

	function __construct($arg1, $arg2=false, $arg3=false) {

		global $db;
		$this->db=$db;

		$this->table_name='Part';
		$this->ignore_fields=array(
			'Part Key'
		);

		if (is_numeric($arg1) and !$arg2) {
			$this->get_data('id', $arg1);
			return;
		}


		if (preg_match('/^find/i', $arg1)) {

			$this->find($arg2, $arg3);
			return;
		}

		if (preg_match('/^create/i', $arg1)) {
			$this->create($arg2);
			return;
		}


		$this->get_data($arg1, $arg2);

	}




	function get_data($tipo, $tag) {
		if ($tipo=='id' or $tipo=='sku')
			$sql=sprintf("select * from `Part Dimension` where `Part SKU`=%d ", $tag);
		else if ($tipo=='code' or $tipo=='reference')
			$sql=sprintf("select * from `Part Dimension` where `Part Reference`=%s ", prepare_mysql($tag));
		else
			return;

		if ($this->data = $this->db->query($sql)->fetch()) {
			$this->id=$this->data['Part SKU'];
			$this->sku=$this->data['Part SKU'];
		}



	}


	function get_products($scope='keys') {


		if (   $scope=='objects') {
			include_once 'class.Product.php';
		}

		$sql=sprintf('select `Product Part Product ID` from `Product Part Bridge` where `Product Part Part SKU`=%d ', $this->id);

		$products=array();

		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {

				if ($scope=='objects') {
					$products[$row['Product Part Product ID']]=new Product($row['Product Part Product ID']);
				}else {
					$products[$row['Product Part Product ID']]=$row['Product Part Product ID'];
				}


			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}

		return $products;
	}



	function get_supplier_parts($scope='keys') {


		if (   $scope=='objects') {
			include_once 'class.SupplierPart.php';
		}

		$sql=sprintf('select `Supplier Part Key` from `Supplier Part Dimension` where `Supplier Part Part SKU`=%d ', $this->id);

		$supplier_parts=array();

		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {

				if ($scope=='objects') {
					$supplier_parts[$row['Supplier Part Key']]=new SupplierPart($row['Supplier Part Key']);
				}else {
					$supplier_parts[$row['Supplier Part Key']]=$row['Supplier Part Key'];
				}


			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}

		return $supplier_parts;
	}


	function load_acc_data() {
		$sql=sprintf("select * from `Part Data` where `Part SKU`=%d", $this->id);


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


	function find($raw_data, $options) {



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


		$data=$this->base_data();
		foreach ($raw_data as $key=>$value) {
			if (array_key_exists($key, $data)) {
				$data[$key]=_trim($value);
			}
		}



		$sql=sprintf("select `Part SKU` from `Part Dimension` where `Part Reference`=%s", prepare_mysql($data['Part Reference']));


		if ($result=$this->db->query($sql)) {
			if ($row = $result->fetch()) {
				$this->found=true;
				$this->found_key=$row['Part SKU'];
				$this->get_data('id', $this->found_key);
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

		include_once 'class.Account.php';
		include_once 'class.Category.php';

		// print_r($data);
		$account=new Account($this->db);

		if (array_key_exists('Part Family Category Code', $data)) {

			$root_category=new Category($account->get('Account Part Family Category Key'));
			if ($root_category->id) {
				$root_category->editor=$this->editor;
				$family=$root_category->create_category(array('Category Code'=>$data['Part Family Category Code']));
				if ($family->id) {
					$data['Part Family Category Key']=$family->id;
				}
			}
		}

		if (!isset($data['Part Valid From']) or $data['Part Valid From']=='') {
			$data['Part Valid From']=gmdate('Y-m-d H:i:s');
		}
		$base_data=$this->base_data();
		foreach ($data as $key=>$value) {
			if (array_key_exists($key, $base_data)) {
				$base_data[$key]=_trim($value);
			}
		}


		//   $base_data['Part Available']='No';

		//  if ($base_data['Part XHTML Description']=='') {
		//   $base_data['Part XHTML Description']=strip_tags($base_data['Part XHTML Description']);
		//  }

		//print_r($base_data);

		$keys='(';
		$values='values(';
		foreach ($base_data as $key=>$value) {
			$keys.="`$key`,";

			if (in_array($key, array('Part XHTML Next Supplier Shipment', 'Part XHTML Picking Location'))) {
				$values.=prepare_mysql($value, false).",";

			}else {

				$values.=prepare_mysql($value).",";
			}
		}
		$keys=preg_replace('/,$/', ')', $keys);
		$values=preg_replace('/,$/', ')', $values);

		//print_r($base_data);

		$sql=sprintf("insert into `Part Dimension` %s %s", $keys, $values);


		if ($this->db->exec($sql)) {
			$this->id=$this->db->lastInsertId();
			$this->sku =$this->id ;
			$this->new=true;

			$sql="insert into `Part Data` (`Part SKU`) values(".$this->id.");";
			$this->db->exec($sql);


			$this->get_data('id', $this->id);

			$this->update_products_web_status();

			$history_data=array(
				'Action'=>'created',
				'History Abstract'=>_('Part created'),
				'History Details'=>''
			);
			$this->add_subject_history($history_data, true, 'No', 'Changes', $this->get_object_name(), $this->get_main_id());




			if ($this->get('Part Family Category Key')) {
				$family=new Category($this->get('Part Family Category Key'));
				$family->editor=$this->editor;


				if ($family->id) {
					$family->associate_subject($this->id);
				}
			}


		} else {
			print "Error Part can not be created $sql\n";
			$this->msg='Error Part can not be created';
			exit;
		}

	}


	function update_custom_fields($id, $value) {
		$this->update(array($id=>$value));
	}



	function discontinue_trigger() {

		if ($this->get('Part Status')=='Discontinuing' and   ($this->data['Part Current On Hand Stock']<=0 and $this->data['Part Current Stock In Process']==0  ) ) {
			$this->update_status('Not In Use');
			return;
		}
		if ($this->get('Part Status')=='Not In Use' and   ($this->data['Part Current On Hand Stock']>0 or $this->data['Part Current Stock In Process']>0  ) ) {
			$this->update_status('Discontinuing');
			return;
		}if ($this->get('Part Status')=='Not In Use' and   ($this->data['Part Current On Hand Stock']<0  ) ) {


			$this->update_status('Not In Use', '', true);


		}

	}



	function update_status($value, $options='', $force=false) {


		if ($value=='Not In Use' and  ($this->data['Part Current On Hand Stock']-$this->data['Part Current Stock In Process'])>0) {
			$value='Discontinuing';
		}


		if ($value==$this->get('Part Status') and !$force) return;

		$this->update_field('Part Status', $value, $options);

		if ($value=='Discontinuing') {
			$this->discontinue_trigger();

		}
		elseif ($value=='Not In Use') {






			foreach ($this->get_locations('part_location_object') as $part_location) {
				$part_location->disassociate();
			}

			$this->update_stock();


			$this->update(array('Part Valid To'=>gmdate("Y-m-d H:i:s")), 'no_history');


			$this->get_data('sku', $this->sku);




		}





		$this->update_stock_status();
		$this->update_available_forecast();


		include_once 'class.Category.php';
		$sql=sprintf("select `Category Key` from `Category Bridge` where `Subject`='Part' and `Subject Key`=%d", $this->sku);

		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {
				$category=new Category($row['Category Key']);
				$category->update_part_category_status();
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}


		$products=$this->get_products('objects');
		foreach ($products as $product) {

			$product->update_status_from_parts();
		}


	}


	function update_availability_for_products_configuration($value, $options) {

		$this->update_field('Part Available for Products Configuration', $value, $options);
		$new_value=$this->new_value;
		$updated=$this->updated;

		if (preg_match('/dont_update_pages/', $options)) {
			$update_products=false;
		}else {
			$update_products=true;
		}

		$this->update_availability_for_products($update_products);
		$this->new_value=$new_value;
		$this->updated=$updated;

	}



	function update_availability_for_products($update_pages=true) {

		switch ($this->data['Part Available for Products Configuration']) {
		case 'Yes':
		case 'No':
			$this->update_field('Part Available for Products', $this->data['Part Available for Products Configuration']);
			break;
		case 'Automatic':
			if ($this->data['Part Current Stock']>0 and $this->data['Part Status']=='In Use') {
				$this->update_field('Part Available for Products', 'Yes');
			}else {
				$this->update_field('Part Available for Products', 'No');
			}

		}



		if ($this->updated) {


			if (isset($this->editor['User Key'])and is_numeric($this->editor['User Key'])  )
				$user_key=$this->editor['User Key'];
			else
				$user_key=0;

			$sql=sprintf("select UNIX_TIMESTAMP(`Date`) as date,`Part Availability for Products Key` from `Part Availability for Products Timeline` where `Part SKU`=%d and `Warehouse Key`=%d  order by `Date` desc ,`Part Availability for Products Key` desc limit 1",
				$this->sku,
				$this->warehouse_key
			);


			$res=mysql_query($sql);
			if ($row=mysql_fetch_assoc($res)) {
				$last_record_key=$row['Part Availability for Products Key'];
				$last_record_date=$row['date'];
			}else {
				$last_record_key=false;
				$last_record_date=false;
			}

			$new_date_formatted=gmdate('Y-m-d H:i:s');
			$new_date=gmdate('U');

			$sql=sprintf("insert into `Part Availability for Products Timeline`  (`Part SKU`,`User Key`,`Warehouse Key`,`Date`,`Availability for Products`) values (%d,%d,%d,%s,%s) ",
				$this->sku,
				$user_key,
				$this->warehouse_key,
				prepare_mysql($new_date_formatted),
				prepare_mysql($this->data['Part Available for Products'])

			);
			$this->db->exec($sql);

			if ($last_record_key) {
				$sql=sprintf("update `Part Availability for Products Timeline` set `Duration`=%d where `Part Availability for Products Key`=%d",
					$new_date-$last_record_date,
					$last_record_key

				);
				$this->db->exec($sql);

			}


			foreach ($this->get_products('objects') as $product) {
				$product->editor=$this->editor;
				//$product->update_web_state($update_pages);

			}

		}

	}





	function update_field_switcher($field, $value, $options='', $metadata='') {

		if ($this->update_asset_field_switcher($field, $value, $options, $metadata)) {
			return;
		}

		switch ($field) {

		case 'Part Unit Label':


			if ($value==''   ) {
				$this->error=true;
				$this->msg=_('Unit label missing');
				return;
			}

			$this->update_field($field, $value, $options);

			break;

		case 'Part Unit Description':


			if ($value==''   ) {
				$this->error=true;
				$this->msg=_('Unit description missing');
				return;
			}

			$this->update_field($field, $value, $options);


			break;
		case 'Part Package Description':
			if ($value==''   ) {
				$this->error=true;
				$this->msg=_('Outers (SKO) description');
				return;
			}

			$this->update_field($field, $value, $options);

			break;

		case 'Part Reference':

			if ($value=='') {
				$this->error=true;
				$this->msg=sprintf(_('Reference missing'));
				return;
			}

			$sql=sprintf('select count(*) as num from `Part Dimension` where `Part Reference`=%s and `Part SKU`!=%d ',
				prepare_mysql($value),
				$this->id
			);


			if ($result=$this->db->query($sql)) {
				if ($row = $result->fetch()) {
					if ($row['num']>0) {
						$this->error=true;
						$this->msg=sprintf(_('Duplicated reference (%s)'), $value);
						return;
					}
				}
			}else {
				print_r($error_info=$this->db->errorInfo());
				exit;
			}
			$this->update_field($field, $value, $options);
			break;
		case 'Part Unit Price':

			/*
			if ($value==''   ) {
				$this->error=true;
				$this->msg=_('Unit recommended price missing');
				return;
			}
*/
			if (  $value!=''and (    !is_numeric($value) or $value<0  )) {
				$this->error=true;
				$this->msg=sprintf(_('Invalid unit recommended price (%s)'), $value);
				return;
			}



			$this->update_field('Part Unit Price', $value, $options);

			$this->other_fields_updated=array(

				'Part_Unit_RRP'=>array(
					'field'=>'Part_Unit_RRP',
					'render'=>true,
					'value'=>$this->get('Part Unit RRP'),
					'formatted_value'=>$this->get('Unit RRP'),
				),

			);

			break;


		case 'Part Unit RRP':

			/*
			if ($value==''   ) {
				$this->error=true;
				$this->msg=_('Unit recommended price missing');
				return;
			}
*/
			if (  $value!=''and (    !is_numeric($value) or $value<0  )) {
				$this->error=true;
				$this->msg=sprintf(_('Invalid unit recommended RRP (%s)'), $value);
				return;
			}



			$this->update_field('Part Unit RRP', $value, $options);



			break;

		case 'Part Units Per Package':
			if ($value==''   ) {
				$this->error=true;
				$this->msg=_('Units per SKO missing');
				return;
			}

			if (!is_numeric($value) or $value<0  ) {
				$this->error=true;
				$this->msg=sprintf(_('Invalid units per SKO (%s)'), $value);
				return;
			}

			$this->update_field('Part Units Per Package', $value, $options);

			if (!preg_match('/skip_update_historic_object/', $options)) {
				foreach ($this->get_supplier_parts('objects') as $supplier_part) {
					$supplier_part->update_historic_object();
				}
			}


			$this->other_fields_updated=array(
				'Part_Unit_Price'=>array(
					'field'=>'Part_Unit_Price',
					'render'=>true,
					'value'=>$this->get('Part Unit Price'),
					'formatted_value'=>$this->get('Unit Price'),
				),
				'Part_Unit_RRP'=>array(
					'field'=>'Part_Unit_RRP',
					'render'=>true,
					'value'=>$this->get('Part Unit RRP'),
					'formatted_value'=>$this->get('Unit RRP'),
				),

			);

			break;

		case 'Part Family Code':
		case 'Part Family Category Code':
			$account= new Account($this->db);
			if ($value=='') {
				$this->error=true;
				$this->msg=_("Family's code missing");
				return;
			}

			include_once 'class.Category.php';


			$root_category=new Category($account->get('Account Part Family Category Key'));
			if ($root_category->id) {
				$root_category->editor=$this->editor;
				$family=$root_category->create_category(array('Category Code'=>$value));
				if ($family->id) {

					$this->update_field_switcher('Part Family Category Key', $family->id, $options);


				}else {
					$this->error=true;
					$this->msg=_("Can't create family");
					return;
				}
			}else {
				$this->error=true;
				$this->msg=_("Part's families not configured");
				return;
			}


			break;
		case 'Part Family Category Key';

			$account=new Account($this->db);
			include_once 'class.Category.php';





			if ($value!='') {


				$category=new Category($value);
				if ($category->id and $category->get('Category Root Key')==$account->get('Account Part Family Category Key') ) {
					$category->associate_subject($this->id);
				}else {
					$this->error=true;
					$this->msg='wrong category';
					return;

				}

			}else {

				if ($this->data['Part Family Category Key']!='') {


					$category=new Category($this->data['Part Family Category Key']);

					if ($category->id) {
						$category->disassociate_subject($this->id);
					}

				}


			}
			$this->update_field('Part Family Category Key', $value, 'no_history');


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
		case 'Part Materials':
			include_once 'utils/parse_materials.php';


			$materials_to_update=array();
			$sql=sprintf('select `Material Key` from `Part Material Bridge` where `Part SKU`=%d', $this->id);
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




				$sql=sprintf("delete from `Part Material Bridge` where `Part SKU`=%d ", $this->sku);
				$this->db->exec($sql);

			}else {

				$materials_data=parse_materials($value, $this->editor);

				$sql=sprintf("delete from `Part Material Bridge` where `Part SKU`=%d ", $this->sku);

				$this->db->exec($sql);

				foreach ($materials_data as $material_data) {

					if ($material_data['id']>0) {
						$sql=sprintf("insert into `Part Material Bridge` (`Part SKU`, `Material Key`, `Ratio`, `May Contain`) values (%d, %d, %s, %s) ",
							$this->sku,
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


			$this->update_field('Part Materials', $materials, $options);
			$updated=$this->updated;
			//$this->update_linked_products($field, $value, $options, $metadata);


			foreach ($this->get_products('objects') as $product) {

				if ( count($product->get_parts())==1 ) {
					$product->editor=$this->editor;
					$product->update(array('Product Materials'=>$value), $options.' from_part');
				}

			}


			$this->updated=$updated;
			break;

		case 'Part Package Dimensions':
		case 'Part Unit Dimensions':


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

			$this->update_field($tag.' Dimensions', $dim, $options);
			$updated=$this->updated;
			$this->update_field($tag.' Volume', $vol,  'no_history');
			//$this->update_linked_products($field, $value, $options, $metadata);

			if ($field=='Part Unit Dimensions') {
				foreach ($this->get_products('objects') as $product) {

					if ( count($product->get_parts())==1 ) {
						$product->editor=$this->editor;
						$product->update(array('Product Unit Dimensions'=>$value), $options.' from_part');
					}

				}
			}
			$this->updated=$updated;

			break;
		case 'Part Package Weight':
		case 'Part Unit Weight':

			if (  $value!=''and (    !is_numeric($value) or $value<0  )) {
				$this->error=true;
				$this->msg=sprintf(_('Invalid weight (%s)'), $value);
				return;
			}


			$tag=preg_replace('/ Weight$/', '', $field);
			$tag2=preg_replace('/^Part /', '', $tag);
			$tag3=preg_replace('/ /', '_', $tag);

			$this->update_field($field, $value, $options);
			$updated=$this->updated;
			$this->other_fields_updated=array(
				$tag3.'_Dimensions'=>array(
					'field'=>$tag3.'_Dimensions',
					'render'=>true,
					'value'=>$this->get($tag.' Dimensions'),
					'formatted_value'=>$this->get($tag2.' Dimensions'),


				)
			);
			//$this->update_linked_products($field, $value, $options, $metadata);


			if ($field=='Part Package Weight') {

				if ($value!='') {
					$purchase_order_keys=array();
					$sql=sprintf("select `Purchase Order Transaction Fact Key`,`Purchase Order Key`,`Purchase Order Quantity`,`Supplier Part Packages Per Carton` from `Purchase Order Transaction Fact` POTF left join `Supplier Part Dimension` S on (POTF.`Supplier Part Key`=S.`Supplier Part Key`)  where `Supplier Part Part SKU`=%d  and `Purchase Order Weight` is NULL and `Purchase Order Transaction State` in ('InProcess','Submitted')  ",
						$this->id
					);
					//print $sql;
					if ($result=$this->db->query($sql)) {
						foreach ($result as $row) {
							$purchase_order_keys[$row['Purchase Order Key']]=$row['Purchase Order Key'];
							$sql=sprintf('update `Purchase Order Transaction Fact` set  `Purchase Order Weight`=%f where `Purchase Order Transaction Fact Key`=%d',
								$this->get('Part Package Weight')*$row['Supplier Part Packages Per Carton']*$row['Purchase Order Quantity'],
								$row['Purchase Order Transaction Fact Key']
							);
							$this->db->exec($sql);
						}
						include_once 'class.PurchaseOrder.php';
						foreach ($purchase_order_keys as $purchase_order_key) {
							$purchase_order=new PurchaseOrder($purchase_order_key);
							$purchase_order->update_totals();
						}

					}else {
						print_r($error_info=$this->db->errorInfo());
						exit;
					}
				}

			}

			if ($field=='Part Unit Weight') {

				foreach ($this->get_products('objects') as $product) {

					if ( count($product->get_parts())==1 ) {
						$product->editor=$this->editor;
						$product->update(array('Product Unit Weight'=>$this->get('Part Unit Weight')), $options.' from_part');
					}

				}
			}

			$this->updated=$updated;
			break;
		case('Part Tariff Code'):

			if ($value=='') {
				$tariff_code_valid='';
			}else {
				include_once 'utils/validate_tariff_code.php';
				$tariff_code_valid=validate_tariff_code($value, $this->db);
			}




			$this->update_field($field, $value, $options);
			$updated=$this->updated;
			$this->update_field('Part Tariff Code Valid', $tariff_code_valid, 'no_history');



			foreach ($this->get_products('objects') as $product) {

				if ( count($product->get_parts())==1 ) {
					$product->editor=$this->editor;
					$product->update(array('Product Tariff Code'=>$this->get('Part Tariff Code')), $options.' from_part');
				}

			}

			//$this->update_linked_products($field, $value, $options, $metadata);
			$this->updated=$updated;

			break;
		case 'Part UN Number':
		case 'Part UN Class':
		case 'Part Packing Group':
		case 'Part Proper Shipping Name':
		case 'Part Hazard Indentification Number':
		case('Part Duty Rate'):
			$this->update_field($field, $value, $options);
			$updated=$this->updated;
			//$this->update_linked_products($field, $value, $options, $metadata);



			foreach ($this->get_products('objects') as $product) {

				if ( count($product->get_parts())==1 ) {
					$product->editor=$this->editor;

					$product_field=preg_replace('/^Part /', 'Product ', $field);

					$product->update(array($product_field=>$this->get($field)), $options.' from_part');
				}

			}



			$this->updated=$updated;
			break;

		case 'Part Origin Country Code':


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


			$this->update_field($field, $country->get('Country Code'), $options);
			$updated=$this->updated;

			foreach ($this->get_products('objects') as $product) {

				if ( count($product->get_parts())==1 ) {
					$product->editor=$this->editor;

					$product_field=preg_replace('/^Part /', 'Product ', $field);

					$product->update(array($product_field=>$this->get($field)), $options.' from_part');
				}

			}



			$this->updated=$updated;
			break;

		case('Part Status'):




			if (! in_array($value, array('In Use', 'Not In Use' , 'Discontinuing', 'In Process'))) {
				$this->error=true;
				$this->msg=_('Invalid part status').' ('.$value.')';
				return;
			}

			if ($this->get('Part Status')=='In Process'  and $value!='In Process'  and !( $this->get('Part Stock In Hand')>0  and $this->get('Part Main Image Key')>0  )   ) {

				$this->error=true;
				$this->msg=_("Part status can't be modified").' ('.$value.')';
				return;

			}


			if ($value=='Not In Use') {
				if ($this->get('Part Stock In Hand')>0) {
					$value='Discontinuing';
				}elseif ($this->get('Part Stock In Hand')<0) {

				}
			}


			$this->update_status($value, $options);
			break;
		case('Part Available for Products Configuration'):
			$this->update_availability_for_products_configuration($value, $options);
			break;


		case 'Part Next Set Supplier Shipment':
			$this->update_set_next_supplier_shipment($value, $options);
			break;
		default:
			$base_data=$this->base_data();

			//print_r( $this->base_data('Part Data'));
			//print "$field\n";
			if (array_key_exists($field, $base_data)) {

				if ($value!=$this->data[$field]) {

					if ($field=='Part General Description' or $field=='Part Health And Safety')
						$options.=' nohistory';
					$this->update_field($field, $value, $options);




				}
			}




			elseif (array_key_exists($field, $this->base_data('Part Data'))   ) {
				$this->update_table_field($field, $value, $options, 'Part Data', 'Part Data', $this->id);
			}


			elseif (preg_match('/^custom_field_part/i', $field)) {
				$this->update_field($field, $value, $options);
			}

		}




	}


	function update_on_demand() {

		$on_demand_available='No';

		foreach ($this->get_supplier_parts('objects')as $supplier_part) {


			if ($supplier_part->get('Supplier Part On Demand')=='Yes' and $supplier_part->get('Supplier Part Status')=='Available' ) {
				$on_demand_available='Yes';
				break;
			}


		}


		$this->update_field('Part On Demand', $on_demand_available, 'no_history');

		foreach ($this->get_products('objects') as $product) {
			$product->update_availability();
		}




	}


	function update_cost() {

		$account=new Account($this->db);

		$supplier_parts=$this->get_supplier_parts('objects');

		$cost_available=array();
		$cost_no_available=array();
		$cost_discontinued=array();



		foreach ($supplier_parts as $supplier_part) {


			if ($supplier_part->get('Supplier Part Currency Code')!= $account->get('Account Currency')) {
				include_once 'utils/currency_functions.php';
				$exchange=currency_conversion($this->db, $supplier_part->get('Supplier Part Currency Code'), $account->get('Account Currency'), '- 15 minutes');

			}else {
				$exchange=1;
			}

			$_cost=$exchange*($supplier_part->get('Supplier Part Unit Cost')+$supplier_part->get('Supplier Part Unit Extra Cost'));




			if ($supplier_part->get('Supplier Part Status')=='Available') {
				$cost_available[]=$_cost;

			}elseif ($supplier_part->get('Supplier Part Status')=='NoAvailable') {
				$cost_no_available[]=$_cost;

			}elseif ($supplier_part->get('Supplier Part Status')=='Discontinued') {
				$cost_discontinued[]=$_cost;

			}


		}


		$cost=0;
		$cost_set=false;




		if (count($cost_available)>0) {

			$cost=array_sum($cost_available) / count($cost_available);
			$cost_set=true;
		}

		if ( !$cost_set and count($cost_no_available)>0) {
			$cost=array_sum($cost_no_available) / count($cost_no_available);
			$cost_set=true;
		}

		if ( !$cost_set and count($cost_discontinued)>0) {
			$cost=array_sum($cost_discontinued) / count($cost_discontinued);
			$cost_set=true;
		}



		if ($cost_set) {
			$cost=$cost*$this->data['Part Units Per Package'];
		}

		$this->update_field('Part Cost', $cost, 'no_history');

		//print $this->get('Referece')." $cost\n";

		foreach ($this->get_products('objects') as $product) {
			$product->update_cost();
		}


	}


	function update_weight_dimensions_data($field, $value, $type) {

		include_once 'utils/units_functions.php';

		//print "$field $value |";

		$this->update_field($field, $value);
		$_new_value=$this->new_value;
		$_updated=$this->updated;

		$this->updated=true;
		$this->new_value=$value;
		if ($this->updated) {

			if (preg_match('/Package/i', $field)) {
				$tag='Package';
			}else {
				$tag='Unit';
			}
			if ($field!='Part '.$tag.' '.$type.' Display Units') {
				$value_in_standard_units=convert_units($value, $this->data['Part '.$tag.' '.$type.' Display Units'], ($type=='Dimensions'?'m':'Kg'));



				$this->update_field(preg_replace('/\sDisplay$/', '', $field), $value_in_standard_units, 'nohistory');
			}elseif ($field=='Part '.$tag.' Dimensions Display Units') {

				$width_in_standard_units=convert_units($this->data['Part '.$tag.' Dimensions Width Display'], $value, 'm');
				$depth_in_standard_units=convert_units($this->data['Part '.$tag.' Dimensions Depth Display'], $value, 'm');
				$length_in_standard_units=convert_units($this->data['Part '.$tag.' Dimensions Length Display'], $value, 'm');
				$diameter_in_standard_units=convert_units($this->data['Part '.$tag.' Dimensions Diameter Display'], $value, 'm');


				$this->update_field('Part '.$tag.' Dimensions Width', $width_in_standard_units, 'nohistory');
				$this->update_field('Part '.$tag.' Dimensions Depth', $depth_in_standard_units, 'nohistory');
				$this->update_field('Part '.$tag.' Dimensions Length', $length_in_standard_units, 'nohistory');
				$this->update_field('Part '.$tag.' Dimensions Diameter', $diameter_in_standard_units, 'nohistory');



			}

			//print "x".$this->updated."<<";



			//print "x".$this->updated."< $type <";
			if ($type=='Dimensions') {
				include_once 'utils/geometry_functions.php';
				$volume=get_volume($this->data["Part $tag Dimensions Type"], $this->data["Part $tag Dimensions Width"], $this->data["Part $tag Dimensions Depth"], $this->data["Part $tag Dimensions Length"], $this->data["Part $tag Dimensions Diameter"]);

				//print "*** $volume $volume";
				if (is_numeric($volume) and $volume>0) {

					$this->update_field('Part '.$tag.' Dimensions Volume', $volume, 'nohistory');
				}
				$this->update_field('Part '.$tag.' XHTML Dimensions', $this->get_xhtml_dimensions($tag), 'nohistory');

			}else {
				$this->update_field('Part '.$tag.' Weight', convert_units($this->data['Part '.$tag.' Weight Display'], $this->data['Part '.$tag.' '.$type.' Display Units'], 'Kg'), 'nohistory');

			}





			$this->updated=$_updated;
			$this->new_value=$_new_value;
		}
	}








	function update_products_web_status() {

		$products=0;
		$products_web_status='';
		//'Offline','No Products','Online','Out of Stock'

		foreach ($this->get_products('objects') as $product) {
			if (!($product->get('Product Status')=='Discontinued' or $product->get('Product Web State')=='Discontinued')) {

				//'For Sale','Out of Stock','Discontinued','Offline'

				if ($product->get('Product Web State')=='For Sale') {
					$products_web_status='Online';
					break;
				}elseif ($product->get('Product Web State')=='Out of Stock') {
					$products_web_status='Out of Stock';
				}elseif ($product->get('Product Web State')=='Offline') {

					if ($products_web_status=='') {
						$products_web_status='Offline';
					}

				}



				$products++;
			}
		}

		if ($products_web_status=='') {
			$products_web_status='No Products';
		}

		//print $this->get('Reference').' '.$products_web_status."\n";

		$this->update(array(
				'Part Products Web Status'=>$products_web_status
			), 'no_history');



	}










	function get_period($period, $key) {
		return $this->get($period.' '.$key);
	}


	function get($key='', $args=false) {

		$account=new Account($this->db);

		list($got, $result)=$this->get_asset_common($key, $args);
		if ($got)return $result;

		if (!$this->id)
			return;


		switch ($key) {
		case 'Stock Status Icon':

			if ($this->data['Part Status']=='In Process') {
				return '';
			}

			switch ($this->data[$this->table_name.' Stock Status']) {
			case 'Surplus':
				$stock_status='<i class="fa  fa-plus-circle fa-fw" aria-hidden="true" title="'._('Surplus stock').'"></i>';
				break;
			case 'Optimal':
				$stock_status='<i class="fa fa-check-circle fa-fw" aria-hidden="true" title="'._('Optimal stock').'"></i>';
				break;
			case 'Low':
				$stock_status='<i class="fa fa-minus-circle fa-fw" aria-hidden="true" title="'._('Low stock').'"></i>';
				break;
			case 'Critical':
				$stock_status='<i class="fa error fa-minus-circle fa-fw" aria-hidden="true"  title="'._('Critical stock').'"></i>';
				break;
			case 'Out_Of_Stock':
				$stock_status='<i class="fa error fa-ban fa-fw" aria-hidden="true"  title="'._('Out of stock').'"></i>';
				break;
			case 'Error':
				$stock_status='<i class="fa fa-question-circle fa-fw" aria-hidden="true"  title="'._('Error').'"></i>';
				break;
			default:
				$stock_status=$this->data[$this->table_name.' Stock Status'];
				break;
			}
			return  $stock_status;
			break;
		case 'Part Family Category Code':

			if ($this->data['Part Family Category Key']=='')return '';

			include_once 'class.Category.php';

			$category=new Category($this->data['Part Family Category Key']);

			if ($category->id) {
				return $category->get('Code');
			}else {
				return '';
			}


			break;
		case 'Products Web Status':

			if ($this->data['Part Status']=='Not In Use') {

				if ($this->data['Part Products Web Status']=='Online') {
					return '<i class="fa fa-exclamation-circle error" aria-hidden="true"></i> '._('Online');
				}elseif ($this->data['Part Products Web Status']=='Out of Stock') {
					return '<i class="fa fa-exclamation-circle warning" aria-hidden="true"></i> '._('Out of stock');
				}



			}else {


				if ($this->data['Part Products Web Status']=='Offline') {
					return '<span class="warning"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '._('Offline').'</span>';
				}elseif ($this->data['Part Products Web Status']=='No Products') {
					return _('No products associated');
				}elseif ($this->data['Part Products Web Status']=='Online') {

					if ($this->data['Part Stock Status']=='Out_Of_Stock' or $this->data['Part Stock Status']=='Error' ) {
						return '<span class="error"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '._('Online').'</span>';

					}else {

						return _('Online');
					}
				}elseif ($this->data['Part Products Web Status']=='Out of Stock') {
					return _('Out of stock');
				}else {
					return $this->data['Part Products Web Status'];
				}
			}

			break;
		case 'Status':
			if ($this->data['Part Status']=='In Use') {
				return _('Active');
			}elseif ($this->data['Part Status']=='Discontinuing') {
				return _('Discontinuing');
			}elseif ($this->data['Part Status']=='Not In Use') {
				return _('Discontinued');
			}elseif ($this->data['Part Status']=='In Process') {
				return _('In process');
			}else {
				return $this->data['Part Status'];
			}
			break;
		case 'Unit Price':
			if ($this->data['Part Unit Price']=='')return '';
			include_once 'utils/natural_language.php';
			$unit_price= money($this->data['Part Unit Price'], $account->get('Account Currency'));

			$price_other_info='';
			if ($this->data['Part Units Per Package']!=1 and  is_numeric($this->data['Part Units Per Package'])) {
				$price_other_info='('.money($this->data['Part Unit Price']*$this->data['Part Units Per Package'], $account->get('Account Currency')).' '._('per SKO').'), ';
			}


			if ($this->data['Part Units Per Package']!=0 and  is_numeric($this->data['Part Units Per Package'])) {

				$unit_margin=$this->data['Part Unit Price']-($this->data['Part Cost']/$this->data['Part Units Per Package']);

				$price_other_info.=sprintf('<span class="'.($unit_margin<0?'error':'').'">'._('margin %s').'</span>', percentage($unit_margin, $this->data['Part Unit Price']));
			}

			$price_other_info=preg_replace('/^, /', '', $price_other_info);
			if ($price_other_info!='') {
				$unit_price.=' <span class="discreet">'.$price_other_info.'</span>';
			}

			return $unit_price;
			break;
		case 'Unit RRP':
			if ($this->data['Part Unit RRP']=='')return '';

			include_once 'utils/natural_language.php';
			$rrp= money($this->data['Part Unit RRP'], $account->get('Account Currency'));


			$unit_margin=$this->data['Part Unit RRP']-$this->data['Part Unit Price'];
			$rrp_other_info=sprintf(_('margin %s'), percentage($unit_margin, $this->data['Part Unit RRP']));



			$rrp_other_info=preg_replace('/^, /', '', $rrp_other_info);
			if ($rrp_other_info!='') {
				$rrp.=' <span class="'.($unit_margin<0?'error':'').'  discreet">'.$rrp_other_info.'</span>';
			}
			return $rrp;
			break;
		case 'Barcode':

			if ($this->get('Part Barcode Number')=='')return '';

			return '<i '.
				($this->get('Part Barcode Key')?
				'class="fa fa-barcode button" onClick="change_view(\'inventory/barcode/'.$this->get('Part Barcode Key').'\')"':'class="fa fa-barcode"').
				' ></i><span class="Part_Barcode_Number ">'.$this->get('Part Barcode Number').'</span>';

			break;

		case 'Available Forecast':

			$available_forecast='';

			if ($this->data['Part Stock Status']=='Out_Of_Stock' or  $this->data['Part Stock Status']=='Error') return '';

			if (in_array($this->data['Part Products Web Status'], array('No Products', 'Offline', 'Out of Stock'))) return '';


			include_once 'utils/natural_language.php';

			if ($this->data['Part On Demand']=='Yes') {
				$available_forecast= '<span >'.sprintf(_('%s in stock'), '<span  title="'.sprintf("%s %s", number($this->data['Part Days Available Forecast'], 1) ,
						ngettext("day", "days", intval($this->data['Part Days Available Forecast'] ) )).'">'.seconds_to_until($this->data['Part Days Available Forecast']*86400).'</span>').'</span>';


				$available_forecast.=' <i class="fa fa-fighter-jet padding_left_5" aria-hidden="true" title="'._('On demand').'"></i>';
			}else {
				$available_forecast= '<span >'.sprintf(_('%s availability'), '<span  title="'.sprintf("%s %s", number($this->data['Part Days Available Forecast'], 1) ,
						ngettext("day", "days", intval($this->data['Part Days Available Forecast'] ) )).'">'.seconds_to_until($this->data['Part Days Available Forecast']*86400).'</span>').'</span>';


				$available_forecast.=' <i class="fa fa-fighter-jet padding_left_5" aria-hidden="true"></i>';
			}




			return $available_forecast;
			break;

		case 'Origin Country Code':
			if ($this->data['Part Origin Country Code']) {
				include_once 'class.Country.php';
				$country=new Country('code', $this->data['Part Origin Country Code']);
				return '<img src="/art/flags/'.strtolower($country->get('Country 2 Alpha Code')).'.gif" title="'.$country->get('Country Code').'"> '._($country->get('Country Name'));
			}else {
				return '';
			}

			break;
		case 'Origin Country':
			if ($this->data['Part Origin Country Code']) {
				include_once 'class.Country.php';
				$country=new Country('code', $this->data['Part Origin Country Code']);
				return $country->get('Country Name');
			}else {
				return '';
			}

			break;



		case 'Next Supplier Shipment':
			if ($this->data['Part Next Supplier Shipment']=='') {
				return '';
			}else {
				return strftime("%a, %e %b %y", strtotime($this->data['Part Next Supplier Shipment'].' +0:00'));
			}
			break;

		case('Current Stock Available'):

			return number($this->data['Part Current On Hand Stock']-$this->data['Part Current Stock In Process'], 6);

		case('Cost'):
			global $corporate_currency;
			return money($this->data['Part Current Stock Cost Per Unit'], $corporate_currency);


			break;

		case('Current On Hand Stock'):
		case('Current Stock'):
		case ('Current Stock Picked'):
		case ('Current Stock In Process'):
			return number($this->data['Part '.$key], 6);


			break;


		case('Valid From'):
		case('Valid From Datetime'):

			return strftime("%a %e %b %Y %H:%M %Z", strtotime($this->data['Part Valid From']+' 0:00'));
			break;
		case('Valid To'):
			return strftime("%a %e %b %Y %H:%M %Z", strtotime($this->data['Part Valid To']+' 0:00'));
			break;
		default:

			if (preg_match('/No Supplied$/', $key)) {

				$_key=preg_replace('/ No Supplied$/', '', $key);
				if (preg_match('/^Part /', $key)) {
					return $this->data["$_key Required"]-$this->data["$_key Provided"];

				} else {
					return number($this->data["Part $_key Required"]-$this->data["Part $_key Provided"]);
				}

			}


			if (preg_match('/^(Last|Yesterday|Total|1|10|6|3|Year To|Quarter To|Month To|Today|Week To).*(Amount|Profit)$/', $key)) {

				$amount='Part '.$key;

				return money($this->data[$amount], $account->get('Account Currency'));
			}
			if (preg_match('/^(Last|Yesterday|Total|1|10|6|3|4|2|Year To|Quarter To|Month To|Today|Week To).*(Amount|Profit) Minify$/', $key)) {

				$field='Part '.preg_replace('/ Minify$/', '', $key);

				$suffix='';
				$fraction_digits='NO_FRACTION_DIGITS';
				if ($this->data[$field]>=10000) {
					$suffix='K';
					$_amount=$this->data[$field]/1000;
				}elseif ($this->data[$field]>100 ) {
					$fraction_digits='SINGLE_FRACTION_DIGITS';
					$suffix='K';
					$_amount=$this->data[$field]/1000;
				}else {
					$_amount=$this->data[$field];
				}

				$amount= money($_amount, $account->get('Account Currency'), $locale=false, $fraction_digits).$suffix;

				return $amount;
			}
			if (preg_match('/^(Last|Yesterday|Total|1|10|6|3|4|2|Year To|Quarter To|Month To|Today|Week To).*(Amount|Profit) Soft Minify$/', $key)) {

				$field='Part '.preg_replace('/ Soft Minify$/', '', $key);


				$suffix='';
				$fraction_digits='NO_FRACTION_DIGITS';
				$_amount=$this->data[$field];

				$amount= money($_amount, $account->get('Account Currency'), $locale=false, $fraction_digits).$suffix;

				return $amount;
			}

			if (preg_match('/^(Last|Yesterday|Total|1|10|6|3|Year To|Quarter To|Month To|Today|Week To).*(Margin|GMROI)$/', $key)) {

				$amount='Part '.$key;

				return percentage($this->data[$amount], 1);
			}
			if (preg_match('/^(Last|Yesterday|Total|1|10|6|3|Year To|Quarter To|Month To|Today|Week To).*(Given|Lost|Required|Sold|Dispatched|Broken|Acquired)$/', $key) or $key=='Current Stock'  ) {

				$amount='Part '.$key;

				return number($this->data[$amount]);
			}
			if (preg_match('/^(Last|Yesterday|Total|1|10|6|3|2|4|Year To|Quarter To|Month To|Today|Week To).*(Given|Lost|Required|Sold|Dispatched|Broken|Acquired) Minify$/', $key) or $key=='Current Stock'  ) {

				$field='Part '.preg_replace('/ Minify$/', '', $key);

				$suffix='';
				$fraction_digits=0;
				if ($this->data[$field]>=10000) {
					$suffix='K';
					$_number=$this->data[$field]/1000;
				}elseif ($this->data[$field]>100 ) {
					$fraction_digits=1;
					$suffix='K';
					$_number=$this->data[$field]/1000;
				}else {
					$_number=$this->data[$field];
				}

				return number($_number, $fraction_digits).$suffix;
			}
			if (preg_match('/^(Last|Yesterday|Total|1|10|6|3|2|4|Year To|Quarter To|Month To|Today|Week To).*(Given|Lost|Required|Sold|Dispatched|Broken|Acquired) Soft Minify$/', $key) or $key=='Current Stock'  ) {

				$field='Part '.preg_replace('/ Soft Minify$/', '', $key);

				$suffix='';
				$fraction_digits=0;
				$_number=$this->data[$field];

				return number($_number, $fraction_digits).$suffix;
			}


			if (array_key_exists($key, $this->data))
				return $this->data[$key];

			if (array_key_exists('Part '.$key, $this->data))
				return $this->data['Part '.$key];

		}

		return false;
	}


	function get_unit($number) {
		//'10','25','100','200','bag','ball','box','doz','dwt','ea','foot','gram','gross','hank','kilo','ib','m','oz','ozt','pair','pkg','set','skein','spool','strand','ten','tube','vial','yd'
		switch ($this->data['Part Unit']) {
		case 'bag':
			$unit=ngettext('bag', 'bags', $number);
			break;
		case 'box':
			$unit=ngettext('box', 'boxes', $number);

			break;
		case 'doz':
			$unit=ngettext('dozen', 'dozens', $number);

			break;
		case 'ea':
			$unit=ngettext('unit', 'units', $number);

			break;
		default:
			$unit=$this->data['Part Unit'];
			break;
		}
		return $unit;
	}


	function get_current_stock_old() {
		$stock=0;
		$value=0;
		$in_process=0;

		/*

			$sql=sprintf("select sum(`Quantity On Hand`) as stock , sum(`Quantity In Process`) as in_process , sum(`Stock Value`) as value from `Part Location Dimension` where `Part SKU`=%d ",$this->id);
			$res=mysql_query($sql);
			//print $sql;
			if ($row=mysql_fetch_array($res)) {
				$stock=round($row['stock'],3);
				$in_process=round($row['in_process'],3);
				$value=$row['value'];

			}
*/

		$sql=sprintf("select sum(`Inventory Transaction Quantity`) as stock , sum(`Inventory Transaction Amount`) as value
																																																								from `Inventory Transaction Fact` where `Part SKU`=%d ",
			$this->sku
		);

		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {

			$stock = ceil($row['stock'] * 10000) / 10000; // 6.26
			if ($stock==-0)$stock=0;
			//$stock=round($row['stock'], 4);
			$value=$row['value'];
		}


		return array($stock, $value, $in_process);

	}


	function get_current_stock() {
		$stock=0;
		$value=0;
		$in_process=0;



		$sql=sprintf("select sum(`Quantity On Hand`) as stock , sum(`Quantity In Process`) as in_process , sum(`Stock Value`) as value from `Part Location Dimension` where `Part SKU`=%d ", $this->id);
		$res=mysql_query($sql);
		//print $sql;
		if ($row=mysql_fetch_array($res)) {
			$stock=round($row['stock'], 3);
			$in_process=round($row['in_process'], 3);
			$value=$row['value'];

		}




		return array($stock, $value, $in_process);

	}


	function get_stock($date) {
		$stock=0;
		$value=0;
		$sql=sprintf("select ifnull(sum(`Quantity On Hand`), 0) as stock, ifnull(sum(`Value At Cost`), 0) as value from `Inventory Spanshot Fact` where `Part SKU`=%d and `Date`=%s"
			, $this->id, prepare_mysql($date));
		$res=mysql_query($sql);

		if ($row=mysql_fetch_array($res)) {
			$stock=$row['stock'];
			$value=$row['value'];
		}
		return array($stock, $value);
	}





	function update_stock_status() {

		if ($this->data['Part Current Stock']<0) {
			$stock_state='Error';
		}elseif ($this->data['Part Current Stock']==0) {
			$stock_state='Out_of_Stock';
		}elseif ($this->data['Part Days Available Forecast']<=$this->data['Part Delivery Days']) {
			$stock_state='Critical';
		}elseif ($this->data['Part Days Available Forecast']<=$this->data['Part Delivery Days']+7) {
			$stock_state='Low';
		}elseif ($this->data['Part Days Available Forecast']>=$this->data['Part Excess Availability Days Limit']) {
			$stock_state='Surplus';
		}else {
			$stock_state='Optimal';
		}
		$this->data['Part Stock State']=$stock_state;

		$sql=sprintf("update `Part Dimension`  set `Part Stock Status`=%s where  `Part SKU`=%d   ",
			prepare_mysql($this->data['Part Stock State']),
			$this->id
		);

		$this->db->exec($sql);


		/*
		$products=$this->get_current_product_ids();

		foreach ($products as  $product_id=>$values) {
			$product=new Product('id', $product_id);
			if ($product->id) {
				$product->update_availability();
			}
		}
*/

	}


	function update_stock() {


		$picked=0;
		$required=0;


		$sql=sprintf("select sum(`Picked`) as picked, sum(`Required`) as required from `Inventory Transaction Fact` where `Part SKU`=%d and `Inventory Transaction Type`='Order In Process'"
			, $this->id
		);


		if ($result=$this->db->query($sql)) {
			if ($row = $result->fetch()) {
				$picked=round($row['picked'], 3);
				$required=round($row['required'], 3);
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}




		list($stock, $value, $in_process)=$this->get_current_stock();
		//print $stock;
		$this->data['Part Current Stock']=$stock+$picked;
		$this->data['Part Current Value']=$value;
		$this->data['Part Current Stock In Process']=$required-$picked;
		$this->data['Part Current Stock Picked']=$picked;
		$this->data['Part Current On Hand Stock']=$stock;



		$sql=sprintf("update `Part Dimension`  set `Part Current Stock`=%f , `Part Current Value`=%f, `Part Current Stock In Process`=%f, `Part Current Stock Picked`=%f,`Part Current On Hand Stock`=%f where  `Part SKU`=%d   ",
			$stock+$picked,
			$value,
			$required-$picked,
			$picked,
			$stock,
			$this->id
		);
		$this->db->exec($sql);
		//print "-> $stock , $picked, $required, , , ";
		$this->get_data('id', $this->id);

		$this->activate();

		$this->discontinue_trigger();


		$this->update_stock_status();




		$this->update_available_forecast();

		include_once 'utils/new_fork.php';
		global $account;

		list($fork_key, $msg)=new_fork('au_housekeeping', array('type'=>'update_part_products_availability', 'part_sku'=>$this->id), $account->get('Account Code'), $this->db);







		//print "$sql\n";
	}






	function update_last_date_from_transactions($type) {

		if ($type=='Sale') {
			$field='Part Last Sale Date';
		}elseif ($type=='In') {
			$field='Part Last Booked In Date';
		}else {
			print "$type\n";
			return false;
		}
		$date='';
		$sql=sprintf("select `Date` from `Inventory Transaction Fact` where `Part SKU`=%d and `Inventory Transaction Type`=%s order by `Date` desc limit 1",
			$this->id,
			prepare_mysql($type)
		);
		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {
			$date=$row['Date'];
		}
		$sql=sprintf("update `Part Dimension`  set `%s`=%s where  `Part SKU`=%d",
			$field,
			prepare_mysql($date),
			$this->id
		);

		$this->db->exec($sql);
		$this->data[$field]=$date;
	}


	function update_last_sale_date() {
		$date='';
		$sql=sprintf("select `Date` from `Inventory Transaction Fact` where `Part SKU`=%d and `Inventory Transaction Type` like 'Sale' order by `Date` desc limit 1",
			$this->id
		);
		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {
			$date=$row['Date'];
		}
		$sql=sprintf("update `Part Dimension`  set `Part Last Sale Date`=%s where  `Part SKU`=%d",
			prepare_mysql($date),
			$this->id
		);
		$this->db->exec($sql);
		$this->data['Part Last Sale Date']=$date;
	}





	function get_barcode_data() {

		switch ($this->data['Part Barcode Data Source']) {
		case 'SKU':
			return $this->sku;
		case 'Reference':
			return $this->data['Part Reference'];
		default:
			return $this->data['Part Barcode Data'];


		}

	}






	function get_locations($scope='keys') {


		if ($scope=='objects') {
			include_once 'class.Location.php';
		}elseif ($scope=='part_location_object') {
			include_once 'class.PartLocation.php';
		}

		$sql=sprintf("select PL.`Location Key`,`Location Code`,`Quantity On Hand`,`Location Warehouse Key`,`Location Mainly Used For`,`Part SKU`,`Minimum Quantity`,`Maximum Quantity`,`Moving Quantity`,`Can Pick` from `Part Location Dimension` PL left join `Location Dimension` L on (L.`Location Key`=PL.`Location Key`)  where `Part SKU`=%d",
			$this->sku);


		$part_locations=array();


		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {

				if ($scope=='keys') {
					$part_locations[$row['Location Key']]=$row['Location Key'];
				}elseif ($scope=='objects') {
					$part_locations[$row['Location Key']]=new Location($row['Location Key']);
				}elseif ($scope=='part_location_object') {
					$part_locations[$row['Location Key']]=new  PartLocation($this->sku.'_'.$row['Location Key']);
				}else {


					switch ($row['Location Mainly Used For']) {
					case 'Picking':
						$used_for=sprintf('<i class="fa fa-fw fa-shopping-basket" aria-hidden="true" title="%s" ></i>', _('Picking'));
						break;
					case 'Storing':
						$used_for=sprintf('<i class="fa fa-fw  fa-hdd-o" aria-hidden="true" title="%s"></i>', _('Storing'));
						break;
					default:
						$used_for=sprintf('<i class="fa fa-fw  fa-map-maker" aria-hidden="true" title="%s"></i>', $row['Location Mainly Used For']);
					}

					$part_locations[]=array(
						'formatted_stock'=>number($row['Quantity On Hand'], 3),
						'stock'=>$row['Quantity On Hand'],
						'warehouse_key'=>$row['Location Warehouse Key'],

						'location_key'=>$row['Location Key'],
						'part_sku'=>$row['Part SKU'],

						'location_code'=>$row['Location Code'],
						'location_used_for_icon'=>$used_for,
						'location_used_for'=>$row['Location Mainly Used For'],
						'formatted_min_qty'=>($row['Minimum Quantity']!=''?$row['Minimum Quantity']:'?'),
						'formatted_max_qty'=>($row['Maximum Quantity']!=''?$row['Maximum Quantity']:'?'),
						'formatted_move_qty'=>($row['Moving Quantity']!=''?$row['Moving Quantity']:'?'),
						'min_qty'=>$row['Minimum Quantity'],
						'max_qty'=>$row['Maximum Quantity'],
						'move_qty'=>$row['Moving Quantity'],

						'can_pick'=>$row['Can Pick']
					);

				}

			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}



		return $part_locations;
	}









	function get_current_formatted_value_at_cost() {
		//return number($this->data['Part Current Value'],2);
		return money( $this->data['Part Current Value']);
	}



	function get_current_formatted_value_at_current_cost() {

		$a=floatval(3.000*3.575);
		$a=round(3.575+3.575+3.575, 3);
		return money($this->data['Part Current On Hand Stock']*$this->data['Part Cost']  );
	}



	function fix_stock_transactions() {

		include_once 'class.PartLocation.php';

		$sql=sprintf("select `Location Key`  from `Inventory Transaction Fact` where `Part SKU`=%d group by `Location Key`", $this->sku);



		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {
				$part_location=new PartLocation($this->sku.'_'.$row['Location Key']);
				$part_location->redo_adjusts();
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}


		$sql=sprintf("select `Inventory Transaction Key`,`Date`,`Inventory Transaction Record Type`,`Inventory Transaction Section`,`Location Key`,`Note`,`Inventory Transaction Quantity`,`Required`  from `Inventory Transaction Fact` where `Part SKU`=%d and `Inventory Transaction Section` in ('Out','OIP') order by `Date`", $this->sku);



		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {


				if ($row['Inventory Transaction Section']=='OIP') {
					$qty=$row['Required'];
				}else {
					$qty=-1*$row['Inventory Transaction Quantity'];
				}
				$picking_locations=$this->get_picking_location_historic($row['Date'], $qty);

				if (count($picking_locations==1) and $picking_locations[0]['location_key']!=$row['Location Key']) {

					$_location=new Location($picking_locations[0]['location_key']);
					$note=$row['Note'];

					if (preg_match('/(<.*a> )(.*)/', $note, $matches)) {

						if ($_location->id==1) {
							$location_note.=' '._('Taken from an')." ".sprintf("<a href='location.php?id=1'>%s</a>", _('Unknown Location'));
						} else {
							$location_note=' '._('Taken from').": ".sprintf("<a href='location.php?id=%d'>%s</a>", $_location->id, $_location->data['Location Code']);
						}


						$note=$matches[1].$location_note;
					}else {

						$note.=' (WL)';
					}



					$sql=sprintf('update `Inventory Transaction Fact` set `Location Key`=%d ,`Note`=%s where `Inventory Transaction Key`=%d',
						$_location->id,
						prepare_mysql($note),
						$row['Inventory Transaction Key']
					);
					print $sql;
					$this->db->exec($sql);
					print_r($row);
					print_r($picking_locations);
				}


			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}


		$this->update_stock();

	}


	function update_stock_history() {


		$sql=sprintf("select `Location Key`  from `Inventory Transaction Fact` where `Part SKU`=%d group by `Location Key`", $this->sku);



		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {
				$part_location=new PartLocation($this->sku.'_'.$row['Location Key']);
				$part_location->update_stock_history();
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}


	}


	function update_stock_in_transactions() {

		$locations_data=array();
		$stock=0;
		$sql=sprintf("select `Inventory Transaction Quantity` ,`Inventory Transaction Key`,`Location Key` from `Inventory Transaction Fact` where `Part SKU`=%d order by `Date`,`Event Order`", $this->sku);
		$result=mysql_query($sql);
		while ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {

			if (array_key_exists($row['Location Key'], $locations_data)) {
				$locations_data[$row['Location Key']]+=$row['Inventory Transaction Quantity'];
			}else {
				$locations_data[$row['Location Key']]=$row['Inventory Transaction Quantity'];
			}

			$stock+=$row['Inventory Transaction Quantity'];
			$sql=sprintf("update `Inventory Transaction Fact` set `Part Stock`=%f,`Part Location Stock`=%f where `Inventory Transaction Key`=%d",
				$stock,
				$locations_data[$row['Location Key']],
				$row['Inventory Transaction Key']
			);
			mysql_query($sql);
			//print "$sql\n";
		}


	}


	function update_up_today_sales() {
		$this->update_sales_from_invoices('Total');

		$this->update_sales_from_invoices('Today');
		$this->update_sales_from_invoices('Week To Day');
		$this->update_sales_from_invoices('Month To Day');
		$this->update_sales_from_invoices('Year To Day');


	}


	function update_last_period_sales() {

		$this->update_sales_from_invoices('Yesterday');
		$this->update_sales_from_invoices('Last Week');
		$this->update_sales_from_invoices('Last Month');
	}


	function update_interval_sales() {

		$this->update_sales_from_invoices('3 Year');
		$this->update_sales_from_invoices('1 Year');
		$this->update_sales_from_invoices('6 Month');
		$this->update_sales_from_invoices('1 Quarter');
		$this->update_sales_from_invoices('1 Month');
		$this->update_sales_from_invoices('10 Day');
		$this->update_sales_from_invoices('1 Week');

	}




	function update_sales_from_invoices($interval) {

		include_once 'utils/date_functions.php';
		list($db_interval, $from_date, $to_date, $from_date_1yb, $to_date_1yb)=calculate_interval_dates($this->db, $interval);



		$sales_data=$this->get_sales_data($from_date, $to_date);


		$data_to_update=array(
			"Part $db_interval Acc Customers"=>$sales_data['customers'],
			"Part $db_interval Acc Repeat Customers"=>$sales_data['repeat_customers'],
			"Part $db_interval Acc Deliveries"=>$sales_data['deliveries'],
			"Part $db_interval Acc Profit"=>$sales_data['profit'],
			"Part $db_interval Acc Invoiced Amount"=>$sales_data['invoiced_amount'],
			"Part $db_interval Acc Required"=>$sales_data['required'],
			"Part $db_interval Acc Dispatched"=>$sales_data['dispatched'],
			"Part $db_interval Acc Keeping Day"=>$sales_data['keep_days'],
			"Part $db_interval Acc With Stock Days"=>$sales_data['with_stock_days'],
		);



		$this->update( $data_to_update, 'no_history');

		if ($from_date_1yb) {


			$sales_data=$this->get_sales_data($from_date_1yb, $to_date_1yb);


			$data_to_update=array(

				"Part $db_interval Acc 1YB Customers"=>$sales_data['customers'],
				"Part $db_interval Acc 1YB Repeat Customers"=>$sales_data['repeat_customers'],
				"Part $db_interval Acc 1YB Deliveries"=>$sales_data['deliveries'],
				"Part $db_interval Acc 1YB Profit"=>$sales_data['profit'],
				"Part $db_interval Acc 1YB Invoiced Amount"=>$sales_data['invoiced_amount'],
				"Part $db_interval Acc 1YB Required"=>$sales_data['required'],
				"Part $db_interval Acc 1YB Dispatched"=>$sales_data['dispatched'],
				"Part $db_interval Acc 1YB Keeping Day"=>$sales_data['keep_days'],
				"Part $db_interval Acc 1YB With Stock Days"=>$sales_data['with_stock_days'],

			);
			$this->update( $data_to_update, 'no_history');


		}


	}



	function update_previous_years_data() {

		$data_1y_ago=$this->get_sales_data(date('Y-01-01 00:00:00', strtotime('-1 year')), date('Y-01-01 00:00:00'));
		$data_2y_ago=$this->get_sales_data(date('Y-01-01 00:00:00', strtotime('-2 year')), date('Y-01-01 00:00:00', strtotime('-1 year')));
		$data_3y_ago=$this->get_sales_data(date('Y-01-01 00:00:00', strtotime('-3 year')), date('Y-01-01 00:00:00', strtotime('-2 year')));
		$data_4y_ago=$this->get_sales_data(date('Y-01-01 00:00:00', strtotime('-4 year')), date('Y-01-01 00:00:00', strtotime('-3 year')));
		$data_5y_ago=$this->get_sales_data(date('Y-01-01 00:00:00', strtotime('-5 year')), date('Y-01-01 00:00:00', strtotime('-4 year')));




		$data_to_update=array(
			"Part 1 Year Ago Customers"=>$data_1y_ago['customers'],
			"Part 1 Year Ago Repeat Customers"=>$data_1y_ago['repeat_customers'],
			"Part 1 Year Ago Deliveries"=>$data_1y_ago['deliveries'],
			"Part 1 Year Ago Profit"=>$data_1y_ago['profit'],
			"Part 1 Year Ago Invoiced Amount"=>$data_1y_ago['invoiced_amount'],
			"Part 1 Year Ago Required"=>$data_1y_ago['required'],
			"Part 1 Year Ago Dispatched"=>$data_1y_ago['dispatched'],
			"Part 1 Year Ago Keeping Day"=>$data_1y_ago['keep_days'],
			"Part 1 Year Ago With Stock Days"=>$data_1y_ago['with_stock_days'],

			"Part 2 Year Ago Customers"=>$data_2y_ago['customers'],
			"Part 2 Year Ago Repeat Customers"=>$data_2y_ago['repeat_customers'],
			"Part 2 Year Ago Deliveries"=>$data_2y_ago['deliveries'],
			"Part 2 Year Ago Profit"=>$data_2y_ago['profit'],
			"Part 2 Year Ago Invoiced Amount"=>$data_2y_ago['invoiced_amount'],
			"Part 2 Year Ago Required"=>$data_2y_ago['required'],
			"Part 2 Year Ago Dispatched"=>$data_2y_ago['dispatched'],
			"Part 2 Year Ago Keeping Day"=>$data_2y_ago['keep_days'],
			"Part 2 Year Ago With Stock Days"=>$data_2y_ago['with_stock_days'],

			"Part 3 Year Ago Customers"=>$data_3y_ago['customers'],
			"Part 3 Year Ago Repeat Customers"=>$data_3y_ago['repeat_customers'],
			"Part 3 Year Ago Deliveries"=>$data_3y_ago['deliveries'],
			"Part 3 Year Ago Profit"=>$data_3y_ago['profit'],
			"Part 3 Year Ago Invoiced Amount"=>$data_3y_ago['invoiced_amount'],
			"Part 3 Year Ago Required"=>$data_3y_ago['required'],
			"Part 3 Year Ago Dispatched"=>$data_3y_ago['dispatched'],
			"Part 3 Year Ago Keeping Day"=>$data_3y_ago['keep_days'],
			"Part 3 Year Ago With Stock Days"=>$data_3y_ago['with_stock_days'],

			"Part 4 Year Ago Customers"=>$data_4y_ago['customers'],
			"Part 4 Year Ago Repeat Customers"=>$data_4y_ago['repeat_customers'],
			"Part 4 Year Ago Deliveries"=>$data_4y_ago['deliveries'],
			"Part 4 Year Ago Profit"=>$data_4y_ago['profit'],
			"Part 4 Year Ago Invoiced Amount"=>$data_4y_ago['invoiced_amount'],
			"Part 4 Year Ago Required"=>$data_4y_ago['required'],
			"Part 4 Year Ago Dispatched"=>$data_4y_ago['dispatched'],
			"Part 4 Year Ago Keeping Day"=>$data_4y_ago['keep_days'],
			"Part 4 Year Ago With Stock Days"=>$data_4y_ago['with_stock_days'],

			"Part 5 Year Ago Customers"=>$data_5y_ago['customers'],
			"Part 5 Year Ago Repeat Customers"=>$data_5y_ago['repeat_customers'],
			"Part 5 Year Ago Deliveries"=>$data_5y_ago['deliveries'],
			"Part 5 Year Ago Profit"=>$data_5y_ago['profit'],
			"Part 5 Year Ago Invoiced Amount"=>$data_5y_ago['invoiced_amount'],
			"Part 5 Year Ago Required"=>$data_5y_ago['required'],
			"Part 5 Year Ago Dispatched"=>$data_5y_ago['dispatched'],
			"Part 5 Year Ago Keeping Day"=>$data_5y_ago['keep_days'],
			"Part 5 Year Ago With Stock Days"=>$data_5y_ago['with_stock_days'],


		);
		$this->update( $data_to_update, 'no_history');






	}


	function update_previous_quarters_data() {


		include_once 'utils/date_functions.php';


		foreach (range(1, 4) as $i) {
			$dates=get_previous_quarters_dates($i);
			$dates_1yb=get_previous_quarters_dates($i+4);


			$sales_data=$this->get_sales_data($dates['start'], $dates['end']);
			$sales_data_1yb=$this->get_sales_data($dates_1yb['start'], $dates_1yb['end']);

			$data_to_update=array(
				"Part $i Quarter Ago Customers"=>$sales_data['customers'],
				"Part $i Quarter Ago Repeat Customers"=>$sales_data['repeat_customers'],
				"Part $i Quarter Ago Deliveries"=>$sales_data['deliveries'],
				"Part $i Quarter Ago Profit"=>$sales_data['profit'],
				"Part $i Quarter Ago Invoiced Amount"=>$sales_data['invoiced_amount'],
				"Part $i Quarter Ago Required"=>$sales_data['required'],
				"Part $i Quarter Ago Dispatched"=>$sales_data['dispatched'],
				"Part $i Quarter Ago Keeping Day"=>$sales_data['keep_days'],
				"Part $i Quarter Ago With Stock Days"=>$sales_data['with_stock_days'],

				"Part $i Quarter Ago 1YB Customers"=>$sales_data_1yb['customers'],
				"Part $i Quarter Ago 1YB Repeat Customers"=>$sales_data_1yb['repeat_customers'],
				"Part $i Quarter Ago 1YB Deliveries"=>$sales_data_1yb['deliveries'],
				"Part $i Quarter Ago 1YB Profit"=>$sales_data_1yb['profit'],
				"Part $i Quarter Ago 1YB Invoiced Amount"=>$sales_data_1yb['invoiced_amount'],
				"Part $i Quarter Ago 1YB Required"=>$sales_data_1yb['required'],
				"Part $i Quarter Ago 1YB Dispatched"=>$sales_data_1yb['dispatched'],
				"Part $i Quarter Ago 1YB Keeping Day"=>$sales_data_1yb['keep_days'],
				"Part $i Quarter Ago 1YB With Stock Days"=>$sales_data_1yb['with_stock_days'],
			);
			$this->update( $data_to_update, 'no_history');
		}

	}


	function get_customers_total_data() {

		$repeat_customers=0;


		$sql=sprintf('select count(`Customer Part Customer Key`) as num  from `Customer Part Bridge` where `Customer Part Delivery Notes`>1 and `Customer Part Part SKU`=%d    ',
			$this->id
		);


		if ($result=$this->db->query($sql)) {
			if ($row = $result->fetch()) {
				$repeat_customers=$row['num'];
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}



		return $repeat_customers;

	}


	function get_sales_data($from_date, $to_date) {

		$sales_data=array(
			'invoiced_amount'=>0,
			'profit'=>0,
			'required'=>0,
			'dispatched'=>0,
			'deliveries'=>0,
			'customers'=>0,
			'repeat_customers'=>0,
			'keep_days'=>0,
			'with_stock_days'=>0,

		);


		if ($from_date=='' and  $to_date=='') {
			$sales_data['repeat_customers']=$this->get_customers_total_data();
		}


		$sql=sprintf("select count(distinct `Delivery Note Customer Key`) as customers, count( distinct ITF.`Delivery Note Key`) as deliveries, round(ifnull(sum(`Amount In`),0),2) as invoiced_amount,round(ifnull(sum(`Amount In`+`Inventory Transaction Amount`),0),2) as profit,round(ifnull(sum(`Inventory Transaction Quantity`),0),1) as dispatched,round(ifnull(sum(`Required`),0),1) as required from `Inventory Transaction Fact` ITF  left join `Delivery Note Dimension` DN on (DN.`Delivery Note Key`=ITF.`Delivery Note Key`) where `Inventory Transaction Type` like 'Sale' and `Part SKU`=%d %s %s" ,
			$this->id,
			($from_date?sprintf('and  `Date`>=%s', prepare_mysql($from_date)):''),
			($to_date?sprintf('and `Date`<%s', prepare_mysql($to_date)):'')
		);

		//print "$sql\n";

		if ($result=$this->db->query($sql)) {
			if ($row = $result->fetch()) {
				$sales_data['customers']=$row['customers'];
				$sales_data['invoiced_amount']=$row['invoiced_amount'];
				$sales_data['profit']=$row['profit'];
				$sales_data['dispatched']=-1.0*$row['dispatched'];
				$sales_data['required']=$row['required'];
				$sales_data['deliveries']=$row['deliveries'];
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}




		return $sales_data;

	}



	function update_available_forecast() {

		$this->load_acc_data();


		// -------------- simple forecast -------------------------

		$sql=sprintf("select `Date` from `Inventory Transaction Fact` where `Part SKU`=%d and `Inventory Transaction Type` like 'Associate' order by `Date` desc"
			, $this->id);
		$res=mysql_query($sql);

		if ($row=mysql_fetch_array($res)) {
			$date=$row['Date'];
			$interval=(date('U')-strtotime($date))/3600/24;
		} else
			$interval=0;

		if ($this->data['Part Current Stock']=='' or $this->data['Part Current Stock']<0) {
			$this->data['Part Days Available Forecast']=0;
			$this->data['Part XHTML Available For Forecast']='?';
		}
		elseif ($this->data['Part Current Stock']==0) {
			$this->data['Part Days Available Forecast']=0;
			$this->data['Part XHTML Available For Forecast']=0;
		}
		else {

			if ($this->data['Part 1 Year Acc Required']>0) {
				if ($interval>(365)) {
					$interval=365;
				}

				$this->data['Part Days Available Forecast']=$interval*$this->data['Part Current Stock']/$this->data['Part 1 Year Acc Required'];
				$this->data['Part XHTML Available For Forecast']=number($this->data['Part Days Available Forecast'], 0).' '._('d');
			}
			elseif ($this->data['Part 1 Quarter Acc Required']>0) {



				// print $this->data['Part 1 Quarter Acc Required']."xxxx\n";
				if ($interval>(365/4)) {
					$interval=365/4;
				}
				//print $this->data['Part 1 Quarter Acc Required']/$interval;


				$this->data['Part Days Available Forecast']=$interval*$this->data['Part Current Stock']/$this->data['Part 1 Quarter Acc Required'];
				$this->data['Part XHTML Available For Forecast']=number($this->data['Part Days Available Forecast'], 0).' '._('d');
			}
			else {

				$from_since=(date('U')-strtotime($this->data['Part Valid From'])/86400);
				if ($from_since<($this->data['Part Excess Availability Days Limit']/2)) {
					$forecast=$this->data['Part Excess Availability Days Limit']-1;
				}else {
					$forecast=$this->data['Part Excess Availability Days Limit']+$from_since;
				}



				$this->data['Part Days Available Forecast']=$forecast;
				$this->data['Part XHTML Available For Forecast']=number($this->data['Part Days Available Forecast'], 0).' '._('d');




			}





		}

		$sql=sprintf("update `Part Dimension` set `Part Days Available Forecast`=%s,`Part XHTML Available for Forecast`=%s where `Part SKU`=%d", $this->data['Part Days Available Forecast'], prepare_mysql($this->data['Part XHTML Available For Forecast']), $this->id );
		//print $sql;
		mysql_query($sql);

	}


	function update_days_until_out_of_stock() {
		$this->get_days_until_out_of_stock();
	}


	function get_days_until_out_of_stock() {

		if ($this->data['Part Current Stock']==0) {
			$days=0;
			$days_formatted='0';
			return array($days, $days_formatted);
		}


		$sql=sprintf("select `Date` from `Inventory Transaction Fact` where `Part SKU`=%d and `Inventory Transaction Type` like 'Associate' order by `Date` desc"
			, $this->id);
		$res=mysql_query($sql);

		if ($row=mysql_fetch_array($res)) {
			$date=$row['Date'];
			$interval=(date('U')-strtotime($date))/3600/24;
			if ($interval<21) {
				$qty=$this->data['Part Total Acc Provided']+$this->data['Part Total Acc Lost'];
				if ($interval!=0) {
					$qty_per_day=$qty/$interval;

					if ($qty==0) {
						$days=0;
					}else {
						$days=$this->data['Part Current Stock']/$qty_per_day;
					}

				}else {
					$days=100;

				}

				$days_formatted=$days.' '._('days');


				return array($days, $days_formatted);

			}


		} else {
			$days=0;
			$days_formatted='ND';
			return array($days, $days_formatted);
		}

		//include_once('class.Timeserie.php');
		/*

		$sql=sprintf("select `First Day` from kbase.`Week Dimension` where `Year Week`=%s",date("YW"));
		$res=mysql_query($sql);
		$no_data=true;
		if ($row=mysql_fetch_array($res)) {
			$date=date("Y-m-d",strtotime($row['First Day'].' -1 day'));
		}
		list($stock,$value)=$this->get_stock($date);
		print "$stock,$value\n";



		// $tm=new TimeSeries(array('m','part sku '.$row['Part SKU']));
		//  $tm->get_values();$tm->save_values();
		//  $tm->forecast();

		$sql=sprintf("select `Time Series Value` from `Time Series Dimension` where `Time Series Frequency`='Weekly' and `Times Series Name`='SkuS' and `Time Series Name Key`=%d  and `Time Series Type`='Forecast' order by `Time Series Date`",$this->id);


		$resmysql_query($sql);
		$future_stock='';
		while ($row=mysql_fetch_array($res)) {

		}
*/



	}










	function update_number_transactions() {



		$transactions=array('all_transactions'=>0, 'in_transactions'=>0, 'out_transactions'=>0, 'audit_transactions'=>0, 'oip_transactions'=>0, 'move_transactions'=>0);
		$sql=sprintf("select sum(if(`Inventory Transaction Type` not in ('Move In','Move Out','Associate','Disassociate'),1,0))  as all_transactions , sum(if(`Inventory Transaction Type`='Not Found' or `Inventory Transaction Type` like 'No Dispatched' or `Inventory Transaction Type` like 'Audit',1,0)) as audit_transactions,sum(if(`Inventory Transaction Type`='Move',1,0)) as move_transactions,sum(if(`Inventory Transaction Type` like 'Sale' or `Inventory Transaction Type`='Broken' or  `Inventory Transaction Type` like 'Other Out' or `Inventory Transaction Type` like 'Lost',1,0)) as out_transactions, sum(if(`Inventory Transaction Type`='Order In Process',1,0)) as oip_transactions, sum(if(`Inventory Transaction Type` like 'In',1,0)) as in_transactions from `Inventory Transaction Fact` where `Part SKU`=%d",
			$this->sku);

		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {
			$this->data['Part Transactions']=$row['all_transactions'];
			$this->data['Part Transactions In']=$row['in_transactions'];
			$this->data['Part Transactions Out']=$row['out_transactions'];
			$this->data['Part Transactions Audit']=$row['audit_transactions'];
			$this->data['Part Transactions OIP']=$row['oip_transactions'];
			$this->data['Part Transactions Move']=$row['move_transactions'];

			$sql=sprintf("Update `Part Dimension` set `Part Transactions`=%d ,`Part Transactions In`=%d,`Part Transactions Out`=%d ,`Part Transactions Audit`=%d,`Part Transactions OIP`=%d ,`Part Transactions Move`=%d where `Part SKU`=%d ",
				$this->data['Part Transactions'],
				$this->data['Part Transactions In'],
				$this->data['Part Transactions Out'],
				$this->data['Part Transactions Audit'],
				$this->data['Part Transactions OIP'],
				$this->data['Part Transactions Move'],
				$this->sku

			);
			//print "$sql\n";
			mysql_query($sql);
		}


	}


	function delete($metadata=false) {




		$sql=sprintf('insert into `Part Deleted Dimension`  (`Part Deleted Key`,`Part Deleted Reference`,`Part Deleted Date`,`Part Deleted Metadata`) values (%d,%s,%s,%s) ',
			$this->id,
			prepare_mysql($this->get('Part Reference')),
			prepare_mysql(gmdate('Y-m-d H:i:s')),
			prepare_mysql(gzcompress(json_encode($this->data), 9))

		);
		$this->db->exec($sql);




		$sql=sprintf('delete from `Part Dimension`  where `Part SKU`=%d ',
			$this->id
		);
		$this->db->exec($sql);


		$history_data=array(
			'History Abstract'=>sprintf(_("Part record %s deleted"), $this->data['Part Reference']),
			'History Details'=>'',
			'Action'=>'deleted'
		);

		$this->add_subject_history($history_data, true, 'No', 'Changes', $this->get_object_name(), $this->get_main_id());




		$this->deleted=true;


		$sql=sprintf('select `Supplier Part Key` from `Supplier Part Dimension` where `Supplier Part Part SKU`=%d  ', $this->id);

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


	function get_categories() {


		$part_categories=array();


		$sql=sprintf("select C.`Category Key`,`Category Label` from `Category Dimension` C left join `Category Bridge` B on (B.`Category Key`=C.`Category Key`) where `Subject`='Part' and `Subject Key`=%d and `Category Branch Type`='Head'", $this->sku);
		// print $sql;
		$result=mysql_query($sql);
		while ($row=mysql_fetch_assoc($result)) {
			$part_categories[]=array('category_key'=>$row['Category Key'], 'category_label'=>$row['Category Label']);
		}

		return $part_categories;
	}






	function get_field_label($field) {
		global $account;

		switch ($field) {

		case 'Part SKU':
			$label=_('SKU');
			break;
		case 'Part Status':
			$label=_('Status');
			break;
		case 'Part Reference':
			$label=_('reference');
			break;
		case 'Part Unit Description':
			$label=_('unit description');
			break;
		case 'Part Unit Label':
			$label=_('unit label');
			break;
		case 'Part Package Description':
			$label=_('SKO description');
			break;
		case 'Part Unit Price':
			$label=_('unit recommended price');
			break;
		case 'Part Unit RRP':
			$label=_('unit recommended RRP');
			break;

		case 'Part Package Weight':
			$label=_('SKO weight');
			break;
		case 'Part Package Dimensions':
			$label=_('SKO dimensions');
			break;
		case 'Part Unit Weight':
			$label=_('unit weight');
			break;
		case 'Part Unit Dimensions':
			$label=_('unit dimensions');
			break;
		case 'Part Tariff Code':
			$label=_('tariff code');
			break;

		case 'Part Duty Rate':
			$label=_('duty rate');
			break;

		case 'Part UN Number':
			$label=_('UN number');
			break;

		case 'Part UN Class':
			$label=_('UN class');
			break;
		case 'Part Packing Group':
			$label=_('packing group');
			break;
		case 'Part Proper Shipping Name':
			$label=_('proper shipping name');
			break;
		case 'Part Hazard Indentification Number':
			$label=_('hazard indentification number');
			break;
		case 'Part Materials':
			$label=_('Materials/Ingredients');
			break;
		case 'Part Origin Country Code':
			$label=_('country of origin');
			break;
		case 'Part Units Per Package':
			$label=_('units per SKO');
			break;
		case 'Part Barcode Number':
			$label=_('barcode');
			break;


		default:
			$label=$field;

		}

		return $label;

	}


	function get_products_data($with_objects=false) {

		include_once 'class.Product.php';

		$sql=sprintf("select `Linked Fields`,`Store Product Key`,`Parts Per Product`,`Note` from `Store Product Part Bridge` where `Part SKU`=%d ",
			$this->id
		);
		$products_data=array();
		if ($result=$this->db->query($sql)) {
			foreach ($result as $row) {
				$product_data=$row;
				if ($product_data['Linked Fields']=='') {
					$product_data['Linked Fields']=array();
					$product_data['Number Linked Fields']=0;
				}else {
					$product_data['Linked Fields']=json_decode($row['Linked Fields'], true);
					$product_data['Number Linked Fields']=count($product_data['Linked Fields']);
				}
				if ($with_objects) {
					$product_data['Product']=new Product('id', $row['Store Product Key']);
				}
				$products_data[]=$product_data;
			}
		}else {
			print_r($error_info=$this->db->errorInfo());
			exit;
		}

		return $products_data;
	}


	function create_supplier_part_record($data) {


		include_once 'class.Supplier.php';

		$data['editor']=$this->editor;


		$supplier=new Supplier($data['Supplier Part Supplier Key']);
		if (!$supplier->id) {
			$this->error=true;
			$this->error_code='supplier_not_found';
			$this->msg=_('Supplier not found');
		}

		if ($data['Supplier Part Minimum Carton Order']=='') {
			$data['Supplier Part Minimum Carton Order']=1;
		}else {
			$data['Supplier Part Minimum Carton Order']=ceil($data['Supplier Part Minimum Carton Order']);
		}


		$data['Supplier Part Currency Code']=$supplier->get('Supplier Default Currency Code');


		if ($data['Supplier Part Unit Extra Cost']=='' ) {
			$data['Supplier Part Unit Extra Cost']=0;
		}




		$supplier_part= new SupplierPart('find', $data, 'create');



		if ($supplier_part->id) {
			$this->new_object_msg=$supplier_part->msg;

			if ($supplier_part->new) {
				$this->new_object=true;
				$supplier->update_supplier_parts();


				$supplier_part->update(array('Supplier Part Part SKU'=>$this->sku));
				$supplier_part->get_data('id', $supplier_part->id);

				$this->update_cost();
				$supplier_part->update_historic_object();







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


	function get_category_data() {


		$type='Part';

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


	function updated_linked_products() {
		include_once 'class.Image.php';
		foreach ($this->get_products('objects') as $product) {

			if ( count($product->get_parts())==1 ) {
				$product->editor=$this->editor;

				$product->update(array('Product Tariff Code'=>$this->get('Part Tariff Code')), 'no_history from_part');
				$product->update(array('Product Duty Rate'=>$this->get('Part Duty Rate')), 'no_history from_part');
				$product->update(array('Product Origin Country Code'=>$this->get('Part Origin Country Code')), 'no_history from_part');


				$product->update(array('Product UN Number'=>$this->get('Part UN Number')), 'no_history from_part');
				$product->update(array('Product UN Class'=>$this->get('Part UN Class')), 'no_history from_part');
				$product->update(array('Product Packing Group'=>$this->get('Part Packing Group')), 'no_history from_part');
				$product->update(array('Product Proper Shipping Name'=>$this->get('Part Proper Shipping Name')), 'no_history from_part');
				$product->update(array('Product Hazard Indentification Number'=>$this->get('Part Hazard Indentification Number')), 'no_history from_part');




				$product->update(array('Product Unit Weight'=>$this->get('Part Unit Weight')), 'no_history from_part');


				$product->update(array('Product Unit Dimensions'=>$this->get('Part Unit Dimensions')), 'no_history from_part');
				$product->update(array('Product Materials'=>strip_tags($this->get('Materials'))), 'no_history from_part');

				$sql=sprintf('select `Image Subject Image Key` from `Image Subject Bridge` where `Image Subject Object`="Part" and `Image Subject Object Key`=%d  ',
					$this->id
				);

				//   print "$sql\n";

				if ($result=$this->db->query($sql)) {
					foreach ($result as $row) {
						//print_r($row);
						$product->link_image($row['Image Subject Image Key']);
					}
				}else {
					print_r($error_info=$this->db->errorInfo());
					exit;
				}


			}

		}


	}


	function activate() {

		if ($this->get('Part Status')=='In Process') {

			if ($this->get('Part Main Image Key')>0 and $this->get('Part Current On Hand Stock')>0) {

				$this->update(array(
						'Part Status'=>'In Use',
						'Part Active From'=>gmdate('Y-m-d H:i:s')
					), 'no_history');
			}


		}


	}


}
