{*
<!--
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 28 March 2017 at 17:45:30 GMT+8, Cyberjaya, Malaysia
 Copyright (c) 2016, Inikoo

 Version 3
-->
*}


<span id="webpage_data" style="display:none" data-webpage_key="{$webpage->id}" data-customer_key="{$customer_key}" data-order_key="{$order_key}"></span>
<header id="header">
    <div id="topHeader">
        <div class="wrapper" style="position: relative">
            <div class="top_nav">
                <div class="container">
                    <div class="left">
                        <a href="index.php" id="logo"> </a>

                    </div>


                    <div class="right {if $webpage->get('Webpage Code')=='search.sys'}hide{/if} ">

                        <div style="float:right;background-color: black;height:30px;width: 30px ;text-align: center">
                            <i id="header_search_icon" class=" fa fa-search" style="color:#fff;font-size:20px;position: relative;top:4px;cursor: pointer;" aria-hidden="true"></i></div>
                        <input id="header_search_input"/>


                    </div>

                </div>
            </div>

        </div>

    </div>

    <div id="trueHeader">

        <div class="wrapper">

            <div class="" style="padding-right:10px">

                <nav class="menu_main2" stycle="float:left">


                    <div class="navbar yamm navbar-default">


                        <div id="navbar-collapse-1" class="navbar-collapse collapse">

                            <ul id="_columns" class="nav navbar-nav three">
                                {foreach from=$header_data.menu.columns item=column key=key}
                                    <li id="menu_column_{$key}" class="dropdown {if !$column.show}hide{/if} on _column {if $column.type=='three_columns'}yamm-fw  3_columns{else}single_column{/if}  ">
                                        <a href="" data-toggle="dropdown" class="dropdown-toggle ">
                                            {if $column.icon!=''}<i class="fa _column_label_icon {$column.icon} item_icon padding_right_5  " aria-hidden="true"></i>  {/if}<span>{$column.label}</span>
                                        </a>


                                        {if $column.type=='three_columns'}
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <div class="yamm-content">
                                                        <div class="row">
                                                            {foreach from=$column.sub_columns item=sub_column}
                                                                {if $sub_column.type=='items'}
                                                                    <ul class="col-sm-6 col-md-4 list-unstyled two">

                                                                        <li>
                                                                            <p>{if isset($sub_column.title)}{$sub_column.title}{/if}</p>
                                                                        </li>

                                                                        {foreach from=$sub_column.items item=item}
                                                                            <li class="item_li">
                                                                                <a href="{$item.url}"><i class="fa item_icon fa-fw {$item.icon}"></i> <span class="_item_label">{$item.label}</span></a>
                                                                            </li>
                                                                        {/foreach}

                                                                    </ul>
                                                                {elseif $sub_column.type=='text'}
                                                                    <ul class="col-sm-6 col-md-4 list-unstyled two">
                                                                        <li>
                                                                            <p>{$sub_column.title}</p>
                                                                        </li>
                                                                        <li class="dart">
                                                                            {if  $sub_column.url!=''}
                                                                                <a href="{$sub_column.url}"><img src="{if $sub_column.image==''}https://placehold.it/230x80{else}{$sub_column.image}{/if}" alt=""
                                                                                                                 class="rimg marb1"/></a>
                                                                            {else}
                                                                                <img src="{if $sub_column.image==''}https://placehold.it/230x80{else}{$sub_column.image}{/if}" alt="" class="rimg marb1"/>
                                                                            {/if}
                                                                            <span>{$sub_column.text}</span>
                                                                        </li>
                                                                    </ul>
                                                                {elseif $sub_column.type=='image'}
                                                                    <ul class="col-sm-6 col-md-4 list-unstyled two">
                                                                        <li>
                                                                            <p>{$sub_column.title}</p>
                                                                        </li>
                                                                        <li class="dart">
                                                                            {if  $sub_column.url!=''}
                                                                                <a href="{$sub_column.url}"><img src="{if $sub_column.image==''}https://placehold.it/230x160{else}{$sub_column.image}{/if}" alt=""
                                                                                                                 class="rimg marb1"/></a>
                                                                            {else}
                                                                                <img src="{if $sub_column.image==''}https://placehold.it/230x160{else}{$sub_column.image}{/if}" alt="" class="rimg marb1"/>
                                                                            {/if}
                                                                        </li>
                                                                    </ul>
                                                                {elseif $sub_column.type=='departments' or   $sub_column.type=='families' or  $sub_column.type=='web_departments' or   $sub_column.type=='web_families'}
                                                                    <ul class="col-sm-6 col-md-4 list-unstyled two _3c_col {$sub_column.type}">
                                                                        <li class="title">
                                                                            <p>{$sub_column.label}</p>
                                                                        </li>
                                                                        {foreach from=$store->get_categories({$sub_column.type},{$sub_column.page},'menu') item=item}
                                                                            <li class="item">
                                                                                <a href="{$item['url']}"><i class="fa fa-caret-right" style="margin-right:5px"></i>{$item['label']} {if $item['new']}<b
                                                                                            class="mitemnew">{t}New{/t}</b>{/if}</a>
                                                                            </li>
                                                                        {/foreach}

                                                                    </ul>
                                                                {elseif $sub_column.type=='empty'}
                                                                    <ul class="col-sm-6 col-md-4 list-unstyled two _3c_col {$sub_column.type} "></ul>
                                                                {/if}




                                                            {/foreach}

                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        {elseif $column.type=='single_column'}
                                            <ul class="dropdown-menu multilevel sortable" role="menu">


                                                {foreach from=$column.items item=item}
                                                    {if $item.type=='item'}
                                                        <li><a href="{$item['url']}">{$item['label']}</a></li>
                                                    {elseif $item.type=='submenu'}
                                                        <li class="dropdown-submenu mul"><a tabindex="-1" href="#">{$item['label']}</a>
                                                            <ul class="dropdown-menu sortable">
                                                                {foreach from=$item.sub_items item=sub_item}
                                                                    <li><a href="{$sub_item.url}">{$sub_item.label}</a></li>
                                                                {/foreach}


                                                            </ul>
                                                        </li>
                                                    {/if}
                                                {/foreach}


                                            </ul>
                                        {/if}
                                    </li>
                                {/foreach}


                                <li style="float: right">
                                    <div id="menu_control_panel" style=";float:right;z-index: 2000">
                                        {if $logged_in}
                                            <p>
                                                <span id="logout" class="button"><i class="fa fa-sign-out fa-flip-horizontal  " title="{t}Log out{/t}" aria-hidden="true"></i> <span>{if empty($labels._Logout)}{t}Log out{/t}{else}{$labels._Logout}{/if}</span></span>

                                                <a href="profile.sys" class="button"><i class="fa fa-user fa-flip-horizontal  " title="{t}Profile{/t}" aria-hidden="true"></i> <span>{if empty($labels._Profile)}{t}Profile{/t}{else}{$labels._Profile}{/if}</span></a>
                                                <a href="favourites.sys"><i class=" fa fa-heart fa-flip-horizontal button " style="cursor:pointer;margin-right:20px" title="{if empty($labels._Favourites)}{t}My favourites{/t}{else}{$labels._Favourites}{/if}"
                                                                            aria-hidden="true"></i></a>
                                                <a href="basket.sys" class="button">
                                                    <span id="header_order_products" class="ordered_products_number">{if isset($order)}{$order->get('Products')}{else}0{/if}</span>
                                                    <i style="padding-right:5px;padding-left:5px" class="fa fa-shopping-cart fa-flip-horizontal  " style="cursor:pointer" title="{if empty($labels._Basket)}{t}Basket{/t}{else}{$labels._Basket}{/if}" aria-hidden="true"></i>
                                                    {if !empty($website->settings['Info Bar Basket Amount Type']) and $website->settings['Info Bar Basket Amount Type']=='items_net'}
                                                        <span id="header_order_items_net_amount" class="order_items_net" style="padding-right:10px"
                                                              title="{if isset($labels._items_net) and $labels._items_net!=''}{$labels._items_net}{else}{t}Items Net{/t}{/if}">{if isset($order)}{$order->get('Items Net Amount')}{else}{$zero_money}{/if}</span>
                                                    {else}
                                                        <span id="header_order_total_amount" class="order_total" style="padding-right:10px"
                                                              title="{if isset($labels._total) and $labels._total!=''}{$labels._total}{else}{t}Total{/t}{/if}">{if isset($order)}{$order->get('Total')}{else}{$zero_money}{/if}</span>
                                                    {/if}
                                                </a>


                                            </p>
                                        {else}
                                            <p>
                                                <a href="/login.sys" class="button"><i class="fa fa-sign-in" aria-hidden="true"></i> {if empty($labels._Login)}{t}Login{/t}{else}{$labels._Login}{/if}</a>
                                                <a href="/register.sys" class="button"><i class="fa fa-user-plus" aria-hidden="true"></i> {if empty($labels._Register)}{t}Register{/t}{else}{$labels._Register}{/if}</a>
                                            </p>
                                        {/if}

                                    </div>
                                </li>


                            </ul>


                        </div>


                    </div>


                </nav>


            </div>

        </div>

    </div>

</header>

