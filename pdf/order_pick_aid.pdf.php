<?php
chdir('../');
require_once __DIR__.'/../vendor/autoload.php';



require_once 'utils/object_functions.php';

require_once 'common.php';


$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
if (!$id) {
    exit("no id");
}
$delivery_note =  get_object('DeliveryNote',$id);
if (!$delivery_note->id) {
    exit("no dn");
}
$store    = get_object('Store',$delivery_note->get('Delivery Note Store Key'));
$customer    = get_object('Customer',$delivery_note->get('Delivery Note Customer Key'));





$mpdf = new \Mpdf\Mpdf(
    [
        'tempDir'       => __DIR__.'/../server_files/pdf_tmp',
        'mode'          => 'utf-8',
        'margin_left'   => 15,
        'margin_right'  => 15,
        'margin_top'    => 22,
        'margin_bottom' => 25,
        'margin_header' => 10,
        'margin_footer' => 10
    ]
);


$mpdf->useOnlyCoreFonts = true;    // false is default
$mpdf->SetTitle(_('Picking Aid').' '.$delivery_note->data['Delivery Note ID']);
$mpdf->SetAuthor($store->data['Store Name']);


//$mpdf->SetDisplayMode('fullpage');
//$mpdf->SetJS('this.print();');    // set when we want to print....

$smarty->assign('store', $store);
$smarty->assign('delivery_note', $delivery_note);


$transactions = array();


$sql    = sprintf(
    "SELECT  `Map To Order Transaction Fact Parts Multiplicity` AS part_multiplicity,`Map To Order Transaction Fact XHTML Info` AS multiple_parts_info,Part.`Part Current On Hand Stock` AS total_stock, PLD.`Quantity On Hand` AS stock_in_picking,`Part Current Stock`,`Part Reference` AS reference,`Picking Note` AS notes,ITF.`Part SKU`,`Part Package Description` AS description,
(`Required`+`Given`) AS qty,`Location Code` AS location ,
`Part UN Number` AS un_number,
`Part Packing Group` AS part_packing_group
FROM 
`Inventory Transaction Fact` ITF   LEFT JOIN  
`Part Dimension` Part ON  (Part.`Part SKU`=ITF.`Part SKU`) LEFT JOIN  
`Location Dimension` L ON  (L.`Location Key`=ITF.`Location Key`)  LEFT JOIN 
`Part Location Dimension` PLD ON (ITF.`Location Key`=PLD.`Location Key` AND ITF.`Part SKU`=PLD.`Part SKU`) 
WHERE `Delivery Note Key`=%d ORDER BY `Location File As`,`Part Reference` ", $delivery_note->id
);

if ($result=$db->query($sql)) {
		foreach ($result as $row) {
            $stock_in_picking = $row['stock_in_picking'];
            $total_stock      = $row['total_stock'];

            $row['stock']   = sprintf("[<b>%d</b>,%d]", $stock_in_picking, $total_stock);
            $row['description_note']='';
            $row['images']='';
            $transactions[] = $row;
		}
}else {
		print_r($error_info=$db->errorInfo());
		print "$sql\n";
		exit;
}

$smarty->assign('transactions', $transactions);

$number_of_items = 0;
$number_of_picks = 0;
$sql             = sprintf(
    "SELECT count(*) AS items,sum(`Required`+`Given`) AS picks FROM `Inventory Transaction Fact`  WHERE `Delivery Note Key`=%d ", $delivery_note->id
);

if ($result=$db->query($sql)) {
    if ($row = $result->fetch()) {
        $number_of_items = $row['items'];
        $number_of_picks = $row['picks'];
	}
}else {
	print_r($error_info=$db->errorInfo());
	print "$sql\n";
	exit;
}

//print $sql;

$formatted_number_of_items = '<b>'.number($number_of_items).'</b> '.ngettext(
        'item', 'items', $number_of_items
    );
$formatted_number_of_picks = '<b>'.number($number_of_picks).'</b> '.ngettext(
        'pick', 'picks', $number_of_picks
    );

$smarty->assign('formatted_number_of_items', $formatted_number_of_items);
$smarty->assign('formatted_number_of_picks', $formatted_number_of_picks);


$html = $smarty->fetch('order_pick_aid.pdf.tpl');

$mpdf->WriteHTML($html);


$mpdf->Output();

?>
