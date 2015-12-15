<table class="table table-hover table-primary table-bordered table-data_release dynamictable" border="0">
    <thead>
        {section name=header loop=$headers}
            <th class="info" nowrap="nowrap">
                    <a href="main.php?test_name=data_release&filter[order][field]={$headers[header].name}&filter[order][fieldOrder]={$headers[header].fieldOrder}" class = "sortHeaders">
                        {$headers[header].displayName}
                    </a>
            </th>
        {/section}
    </thead>
<tbody>

                            {section name=item loop=$items}
                            <tr>
                                {section name=piece loop=$items[item]}
                                    {if $items[item][piece].name != ""}
                                        <td>
{if $items[item][piece].name == 'file_name'}
<a href="AjaxHelper.php?Module=data_release&script=GetFile.php&File={$items[item][piece].value}" target="_blank" download="{$items[item][piece].value}">
                                                {$items[item][piece].value}
</a>
{else}
                                                {$items[item][piece].value}
{/if}
                                        </td>
                                    {/if}
                                {/section}
                            </tr>
                            {/section}

</tbody>
</table>
