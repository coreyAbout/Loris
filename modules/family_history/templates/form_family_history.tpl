<button type="button" name = "firstdegree" class = "btn btn-sm btn-primary" data-toggle="modal" data-target="#FirstDegreeModal">First Degree</button>

<div class="modal fade" id="FirstDegreeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="myModalLabel">First Degree</h3>
            </div>
            <form name = "FirstDegreeForm" id = "FirstDegreeForm" method = "POST" enctype="multipart/form-data" action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="family_member">Family Member</label>
                            <div class="col-xs-8">
                                <select name="family_member" id = "family_member" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $family_members item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="living_age">Age living</label>
                            <div class="col-xs-8">
                                <select name="living_age" id = "living_age" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $living_ages item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="death_age">Age of death</label>
                            <div class="col-xs-8">
                                <select name="death_age" id = "death_age" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $death_ages item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="death_cause">Cause of death</label>
                            <div class="col-xs-8">
                                <input type="text" size = "27" name="death_cause" id="death_cause" class="ui-corner-all form-fields form-control input-sm" />
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="death_cause_status"></label>
                            <div class="col-xs-8">
                                <select name="death_cause_status" id = "death_cause_status" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $death_cause_statuss item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="ad_dementia">AD-like dementia</label>
                            <div class="col-xs-8">
                                <select name="ad_dementia" id = "ad_dementia" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $ad_dementias item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="ad_dementia_age">Age of onset</label>
                            <div class="col-xs-8">
                                <select name="ad_dementia_age" id = "ad_dementia_age" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $ad_dementia_ages item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="diagnosis_history">Diagnosis history</label>
                            <div class="col-xs-8">
                                <input type="text" size = "27" name="diagnosis_history" id="diagnosis_history" class="ui-corner-all form-fields form-control input-sm" />
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="diagnosis_history_status"></label>
                            <div class="col-xs-8">
                                <select name="diagnosis_history_status" id = "diagnosis_history_status" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $diagnosis_history_statuss item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name = "action" id = "action" value = "firstdegree">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id = "FirstDegreeButton" role="button" aria-disabled="false">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


<button type="button" name = "ad_other" class = "btn btn-sm btn-primary" data-toggle="modal" data-target="#ADOtherModal">AD Other</button>

<div class="modal fade" id="ADOtherModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="myModalLabel">AD Other</h3>
            </div>
            <form name = "ADOtherForm" id = "ADOtherForm" method = "POST" enctype="multipart/form-data" action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="family_member">Family Member</label>
                            <div class="col-xs-8">
                                <select name="family_member" id = "family_member" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $family_members item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="parental_side">Parental Side</label>
                            <div class="col-xs-8">
                                <select name="parental_side" id = "parental_side" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $parental_sides item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="ad_dementia_age">Age of onset</label>
                            <div class="col-xs-8">
                                <select name="ad_dementia_age" id = "ad_dementia_age" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $ad_dementia_ages item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="living_age">Age living</label>
                            <div class="col-xs-8">
                                <select name="living_age" id = "living_age" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $living_ages item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="death_age">Age of death</label>
                            <div class="col-xs-8">
                                <select name="death_age" id = "death_age" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $death_ages item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="death_cause">Cause of death</label>
                            <div class="col-xs-8">
                                <input type="text" size = "27" name="death_cause" id="death_cause" class="ui-corner-all form-fields form-control input-sm" />
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="death_cause_status"></label>
                            <div class="col-xs-8">
                                <select name="death_cause_status" id = "death_cause_status" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $death_cause_statuss item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name = "action" id = "action" value = "adother">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id = "ADOtherButton" role="button" aria-disabled="false">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<button type="button" name = "memoryproblemothers" class = "btn btn-sm btn-primary" data-toggle="modal" data-target="#MemoryProblemOthersModal">Memory Problem Others</button>

