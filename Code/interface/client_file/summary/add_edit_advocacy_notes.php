<?php
require_once("../../globals.php");
require_once("$srcdir/client.inc");
require_once("$srcdir/options.inc.php");

use OpenEMR\Common\Csrf\CsrfUtils;

//Get advocacy case note and advocacy case notes details table data

$result_acnd = getAdvocacyCaseNoteDetails($pid);
$advocacy_case_details = array(id=>$result_acnd[id],fcontact=>$result_acnd[family_contact], scontact=>$result_acnd[service_contact], ndiscontact=>$result_acnd[ndis_contacts], alert=>$result_acnd[alerts], ad_goal_date=>$result_acnd[advocacy_goal_date]);
$adv_case_notes_details_id = $advocacy_case_details[id];

//Get clients advocacy notes

$result_acn= getAdvocacyCaseNotes($adv_case_notes_details_id);


// Get client/employer/insurance information.
//
$result  = getClientData($pid, "*, DATE_FORMAT(DOB,'%Y-%m-%d') as DOB_YMD");

//get advocacy case note client data
$advocacy_data = array(fname=>$result[fname], lname=>$result[lname], dob=>$result[DOB],
 street=>$result[street], postal_code=>$result[postal_code], city=>$result[city], phone=>$result[phone_cell]);

?>

<html>
<head>

<link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/public/assets/bootstrap/dist/css/bootstrap.min.css?v=<?php echo $v_js_includes; ?>" type="text/css">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/public/themes/style_light.css?v=<?php echo $v_js_includes; ?>" type="text/css">
<link rel="stylesheet" href="<?php echo $GLOBALS['assets_static_relative']; ?>/font-awesome/css/font-awesome.min.css?v=<?php echo $v_js_includes; ?>" type="text/css">
<link rel="stylesheet" href="<?php echo $GLOBALS['assets_static_relative']; ?>/jquery-datetimepicker/build/jquery.datetimepicker.min.css?v=<?php echo $v_js_includes; ?>" type="text/css">
<script type="text/javascript" src="<?php echo $GLOBALS['assets_static_relative']; ?>/jquery/dist/jquery.min.js?v=<?php echo $v_js_includes; ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['assets_static_relative']; ?>/assets/bootstrap/dist/js/bootstrap.min.js?v=<?php echo $v_js_includes; ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['assets_static_relative']; ?>/jquery-ui/jquery-ui.min.js?v=<?php echo $v_js_includes; ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['assets_static_relative']; ?>/jquery-datetimepicker/build/jquery.datetimepicker.full.min.js?v=<?php echo $v_js_includes; ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js?v=<?php echo $v_js_includes; ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/textformat.js?v=<?php echo $v_js_includes; ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/dialog.js?v=<?php echo $v_js_includes; ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/userDebug.js?v=<?php echo $v_js_includes; ?>"></script>

<!-- Adding external JS file in order to call addNewRow for Advocacy Case Notes table -->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/gemba360_custom/custom.js?v=<?php echo $v_js_includes; ?>"></script>

<!-- page styles -->
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">

<script>
    $(function (){
        $('.datepicker').datetimepicker({
            <?php $datetimepicker_timepicker = false; ?>
            <?php $datetimepicker_showseconds = false; ?>
            <?php $datetimepicker_formatInput = true; ?>
            <?php require($GLOBALS['srcdir'] . '/js/xl/jquery-datetimepicker-2-5-4.js.php'); ?>
            <?php // can add any additional javascript settings to datetimepicker here; need to prepend first setting with a comma ?>
        });
        $('.datetimepicker').datetimepicker({
            <?php $datetimepicker_timepicker = true; ?>
            <?php $datetimepicker_showseconds = false; ?>
            <?php $datetimepicker_formatInput = true; ?>
            <?php require($GLOBALS['srcdir'] . '/js/xl/jquery-datetimepicker-2-5-4.js.php'); ?>
            <?php // can add any additional javascript settings to datetimepicker here; need to prepend first setting with a comma ?>
        });
    });

// function to call datetimepicker on date input fields
function applyDatetimepicker(className){
    $('.'+className).datetimepicker({
        <?php $datetimepicker_timepicker = false; ?>
        <?php $datetimepicker_showseconds = false; ?>
        <?php $datetimepicker_formatInput = true; ?>
        <?php require($GLOBALS['srcdir'] . '/js/xl/jquery-datetimepicker-2-5-4.js.php'); ?>
        <?php // can add any additional javascript settings to datetimepicker here; need to prepend first setting with a comma ?>
    });
}
</script>

<style>
.highlight {
  color: green;
}
tr.selected {
  background-color: white;
}
</style>

</head>

<body class="body_top">

<form action="add_edit_advocacy_notes.php" name="add_edit_advocacy_notes.php" id="add_edit_advocacy_notes.php" method="post" onsubmit='return top.restoreSession()'>
<input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
<span class="title">

