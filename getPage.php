<?php
	include('common.php');
	require_once 'csvparser.php';
	$csv = new CSV_PARSER;
	//loading the CSV File
	$csv->load($_SESSION['file_path']);
	//extracting the HEADERS
	$h = $csv->getHeaders();
	$count_rows = $csv->countRows();
	$index = $_REQUEST['v'];
	$r = $csv->getRow($index);
	$leftColumnArray = array();
	$rightColumnArray = array();
	//$assign = $_POST['assign_field'];	
	
?>
<table class="recordList">
		
		<tr>
		<th class="list-column-left" style="text-align: left; width: 20%; padding-left:5px;">
		    Column
		</th>
		<th class="list-column-left" style="text-align: left; width: 40%;padding-left:10px;">
		Assigned Field
		</th>
		<th class="list-column-left" style="text-align: left; width: 30%;">
		<span style="float: left;">Sample Values</span>
		<span style="float: right;padding-right:5px;"> 
			     <?php
              			 if($index > 0)
              			 {
       			    ?>
       				<a href="#" id="prev" onclick="getPrev(<?php echo $index; ?>)">Previous</a>
		       	   <?php
			         }
			       if($index < $count_rows-1)        
			         {
		       	   ?>
       				<a href="#" id="next" onclick="getNext(<?php echo $index; ?>)">Next</a>
		          <?php
			         }
		          ?>
			<img src='art/icons/del.png' onclick='history.go(-2)'>
		</span>
		</th>
		</tr>
<tr>&nbsp;</tr>
		
	<?php
	for($j=0; $j<count($h)-1; $j++)
	{ 
	?>
	<tr>
		<td width=150 align=center >
		<!-- Header Output -->
		<?php echo $h[$j]; 	$leftColumnArray = $h; ?>
		</td>
		<td align=center>
		<select name="assign_field[]" id="assign_field">
		    <option value="0">Unknown Please Choose</option>
		    <option value="Customer Main Contact Name">Contact Name</option>
		    <option value="Customer Name">Name</option>
		    <option value="Customer Type">Type</option>
		    <option value="Customer Company Name">Company Name</option>
		    <option value="Customer Main Plain Email">Email</option>
		    <option value="Contact Main Plain Mobile">Mobile</option>
		    <option value="Customer Main Plain Telephone">Customer Main Plain Telephone</option>	
		    <option value="Customer Main Plain FAX">Customer Main Plain FAX</option>
		    <option value="Customer Main Plain Address">Customer Main Plain Address</option>
		    <option value="Customer Address Line 1">Customer Address Line 1</option>
		    <option value="Customer Address Line 2">Customer Address Line 2</option>
		    <option value="Customer Address Line 3">Customer Address Line 3</option>
		    <option value="Customer Address Line 2">Customer Address Line 2</option>
		    <option value="Customer Address Town">Customer Address Town</option>
		    <option value="Customer Address Postal Code">Customer Address Postal Code</option>
		    <option value="Customer Address Country Name">Customer Address Country Name</option>
		    <option value="Customer Address Country First Division">Customer Address Country First Division</option>
		    <option value="Customer Address Country Second Division">Customer Address Country Second Division</option>
		    <option value="Customer Tax Number">Customer Tax Number</option>
		 </select>
		</td>
		<td>
			<!-- Value Output -->
			<?php echo $r[$j];  	$rightColumnArray = $r;?>
		</td>
	</tr>
	<?php
		
	}
	?>
</table>
<?php
	echo '<pre>';
	print_r($rightColumnArray);
	
?>
