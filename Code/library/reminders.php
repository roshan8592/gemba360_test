<?php
/**
 * Client reminders functions.
 *
 * These functions should not ever attempt to write to
 * session variables, because the session_write_close() function
 * is typically called before utilizing these functions.
 *
 * Functions for collection/displaying/sending client reminders. This is
 * part of the CDR engine, which can be found at library/clinical_rules.php.
 *
 * Copyright (C) 2010-2012 Brady Miller <brady.g.miller@gmail.com>
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * @package OpenEMR
 * @author  Brady Miller <brady.g.miller@gmail.com>
 * @link    https://www.open-emr.org
 */

/**
 * Include the main CDR engine library, email class and maviq class
 */
require_once(dirname(__FILE__) . "/clinical_rules.php");
require_once(dirname(__FILE__) . "/maviq_phone_api.php");

//only used in commented out code
use OpenEMR\Common\Crypto\CryptoGen;

/**
 * Display the client reminder widget.
 *
 * @param  integer  $client_id  pid of selected client
 * @param  string   $dateTarget  target date (format Y-m-d H:i:s). If blank then will test with current date as target.
 */
function client_reminder_widget($client_id, $dateTarget = '')
{

  // Set date to current if not set
    $dateTarget = ($dateTarget) ? $dateTarget : date('Y-m-d H:i:s');

  // Update reminders for client
    update_reminders($dateTarget, $client_id);

  // Fetch the active reminders
    $listReminders = fetch_reminders($client_id);

    if (empty($listReminders)) {
        // No reminders to show.
        echo xlt('No active client reminders.');
        return;
    }

    echo "<table cellpadding='0' cellspacing='0'>";
    foreach ($listReminders as $reminder) {
        echo "<tr><td style='padding:0 1em 0 1em;'><span class='small'>";
        // show reminder label
        echo generate_display_field(array('data_type'=>'1','list_id'=>'rule_action_category'), $reminder['category']) .
        ": " . generate_display_field(array('data_type'=>'1','list_id'=>'rule_action'), $reminder['item']);
        echo "</span></td><td style='padding:0 1em 0 1em;'><span class='small'>";
        // show reminder due status
        echo generate_display_field(array('data_type'=>'1','list_id'=>'rule_reminder_due_opt'), $reminder['due_status']);
        echo "</span></td><td style='padding:0 1em 0 1em;'><span class='small'>";
        // show reminder sent date
        if (empty($reminder['date_sent'])) {
            echo xlt('Reminder Not Sent Yet');
        } else {
            echo text(xl('Reminder Sent On').": ".$reminder['date_sent']);
        }

        echo "</span></td></tr>";
    }

    echo "</table>";
}

/**
 * Function to update reminders via a batching method to improve performance and decrease memory overhead.
 *
 * Function that updates reminders and returns an array with a specific data structure.
 * <pre>The data structure of the return array includes the following elements
 *  'total_active_actions'         - Number of active actions.
 *  'total_pre_active_reminders'   - Number of active reminders before processing.
 *  'total_pre_unsent_reminders'   - Number of unsent reminders before processing.
 *  'total_post_active_reminders'  - Number of active reminders after processing.
 *  'total_post_unsent_reminders'  - Number of unsent reminders after processing.
 *  'number_new_reminders'         - Number of new reminders
 *  'number_updated_reminders'     - Number of updated reminders (due_status change)
 *  'number_inactivated_reminders' - Number of inactivated reminders.
 *  'number_unchanged_reminders'   - Number of unchanged reminders.
 * </pre>
 *
 * @param  string   $dateTarget  target date (format Y-m-d H:i:s). If blank then will test with current date as target.
 * @param  integer  $batchSize   number of clients to batch (default is 25; plan to optimize this default setting in the future)
 * @param  integer  $report_id   id of report in database (if already bookmarked)
 * @param  boolean  $also_send   if TRUE, then will also call send_reminder when done
 * @return array                 see above for data structure of returned array
 */
