<?php
 /**
  * Dash Board Header.
  *
  * @package   OpenEMR
  * @link      http://www.open-emr.org
  * @author    Ranganath Pathak <pathak@scrs1.org>
  * @author    Brady Miller <brady.g.miller@gmail.com>
  * @copyright Copyright (c) 2018 Ranganath Pathak <pathak@scrs1.org>
  * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
  * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
  */

require_once("$srcdir/display_help_icon_inc.php");

use OpenEMR\Common\Csrf\CsrfUtils;

$url_webroot = $GLOBALS['webroot'];
$portal_login_href = $url_webroot ."/interface/client_file/summary/create_portallogin.php";
?>

<div class="page-header clearfix">
        <?php echo  $oemr_ui->pageHeading() . "\r\n"; ?>
<?php
// If client is deceased, then show this (along with the number of days client has been deceased for)
$days_deceased = is_client_deceased($pid);
if ($days_deceased) { ?>
    <p class="deceased" style="font-weight:bold;color:red">

        <?php
        $deceased_days = intval($days_deceased['days_deceased']);
        if ($deceased_days == 0) {
            $num_of_days = xl("Today");
        } elseif ($deceased_days == 1) {
             $num_of_days =  $deceased_days . " " . xl("day ago");
        } elseif ($deceased_days > 1 && $deceased_days < 90) {
             $num_of_days =  $deceased_days . " " . xl("days ago");
        } elseif ($deceased_days >= 90 && $deceased_days < 731) {
            $num_of_days =  "~". round($deceased_days/30) . " " . xl("months ago");  // function intdiv available only in php7
        } elseif ($deceased_days >= 731) {
             $num_of_days =  xl("More than") . " " . round($deceased_days/365) . " " . xl("years ago");
        }

        if (strlen($days_deceased['date_deceased']) > 10 && $GLOBALS['date_display_format'] < 1) {
            $deceased_date = substr($days_deceased['date_deceased'], 0, 10);
        } else {
            $deceased_date = oeFormatShortDate($days_deceased['date_deceased']);
        }

        //echo  xlt("Deceased") . " - " . text(oeFormatShortDate($days_deceased['date_deceased'])) . " (" . text($num_of_days) . ")" ;
        echo  xlt("Deceased") . " - " . text($deceased_date) . " (" . text($num_of_days) . ")" ;
        ?>
    </p>
    <?php
} ?>
    <div class="form-group">

            <div class="btn-group oe-opt-btn-group-pinch" role="group">

            <?php
            if (acl_check('admin', 'super') && $GLOBALS['allow_pat_delete']) { ?>
                <a class='btn btn-default btn-sm btn-delete deleter delete'
                   href='<?php echo attr($url_webroot)?>/interface/client_file/deleter.php?client=<?php echo attr_url($pid);?>&csrf_token_form=<?php echo attr_url(CsrfUtils::collectCsrfToken()); ?>'
                   onclick='return top.restoreSession()'>
                    <span><?php echo xlt('Delete');?></span>
                </a>
                <?php
            } // Allow PT delete
            if ($GLOBALS['erx_enable']) { ?>
                <a class="btn btn-default btn-sm btn-add erx" href="<?php echo attr($url_webroot)?>/interface/eRx.php?page=medentry" onclick="top.restoreSession()">
                    <span><?php echo xlt('NewCrop MedEntry');?></span>
                </a>
                <a class="btn btn-default btn-sm btn-save iframe1"
                   href="<?php echo attr($url_webroot)?>/interface/soap_functions/soap_accountStatusDetails.php"
                   onclick="top.restoreSession()">
                    <span><?php echo xlt('NewCrop Account Status');?></span>
                </a>
            <!--<div id='accountstatus'></div>RP_MOVED-->
                <?php
            } // eRX Enabled
            //Client Portal
            $portalUserSetting = true; //flag to see if client has authorized access to portal
            if ($GLOBALS['portal_onsite_two_enable'] && $GLOBALS['portal_onsite_two_address']) {
                $portalStatus = sqlQuery("SELECT allow_client_portal FROM client_data WHERE pid=?", array($pid));
                if ($portalStatus['allow_client_portal']=='YES') {
                    $portalLogin = sqlQuery("SELECT pid FROM `client_access_onsite` WHERE `pid`=?", array($pid));?>
                    <?php $display_class = (empty($portalLogin)) ? "btn-save" : "btn-undo"; ?>
                    <a class='small_modal btn btn-default btn-sm <?php echo attr($display_class); ?>'
                        href='<?php echo attr($portal_login_href); ?>?portalsite=on&client=<?php echo attr_url($pid);?>'
                        onclick='top.restoreSession()'>
                        <?php $display = (empty($portalLogin)) ? xl('Create Onsite Portal Credentials') : xl('Reset Onsite Portal Credentials'); ?>
                        <span><?php echo text($display); ?></span>
                    </a>

                    <?php
                } else {
                    $portalUserSetting = false;
                } // allow client portal
            } // Onsite Client Portal
            if ($GLOBALS['portal_offsite_enable'] && $GLOBALS['portal_offsite_address']) {
                $portalStatus = sqlQuery("SELECT allow_client_portal FROM client_data WHERE pid=?", array($pid));
                if ($portalStatus['allow_client_portal']=='YES') {
                    $portalLogin = sqlQuery("SELECT pid FROM `client_access_offsite` WHERE `pid`=?", array($pid));
                    ?>
                    <?php $display_class = (empty($portalLogin)) ? "btn-save" : "btn-undo"; ?>
                    <a class='small_modal btn btn-default btn-sm <?php echo attr($display_class); ?>'
                       href='<?php echo attr($portal_login_href); ?>?portalsite=off&client=<?php echo attr_url($pid);?>'
                       onclick='top.restoreSession()'>
                        <span>
                            <?php $text = (empty($portalLogin)) ? xl('Create Offsite Portal Credentials') : xl('Reset Offsite Portal Credentials'); ?>
                            <?php echo text($text); ?>
                        </span>
                    </a>
                    <?php
                } else {
                    $portalUserSetting = false;
                } // allow_client_portal
            } // portal_offsite_enable
            if (!($portalUserSetting)) { // Show that the client has not authorized portal access ?>
                <p>
                    <i class="fa fa-exclamation-circle oe-text-orange"  aria-hidden="true"></i> <?php echo xlt('Client has not authorized the Client Portal.');?>
                </p>
                <?php
            }
            //Client Portal
            if ($GLOBALS['erx_enable']) { ?>
                <div id='accountstatus'></div>
                <?php
            } ?>
            </div>

    </div>
</div>