<?php //Start of Advocacy Case Notes?> 
                             <tr>
                                <td width="650px">
                                   
                                    <div id="advocacy_case_notes"  class="summary_item small">
                                        <table width="100%">
                                            <tbody >
                                                <tr style="border-bottom:2px solid #000;">
                                                    <th width="40%" style="padding: 5px;">Name: <?php echo $advocacy_data[fname]." ".$advocacy_data[lname]; ?> <br>DOB: <?php  if(!(is_null($advocacy_data[dob]))) echo date("d-m-Y", strtotime($advocacy_data[dob])); ?>
                                                    <br>Address: <?php echo $advocacy_data[street]." ".$advocacy_data[city]." ".$advocacy_data[postal_code]; ?><br>Phone: <?php echo $advocacy_data[phone]; ?><br></th>
                                                    <th width="60%" style="padding: 5px;">[Advocacy Goal]<br>[Other key information needed to identify case issue]</th>
                                                </tr>
                                                <tr style="border-bottom:2px solid #000;">
                                                    <td style="padding: 5px;">Family contacts</td>
                                                    <td style="padding: 5px;"><input class='form-control' type='text' name='fcontact' value='<?php echo $advocacy_case_details[fcontact]; ?>'></td>
                                                </tr>
                                                <tr style="border-bottom:2px solid #000;">
                                                    <td style="padding: 5px;">Service contacts</td>
                                                    <td style="padding: 5px;"><input type='text' class='form-control' name='scontact' value='<?php echo $advocacy_case_details[scontact]; ?>'></td>
                                                </tr>
                                                <tr style="border-bottom:2px solid #000;">
                                                    <td style="padding: 5px;">NDIS contacts</td>
                                                    <td style="padding: 5px;"><input type='text' class='form-control' name='ndiscontact' value='<?php echo $advocacy_case_details[ndiscontact]; ?>'></td>
                                                </tr>
                                                <tr style="border-bottom:2px solid #000;">
                                                    <td style="padding: 5px;">Alerts (Eg. Violent)</td>
                                                    <td style="padding: 5px;"><input type='text' class='form-control' name='alert' value='<?php echo $advocacy_case_details[alert]; ?>'></td>
                                                </tr>
                                                <tr style="border-bottom:2px solid #000;">
                                                    <td style="padding: 5px;">Advocacy Goal Date</td>
                                                    <td style="padding: 5px;"><input type='text' class='datepicker form-control' name='ad_goal_date' readonly="readonly" value="<?php if(!(is_null($advocacy_case_details[ad_goal_date]))){echo date('d-m-Y', strtotime($advocacy_case_details[ad_goal_date]));}else{ echo date("d-m-Y");} ?>"></td>
                                                    <!-- <?php if(!(is_null($advocacy_case_details[ad_goal_date])))echo date("d-m-Y", strtotime($advocacy_case_details[ad_goal_date])); ?> -->
                                                </tr>
                                            </tbody>
                                        </table>
                                    <div>
                                    <center style = "margin-top: 10px;"><strong>Advocacy Case Notes </strong></center>
                                    <hr>
                                 
                                        <table width="100%" id="advocacy_case_notes_table">
                                            <thead>
                                                <tr style="border-bottom:2px solid #000;">
                                                    <th style="padding: 5px;">Date</th>
                                                    <th  style="padding: 5px;">Type</th>
                                                    <th  style="padding: 5px;">Notes</th>
                                                    <th  style="padding: 5px;">Actions/Outcomes</th>
                                                    <th  style="padding: 5px;">Duration(Minutes)</th>
                                                    <th  style="padding: 5px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr style="border-bottom:2px solid #000;" id="acn_tr_id">
                                                        <td style="padding: 5px;"><input type='text' class='form-control datepicker' readonly="readonly" name='' id="acn_date_id" value='<?php echo date("d-m-Y");?>'></td>
                                                        <td style="padding: 5px;"><input type='text' id='acn_type_id' class='form-control' name='' value=''></td>
                                                        <td style="padding: 5px;"><textarea name='' id="acn_note_id" rows='5' class='form-control acn_textarea' ></textarea></td>
                                                        <td style="padding: 5px;"><input type='text' id="acn_action_id" class='form-control' name='' value=''></td>
                                                        <td style="padding: 5px;"><input type='text' id="acn_duration_id" class='form-control' name='' value=''></td>
                                                        <td style="padding: 5px;text-align: center;vertical-align: middle;"><i class="fa fa-plus fa-lg" id="add_id" title="Add Advocacy Case Note" style="cursor: pointer;font-size: 30px" aria-hidden="true"></i></td>
                                                    </tr>
                                                </tbody>
                                                <tbody>
                                                <?php while($row = sqlFetchArray($result_acn)){ ?>
                                                    <tr style="border-bottom:2px solid #000;">
                                                        <td style="padding: 5px;"><input type="hidden" name="acn_id_edit[]" value="<?php echo $row["id"]; ?>"><input type='text' class='form-control datepicker' name='acn_date_edit[]' value='<?php if(!(is_null($row["case_note_date"]))) echo $row["case_note_date"]; ?>'></td>
                                                        <td style="padding: 5px;"><input type='text' class='form-control' name='acn_type_edit[]' value='<?php echo $row["case_note_type"]; ?>'></td>
                                                        <td style="padding: 5px;"><textarea name='acn_notes_edit[]' class='form-control acn_textarea' ><?php echo $row["case_notes"]; ?></textarea></td>
                                                        <td style="padding: 5px;"><input type='text' class='form-control' name='acn_action_edit[]' value='<?php echo $row["case_note_actions"]; ?>'></td>
                                                        <td style="padding: 5px;"><input type='text' class='form-control' name='acn_duration_edit[]' value='<?php echo $row["case_note_duration"]; ?>'></td>
                                                        <td style="padding: 5px;text-align: center;vertical-align: middle;"><i class="fa fa-times fa-lg" id="delete_id" title="Delete Advocacy Case Note" style="cursor: pointer;font-size: 30px" aria-hidden="true"></i><!--  <img src="" style="cursor: pointer;" title="Add Advocacy Case Note" alt="Add Advocacy Case Note"> --></td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                        </table>
                                    </div>
    
                                    </div>
                                </td>
                            </tr>
                            <?php // End of the Advocacy Case Notes ?>

</span>&nbsp;

</form>
</body>

</html>
