<br />
<form method="post" name="family_information" id="family_information">
<div class="panel panel-primary">
    <div class="panel-heading">
        Family Information
    </div>
    <div class="panel-body">
        <div class="row">
                <label class="col-sm-2">{$form.pscid.label}</label>
                <div class="col-sm-8">
                    {$pscid}
                </div>
        </div>
        <div class="row">
                <label class="col-sm-2">{$form.entry_staff.label}</label>
                <div class="col-sm-8">
                    {$entry_staff}
                </div>
        </div>
        <div class="row">
                <label class="col-sm-2">{$form.related_participant_PSCID.label}</label>
                <div class="col-sm-8">
                    {$form.related_participant_PSCID.html}
                        {if $form.errors.related_participant_PSCID}
                            <span class='error'>{$form.errors.related_participant_PSCID}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.related_participant_status.label}</label>
                <div class="col-sm-8">
                    {$form.related_participant_status.html}
                        {if $form.errors.related_participant_status}
                            <span class='error'>{$form.errors.related_participant_status}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.related_participant_status_specify.label}</label>
                <div class="col-sm-8">
                    {$form.related_participant_status_specify.html}
                        {if $form.errors.related_participant_status_specify}
                            <span class='error'>{$form.errors.related_participant_status_specify}</span>
                        {/if}
                </div>
        </div>
        <br>
        {if not $success}
            <input class="btn btn-sm btn-primary col-sm-offset-2" name="fire_away" value="Save" type="submit" />
        {/if}
            <input class="btn btn-sm btn-primary" onclick="location.href='main.php?test_name=timepoint_list&candID={$candID}'" value="Return to profile" type="button" />
    </div>
    <br>
        <div class="panel-heading">
            Family Information Details
        </div>
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <td><b>Related Participant PSCID</b></td>
                    <td><b>Related Participant CandID</b></td>
                    <td><b>Related Participant Status Degree</b></td>
                    <td><b>Related Participant Status</b></td>
                    <td><b>Related Participant Status (specify)</b></td>
                    <td><b>Data Entry Staff</b></td>
                </tr>
            </thead>
            <tbody>
                {foreach from=$history_list item=row}
                    <tr>
                        {foreach from=$row item=value key=name}
                            <td>{$value}</td>
                        {/foreach}
                    </tr>
                {/foreach}
            </tbody>
        </table>
        </div>
</div>

{$form.hidden}
</form>
