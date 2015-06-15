<br />
<form method="post" name="drug_compliance" id="drug_compliance">
<table class="std">
    <tr><th colspan="9">Drug Compliance</th></tr>

    <tr>
        <td nowrap="nowrap">{$form.pscid.label}</td>
        <td nowrap="nowrap">{$pscid}
        </td>
    </tr>
    <tr>
        <td nowrap="nowrap">{$form.entry_staff.label}</td>
        <td nowrap="nowrap">{$entry_staff}
        </td>
    </tr>
    <tr>
        <td nowrap="nowrap">{$form.drug.label}</td>
        <td nowrap="nowrap">{$form.drug.html}
        {if $form.errors.drug}
            <span class='error'>{$form.errors.drug}</span>
        {/if}
        </td>
    </tr>
    <tr>
        <td nowrap="nowrap">{$form.drug_issued_date_group.label}</td>
        <td nowrap="nowrap">{$form.drug_issued_date_group.html}
        {if $form.drug_issued_date_group.error}
            <span class='error'>{$form.drug_issued_date_group.error}</span>
        {/if}
        </td>
    </tr>
    <tr>
        <td nowrap="nowrap">{$form.pills_issued_group.label}</td>
        <td nowrap="nowrap">{$form.pills_issued_group.html}
        {if $form.pills_issued_group.error}
            <span class='error'>{$form.pills_issued_group.error}</span>
        {/if}
        </td>
    </tr>
    <tr>
        <td nowrap="nowrap">{$form.drug_returned_date_group.label}</td>
        <td nowrap="nowrap">{$form.drug_returned_date_group.html}
        {if $form.drug_returned_date_group.error}
            <span class='error'>{$form.drug_returned_date_group.error}</span>
        {/if}
        </td>
    </tr>
    <tr>
        <td nowrap="nowrap">{$form.pills_returned_group.label}</td>
        <td nowrap="nowrap">{$form.pills_returned_group.html}
        {if $form.pills_returned_group.error}
            <span class='error'>{$form.pills_returned_group.error}</span>
        {/if}
        </td>
    </tr>
    <tr>
        <td nowrap="nowrap">{$form.visit_label.label}</td>
        <td nowrap="nowrap">{$form.visit_label.html}
        {if $form.errors.visit_label}
            <span class='error'>{$form.errors.visit_label}</span>
        {/if}
        </td>
    </tr>


        <tr>
        <td nowrap="nowrap">&nbsp;</td>
                <td nowrap="nowrap" colspan="2">
    {if not $success}
        <input class="button" name="fire_away" value="Save" type="submit" />
    {/if}
        <input class="button" onclick="location.href='main.php?test_name=timepoint_list&candID={$candID}'" value="Return to profile" type="button" />
        </td>
        </tr>

<tr>
<td colspan="2">
<table class="fancytable" width="100%">
    <tr><th colspan="10">Naproxen Details</th></tr>
    <tr>
        <td nowrap="nowrap"><b>Date Issued</b></td>
        <td nowrap="nowrap"><b>Pills Issued</b></td>
        <td nowrap="nowrap"><b>Date Returned</b></td>
        <td nowrap="nowrap"><b>Pills Returned</b></td>
        <td nowrap="nowrap"><b>Visit Label</b></td>
        <td nowrap="nowrap"><b>Compliance</b></td>
        <td nowrap="nowrap"><b>Behavioral Compliance</b></td>
        <td nowrap="nowrap"><b>Data Entry Staff</b></td>
    </tr>

    {foreach from=$history_list_naproxen item=row}
        <tr>
        {foreach from=$row item=value key=name}
	    <td>{$value}</td>
        {/foreach}
        </tr>
    {/foreach}

</table>
</td>
</tr>

<tr>
<td colspan="2">
<table class="fancytable" width="100%">
    <tr><th colspan="10">Probucol Details</th></tr>
    <tr>
        <td nowrap="nowrap"><b>Date Issued</b></td>
        <td nowrap="nowrap"><b>Pills Issued</b></td>
        <td nowrap="nowrap"><b>Date Returned</b></td>
        <td nowrap="nowrap"><b>Pills Returned</b></td>
        <td nowrap="nowrap"><b>Visit Label</b></td>
        <td nowrap="nowrap"><b>Compliance</b></td>
        <td nowrap="nowrap"><b>Behavioral Compliance</b></td>
        <td nowrap="nowrap"><b>Data Entry Staff</b></td>
    </tr>

    {foreach from=$history_list_probucol item=row}
        <tr>
        {foreach from=$row item=value key=name}
            <td>{$value}</td>
        {/foreach}
        </tr>
    {/foreach}

</table>
</td>
</tr>

</table>
{$form.hidden}
</form>

