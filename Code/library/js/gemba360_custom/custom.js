/**
* Custome javascript functions relative to Gemba360 features
*
* @author    Kovid BioAnalytics <info@kovidbioanalytics>
*/

// declaring rowCounter as a global variable in order to set this as ID attribute to newly created TR
var rowCounter = 1;

$(function(){

	// to set the height of all textareas with class as acn_textarea same as that of the content within each textarea
	$(".acn_textarea").each(function(){
		$(this).height($(this).prop("scrollHeight"));
	});

	/*
	*  Function to add new row in Advocacy case note table 
	*/
	$("#add_id").on("click", function(){
		var acn_date = $("#acn_date_id").val();
		var acn_type = $("#acn_type_id").val();
		var acn_note = $("#acn_note_id").val();
		var acn_action = $("#acn_action_id").val();
		var acn_duration = $("#acn_duration_id").val();

		var trID = "new_acn_tr_id" + rowCounter;

		var trTag = "<tr id="+trID+" style='border-bottom:2px solid #000;' >"
					+ "<td style='padding: 5px;'>"
					+ "<input type='text' class='form-control datepicker' readonly='readonly' name='acn_date[]' value='"+acn_date+"'></td>"
					+ "<td style='padding: 5px;'>"
					+ "<input type='text' class='form-control' name='acn_type[]' value='"+acn_type.trim()+"'></td>"
					+ "<td style='padding: 5px;'>"
					+ "<textarea class='form-control acn_textarea' name='acn_note[]' >"+acn_note.trim()+"</textarea></td>"
					+ "<td style='padding: 5px;'>"
					+ "<input type='text' class='form-control' name='acn_action[]' value='"+acn_action.trim()+"'></td>"
					+ "<td style='padding: 5px;'>"
					+ "<input type='text' class='form-control' name='acn_duration[]' value='"+acn_duration.trim()+"'></td>"
					+ "<td style='text-align:center; vertical-align:middle;'><i class='fa fa-times fa-lg' onclick='removeTr(\""+trID+"\",\"add\")' title='Delete Advocacy Case Note' style='cursor: pointer;font-size: 30px'></i></td></tr>";

		$(trTag).insertAfter($('#acn_tr_id'));

		rowCounter++;

		//getting current date to set as a default value to Advocacy Date field
		var today = new Date();
		var dd = String(today.getDate()).padStart(2, '0');
		var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
		var yyyy = today.getFullYear();

		today = dd + '-' + mm + '-' + yyyy;

		$("#acn_date_id").val(today);
		$("#acn_type_id").val("");
		$("#acn_note_id").val("");
		$("#acn_action_id").val("");
		$("#acn_duration_id").val("");

		// call to applyDatetimepicker function in order to apply dateptimepicker on respective date field
		applyDatetimepicker('datepicker');

		//call to adjustTextareaHeight function to set the height of textarea same as that of the content within it
		adjustTextareaHeight('acn_textarea');
	});

});

// function to delete the added row from table
function removeTr(tr_id, check){
	if(check == "add"){
		if(confirm("Are you sure you want to delete this row?")){
			$("#"+tr_id).remove();
		}
	}else{

	}
}

// to set the height of all textareas with class as acn_textarea same as that of the content within each textarea whenever new textarea is appended anywhere in the page
function adjustTextareaHeight(className){
	$(".acn_textarea").each(function(){
		$(this).height($(this).prop("scrollHeight"));
	});
}