function update_reminders_batch_method($dateTarget = '', $batchSize = 25, $report_id = null, $also_send = false)
{

  // Default to a batchsize, if empty
    if (empty($batchSize)) {
        $batchSize=25;
    }

  // Collect total number of pertinent clients (to calculate batching parameters)
    $totalNumClients = buildClientArray('', '', '', null, null, true);

  // Cycle through the batches and collect/combine results
    if (($totalNumClients%$batchSize) > 0) {
        $totalNumberBatches = floor($totalNumClients/$batchSize) + 1;
    } else {
        $totalNumberBatches = floor($totalNumClients/$batchSize);
    }

  // Prepare the database to track/store results
    if ($also_send) {
        $report_id = beginReportDatabase("process_send_reminders", '', $report_id);
    } else {
        $report_id = beginReportDatabase("process_reminders", '', $report_id);
    }

    setTotalItemsReportDatabase($report_id, $totalNumClients);

    $client_counter=0;
    for ($i=0; $i<$totalNumberBatches; $i++) {
        $client_counter = $batchSize*($i+1);
        if ($client_counter > $totalNumClients) {
            $client_counter = $totalNumClients;
        }

        $update_rem_log_batch = update_reminders($dateTarget, '', (($batchSize*$i)+1), $batchSize);
        if ($i == 0) {
            // For first cycle, simply copy it to update_rem_log
            $update_rem_log = $update_rem_log_batch;
        } else {
            // Debug statements
            //error_log("CDR: ".print_r($update_rem_log,TRUE),0);
            //error_log("CDR: ".($batchSize*$i). " records",0);

            // Integrate batch results into main update_rem_log
            $update_rem_log['total_active_actions'] = $update_rem_log['total_active_actions'] + $update_rem_log_batch['total_active_actions'];
            $update_rem_log['total_pre_active_reminders'] = $update_rem_log['total_pre_active_reminders'] + $update_rem_log_batch['total_pre_active_reminders'];
            $update_rem_log['total_pre_unsent_reminders'] = $update_rem_log['total_pre_unsent_reminders'] + $update_rem_log_batch['total_pre_unsent_reminders'];
            $update_rem_log['number_new_reminders'] = $update_rem_log['number_new_reminders'] + $update_rem_log_batch['number_new_reminders'];
            $update_rem_log['number_updated_reminders'] = $update_rem_log['number_updated_reminders'] + $update_rem_log_batch['number_updated_reminders'];
            $update_rem_log['number_unchanged_reminders'] = $update_rem_log['number_unchanged_reminders'] + $update_rem_log_batch['number_unchanged_reminders'];
            $update_rem_log['number_inactivated_reminders'] = $update_rem_log['number_inactivated_reminders'] + $update_rem_log_batch['number_inactivated_reminders'];
            $update_rem_log['total_post_active_reminders'] = $update_rem_log['total_post_active_reminders'] + $update_rem_log_batch['total_post_active_reminders'];
            $update_rem_log['total_post_unsent_reminders'] = $update_rem_log['total_post_unsent_reminders'] + $update_rem_log_batch['total_post_unsent_reminders'];
        }

        //Update database to track results
        updateReportDatabase($report_id, $client_counter);
    }

  // Create an array for saving to database (allows combining with the send log)
    $save_log = array();
    $save_log[] = $update_rem_log;

  // Send reminders, if this was selected
    if ($also_send) {
        $log_send = send_reminders();
        $save_log[] = $log_send;
    }

  // Record combo results in database
    finishReportDatabase($report_id, json_encode($save_log));

  // Just return the process reminders array
    return $update_rem_log;
}

/**
 * Function to update reminders.
 *
 * Function that updates reminders and returns an array with a specific data structure.
 * <pre>The data structure of the return array includes the following elements
 *  'total_active_actions'         - Number of active actions.
 *  'total_pre_active_reminders'   - Number of active reminders before processing.
 *  'total_pre_unsent_reminders'   - Number of unsent reminders before processing.
 *  'total_post_active_reminders'  - Number of active reminders after processing.
 *  'total_post_unsent_reminders'  - Number of unsent reminders after processing.
 *  'number_new_reminders'         - Number of new reminders
 *  'number_updated_reminders'     - Number of updated reminders (due_status change)
 *  'number_inactivated_reminders' - Number of inactivated reminders.
 *  'number_unchanged_reminders'   - Number of unchanged reminders.
 * </pre>
 *
 * @param  string   $dateTarget  target date (format Y-m-d H:i:s). If blank then will test with current date as target.
 * @param  integer  $client_id  pid of client. If blank then will check all clients.
 * @param  integer  $start       applicable client to start at (when batching process)
 * @param  integer  $batchSize   number of clients to batch (when batching process)
 * @return array                 see above for data structure of returned array
 */
