<br />
<form method="post" name="drug_compliance" id="drug_compliance">

<div class="panel panel-primary">
    <div class="panel-heading">
        Drug Compliance
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
                <label class="col-sm-2">{$form.drug.label}</label>
                <div class="col-sm-8">
                    {$form.drug.html}
                        {if $form.errors.drug}
                            <span class='error'>{$form.errors.drug}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.drug_issued_date_group.label}</label>
                <div class="col-sm-8">
                    {$form.drug_issued_date_group.html}
                        {if $form.drug_issued_date_group.error}
                            <span class='error'>{$form.drug_issued_date_group.error}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.pills_issued_group.label}</label>
                <div class="col-sm-8">
                    {$form.pills_issued_group.html}
                        {if $form.pills_issued_group.error}
                            <span class='error'>{$form.pills_issued_group.error}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.drug_returned_date_group.label}</label>
                <div class="col-sm-8">
                    {$form.drug_returned_date_group.html}
                        {if $form.drug_returned_date_group.error}
                            <span class='error'>{$form.drug_returned_date_group.error}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.pills_returned_group.label}</label>
                <div class="col-sm-8">
                    {$form.pills_returned_group.html}
                        {if $form.pills_returned_group.error}
                            <span class='error'>{$form.pills_returned_group.error}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.visit_label.label}</label>
                <div class="col-sm-8">
                    {$form.visit_label.html}
                        {if $form.errors.visit_label}
                            <span class='error'>{$form.errors.visit_label}</span>
                        {/if}
                </div>
        </div>
        <br>
        <div class="row">
                <label class="col-sm-2">{$form.comments_group.label}</label>
                <div class="col-sm-8">
                    {$form.comments_group.html}
                        {if $form.coments_group.error}
                            <span class='error'>{$form.comments_group.error}</span>
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
            Naproxen Details
        </div>
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <td><b>Date Issued</b></td>
                    <td><b>Pills Issued</b></td>
                    <td><b>Date Returned</b></td>
                    <td><b>Pills Returned</b></td>
                    <td><b>Compliance</b></td>
                    <td><b>Behavioral Compliance</b></td>
                    <td><b>Visit Label</b></td>
                    <td><b>Data Entry Staff</b></td>
                </tr>
            </thead>
            <tbody>
                {foreach from=$history_list_naproxen item=row}
                    <tr>
                        {foreach from=$row item=value key=name}
                            <td>{$value}</td>
                        {/foreach}
                    </tr>
                {/foreach}
            </tbody>
        </table>
        </div>
    <br>
        <div class="panel-heading">
            Probucol Details
        </div>
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <td><b>Date Issued</b></td>
                    <td><b>Pills Issued</b></td>
                    <td><b>Date Returned</b></td>
                    <td><b>Pills Returned</b></td>
                    <td><b>Compliance</b></td>
                    <td><b>Behavioral Compliance</b></td>
                    <td><b>Visit Label</b></td>
                    <td><b>Data Entry Staff</b></td>
                </tr>
            </thead>
            <tbody>
                {foreach from=$history_list_probucol item=row}
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

