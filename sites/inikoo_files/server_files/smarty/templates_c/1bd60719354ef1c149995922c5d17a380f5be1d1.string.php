<?php /* Smarty version Smarty-3.1.5, created on 2013-10-07 16:27:21
         compiled from "1bd60719354ef1c149995922c5d17a380f5be1d1" */ ?>
<?php /*%%SmartyHeaderCode:21385034605252c4c9a2d577-60168730%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1bd60719354ef1c149995922c5d17a380f5be1d1' => 
    array (
      0 => '1bd60719354ef1c149995922c5d17a380f5be1d1',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '21385034605252c4c9a2d577-60168730',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'page' => 0,
    'see_also' => 0,
    'found_in' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_5252c4c9ae66e',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5252c4c9ae66e')) {function content_5252c4c9ae66e($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/Users/raul/dw/sites/inikoo_files/external_libs/Smarty/plugins/block.t.php';
?><div id="top_bar">
                        <img style="float:left" id="top_bar_logo" src="public_image.php?id=70789" />
                        <div  style="float:right">
                        <?php echo $_smarty_tpl->tpl_vars['page']->value->display_top_bar();?>

                        </div>
                    </div>
                    <div id="header" style="padding:0;margin:0;position:relative;z-index:3">
                    <div style="cursor:pointer;width:130px;height:60px" onclick="window.location='index.php'"></div>
                    
                        <div id="search">
                            <?php echo $_smarty_tpl->tpl_vars['page']->value->display_search();?>

                        </div>
                        <h1 id="header_title" ><?php echo $_smarty_tpl->tpl_vars['page']->value->display_title();?>
</h1>
                        <div id="menu_bar"><?php echo $_smarty_tpl->tpl_vars['page']->value->display_menu();?>
</div>
                    </div>

<div id="bottom_bar" style="position:relative;z-index:2;<?php if ($_smarty_tpl->tpl_vars['page']->value->get('Number See Also Links')==0&&$_smarty_tpl->tpl_vars['page']->value->get('Number Found In Links')==0){?>display:none<?php }?>" >
	<?php if ($_smarty_tpl->tpl_vars['page']->value->get('Number See Also Links')){?> 
	<div id="see_also">
		<div id="see_also_label">
			<span><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
See also<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</span>
		</div>
		<?php  $_smarty_tpl->tpl_vars['see_also'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['see_also']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['page']->value->get_see_also(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['see_also']->key => $_smarty_tpl->tpl_vars['see_also']->value){
$_smarty_tpl->tpl_vars['see_also']->_loop = true;
?><?php echo $_smarty_tpl->tpl_vars['see_also']->value['link'];?>
 <?php } ?> 
	</div>
	<?php }?> 
	<?php if ($_smarty_tpl->tpl_vars['page']->value->get('Number Found In Links')){?> 
	<div id="branch">
		<div id="parent_branch">
			<table>
				<?php  $_smarty_tpl->tpl_vars['found_in'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['found_in']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['page']->value->get_found_in(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['found_in']->key => $_smarty_tpl->tpl_vars['found_in']->value){
$_smarty_tpl->tpl_vars['found_in']->_loop = true;
?> 
				<tr>
					<td> <?php echo $_smarty_tpl->tpl_vars['found_in']->value['link'];?>
 </td>
				</tr>
				<?php } ?> 
			</table>
		</div>

	
	</div>
	<?php }?>
	<div style="clear:both">
	</div>
</div><?php }} ?>