<?php
require_once('common.php');

//$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
//** $customer_id=$myconf['customer_id_prefix'].sprintf("%05d",$id);
require_once('external_libs/PDF/config/lang/eng.php');
require_once('pdf_send_post_main.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);



//////$pdf->SetAuthor('Inikoo');
$pdf->SetTitle('Generate Customer Postal Address');
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setLanguageArray($l);
$pdf->SetFont('helvetica', '', 8);
$pdf->AddPage();
//$resolution= array(102, 255);
//$pdf->AddPage('P', $resolution);


$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
$pdf->SetFont('helvetica', '', 8);
ob_clean();
ob_start();
include('external_libs/PDF/template_send_post.php');
$page1 = ob_get_contents();
ob_clean();
$page1 = preg_replace("/\s\s+/", '', $page1);
$pdf->writeHTML($page1, true, 0, true, 0);
//*$pdf_file_name=$customer_id.'.pdf';
$pdf_file_name='Send Post.pdf';
$pdf->Output($pdf_file_name, 'I');



?>
