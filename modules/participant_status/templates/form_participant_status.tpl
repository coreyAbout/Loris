<br />
<form method="post" name="participant_status" id="participant_status">

<div class="panel panel-primary">
    <div class="panel-heading">
        Participant Status
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
                    {$form.entry_staff.html}
                </div>
        </div>
        <div class="row">
                <label class="col-sm-2">{$form.data_entry_date.label}</label>
                <div class="col-sm-8">
                    {$form.data_entry_date.html}
                </div>
        </div>

        <div class="row">
                <label class="col-sm-2">{$form.participant_status.label}</label>
                <div class="col-sm-8">
                    {$form.participant_status.html}
                        {if $form.errors.participant_status}
                            <span class='error'>{$form.errors.participant_status}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.reason_specify_group.label}</label>
                <div class="col-sm-8">
                    {$form.reason_specify_group.html}
                        {if $form.reason_specify_group.error}
                            <span class='error'>{$form.reason_specify_group.error}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.withdrawal_reasons.label}</label>
                <div class="col-sm-8">
                    {$form.withdrawal_reasons.html}
                        {if $form.withdrawal_reasons.error}
                            <span class='error'>{$form.withdrawal_reasons.error}</span>
                        {/if}
                </div>
        </div>
        <div class="row">
                <label class="col-sm-2">{$form.withdrawal_reasons_other_specify_group.label}</label>
                <div class="col-sm-8">
                    {$form.withdrawal_reasons_other_specify_group.html}
                        {if $form.withdrawal_reasons_other_specify_group.error}
                            <span class='error'>{$form.withdrawal_reasons_other_specify_group.error}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.dna_collected_eligibility.label}</label>
                <div class="col-sm-8">
                    {$form.dna_collected_eligibility.html}
                        {if $form.errors.dna_collected_eligibility}
                            <span class='error'>{$form.errors.dna_collected_eligibility}</span>
                        {/if}
                </div>
        </div>
        <div class="row">
                <label class="col-sm-2">{$form.dna_request_destroy.label}</label>
                <div class="col-sm-8">
                    {$form.dna_request_destroy.html}
                        {if $form.dna_request_destroy.error}
                            <span class='error'>{$form.dna_request_destroy.error}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.dna_destroy_date_group.label}</label>
                <div class="col-sm-8">
                    {$form.dna_destroy_date_group.html}
                        {if $form.dna_destroy_date_group.error}
                            <span class='error'>{$form.dna_destroy_date_group.error}</span>
                        {/if}
                </div>
        </div>
    </div>
    <div class="panel-heading">
        Naproxen Participant Status
    </div>
    <div class="panel-body">
        <div class="row">
                <label class="col-sm-2">{$form.naproxen_eligibility.label}</label>
                <div class="col-sm-8">
                    {$form.naproxen_eligibility.html}
                        {if $form.errors.naproxen_eligibility}
                            <span class='error'>{$form.errors.naproxen_eligibility}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.naproxen_eligibility_reason_specify_group.label}</label>
                <div class="col-sm-8">
                    {$form.naproxen_eligibility_reason_specify_group.html}
                        {if $form.naproxen_eligibility_reason_specify_group.error}
                            <span class='error'>{$form.naproxen_eligibility_reason_specify_group.error}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.naproxen_eligibility_status.label}</label>
                <div class="col-sm-8">
                    {$form.naproxen_eligibility_status.html}
                        {if $form.errors.naproxen_eligibility_status}
                            <span class='error'>{$form.errors.naproxen_eligibility_status}</span>
                        {/if}
                </div>
        </div>
        <div class="row">
                <label class="col-sm-2">{$form.naproxen_excluded_reason_specify_group.label}</label>
                <div class="col-sm-8">
                    {$form.naproxen_excluded_reason_specify_group.html}
                        {if $form.naproxen_excluded_reason_specify_group.error}
                            <span class='error'>{$form.naproxen_excluded_reason_specify_group.error}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.naproxen_withdrawal_reasons.label}</label>
                <div class="col-sm-8">
                    {$form.naproxen_withdrawal_reasons.html}
                        {if $form.naproxen_withdrawal_reasons.error}
                            <span class='error'>{$form.naproxen_withdrawal_reasons.error}</span>
                        {/if}
                </div>
        </div>
        <div class="row">
                <label class="col-sm-2">{$form.naproxen_withdrawal_reasons_other_specify_group.label}</label>
                <div class="col-sm-8">
                    {$form.naproxen_withdrawal_reasons_other_specify_group.html}
                        {if $form.naproxen_withdrawal_reasons_other_specify_group.error}
                            <span class='error'>{$form.naproxen_withdrawal_reasons_other_specify_group.error}</span>
                        {/if}
                </div>
        </div>
    </div>
    <div class="panel-heading">
        Probucol Participant Status
    </div>
    <div class="panel-body">
        <div class="row">
                <label class="col-sm-2">{$form.probucol_eligibility.label}</label>
                <div class="col-sm-8">
                    {$form.probucol_eligibility.html}
                        {if $form.errors.probucol_eligibility}
                            <span class='error'>{$form.errors.probucol_eligibility}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.probucol_eligibility_reason_specify_group.label}</label>
                <div class="col-sm-8">
                    {$form.probucol_eligibility_reason_specify_group.html}
                        {if $form.probucol_eligibility_reason_specify_group.error}
                            <span class='error'>{$form.probucol_eligibility_reason_specify_group.error}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.probucol_eligibility_status.label}</label>
                <div class="col-sm-8">
                    {$form.probucol_eligibility_status.html}
                        {if $form.errors.probucol_eligibility_status}
                            <span class='error'>{$form.errors.probucol_eligibility_status}</span>
                        {/if}
                </div>
        </div>
        <div class="row">
                <label class="col-sm-2">{$form.probucol_excluded_reason_specify_group.label}</label>
                <div class="col-sm-8">
                    {$form.probucol_excluded_reason_specify_group.html}
                        {if $form.probucol_excluded_reason_specify_group.error}
                            <span class='error'>{$form.probucol_excluded_reason_specify_group.error}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.probucol_withdrawal_reasons.label}</label>
                <div class="col-sm-8">
                    {$form.probucol_withdrawal_reasons.html}
                        {if $form.probucol_withdrawal_reasons.error}
                            <span class='error'>{$form.probucol_withdrawal_reasons.error}</span>
                        {/if}
                </div>
        </div>
        <div class="row">
                <label class="col-sm-2">{$form.probucol_withdrawal_reasons_other_specify_group.label}</label>
                <div class="col-sm-8">
                    {$form.probucol_withdrawal_reasons_other_specify_group.html}
                        {if $form.probucol_withdrawal_reasons_other_specify_group.error}
                            <span class='error'>{$form.probucol_withdrawal_reasons_other_specify_group.error}</span>
                        {/if}
                </div>
        </div>
    </div>
    <div class="panel-heading">
        Participant Status Data Changed
    </div>
    <div class="panel-body">
        <div class="row">
                <label class="col-sm-2">{$form.data_changed_date.label}</label>
                <div class="col-sm-8">
                    {$form.data_changed_date.html}
                        {if $form.errors.data_changed_date}
                            <span class='error'>{$form.errors.data_changed_date}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.only_DNA.label}</label>
                <div class="col-sm-8">
                    {$form.only_DNA.html}
                        {if $form.errors.only_DNA}
                            <span class='error'>{$form.errors.only_DNA}</span>
                        {/if}
                </div>
        </div>
    </div>
    <br>
        {if not $success}
            <input class="btn btn-sm btn-primary col-sm-offset-2" name="fire_away" value="Save" type="submit" />
        {/if}
            <input class="btn btn-sm btn-primary" onclick="location.href='main.php?test_name=timepoint_list&candID={$candID}'" value="Return to profile" type="button" />

    <br>
    <br>
    <div class="panel-heading">
        Participant Status History
    </div>
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <td><b>Global</b></td>
                    <td><b>NAP Eligibility</b></td>
                    <td><b>NAP Excluded Reason</b></td>
                    <td><b>NAP Status</b></td>
                    <td><b>PRO Eligibility</b></td>
                    <td><b>PRO Excluded Reason</b></td>
                    <td><b>PRO Status</b></td>
                    <td><b>Data Entry Staff</b></td>
                    <td><b>Status Changed Date</b></td>
                    <td><b>Last Data Entry Date</b></td>
                </tr>
            </thead>
            <tbody>
                {foreach from=$history_list item=row}
                    <tr>
                        {foreach from=$row item=value key=name}
                            {if $value == 'active'}
                                <td>Active</td>
                            {elseif $value == 'stop_medication_active'}
                                <td>Stop Medication Active</td>
                            {elseif $value == 'withdrawn'}
                                <td>Withdrawn</td>
                            {elseif $value == 'excluded'}
                                <td>Excluded</td>
                            {elseif $value == 'death'}
                                <td>Death</td>
                            {elseif $value == 'completed'}
                                <td>Completed</td>
                            {elseif $value == 'stop_medication_completed'}
                                <td>Stop Medication Completed</td>
                            {else}
                                <td>{$value}</td>
                            {/if}
                        {/foreach}
                    </tr>
                {/foreach}
            </tbody>
        </table>
        </div>

</div>
{$form.hidden}
</form>