function update_reminders($dateTarget = '', $client_id = '', $start = null, $batchSize = null)
{

    $logging = array();

  // Set date to current if not set
    $dateTarget = ($dateTarget) ? $dateTarget : date('Y-m-d H:i:s');

  // Collect reminders (note that this function removes redundant and keeps the most distant
  //   reminder (ie. prefers 'past_due' over 'due' over 'soon_due')
  // Note that due to a limitation in the test_rules_clinic function, the client_id is explicitly
  //  needed to work correctly. So rather than pass in a '' client_id to do the entire clinic,
  //  we instead need to pass in each client_id separately.
    $collectedReminders = array();
    $client_id_complete = "";
    if (!(empty($client_id))) {
        // only one client id, so run the function
        $collectedReminders = test_rules_clinic('', 'client_reminder', $dateTarget, 'reminders-due', $client_id);
        $client_id_complete = $client_id;
    } else {
        // as described above, need to pass in each client_id
        // Collect all client ids
        $clientData = buildClientArray('', '', '', $start, $batchSize);
        for ($iter=0; $row=sqlFetchArray($rez); $iter++) {
            $clientData[$iter]=$row;
        }

        $first_flag = true;
        foreach ($clientData as $client) {
            // collect reminders
            $tempCollectReminders = test_rules_clinic('', 'client_reminder', $dateTarget, 'reminders-due', $client['pid']);
            $collectedReminders = array_merge($collectedReminders, $tempCollectReminders);
            // build the $client_id_complete variable
            if ($first_flag) {
                $client_id_complete .= $client['pid'];
                $first_flag = false;
            } else {
                $client_id_complete .= ",".$client['pid'];
            }
        }
    }

    $logging['total_active_actions'] = count($collectedReminders);

  // For logging purposes only:
  //  Collect number active of active and unsent reminders
    $logging['total_pre_active_reminders'] = count(fetch_reminders($client_id_complete));
    $logging['total_pre_unsent_reminders'] = count(fetch_reminders($client_id_complete, 'unsent'));

  // Migrate reminders into the client_reminders table
    $logging['number_new_reminders'] = 0;
    $logging['number_updated_reminders'] = 0;
    $logging['number_unchanged_reminders'] = 0;
    foreach ($collectedReminders as $reminder) {
        // See if a reminder already exist
        $sql = "SELECT `id`, `pid`, `due_status`, `category`, `item` FROM `client_reminders` WHERE " .
        "`active`='1' AND `pid`=? AND `category`=? AND `item`=?";
        $result = sqlQueryCdrEngine($sql, array($reminder['pid'], $reminder['category'], $reminder['item']));

        if (empty($result)) {
            // It does not yet exist, so add a new reminder
            $sql = "INSERT INTO `client_reminders` (`pid`, `due_status`, `category`, `item`, `date_created`) " .
            "VALUES (?, ?, ?, ?, NOW())";
            sqlStatementCdrEngine($sql, array($reminder['pid'], $reminder['due_status'], $reminder['category'], $reminder['item']));
            $logging['number_new_reminders']++;
        } else {
            // It already exist (see if if needs to be updated via adding a new reminder)
            if ($reminder['due_status'] == $result['due_status']) {
                // No change in due status, so no need to update
                $logging['number_unchanged_reminders']++;
                continue;
            } else {
                // Change in due status, so inactivate current reminder and create a new one
                // First, inactivate the previous reminder
                $sql = "UPDATE `client_reminders` SET `active` = '0', `reason_inactivated` = 'due_status_update', " .
                "`date_inactivated` = NOW() WHERE `id`=?";
                sqlStatementCdrEngine($sql, array($result['id']));
                // Then, add the new reminder
                $sql = "INSERT INTO `client_reminders` (`pid`, `due_status`, `category`, `item`, `date_created`) " .
                "VALUES (?, ?, ?, ?, NOW())";
                sqlStatementCdrEngine($sql, array($reminder['pid'], $reminder['due_status'], $reminder['category'], $reminder['item']));
            }
        }
    }

  // Inactivate reminders that no longer exist
  // Go through each active reminder and ensure it is in the current list
    $sqlReminders = fetch_reminders($client_id_complete);
    $logging['number_inactivated_reminders'] = 0;
    foreach ($sqlReminders as $row) {
        $inactivateFlag = true;
        foreach ($collectedReminders as $reminder) {
            if (($row['pid'] == $reminder['pid']) &&
               ($row['category'] == $reminder['category']) &&
               ($row['item'] == $reminder['item']) &&
               ($row['due_status'] == $reminder['due_status']) ) {
                // The sql reminder has been confirmed, so do not inactivate it
                $inactivateFlag = false;
                break;
            }
        }

        if ($inactivateFlag) {
            // The sql reminder was not confirmed, so inactivate it
            $sql = "UPDATE `client_reminders` SET `active` = '0', `reason_inactivated` = 'auto', " .
            "`date_inactivated` = NOW() WHERE `id`=?";
            sqlStatementCdrEngine($sql, array($row['id']));
            $logging['number_inactivated_reminders']++;
        }
    }

  // For logging purposes only:
  //  Collect number of active and unsent reminders
    $logging['total_post_active_reminders'] = count(fetch_reminders($client_id_complete));
    $logging['total_post_unsent_reminders'] = count(fetch_reminders($client_id_complete, 'unsent'));

    return $logging;
}


