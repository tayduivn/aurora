<?php header("Content-Type: text/css"); ?>
html{background-color:#fff;background-image:url('<?php if(isset($_GET["c"])) echo "../uploads/".isset($_GET["c"]); else echo "../art/bg/bd3.jpg"; ?>');background-repeat:no-repeat;}
body{color:#333;margin:0px;padding:0px;font-family:"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, sans-serif;}
td {text-align:left}


.hide{visibility:hidden}

h1 {font-size:160%}
th,td {padding:.2em;}
th {font-weight:bold;text-align:center;}
p,fieldset,table,pre {margin-bottom:1em;}
input.time_input,input.date_input,input.text,input.password,input.file ,textarea{border:1px solid #777}

h1,h2,h3,h4{padding:5px 0px}
h1 {font-size:160%}
h1 a.next,h1 a.prev{position:relative;bottom:0.5em}
h2 {font-size:135%;margin-bottom:0px}

a,table,button{outline: none;}
a {text-decoration:none;color:#000000}
a:hover {text-decoration:underline;color:#000} 

span.aslink{cursor:pointer}
span.aslink:hover{text-decoration:underline;} 


.yui-dt-hidden{ display:none }
.yui-dt-hidden div { display:none }
.aright  {text-align:right;}
.aright .yui-dt-liner{ text-align:right; }
.aleft,td.aleft {text-align:left}
.aleft .yui-dt-liner{text-align:left}
span.nav2 {float:left;background:#B665D2;padding:0 10px;position:relative;bottom:6px;color:#fff;font-size:85%;margin:0 2px}
span.nav2 a {color:#fff}
span.nav2 a.selected {color:yellow;font-weight:400}
span.nav2 span {color:#fff;cursor:pointer}
span.nav2 span.selected {color:yellow;font-weight:400;border:none;background:none}


span.onright {float:right}

#hd{height:3.46em;background:#3b5998 url("../art/4.png") bottom left repeat-x;color:#fff;}
#hd h1{padding:0 15px;font-size:167%;float:left;position:relative;top:10px;}
	#hd a{color:#fff;text-decoration:none}

	#hd a:hover{text-decoration:underline}	       
#hd #navsite ul {padding: 0 15px 0px 0;list-style: none;}
#hd #navsite li {
		float: right;
		background: transparent url("../art/green_header_menu_right.png") 100%  no-repeat!important;
                
                background: none;	
		padding: 0 5px 0px 0;
		margin: 0 5px 0px 0;
	}
	#hd #navsite ul a {
	       
	    	float: left;
		display: block;
		padding: 2px 4px 5px 8px;
		background: transparent url("../art/onion_header_menu.png") 0%  no-repeat!important;
		background:none;
		font-weight: bold;
		outline: none;
		text-decoration: none;
	}
	#hd #navsite ul li:hover {	background: transparent url("../art/green_header_menu_hover_right.png") 100%  no-repeat!important;background:none }
	#hd #navsite ul li:hover a { text-decoration:none;		background: transparent url("../art/green_header_menu_hover.png") 0%  no-repeat!important;background:none}
	#hd .selected li, #hd .selected:hover   {
background:transparent url("../art/header_menu2s.png") 100%  no-repeat !important;
background:none;
}
	#hd .selected a, #hd .selected:hover  a{
background: transparent url("../art/header_menus.png") 0%  no-repeat!important;
background:none;
color:#ff0 !important;
color:red;
}	

.yuimenu{background:white;border:1px solid #999;padding:5px 10px}
#yui-main{clear:both}

#hd span{margin:0 0px}
#bd{background:#fff;border:1px solid #aaa;min-height:490px;padding:0 30px 40px 30px;
height:auto !important;
height:490px;}

#footer{ font-size:77%;text-align:center;margin-top:5px;color:#ffffff;margin-bottom:100px}

.bd labelc { display:block;float:left;width:30%;clear:left; }

.clear{ clear:both; margin:10px 0 }



#rmenu #edit_buttons .yui-button {margin:5px 0;}





.xxxresp{border:1px solid #3b5998;width:50%;margin:10px auto;padding:10px;}
.xxxadduser{padding-left:45px!important;background:  url("../art/icons/user_add.png") no-repeat;background-position:7.5px 10px  }



 td.stock{font-size:153.9%;font-weight:bold}
td.price{font-size:110%;font-weight:bold}


#langmenu{text-align:left;position:absolute;left:0px;top:0 }

table.show_info_product {border-top:1px solid #f29e02;border-bottom:1px solid #f29e02;width:100%}
xtable.show_info_product td {border-top:1px solid #ddd;border-bottom:1px solid #ddd}
.prodinfo{font-size:120%}
table.show_info_product td.number div{width:5em;text-align:right}
-

.prod_info table.notes{clear:both;border:none;}

-

.newcontact{border:1px solid #3b5998;padding:10px}
.newcontact table, .newcontact div {border:none;float:left;margin-right:10px}
.newcontact table td {padding:0 5px;vertical-align:top}
.newcontact table td.noborder {border:none}
.newcontact div {padding:10px}
.newcontact .img{padding:5px 5px 7px 5px}
.newcontact h2{padding:0}
.newcontact legend{background:#3b5998;color:#fff;margin-left:5px;padding:0px 10px 2px 10px;font-size:85%}


.noshow{display:none}
.show{display:block}


.resetfield {position:relative;right:22px;bottom:2px;cursor:pointer}
.calpop {position:relative;right:18px;}
img.calpop {position:relative;bottom:2px;cursor:pointer  }
img.calpop_to {position:relative;bottom:2px;cursor:pointer;right:36px;  }



.alarm_warning{background:yellow}
.alarm_error{background:red}

.nodisplay {display:none}
.display {display:block}
.product_plot{width: 800px;height: 350px;}
.xprodinfo .product_details{clear:both;border:none}
.product_plots{clear:both;border:none}

table.other_images{margin:5px auto ;border:none}
table.other_images td{padding:0;width:30px;height:26px;border:1px solid #aaa;vertical-align: middle;text-align:center}

table.editborder td{border:1px solid #b52b34;}


table.other_images img{border:none;padding:0;margin:0;cursor:pointer}
.xprodinfo div.image_caption{padding:0px ;border:none;margin:5px auto;width:100%}

.xprodinfo td.img_arrow{border:none;width:20px;padding:0;margin:0}

#image{border:none}

.time_picker_div{border:none;}

.date_input{cursor:pointer;background:#fff url('../art/icons/calendar_view_month.png') bottom right no-repeat ;padding-right:16px;width:6em}
.time_input{cursor:pointer;background:#fff url('../art/icons/time.png') bottom right no-repeat ;padding-right:16px}

input .normal_left{color:#000;text-align:left}
input.normal_right{color:#000;text-align:right}



.labels{color:#ccc;text-align:center}


.warning{background:#fbf78d}
.ok{background:#a6edaa}
.text_ok{color:#4aa42b}

img{vertical-align:bottom}

.chooser {width:670px;bxorder:1px solid black;margin:0px auto}
.chooser ul {;margin:0px auto;}
.chooser{padding:0px 0px 0 0px;}
.chooser li {float:left;padding:3px 5px 5px 5px ;margin: 0 5px 10px 0;border: 1px solid #ccc;cursor:pointer;;opacity: .5;filter: alpha(opacity=40);}
.chooser li:hover {opacity: .8;filter: alpha(opacity=80);}
.chooser img {position:relative;top:2px;}
.chooser li.selected {cursor:default;border-color:#aaa;;opacity: 1;filter: alpha(opacity=100);font-weight:800;color:#555}
.chooser li.show {default;border-color:#aaa;;opacity: 1;filter: alpha(opacity=100);font-weight:200;color:#666}
.chooser li.show:hover {default;border-color:#777;;opacity: 1;filter: alpha(opacity=100);font-weight:200;color:#333}



.chooser2 {width:670px;border:0px solid black;margin:0px}
.chooser2 ul {;margin:0px auto;}
.chooser2{padding:0px 0px 0 0px;}
.chooser2 li {float:left;padding:3px 5px 5px 5px ;margin: 0 5px 10px 0;border: 1px solid #ccc;cursor:pointer;;opacity: .5;filter: alpha(opacity=40);}
.chooser2 li:hover {opacity: .8;filter: alpha(opacity=80);}
.chooser2 img {position:relative;top:2px;}
.chooser2 li.selected {cursor:default;border-color:#aaa;;opacity: 1;filter: alpha(opacity=100);font-weight:800;color:#555}




.report_header {padding:5px 0px 0 00px;clear:both;margin-top:0px;}
.report_header li {float:left;padding: 5px;margin: 0 5px 0px 0;cursor:pointer}
.report_header a { text-decoration:none }

#editor_chosser {padding:15px 20px 0 20px;}
#editor_chosser li {float:left;padding: 5px;margin: 0 5px 0px 0;border: 1px solid #ccc;cursor:pointer}
#editor_chosser img{position:relative;top:2px}




.table_manager {float:right;font-size:85%;margin-right:10px;}
.table_manager div {float:right;height:112px;margin-top:14px;}
.table_manager div.centre_block {height:10px;border-top:1px solid  SteelBlue;background:Lavender;}
.table_manager div.left_block,div.right_block{width:10px;height:15px;} 
.table_manager div.left_block{background-image:url('../art/corner_top_table_left.png')} 
.table_manager div.right_block{background-image:url('../art/corner_top_table_right.png')} 
.table_manager div.centre_block input{border:1px solid #777;margin-top:1px}
.table_manager div.hide{margin:0;position:relative;left:0px;top:3px;cursor:pointer}
.table_manager input{border:1px solid #777;position:relative;bottom:4px}
.table_manager div.centre_block img.resetfield{position:relative;bottom:5px;left:-24px}
.table_manager div.centre_block div {position:relative;bottom:10px}
.table_manager div.centre_block img {position:relative;bottom:2px}
.yui-pg-rpp-options{border:1px solid black}



.view_options td {cursor:pointer}

.report_sales1 td {text-align:right;padding:0 15px;}
.compact td{ padding:0 12.5px; }
.xreport_sales1 tr td{border-top:1px solid #ddd}

.report_sales1 tr.org {background:b2bef8;border-top:1px solid RoyalBlue;border-bottom:1px solid RoyalBlue}
.report_sales1 tr.outside {}
.report_sales1 tr.geo,.report_sales1 tr.first  {border-top:1px solid #ddd;border-bottom:1px solid #ddd}
.report_sales1 tr.title td{border-top:none;border-bottom:1px solid #bbb;;font-weight:400}

.report_sales1 tr.first {border-top:1px solid #999;}
.report_sales1 tr.partners {border-top:1px solid #999}
.report_sales1 tr.total,.report_sales1 tr.subtotal {border-top:1px solid #999}
.report_sales1 tr.total {vertical-align:top;font-weight:800}
.report_sales1 tr.last {vertical-align:top;}

.report_sales1 tr.last td {border-bottom:1px solid #999}
.report_sales1 tr.space td{border:1px solid #fff}

.report_sales1 tr td.space {border:1px solid #fff}
.report_sales1 td.compare ,.report_sales1 tr.last  td.compare{font-size:80%;border:2px solid #fff;margin:0px 2px}
.report_sales1 td.compare_title {text-align:center;color:#777;font-size:70%}


.bracket td{padding:0;border:0px}

.report_sales1 tr.bracket_label td{padding:0;border-top: 0px solid red;;text-align:center;color:#777;font-size:90%;padding-top:5px;font-weight:800}

}



.report_export_countries {float:right}
.report_export_countries td {text-align:right;padding:0 12px;}
.report_export_countries td.country {text-align:left;padding:0 12px 0 5 ;}
.report_export_countries tr{border-top:1px solid #ddd}

.report_export_countries tr.tlabels {border-bottom:1px solid #999;border-top:none}

table.plot_menu  {float:right}
table.plot_menu tr.top{border-bottom:1px solid #ddd}
table.plot_menu td.left{border-right:1px solid #ddd}
table.plot_menu img.opaque {cursor:pointer;opacity: .25;filter: alpha(opacity=25);}
table.plot_menu img.opaque:hover {opacity: .8;filter: alpha(opacity=80);}
table.plot_menu img.selected:hover {opacity: 1.0;filter: alpha(opacity=100);}
table.plot_menu img.selected {opacity: 1.0;filter: alpha(opacity=100);}


.opaque00 {
	opacity: 1;
	filter: alpha(opacity=100);
}

.opaque50 {
	opacity: .5;
	filter: alpha(opacity=50);
}
.opaque80 {
	opacity: .2;
	filter: alpha(opacity=20);
}

input {border: 1px solid #777;xposition:relative}
.changed{color:red}
.error{color:red}

table.sbut {margin:5px;border-spacing:10px  10px;border-collapse: separate;width:100%}
table.but{margin:5px 0 ;width:140px;border:none;clear:right;float:right;border-collapse: separate;;border-spacing:3px  3px;}
table.but_edit{clear:both;float:right}



.but td{font-weight:600;font-size:85%;text-align:center;padding:3px 3px;cursor:pointer}
.but td.ok{background:#81c44b;border:1px solid #67ae2e;color:#f6f6f6}
.but td.ok:hover{background:#98ea56;color:#444}
.but td.disabled{background:#fff;border:1px solid #eee;color:#ddd;cursor:default}
.but td.nook{background:#d24242;border:1px solid #ba1111;color:#f6f6f6}
.but td.nook:hover{background:#ea6565;color:#444}



table.options{margin:0 ;float:right;border-collapse: separate;;border-spacing:3px  3px;font-size:100%;xfont-weight:600;}


.options td,.options span{font-size:85%;border:1px solid #aaa;color:#aaa;text-align:center;padding:0px 7px 0px 7px;cursor:pointer;}

.options td:hover,  .options span:hover{border:1px solid #777;color:#777}

.options td.selected,.options span.selected{background:#B665D2;border:1px solid #B665D2;color:#f6f6f6;text-decoration:none}
.options td.disabled{background:#fff;border:1px solid #eee;color:#ddd;cursor:default}
.options tr.title{}
.options tr.title td{background:#fff;border:none;cursor:default;color:#555;text-align:left}
.options tr.title td:hover{color:#555}

span.save,span.reset,span.undo,span.multiple,span.new,span.button ,span.small_button {margin-left:5px;border:1px solid #aaa;color:#aaa;text-align:center;padding:1px 6px 1px 6px;cursor:pointer;font-weight:800}
span.button:hover{color:#777;} 

span.small_button {;padding:0px 3px 0px 3px;font-weight:200;font-size:80%;color:#333;}

span.multiple {padding-left:25px;background:  url("../art/icons/application_cascade.png") no-repeat;background-position:2px 1.5px }
span.new {padding-left:20px;background:  url("../art/icons/new.png") no-repeat;background-position:2px 1.5px }





table.options_mini{margin:0 ;float:right;border-collapse: separate;;border-spacing:3px  3px;font-size:80%;font-weight:400;}


.options_mini td{border:1px solid #aaa;color:#888;text-align:center;padding:1px 2px 2px 2px;cursor:pointer}
.options_mini td:hover{border:1px solid #777;color:#777}
.options_mini td.selected{background:#B665D2;border:1px solid #B665D2;color:#f6f6f6}
.options_mini td.disabled{background:#fff;border:1px solid #eee;color:#ddd;cursor:default}





.edit_button td,td.editx{background:#dc4f58;border:1px solid #b52b34;color:#f4cdd0;}
.edit_button td:hover,td.editx:hover{background:#dc4f58;border:1px solid #b52b34;color:#f4cdd0;}

.show_options{color:#aaa;padding:0 5px 0 0;position:relative;bottom:0px;font-weight:800}
.show_options span{background:#33518d;margin-right:7px;cursor:pointer;border:1px solid #758fc4;padding:0px 3px;font-weight:800}
.show_options span:hover {color:#fff;}

.xshow_options span.selected {color:#f4e846;}
.show_options span.selected:hover {color:#ebe69f;}


.edit_block{clear:both;margin:0 auto;xwidth:800px;padding:0px 0px;margin:0;}


.plot_options{width:130px;float:right;text-align:right  }
.plot_options h3{text-align:left;font-weight:bold;margin-top:10px  }
.plot_options .other_options {clear:both;text-align:right;font-size:80%}
.plot_options .other_options table {float:right}
.plot_options .other_options table tr.title{border-bottom:1px solid #ccc;height:25px;vertical-align:bottom}
.plot_options .other_options table td{text-align:right;vertical-align:bottom}
.plot_options input {vertical-align:bottom}
.caca{ background:green;margin:0;margin:0 0  0 20px }

div.skinnedSelect {
background:red;
width:150px;
display:inline

}
div.skinnedSelect select {
opacity: .5;
filter: alpha(opacity = 0);
moz-opacity: 0;
width: 70px;
margin-left: -70px;
}
div.skinnedSelect .text {

}

div.skinnedSelect .text,
div.skinnedSelect select option
{
font-size: 11px;
color: #316D89;
}




/*Products pages*/


.top_bar{=padding:0 20px  }
.top_bar h1{padding:0 0px 10px 20px;font-size:150%}
.top_bar .nodetails{border:1px solid #ddd}
.top_bar .details{clear:both;padding:10px 20px ;border:1px solid #ddd;margin:auto;}

/*Common search div*/

.right_boxs{xclear:right;sfloat:right;border: 0px solid #ddd;text-align:right;padding:0;margin-top:3px}
.right_boxs form{ margin-bottom:5px }

.right_boxs div{clear:right;float:right}

div.general_options{padding:0 0 5px 0;color:#777}
div.general_options span{float:right;margin-left:15px;font-size:85%; font-weight:200;cursor:pointer;}
div.general_options span:hover{text-decoration:underline;color:#333}


.no_top_nav{position:relative;bottom:10px}



table.search {clear:both;float:right;margin-bottom:5px}
table.search td.label{width:100px;text-align:right;}
table.search td.form{width:325px}
table.search  search.input{  height:16px;color:#555;font-style: italic;padding-left:2px;width:300px}
table.search  .submitsearch {cursor:pointer;z-index:2;right:2px;position:relative;left:305px}






/*EDIT SECTION*/
.but {cursor:pointer }


/*Locations on product.tpl*/
table.locations {  margin:0;padding:0 }
table.locations td {margin:0;padding:0 2px;text-align:right ;vertical-align:top;border:0px solid black}
table.locations img {height:12px;}

/*Edit Locations/Stock on product.tpl*/
.manage_stock {padding:0 0px }
.manage_stock table{margin:0;padding:0  }
table.edit_location{ margin-top:20px }
.manage_stock .actions{  border-collapse: separate;;border-spacing:5px  0px;}
.manage_stock tr.buts td{ border:1px solid #ccc;padding:2px 2px;margin:0px 5px;border-spacing:0px 10px  }
table.edit_location tr{border-bottom:1px solid #ccc;}
table.edit_location td{padding:1px 15px 1px 15px;text-align:left }
table.edit_location td.selected{ background:#edf3ff;cursor:pointer }
table.edit_location tr.totals td {border-top:1px solid #000;border-bottom:none }
table.edit_location tr.totals {border-bottom:none }
.location_state { width:100% }

#manage_stock_desktop { margin:20px 0 0 0;padding:10px 20px;border:1px solid #ccc ;clear:both;}
#manage_stock_messages {margin: 0 0 0px 0 }
#manage_stock_engine {10px 0 0 0 }





.inputs_yellow input  { background:#fff889 }
.inputs_white input  { background:#ffffff }


table.submit_order{ float:right;text-align:right ;border-spacing:0px  3px;border-collapse: separate;}
table.submit_order td.but{ background:#238546;color:#fff;cursor:pointer;padding:2px 3px;text-align:center }


span.but{color:#bbb;cursor:pointer;padding:2px 3px;text-align:center ;border:1px solid #ccc}

span.xxxselected { background:#7296e1;color:#fff ;border:1px solid navy}
span.xxxselected:hover { background:#7296e1;color:#fff ;border:1px solid navy}
span.but:hover {border:1px solid #aaa; color:#999;}


span.but_disabled{cursor:normal;background:#fff;color:#777}
span.but_unselected{cursor:normal;background:#fff;color:#777}
span.small{font-size:80%}



.unselectable_text{
   -moz-user-select: none;
   -khtml-user-select: none;
   user-select: none;
}

.inbox ,.inbox_form{padding:0;margin:0 auto;}
.inbox tr {border-left:1px solid #777;border-right:1px solid #777; }
.inbox tr.bottom {border-bottom:1px solid #777;height:40px;}
.inbox tr.bottom  td { vertical-align:top; }
.inbox tr.tabs { border:none ;border-bottom:1px solid #777;height:30px;vertical-align:bottom}
.inbox tr.tabs td { vertical-align:bottom;padding:0 10px;margin:0 }
.inbox_form tr.buttons,.inbox tr.buttons,.edit tr.buttons { border:none ;height:30px}
.inbox_form tr.buttons,.inbox tr.buttons td, .edit tr.buttons td{ vertical-align:bottom }

 span.tab     { border:1px solid #aaa;border-bottom:1px solid #777;color:#aaa ;vertical-align: bottom;;padding:4px 4px 0 4px;margin:0;cursor:pointer;position}
span.tab.selected{ text-decoration:none;border:1px solid #777;color:#000;border-bottom:1px solid white}
.inbox_form  span.button,.inbox span.button,.edit span.button{border:1px solid #777;padding:4px 4px;cursor:pointer; }


#d_config table.tipo {height:8em;font-size:100%;font-weight:600;}
#d_config table.tipo{ ;border-collapse: separate;;border-spacing:10px 0px}
#d_config table.tipo div{padding:10px;margin:10px;border:1px solid #ddd;width:20em;height:4em}
#d_config table.options{ ;border-collapse: separate;;border-spacing:0px .25em;width:12em}

#d_config table.tipo td.selected {background:#daf8bb;cursor:pointer;color:#555; width:100px;border: 1px solid #ccc;text-align:center;vertical-align:middle }


.list_of_buttons{float:right;padding:0px 0px 0px 10px;;width:250px}
.list_of_buttons div{padding:2.5px 5px;border:1px solid #ccc;margin:3px 5px;cursor:pointer;width:100px;float:left}

.button{ cursor:pointer }

.like_a{cursor:pointer;}
.like_a:hover{text-decoration:underline}

.prod_sdesc{ font-weight:800;}

.rtext_rpp{cursor:pointer}

.no_valid{color:#999}

.addressform { font-size:9pt}
  .addressform .field { font-size:9pt}
  .addressform .label { font-size:9pt; text-align:right}
  .autocomplete {border-bottom:solid 1px #aaaaaa; border-left:solid 1px #aaaaaa; border-right:solid 1px #aaaaaa; background-color:#fafafa}
  .autocomplete A {text-decoration:none; color:#000000}
  .autocomplete .item {padding:5px; border-bottom:solid 1px #aaaaaa; background-color:#fafafa}
  .autocomplete .itemhover {padding:5px; border-bottom:solid 1px #aaaaaa; background-color:#f0f0f0}
  .autocomplete .itemlast {border-top:solid 1px #aaaaaa; background-color:#ffffff}



span.link{cursor:pointer}
.top_navigation span.selected{color:yellow;border:none;background:#6d84b4}


.filter_name{cursor:pointer}
.mix_currency{color:#777;font-style: italic;}

.match{font-weight:800}

div.contact_card {text-align:right;border:1px solid #ccc;padding:10px 10px 15px 10px;width: 250px;}
div.contact_card div.tels{margin-top:3px;font-size:80%;color:#3b5998}
div.contact_card div.address{margin-top:5px;font-size:70%}

td.margin_note{widht:20px;font-family:times;font-size:80%;padding-right:20px;padding-top:5px}


div.tabbed_container{border:1px solid #ccc;padding:20px 20px;clear:both;margin-bottom:20px}

ul.tabs{margin-left:20px;margin-top:10px}

.tabs span.item
{
color: #777;
background: #fff url(../left-tab.gif) left top no-repeat;
text-decoration: none;
padding-left: 10px;
border:1px solid #ddd;
border-bottom:1px solid #ccc;
cursor:pointer
}
.tabs span.option
{
border-bottom:1px solid #ddd;

}



.tabs span.selected
{
 color: #000;
 border:1px solid #999;
 border-bottom:1px solid #fff
}

.tabs span.item span
{
 background: url(../right-tab.gif) right top no-repeat;
 padding-right: 10px;
}

.tabs span.item, .tabs span.item span
{
display: block;
float: left
}

/* Hide from IE5-Mac \*/
.tabs span.item, .tabs span.item span
{
float: none
}
/* End hide */

.tabs span.item:hover
{
color: #000;
background: #fff url(../left-tab-hover.gif) left top no-repeat;
padding-left: 10px
}

.tabs span.item:hover span
{
background: url(../right-tab-hover.gif) right top no-repeat;
padding-right: 10px
}

.tabs ul
{
list-style: none;
padding: 0;
margin: 0
}

.tabs li
{
float: left;
margin: 0;
margin-right:5px;
position:relative;top:1px;
} 

.messages_block{padding:10px}

.branch {margin-top:10px;clear:left;font-style:italic; color:#666;font-size:85%}
.branch span{cursor:pointer}
.branch span.selected{font-weight:800;color:#555}
.product_info_sales_options{cursor:pointer}

.block_list{width:800px}
.block_list h2{border-bottom:1px solid #ddd;padding:10px 0 0 0}

.block_list div{font-size:90%;cursor:pointer;text-align:center;margin-right:20px;padding:10px 10px 5px 10px;margin-bottom:10px;width:120px;height:90px;float:left;border:0px solid #ddd}
.block_list img{width:120px;margin-top:4px}

.plot {width:930px;}
.plot_iframe {position:relative;left:-20px}

.currency_exchanged{font-style:italic}
.thumbnails{margin-top:10px}
.thumbnails div{height:160px;width:136px;float:left;text-align:center;margin-bottom:15px;padding:bottom:10px}
.thumbnails span{display:block;width:100%;text-align:center;margin-bottom:5px;color:red}

.thumbnails img{max-height:140px;max-width: 130px;align:center;margin-top:5px}
.diff_del{color:red}

.diff_ins{color:green}


.quick_button td{color:#fff;background:#334556;font-weight:800;border:2px solid #fff;cursor:pointer}
.quick_button td:hover{background:#131536;}

.table_type {margin-top:.6em}



.search_result{color:#777;width:98%;float:right}
.search_result tr.result{cursor:pointer;;border-top:1px solid #ccc;border-bottom:1px solid #aaa;padding:2px 10px}
.search_result tr.result:hover{background:#d2dffc}
.search_result .prod_sdesc{font-weight:100}
.search_result tr.result xtd{padding:2px 10px}
.search_result tr.result td.naked {display:none}
.search_result tr.selected {color:#fff;background:#7296e1;}
.main_search .col1{width: 250px}


.order_header{border-top:1px solid #777;border-bottom:1px solid #ccc;width:100%,padding:0;margin:0;float:right;margin-left:0px}
.order_actions{text-align:right;margin-top:3px;}

#yui-history-iframe {
  position:absolute;
  top:0; left:0;
  width:1px; height:1px; /* avoid scrollbars */
  visibility:hidden;
}


.edit_mini_button{cursor:pointer}

.splinter_cell{float:left;width:450px;font-size:75%;margin-right:18px;border:1px solid #e7e7e7;padding:5px;position:relative;left:15px;margin-bottom:10px}
.splinter_cell.double{width:930px}

#part_locations .button {width:16px;filter:alpha(opacity=40);
	-moz-opacity:0.4;
	-khtml-opacity: 0.4;
	opacity: 0.4;
}
#part_locations .button:hover {width:16px;filter:alpha(opacity=100);
	-moz-opacity:1;
	-khtml-opacity: 1;
	opacity: 1;
}


#part_locations .quantity{padding-right:10px;text-align:right}

.home_splinter_options span{color:#999;cursor:pointer}
.home_splinter_options span:hover{color:#777}
.home_splinter_options span.selected{color:#333;font-weight:100;text-decoration:none;}


span.state_details,search_sugestion{cursor:pointer;}
span.state_details.selected{color:#000 ;font-weight:600}
span.state_details:hover{color:#222}


a.state_details{cursor:pointer;}
a.state_details.selected{color:#000 ;font-weight:600}
a.state_details:hover{color:#000;text-decoration:none}

.export_csv_menu{}


.options_list td{ border:1px solid #999;text-align:center;cursor:pointer  }
.options_list td.selected{ background:#7296e1;color:white   }
.options_list td.empty{ border:none;cursor:default  }

span.todo,div.todo {background-color: #C8E02B}
div.todo {;padding:20px} 
