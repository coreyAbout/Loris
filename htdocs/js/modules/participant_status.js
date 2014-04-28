$(function () {
    $("[name=naproxen_eligibility_status]").on('change',function() {
	var v = $("[name=naproxen_eligibility_status]").val();
	if (v === "withdrawn" || v === "excluded") {
		alert("Please make sure that the Global Participant Status is appropriately set. (Is the participant still in the study as cohort?)");
	}
    });
    $("[name=probucol_eligibility_status]").on('change',function() {
        var v = $("[name=probucol_eligibility_status]").val(); 
        if (v === "withdrawn" || v === "excluded") {
                alert("Please make sure that the Global Participant Status is appropriately set. (Is the participant still in the study as cohort?)");
        }
    });
});
