<?php
/**
 * Medical Dashboard Help.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author Ranganath Pathak <pathak@scrs1.org>
 * @copyright Copyright (c) 2018 Ranganath Pathak <pathak@scrs1.org>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */
 
use OpenEMR\Core\Header;

require_once("../../interface/globals.php");
?>
<!DOCTYPE HTML>
<html>
    <head>
    <?php Header::setupHeader();?>
    <title><?php echo xlt("Medical Dashboard Help");?></title>
    </head>
    <body>
        <div class="container oe-help-container">
            <div>
                <center><h2><a name='entire_doc'><?php echo xlt("Medical Dashboard Help");?></a></h2></center>
            </div>
            <div class= "row">
                <div class="col-sm-12">
                    <p><?php echo xlt("The dashboard is the central location for convenient access the client's medical record");?>.
                    
                    <p><i class="fa fa-lightbulb-o fa-lg  oe-text-green" aria-hidden="true"></i>&nbsp <?php echo xlt("To help familiarize you with the various components of the Dashboard page it is suggested that you reduce the size of the browser to cover half the viewport, resize the help pop-up by clicking and dragging the bottom right corner of the pop-up. Open another instance of the browser and resize it to cover the other half of the viewport, login to openEMR");?>.
                    
                    <p><?php echo xlt("The Dashboard page is divided into three sections");?>:
                    
                    <ul>
                        <li><a href="#section1"><?php echo xlt("Header");?></a></li>
                        <li><a href="#section2"><?php echo xlt("Nav Bar");?></a></li>
                        <li><a href="#section3"><?php echo xlt("Data Section");?></a></li>
                    </ul>
                </div>
            </div>
            <div class= "row" id="section1">
                <div class="col-sm-12">
                    <h4 class="oe-help-heading"><?php echo xlt("Header"); ?><a href="#"><i class="fa fa-arrow-circle-up oe-pull-away oe-help-redirect" aria-hidden="true"></i></a></h4>
                    <p><?php echo xlt("The header section will reveal client specific information across most pages related to the client's medical record");?>.
                    
                    <p><strong><?php echo xlt("E-PRESCRIBING"); ?> :</strong>
                        
                    <p><?php echo xlt("If NewCrop eRx - the electronic prescription module, is enabled the NewCrop MedEntry and NewCrop Account Status buttons will be appear here");?>.
                    <button type="button" class="btn btn-default btn-add btn-sm oe-no-float"><?php echo xlt("NewCrop MedEntry"); ?></button>
                    <button type="button" class="btn btn-default btn-save btn-sm oe-no-float"><?php echo xlt("NewCrop Account Status"); ?></button>
                    
                    <p><i class="fa fa-exclamation-triangle oe-text-red"  aria-hidden="true"></i> <strong><?php echo xlt("You will need Administrator privileges to setup the NewCrop service and has to be setup in conjunction with technical support from the NewCrop eRx service");?>.</strong>
                    
                    <p><?php echo xlt("This module is subscription based and needs to be enabled from Administration > Globals > Connectors > Enable NewCrop eRx Service");?>.
                    
                    <p><?php echo xlt("The NewCrop eRx Partner Name, NewCrop eRx Name and NewCrop eRx Password will be provided by the vendor");?>.
                    
                    <p><?php echo xlt("The rest of the boxes related to the NewCrop eRx service can be left at default values");?>.
                    
                    <p><?php echo xlt("This module is well integrated with openEMR, there are however two non-subscription based alternatives, Weno and Allscripts that can be used instead");?>.
                    
                    <p><?php echo xlt("The Weno Exchange is well integrated with openEMR and is not subscription based");?>.
                    
                    <p><?php echo xlt("The Allscripts solution integrates the Allscripts ePrescribe web site with openEMR");?>.
                    
                    <p><?php echo xlt("Further information regarding using the e-prescribing modules can be found by clicking this link");?>.
                    <a href="https://www.open-emr.org/wiki/index.php/OpenEMR_ePrescribe" rel="noopener" target="_blank"><i class="fa fa-external-link text-primary" aria-hidden="true" data-original-title="" title=""></i></a>
                    
                    <p><?php echo xlt("At present e-prescribing from openEMR is possible only in the United States");?>.
                    
                    <p><strong><?php echo xlt("CLIENT PORTAL"); ?> :</strong>
                    
                    <p><?php echo xlt("Information regarding the Client Portal is also shown in the header section");?>.
                    
                    <p><?php echo xlt("There are multiple options regarding client portals and information on how to setup the client portal is available here");?>.
                    <a href="https://www.open-emr.org/wiki/index.php/Client_Portal" rel="noopener" target="_blank"><i class="fa fa-external-link text-primary" aria-hidden="true" data-original-title="" title=""></i></a>
                    
                    <p><?php echo xlt("To enable Client Portal go to Administration > Portal > Enable Version 2 Onsite Client Portal, Enable Offsite Client Portal or Enable CMS Portal");?>.
                    
                    <p><?php echo xlt("Enable only one type of portal by checking the relevant check-box");?>.
                    
                    <p><i class="fa fa-exclamation-triangle oe-text-red"  aria-hidden="true"></i> <strong><?php echo xlt("You will need Administrator privileges to enable the client portal");?>.</strong>
                    
                    <p><i class="fa fa-exclamation-circle oe-text-orange"  aria-hidden="true"></i> <?php echo xlt("If the client has not authorized client portal then a Client has not authorized the Client Portal message will be shown here");?>.
                    
                    <p><?php echo xlt("To authorize the client portal for the client go to Dashboard > Demographics > Edit > Choices and select Yes in Allow Client Portal drop-down box and Save");?>.
                    <button type="button" class="btn btn-default btn-sm oe-no-float"><?php echo xlt("Edit"); ?></button>
                    <button type="button" class="btn btn-default btn-save btn-sm oe-no-float"><?php echo xlt("Save"); ?></button>
                    
                    <p><?php echo xlt("If the Online Client portal is enabled there will be either a button that says Create Online Portal Credentials provided the client has given permission to access the online client portal or a message that says Client has not authorized the Client Portal");?>.
                    <button type="button" class="btn btn-default btn-save btn-sm oe-no-float"><?php echo xlt("Create Online Portal Credentials"); ?></button>
                                        
                    <p><?php echo xlt("If the Offsite Client portal is enabled there will be either a button that says Create Offsite Portal Credentials provided the client has given permission to access the online client portal or a message that says Client has not authorized the Client Portal");?>.
                    <button type="button" class="btn btn-default btn-save btn-sm oe-no-float"><?php echo xlt("Create Offsite Portal Credentials"); ?></button>
                    
                    <p><?php echo xlt("Clicking on the Create Online/Offsite Portal Credentials button will generate a username and password for the client that has to be given to the client");?>.
                                        
                    <p><?php echo xlt("These credentials will be used by the client to login to the client portal for the first time");?>.
                    
                    <p><?php echo xlt("The client will have to change their credentials at the first login");?>.
                    
                    <p><?php echo xlt("If the Online/Offsite Portal Credentials has already been set the button will change to");?>
                    <button type="button" class="btn btn-default btn-undo btn-sm oe-no-float"><?php echo xlt("Reset Online Portal Credentials"); ?></button>
                    <button type="button" class="btn btn-default btn-undo btn-sm oe-no-float"><?php echo xlt("Reset Offsite Portal Credentials"); ?></button>
                        
                    <p><strong><?php echo xlt("DECEASED NOTIFICATION"); ?> :</strong>
                    
                    <p><?php echo xlt("If the client is deceased then the deceased notification will appear in red in this section");?>.
                    
                    <p><?php echo xlt("For the deceased notification to appear the date of death must be noted under Medical Dashboard > Edit Demographics > Misc");?>.
                    
                    <p><?php echo xlt("The help icon will let you access context sensitive help for each of the pages accessed");?>. <i class="fa fa-question-circle fa-lg oe-help-redirect" aria-hidden="true"></i>
                    
            </div>
            </div>
            <div class= "row" id="section2">
                <div class="col-sm-12">
                    <h4 class="oe-help-heading"><?php echo xlt("Nav Bar"); ?><a href="#"><i class="fa fa-arrow-circle-up oe-pull-away oe-help-redirect" aria-hidden="true"></i></a></h4>
                    <p><?php echo xlt("The Nav Bar allows one to quickly navigate to various parts of the client's medical record");?>.
                    
                    <p><?php echo xlt("The default installation has the following items");?>:
                    
                        <ul>
                            <li><?php echo xlt("Dashboard"); ?></li>
                            <li><?php echo xlt("History"); ?></li>
                            <li><?php echo xlt("Report"); ?></li>
                            <li><?php echo xlt("Documents"); ?></li>
                            <li><?php echo xlt("Transactions"); ?></li>
                            <li><?php echo xlt("Issues"); ?></li>
                            <li><?php echo xlt("Ledger"); ?></li>
                            <li><?php echo xlt("External Data"); ?></li>
                        </ul>
                    
                    <p><?php echo xlt("Dashboard - summarizes all client related information");?>.
                    
                    <p><?php echo xlt("History - client's past medical history, family history, personal history");?>.
                    
                    <p><?php echo xlt("Report - Generates and downloads the client's Continuity of Care Record (CCR), Continuity of Care Document (CCD) and Client Report");?>.
                    
                    <p><?php echo xlt("Documents - a repository of the client's scanned/faxed paper documents. It also the place to download client specific templates");?>.
                    
                    <p><?php echo xlt("Transactions - lists various notes about happenings in a client's chart with respect to billing, legal, client request, physician request and also generates a client referral or counter-referral");?>.
                    
                    <p><?php echo xlt("Issues - summarizes the client's medical problems, allergies, medications, surgeries and dental issues");?>.
                    
                    <p><?php echo xlt("Ledger - Summarizes and tabulates all the charges, payments, adjustments and balances for all encounters pertaining to the client");?>.
                    
                    <p><?php echo xlt("External Data - any external data linked to either encounters or procedures");?>.
                    
                    <p><?php echo xlt("Additional information about the individual pages can be found in their respective help files");?>.
                </div>
            </div>
            <div class= "row" id="section3">
                <div class="col-sm-12">
                    <h4 class="oe-help-heading"><?php echo xlt("Data Section"); ?><a href="#"><i class="fa fa-arrow-circle-up oe-pull-away oe-help-redirect" aria-hidden="true"></i></a></h4>
                    <p><?php echo xlt("The data section of the dashboard page lists all pertinent items related to a client");?>.
                    
                    <p><?php echo xlt("These items can be edited if the user has sufficient privilege");?>.
                    
                    <p><?php echo xlt("Billing - provides a summary of the balances - Client Balance Due, Insurance Balance Due, Total Balance Due and lists the name of the Primary Insurance along with its effective date");?>.
                    
                    <p><?php echo xlt("Demographics - client demographics and insurance information");?>.
                    
                    <p><?php echo xlt("Client Reminders - a list reminders for preventive or follow-up care according to client preferences based on demographic data, specific conditions, and/or medication list as well as the status of the notification");?>.
                    <a href="https://www.open-emr.org/wiki/index.php/Client_Reminders" rel="noopener" target="_blank"><i class="fa fa-external-link text-primary" aria-hidden="true" data-original-title="" title=""></i></a>
                    
                    <p><?php echo xlt("Disclosures - Record disclosures made for treatment, payment, and health care operations with date, time, client identification (name or number), user identification (name or number), and a description of the disclosure");?>.
                    <a href="https://www.open-emr.org/wiki/index.php/7._Recording_Disclosure" rel="noopener" target="_blank"><i class="fa fa-external-link text-primary" aria-hidden="true" data-original-title="" title=""></i></a>
                    
                    <p><?php echo xlt("Amendments - Enable a user to electronically select the record affected by a client’s request for amendment and either append the amendment to the affected record or include a link that indicates the amendment’s location");?>.
                    <a href="https://www.open-emr.org/wiki/index.php/Amendments_(MU2)" rel="noopener" target="_blank"><i class="fa fa-external-link text-primary" aria-hidden="true" data-original-title="" title=""></i></a>
                    
                    <p><?php echo xlt("ID Card/Photos - will display any ID Card or client photo that has been uploaded to Documents > Client Information > Client ID Card and Client Photograph folders");?>.
                    
                    <p><i class="fa fa-exclamation-triangle oe-text-red"  aria-hidden="true"></i> <strong><?php echo xlt("Please ensure that there is only one image file - jpeg, png or bmp in the Client Photograph folder");?>.</strong>
                    
                    <p><?php echo xlt("Clinical Reminders - is a widget that displays the Passive Alerts for a Clinical Decision Rule");?>.
                    
                    <p><?php echo xlt("A Clinical Decision Rule is client specific information that is filtered and presented at appropriate times to enhance health and health care");?>.
                    
                    <p><?php echo xlt("A detailed guide on how to enable and setup a Clinical Decision rule is found here");?>.
                    <a href="https://open-emr.org/wiki/images/c/ca/Clinical_Decision_Rules_Manual.pdf" rel="noopener" target="_blank"><i class="fa fa-external-link text-primary" aria-hidden="true" data-original-title="" title=""></i></a>
                                        
                    <p><?php echo xlt("Once a rule is setup it can be enabled for a particular client");?>.
                    
                    <p><?php echo xlt("Upon reaching a predetermined point, either a date or value, the rule will trigger one or more events");?>:
                        <ul>
                            <li><?php echo xlt("Active Alert - that presents as a pop-up notification when a client's chart is entered"); ?></li>
                            <li><?php echo xlt("Passive Alert - that will be displayed in the Clinical Reminders widget section"); ?></li>
                            <li><?php echo xlt("Client Reminder - that is used to communicate relevant information pertaining to that particular Clinical Decision Rule and is shown in the Client Reminders widget as Well as under Administration > Client Reminders"); ?></li>
                         </ul>
                    
                    <p><?php echo xlt("More information about Clinical Decision Rule can be found here");?>.
                    <a href="https://www.open-emr.org/wiki/index.php/CDR_User_Manual" rel="noopener" target="_blank"><i class="fa fa-external-link text-primary" aria-hidden="true" data-original-title="" title=""></i></a>
                    
                    <p><?php echo xlt("Appointments - shows all future appointments as well as Recalls");?>.
                    
                    <p><?php echo xlt("Recurrent Appointments - shows all recurring appointments");?>.
                    
                    <p><?php echo xlt("Past Appointments - will show all past appointments");?>.
                    
                    <p><?php echo xlt("Medical Problems - will show the client's medical issues, Issues > Medical Problems");?>.
                    
                    <p><?php echo xlt("Allergies - will show the allergies listed under Issues > Allergies. If eRx is enabled the allergy list has to be entered on the eRx page");?>.
                    
                    <p><?php echo xlt("Medications - lists the medications under Issues > Medications. If eRx is enabled the medication list has to be entered on the eRx page");?>.
                    
                    <p><?php echo xlt("Immunizations - lists immunization history and allows for adding new entries or editing existing ones");?>.
                    
                    <p><?php echo xlt("Prescription - lists the prescriptions of the current client");?>.
                    
                    <p><?php echo xlt("Tracks - if the Track Anything feature is enabled it will display a list of values that can be tracked and graphed");?>.
                    <a href="https://www.open-emr.org/wiki/index.php/Track_Anything_Form" rel="noopener" target="_blank"><i class="fa fa-external-link text-primary" aria-hidden="true" data-original-title="" title=""></i></a>
                </div>    
            </div>
           
        </div><!--end of container div-->
        <script>
           $('#show_hide').click(function() {
                var elementTitle = $('#show_hide').prop('title');
                var hideTitle = '<?php echo xla('Click to Hide'); ?>';
                var showTitle = '<?php echo xla('Click to Show'); ?>';
                $('.hideaway').toggle('1000');
                $(this).toggleClass('fa-eye-slash fa-eye');
                if (elementTitle == hideTitle) {
                    elementTitle = showTitle;
                } else if (elementTitle == showTitle) {
                    elementTitle = hideTitle;
                }
                $('#show_hide').prop('title', elementTitle);
            });
        </script>
    </body>
</html>
