<?php
require_once('external_libs/PDF/config/lang/eng.php');
require_once('pdf_main.php');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAuthor('Kaktus');
$pdf->SetTitle('Generate Invoice');
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
$pdf->SetFont('helvetica', 'B', 20);
$pdf->AddPage();
$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
$pdf->SetFont('helvetica', '', 8);
ob_clean();
ob_start();
include('external_libs/PDF/template.php');
$page1 = ob_get_contents();
ob_clean();
$page1 = preg_replace("/\s\s+/", '', $page1);
$pdf->writeHTML($page1, true, 0, true, 0);
$pdf->Output('invoice_details.pdf', 'I');
?>
