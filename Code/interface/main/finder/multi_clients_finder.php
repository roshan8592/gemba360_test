<?php
/**
 * Multi select client.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Amiel Elboim <amielel@matrix.co.il>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2017 Amiel Elboim <amielel@matrix.co.il
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once('../../globals.php');
require_once("$srcdir/client.inc");

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;

// for editing selected clients
if (isset($_GET['clients'])) {
    if (!CsrfUtils::verifyCsrfToken($_GET["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }

    $clients = rtrim($_GET['clients'], ";");
    $clients = explode(';', $clients);
    $results = array();
    foreach ($clients as $client) {
        $result=getClientData($client, 'id, pid, lname, fname, mname, pubpid, ss, DOB, phone_home');
        $results[] = $result;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <?php Header::setupHeader(['select2', 'opener']); ?>
    <title><?php echo xlt('Client Finder'); ?></title>

    <style>
        #searchCriteria {
            text-align: center;
            width: 100%;
            background-color: #ddddff;
            font-weight: bold;
            padding: 7px;
        }
        .select-box{
            display: inline-block;
        }
        #by-id{
            width: 90px !important;
        }
        #by-name{
            width: 120px !important;
        }
        .buttons-box{
            margin-left: 10px;
            margin-right: 10px;
            display: inline-block;
            vertical-align: middle;
        }
        .inline-box{
            display: inline-block;
            vertical-align: middle;
        }
        .remove-client{
            color: red;
            pointer-events: auto;
        }
        #searchResultsHeader {
            width: 100%;
            border-collapse: collapse;
        }
        #searchResults {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            overflow: auto;
        }

        #searchResults .remove-client {
            cursor: hand;
            cursor: pointer;
        }
        #searchResults td {
            /*font-size: 0.7em;*/
            border-bottom: 1px solid #eee;
        }
    </style>

</head>

<body class="body_top">
<div class="container-responsive">
    <div id="searchCriteria">
        <form class="form-inline">
            <div class="select-box">
                <label><?php echo xlt('Client name') .':'; ?></label>
                <select id="by-name" class="input-sm">
                    <option value=""><?php echo xlt('Enter name'); ?></option>
                </select>
                <label><?php echo xlt('Client ID'); ?></label>
                <select id="by-id" class="input-sm">
                    <option value=""><?php echo xlt('Enter ID'); ?></option>
                </select>
            </div>
            <div class="buttons-box">
                <div class="inline-box">
                    <button id="add-to-list"><?php echo xlt('Add to list'); ?></button>
                </div>
                <div class="inline-box">
                    <button id="send-clients" onclick="selClients()"><?php echo xlt('OK'); ?></button>
                </div>
            </div>
        </form>
    </div>

    <table id="results-table" class="table table-condensed">
        <thead id="searchResultsHeader" class="head">
        <tr>
            <th class="srName"><?php echo xlt('Name'); ?></th>
            <th class="srPhone"><?php echo xlt('Phone'); ?></th>
            <th class="srSS"><?php echo xlt('SS'); ?></th>
            <th class="srDOB"><?php echo xlt('DOB'); ?></th>
            <th class="srID"><?php echo xlt('ID'); ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody id="searchResults">
        <?php
        if (isset($_GET['clients'])) {
            foreach ($results as $index => $result) {
                echo '<tr id="row' . attr($result['pid']) . '">' .
                        '<td>' . text($result['lname']) . ', ' . text($result['fname']) . '</td>' .
                        '<td>' . text($result['phone_home']) . '</td>' .
                        '<td>' . text($result['ss']) . '</td>' .
                        '<td>' . text(oeFormatShortDate($result['DOB'])) . '</td>' .
                        '<td>' . text($result['pubpid']) . '</td>' .
                        '<td><i class="fa fa-remove remove-client" onclick="removeClient(' . attr(addslashes($result['pid'])) . ')"></i></td>' .
                    '<tr>';
            }
        } ?>
        </tbody>
    </table>

</div>

<script>

var currentResult;

<?php if (isset($_GET['clients'])) { ?>
var clientsList = <?php echo json_encode($results); ?>;
<?php } else { ?>
var clientsList = [];
$('#results-table').hide();
<?php } ?>

//Initial select2 library for auto completing using ajax
$('#by-id, #by-name').select2({
    ajax: {
        beforeSend: top.restoreSession,
        url: 'multi_clients_finder_ajax.php',
        data:function (params) {
            var query = {
                search: params.term,
                type: $(this).attr('id'),
                csrf_token_form: "<?php echo attr(CsrfUtils::collectCsrfToken()); ?>"
            }
            return query;
        },
        dataType: 'json',
    },
    <?php require($GLOBALS['srcdir'] . '/js/xl/select2.js.php'); ?>
});

//get all the data of selected client
$('#by-id').on('change', function () {
    top.restoreSession();
    $.ajax({
        url: 'multi_clients_finder_ajax.php',
        data:{
            type:'client-by-id',
            search:$('#by-id').val(),
            csrf_token_form: "<?php echo attr(CsrfUtils::collectCsrfToken()); ?>"
        },
        dataType: 'json'
    }).done(function(data){
        currentResult=data.results;
        //change client name to selected client
        $('#by-name').val(null);
        var newOption = "<option value='" +currentResult.pid+ "' selected>"+currentResult.lname + ', ' + currentResult.fname+"</option>";
        $('#by-name').append(newOption);
    })
});

//get all the data of selected client
$('#by-name').on('change', function () {
    top.restoreSession();
    $.ajax({
        url: 'multi_clients_finder_ajax.php',
        data:{
            type:'client-by-id',
            search:$('#by-name').val(),
            csrf_token_form: "<?php echo attr(CsrfUtils::collectCsrfToken()); ?>"
        },
        dataType: 'json'
    }).done(function(data){
        currentResult=data.results;
        //change client pubpid to selected client
        $('#by-id').val(null);
        var newOption = "<option value='" +currentResult.pid+ "' selected>"+ currentResult.pubpid +"</option>";
        $('#by-id').append(newOption);
    })
});

//add new client to list
$('#add-to-list').on('click', function (e) {
    e.preventDefault();

    if($('#by-name').val() == '')return;

    if(clientsList.length === 0){
        $('#results-table').show();
    }

    // return if client already exist in the list
    var exist
    $.each(clientsList, function (key, client) {
        if (client.pid == currentResult.pid) exist = true;
    })
    if(exist)return;


    // add to array
    clientsList.push(currentResult);

    $('#searchResults').append('<tr id="row'+currentResult.pid +'">' +
        '<td>'+ currentResult.lname + ', ' + currentResult.fname + '</td>' +
        '<td>' + currentResult.phone_home + '</td>' +
        '<td>' + currentResult.ss + '</td>' +
        '<td>' + currentResult.DOB + '</td>' +
        '<td>' + currentResult.pubpid + '</td>' +
        '<td><i class="fa fa-remove remove-client" onclick="removeClient('+currentResult.pid+')"></i></td>' +
    '<tr>');

});

// remove client from list
function removeClient(pid) {

    $.each(clientsList, function (index, client) {
        if (typeof client !== 'undefined' && client.pid == pid) {
            clientsList.splice(index,1);
        }
    });

    $('#row'+pid).remove();
}

//send array of clients to function 'setMultiClients' of the opener
function selClients() {
    if (opener.closed || ! opener.setMultiClients)
        alert("<?php echo xls('The destination form was closed; I cannot act on your selection.'); ?>");
    else
        opener.setMultiClients(clientsList);
    dlgclose();
    return false;
}


</script>

</body>
