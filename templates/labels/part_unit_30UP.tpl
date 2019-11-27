<script>

</script>

<table style="margin: 6.15mm">
    {for $row=1 to 10}
    <tr>
        {for $col=1 to 3}
            <td style="width: 65.9mm;height: 23.1mm">
                <table style='font-size:2.0mm; float:left;  font-family: Arial, " Helvetica Neue , Helvetica, sans-serif";' >
                    <tr>
                        <td style="height:100%;" valign="top">
                            <table style="height: 100%;">
                                <tr>
                                    <td>
                                        <b>{$part->get('Reference')}</b></td>
                                </tr>
                                <tr>
                                    <td style="border-bottom: 1px solid #000">{$part->get('Recommended Product Unit Name')}</td>
                                </tr>
                                <tr>
                                    <td style="height: 1mm"></td>
                                </tr>
                                <tr>
                                    <td>
                                        {if $part->get('Origin Country')!='' and $part->get('Part Origin Country Code')!=$account->get('Account Country Code')  }
                                            Imported from {$part->get('Origin Country')} by {$account->get('Name')}
                                            <br>
                                        {else}
                                            <span >{$account->get('Name')}</span>
                                            <br>
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {$account->get('Label Signature')}

                                    </td>
                                </tr>
                            </table>

                        </td>

                        <td >
                            <barcode code="{$part->get('Part Barcode Number')}"/>

                        </td>
                    </tr>

                </table>

            </td>
        {/for}


        </tr>
    {/for}

</table>