/**
 * Function to send reminders.
 *
 * Function that sends reminders and returns an array with a specific data structure.
 * <pre>The data structure of the return array includes the following elements
 *   'total_pre_unsent_reminders'  - Number of reminders before processing.
 *   'total_post_unsent_reminders' - Number of reminders after processing.
 *   'number_success_emails'       - Number of successfully sent email reminders.
 *   'number_failed_emails'        - Number of failed sent email reminders.
 *   'number_success_calls'        - Number of successfully call reminders.
 *   'number_failed_calls'         - Number of failed call reminders.
 * </pre>
 *
 * @return array                 see above for data structure of returned array
 */
function send_reminders()
{

    $logging = array();

  // Collect active reminders that have not yet been sent.
    $active_unsent_reminders = fetch_reminders('', 'unsent');
    $logging['total_pre_unsent_reminders'] = count($active_unsent_reminders);

  // Send the unsent reminders
    $logging['number_success_emails'] = 0;
    $logging['number_failed_emails'] = 0;
    $logging['number_success_calls'] = 0;
    $logging['number_failed_calls'] = 0;
    foreach ($active_unsent_reminders as $reminder) {
        // Collect client information that reminder is going to.
        $sql = "SELECT `fname`, `lname`, `email`, `phone_home`, `hipaa_voice`, `hipaa_allowemail` from `client_data` where `pid`=?";
        $result = sqlQueryCdrEngine($sql, array($reminder['pid']));
        $clientfname = $result['fname'];
        $clientlname = $result['lname'];
        $clientemail = $result['email'];
        $clientphone = $result['phone_home'];
        $hipaa_voice = $result['hipaa_voice'];
        $hipaa_allowemail = $result['hipaa_allowemail'];

        // Email to client if Allow Email and set reminder sent flag.
        if ($hipaa_allowemail == "YES") {
            $mail = new MyMailer();
            $sender_name = $GLOBALS['client_reminder_sender_name'];
            $email_address = $GLOBALS['client_reminder_sender_email'];
            $mail->FromName = $sender_name;  // required
            $mail->Sender = $email_address;    // required
            $mail->From = $email_address;    // required
            $mail->AddAddress($clientemail, $clientfname.", ".$clientlname);   // required
            $mail->AddReplyTo($email_address, $sender_name);  // required
            $category_title = generate_display_field(array('data_type'=>'1','list_id'=>'rule_action_category'), $reminder['category']);
            $item_title = generate_display_field(array('data_type'=>'1','list_id'=>'rule_action'), $reminder['item']);
            $mail->Body = "Dear ".$clientfname.", This is a message from your clinic to remind you of your ".$category_title.": ".$item_title;
            $mail->Subject = "Clinic Reminder";
            if ($mail->Send()) {
                // deal with and keep track of this successful email
                sqlStatementCdrEngine("UPDATE `client_reminders` SET `email_status`='1', `date_sent`=NOW() WHERE id=?", array($reminder['id']));
                $logging['number_success_emails']++;
            } else {
                // deal with and keep track of this unsuccesful email
                $logging['number_failed_emails']++;
            }
        }

        // Call to client if Allow Voice Message and set reminder sent flag.
        if ($hipaa_voice == "YES") {
      /******************************************************************************
    *     // Maviq does not work, is not currently supported, and seems to break on windows servers, so this
    *     //  feature has been commented out for now.
    *     // Automated VOIP service provided by Maviq. Please visit http://signup.maviq.com for more information.
    *      $siteId = $GLOBALS['phone_gateway_username'];
    *      $cryptoGen = new CryptoGen();
    *      $token = $cryptoGen->decryptStandard($GLOBALS['phone_gateway_password']);
    *      $endpoint = $GLOBALS['phone_gateway_url'];
    *      $client = new MaviqClient($siteId, $token, $endpoint);
    *      //Set up params.
    *      $data = array(
    *        "firstName" => $clientfname,
    *        "lastName" => $clientlname,
    *        "phone" => $clientphone,
    *        //"apptDate" => "$scheduled_date[1]/$scheduled_date[2]/$scheduled_date[0]",
    *        "timeRange" => "10-18",
    *        "type" => "reminder",
    *        "timeZone" => date('P'),
    *        "greeting" => str_replace("[[sender]]", $sender_name, str_replace("[[client_name]]", $clientfname, $myrow['reminder_content']))
    *      );
    *
    *      // Make the call.
    *      $response = $client->sendRequest("appointment", "POST", $data);
    *
    *      if ($response->IsError) {
    *        // deal with and keep track of this unsuccessful call
    *        $logging['number_failed_calls']++;
    *      }
    *      else {
    *        // deal with and keep track of this succesful call
    *        sqlStatementCdrEngine("UPDATE `client_reminders` SET `voice_status`='1', `date_sent`=NOW() WHERE id=?", array($reminder['id']) );
    *        $logging['number_success_calls']++;
    *      }
      *******************************************************************************/
        }
    }

  // For logging purposes only:
  //  Collect active reminders that have not yet been sent.
    $logging['total_post_unsent_reminders'] = count(fetch_reminders('', 'unsent'));

    return $logging;
}

