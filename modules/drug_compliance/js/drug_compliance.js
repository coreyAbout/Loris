function save() {
    
    $('.update').
            bind('blur',function(event){
                event.stopImmediatePropagation();
                id = $(this).closest('tr').attr('id');
                field = event.target.id;
                value = $(this).text();
                $.get("AjaxHelper.php?Module=drug_compliance&script=update_drug_compliance.php&id=" + id + "&field=" + field + "&value=" + value, function(data) {});
            }
    ).keypress(function(e) {
        if(e.which === 13) { // Determine if the user pressed the enter button
            $(this).blur();
        }
    });

};

$(function(){
    save();
});
