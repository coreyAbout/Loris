<br />
<form method="post" name="family_information" id="family_information">
<table class="std">
    <tr><th colspan="9">Family Information</th></tr>

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
        <td nowrap="nowrap">{$form.related_participant_PSCID.label}</td>
        <td nowrap="nowrap">{$form.related_participant_PSCID.html}
        {if $form.errors.related_participant_PSCID}
            <span class='error'>{$form.errors.related_participant_PSCID}</span>
        {/if}
        </td>
    </tr>
    <tr>
        <td nowrap="nowrap">{$form.related_participant_status.label}</td>
        <td nowrap="nowrap">{$form.related_participant_status.html}
        {if $form.errors.related_participant_status}
            <span class='error'>{$form.errors.related_participant_status}</span>
        {/if}
        </td>
    </tr>
    <tr>
        <td nowrap="nowrap">{$form.related_participant_status_specify.label}</td>
        <td nowrap="nowrap">{$form.related_participant_status_specify.html}
        {if $form.related_participant_status_specify.error}
            <span class='error'>{$form.related_participant_status_specify.error}</span>
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

<td colspan="2">
<table class="fancytable" width="100%">
    <tr><th colspan="10">Family Information Details</th></tr>
    <tr>
        <td nowrap="nowrap"><b>Related Participant PSCID</b></td>
        <td nowrap="nowrap"><b>Related Participant CandID</b></td>
        <td nowrap="nowrap"><b>Related Participant Status Degree</b></td>
        <td nowrap="nowrap"><b>Related Participant Status</b></td>
        <td nowrap="nowrap"><b>Related Participant Status (specify)</b></td>
        <td nowrap="nowrap"><b>Data Entry Staff</b></td>
    </tr>

    {foreach from=$history_list item=row}
        <tr>
        {foreach from=$row item=value key=name}
	    <td>{$value}</td>
        {/foreach}
        </tr>
    {/foreach}

</table>
</td>

</table>
{$form.hidden}
</form>