/**
 * Function to fetch reminders.
 *
 * @param  integer/array  $client_id  pid(s) of client(s).
 * @param  string         $type        Can choose unsent ('unsent') vs all active (BLANK) reminders
 * @param  string         $due_status  due status of reminders (soon_due,due,past_due). If blank, then will return all.
 * @param  string         $select      Select component of select statement. If blank, then will return all columns.
 * @return array                 Returns an array of reminders.
 */
function fetch_reminders($client_id = '', $type = '', $due_status = '', $select = '*')
{

    $arraySqlBind = array();

    if (!empty($client_id)) {
        // check the specified pid(s)
        $where = "`pid` IN (?) AND ";
        array_push($arraySqlBind, $client_id);
    }

    if (!empty($due_status)) {
        $where .= "`due_status`=? AND ";
        array_push($arraySqlBind, $due_status);
    }

    if (empty($type)) {
        $where .= "`active`='1'";
    } else { // $type == 'unsent'
        $where .= "`active`='1' AND `date_sent` IS NULL";
    }

    $order = "`due_status`, `date_created`";

    $sql = "SELECT " . $select . " FROM `client_reminders` WHERE " .
    $where . " ORDER BY " . $order;
    $rez = sqlStatementCdrEngine($sql, $arraySqlBind);

    $returnval=array();
    for ($iter=0; $row=sqlFetchArray($rez); $iter++) {
        $returnval[$iter]=$row;
    }

    return $returnval;
}
