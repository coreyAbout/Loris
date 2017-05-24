
{if $isDataEntryPerson}
<button class="btn btn-primary" onclick="location.href='{$baseurl}/create_timepoint/?candID={$candID}&identifier={$candID}'">Create time point</button>
{/if}
{if $isDataEntryPerson}
<button class="btn btn-primary" onclick="location.href='{$baseurl}/candidate_parameters/?candID={$candID}&identifier={$candID}'">Candidate Info</button>
{/if}
{if $isDataEntryPerson}
<button class="btn btn-primary" onclick="location.href='{$baseurl}/participant_status/?candID={$candID}&identifier={$candID}'">Participant Status</button>
{else}
Participant Status
{/if}
{if $isDataEntryPerson}
<button class="btn btn-primary" onclick="location.href='{$baseurl}/family_information/?candID={$candID}&identifier={$candID}'">Family Information</button>
{else}
Family Information
{/if}
{if $isDataEntryPerson}
<button class="btn btn-primary" onclick="location.href='{$baseurl}/drug_compliance/?candID={$candID}&identifier={$candID}'">Drug Compliance</button>
{else}
Drug Compliance
{/if}
{if $isDataEntryPerson}
<button class="btn btn-primary" onclick="location.href='{$baseurl}/family_history/?candID={$candID}&identifier={$candID}'">Family History</button>
{else}
Family History
{/if}
