<!-- 
About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 23 May 2016 at 08:52:51 CEST, Plane (Paris-Malaga)

 Copyright (c) 2016, Inikoo

 Version 3.0
-->


<div class="item">
    <div class="question">
        <i class="fa fa-caret-right bullet fw"></i> {t}How to add new supplier's part?{/t}
    </div>
    <div class="answer hide">
        <p>
            {t}Click in the<i class="fa fa-plus"></i>icon at the table header{/t}
        </p>
    </div>
</div>
<div class="item">
    <div class="question">
        <i class="fa fa-caret-right bullet fw"></i> {t}How to add supplier's parts in bulk?{/t}
    </div>
    <div class="answer hide">
        <p>
            {t}Click in the
                <i class="fa fa-upload"></i>
                icon at the table header to upload a excel or a CSV file with the following fields{/t} — <i
                    class="fa fa-file-excel-o"></i> <a title="{t}You can use this file as template{/t}"
                                                       href="/upload_arrangement.php?object=supplier_part&parent={$object}&parent_key={$key}"> {t}template{/t}</a>
            —
        </p>
        <ul>
            <li><b>{t}Supplier's SKU{/t}</b> <i>({t}required{/t},{t}unique per supplier{/t})</i> [{t}string{/t}]</li>
            <li><b>{t}Part reference{/t}</b> <i>({t}required{/t},{t}unique{/t})</i> [{t}string{/t}]</li>
            <li><b>{t}Part barcode{/t}</b> <i>({t}optional{/t})</i> [12 or 13 digits]
                <i>{t}or{/t}</i> {t}NEXT for next available barcode{/t}</li>
            <li><b>{t}Minimum order{/t}</b> <i>({t}required{/t})</i> [{t}number{/t}
                ] {t}minimum number cartons supplier willing to sell{/t}</li>
            <li><b>{t}Average delivery time{/t}</b> <i>({t}required{/t})</i> [{t}number{/t}] (days)</li>
            <li><b>{t}Carton CBM{/t}</b> <i>({t}optional{/t}) [{t}number{/t}]</i> ({t}Cubic meters per carton{/t})</li>
            <li><b>{t}Unit cost{/t}</b> <i>({t}required{/t})</i> [{t}amount (local supplier's currency){/t}]</li>
            <li><b>{t}Unit extra costs{/t}</b> <i>({t}required{/t})</i> [{t}amount (local supplier's currency){/t}
                <i>{t}or{/t}</i> {t}percentage{/t}]
            </li>
            <li><b>{t}Unit price{/t}</b> <i>({t}required{/t})</i> [{t}amount{/t} ({$account->get('Currency')})
                <i>{t}or{/t}</i> {t}margin{/t}]
            </li>
            <li><b>{t}Unit RRP{/t}</b> <i>({t}required{/t})</i> [{t}amount{/t} ({$account->get('Currency')})
                <i>{t}or{/t}</i> {t}margin{/t}]
            </li>
            <li><b>{t}Units per outer{/t}</b> <i>({t}required{/t})</i> [{t}number{/t}]</li>
            <li><b>{t}Outers per carton{/t}</b> <i>({t}required{/t})</i> [{t}number{/t}]</li>

            <li><b>{t}Unit description{/t}</b> <i>({t}required{/t})</i> [{t}string{/t}]</li>
            <li><b>{t}Unit weight{/t}</b> <i>({t}optional{/t})</i> [{t}numeric{/t}] ({t}Kilograms{/t})</li>
            <li><b>{t}Unit dimensions{/t}</b> <i>({t}optional{/t})</i> [{t}L x W x H{/t}] ({t}centimeters{/t})</li>
            <li><b>{t}SKO description{/t}</b> <i>({t}required{/t})</i> [{t}string{/t}]</li>
            <li><b>{t}SKO weight{/t}</b> <i>({t}optional{/t})</i> [{t}numeric{/t}] ({t}Kilograms{/t})</li>
            <li><b>{t}SKO dimensions{/t}</b> <i>({t}optional{/t})</i> [{t}L x W x H{/t}] ({t}centimeters{/t})</li>

            <li><b>{t}Materials/Ingredients{/t}</b> <i>({t}optional{/t})</i> [{t}comma separated strings{/t}]</li>
            <li><b>{t}Tariff code{/t}</b> <i>({t}optional{/t})</i> [{t}string{/t}]</li>
            <li><b>{t}Duty rate{/t}</b> <i>({t}optional{/t})</i> [{t}string{/t}]</li>

            <li><b>{t}UN number{/t}</b> <i>({t}optional{/t})</i> [{t}string{/t}]</li>
            <li><b>{t}UN class{/t}</b> <i>({t}optional{/t})</i> [{t}string{/t}]</li>
            <li><b>{t}Packing group{/t}</b> <i>({t}optional{/t})</i> [{t}string{/t}]</li>
            <li><b>{t}Proper shipping name{/t}</b> <i>({t}optional{/t})</i> [{t}string{/t}]</li>
            <li><b>{t}Hazard indentification number{/t}</b> <i>({t}optional{/t})</i> [{t}string{/t}]</li>


        </ul>

    </div>
</div>