<div class="modal fade" id="MemoryProblemOthersModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="myModalLabel">Memory Problem Others</h3>
            </div>
            <form name = "MemoryProblemOthersForm" id = "MemoryProblemOthersForm" method = "POST" enctype="multipart/form-data" action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="family_member">Family Member</label>
                            <div class="col-xs-8">
                                <select name="family_member" id = "family_member" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $family_members item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="parental_side">Parental Side</label>
                            <div class="col-xs-8">
                                <select name="parental_side" id = "parental_side" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $parental_sides item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="other_memory_problems">Other kind of memory problems</label>
                            <div class="col-xs-8">
                                <input type="text" size = "27" name="other_memory_problems" id="other_memory_problems" class="ui-corner-all form-fields form-control input-sm" />
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="other_memory_problems_status"></label>
                            <div class="col-xs-8">
                                <select name="other_memory_problems_status" id = "other_memory_problems_status" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $other_memory_problems_statuss item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="living_age">Age living</label>
                            <div class="col-xs-8">
                                <select name="living_age" id = "living_age" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $living_ages item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="death_age">Age of death</label>
                            <div class="col-xs-8">
                                <select name="death_age" id = "death_age" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $death_ages item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="death_cause">Cause of death</label>
                            <div class="col-xs-8">
                                <input type="text" size = "27" name="death_cause" id="death_cause" class="ui-corner-all form-fields form-control input-sm" />
                            </div>
                        </div>
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="death_cause_status"></label>
                            <div class="col-xs-8">
                                <select name="death_cause_status" id = "death_cause_status" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $death_cause_statuss item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name = "action" id = "action" value = "memoryproblemothers">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id = "MemoryProblemOthersButton" role="button" aria-disabled="false">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<br>
<br>

<div class="panel panel-primary">
        <div class="panel-heading">
            First Degree Details
        </div>
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <td><b>ID</b></td>
                    <td><b>CandID</b></td>
                    <td><b>Family member</b></td>
                    <td><b>Living age</b></td>
                    <td><b>Death age</b></td>
                    <td><b>Death cause</b></td>
                    <td><b>Death cause status</b></td>
                    <td><b>AD dementia</b></td>
                    <td><b>AD dementia age</b></td>
                    <td><b>Diagnosis history</b></td>
                    <td><b>Diagnosis history status</b></td>
                </tr>
            </thead>
            <tbody>
                {foreach from=$family_history_first_degree_details item=row}
                    <tr id="{$row.ID}">
                        {foreach from=$row item=value key=name}
                            <td id="{$name}">{$value}</td>
                        {/foreach}
                    </tr>
                {/foreach}
            </tbody>
        </table>
        </div>
        <br>
        <div class="panel-heading">
            AD Other Details
        </div>
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <td><b>ID</b></td>
                    <td><b>CandID</b></td>
                    <td><b>Family member</b></td>
                    <td><b>Parental side</b></td>
                    <td><b>AD dementia age</b></td>
                    <td><b>Living age</b></td>
                    <td><b>Death age</b></td>
                    <td><b>Death cause</b></td>
                    <td><b>Death cause status</b></td>
                </tr>
            </thead>
            <tbody>
                {foreach from=$family_history_ad_other_details item=row}
                    <tr id="{$row.ID}">
                        {foreach from=$row item=value key=name}
                            <td id="{$name}">{$value}</td>
                        {/foreach}
                    </tr>
                {/foreach}
            </tbody>
        </table>
        </div>
        <br>
        <div class="panel-heading">
            Memory Problem Other Details
        </div>
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <td><b>ID</b></td>
                    <td><b>CandID</b></td>
                    <td><b>Family member</b></td>
                    <td><b>Parental side</b></td>
                    <td><b>Other memory problems</b></td>
                    <td><b>Other memory problems status</b></td>
                    <td><b>Living age</b></td>
                    <td><b>Death age</b></td>
                    <td><b>Death cause</b></td>
                    <td><b>Death cause status</b></td>
                </tr>
            </thead>
            <tbody>
                {foreach from=$family_history_memory_problem_other_details item=row}
                    <tr id="{$row.ID}">
                        {foreach from=$row item=value key=name}
                            <td id="{$name}">{$value}</td>
                        {/foreach}
                    </tr>
                {/foreach}
            </tbody>
        </table>
        </div>
</div>

<br>

<div class="panel panel-primary">
        <div class="panel-heading">
            Delete entry
        </div>
        <div class="row">
        <br>
        <form name = "DeleteForm" id = "DeleteForm" method = "POST" enctype="multipart/form-data" action="">
                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="table">Table</label>
                            <div class="col-xs-8">
                                <select name="table" id = "table" class = "form-fields form-control input-sm">
                                <option value=""> </option>
                                    {foreach from = $tables item=val key=k}
                                        <option value={$k}>{$val}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-12 form-group">
                            <label class="col-xs-4" for="entry_ID">Entry ID</label>
                            <div class="col-xs-8">
                                <input type="text" size = "27" name="entry_ID" id="entry_ID" class="ui-corner-all form-fields form-control input-sm" />
                            </div>
                        </div>
                        <input type="hidden" name = "action" id = "action" value = "delete">
                        <div class="col-xs-12 form-group">
                            <div class="col-xs-8">
                                <button class="btn btn-primary" id = "DeleteButton" role="button" aria-disabled="false">Delete</button>
                            </div>
                        </div>
        </div>
        </form>
</div>