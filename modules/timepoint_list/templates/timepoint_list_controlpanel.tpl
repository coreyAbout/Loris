
{if $isDataEntryPerson}
<button class="btn btn-primary" onclick="location.href='main.php?test_name=create_timepoint&candID={$candID}&identifier={$candID}'">Create time point</button>
{/if}
{if $isDataEntryPerson}
<button class="btn btn-primary" onclick="location.href='main.php?test_name=candidate_parameters&candID={$candID}&identifier={$candID}'">Candidate Info</button>
{/if}
{if $isDataEntryPerson}
<button class="btn btn-primary" onclick="location.href='main.php?test_name=participant_status&candID={$candID}&identifier={$candID}'">Participant Status</button>
{else}
Participant Status
{/if}
{if $isDataEntryPerson}
<button class="btn btn-primary" onclick="location.href='main.php?test_name=family_information&candID={$candID}&identifier={$candID}'">Family Information</button>
{else}
Family Information
{/if}
{if $isDataEntryPerson}
<button class="btn btn-primary" onclick="location.href='main.php?test_name=drug_compliance&candID={$candID}&identifier={$candID}'">Drug Compliance</button>
{else}
Drug Compliance
{/if}
{if $isDataEntryPerson}
<button class="btn btn-primary" onclick="location.href='main.php?test_name=family_history&candID={$candID}&identifier={$candID}'">Family History</button>
{else}
Family History
{/if}

