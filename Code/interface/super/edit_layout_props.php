<?php
/**
 * Edit Layout Properties.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Rod Roark <rod@sunsetsystems.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2016-2017 Rod Roark <rod@sunsetsystems.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$phpgacl_location/gacl_api.class.php");

use OpenEMR\Common\Csrf\CsrfUtils;

$alertmsg = "";

// Check authorization.
$thisauth = acl_check('admin', 'super');
if (!$thisauth) {
    die(xlt('Not authorized'));
}

$layout_id = empty($_GET['layout_id']) ? '' : $_GET['layout_id'];
$group_id  = empty($_GET['group_id' ]) ? '' : $_GET['group_id' ];
?>
<html>
<head>
<title><?php echo xlt("Edit Layout Properties"); ?></title>
<link rel="stylesheet" href='<?php echo $css_header ?>' type='text/css'>

<style>
td { font-size:10pt; }
</style>

<script type="text/javascript" src="<?php echo $webroot ?>/interface/main/tabs/js/include_opener.js?v=<?php echo $v_js_includes; ?>"></script>
<script type="text/javascript" src="../../library/textformat.js?v=<?php echo $v_js_includes; ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['assets_static_relative']; ?>/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="../../library/dialog.js?v=<?php echo $v_js_includes; ?>"></script>

<script language="JavaScript">

<?php require($GLOBALS['srcdir'] . "/restoreSession.php"); ?>

// The name of the input element to receive a found code.
var current_sel_name = '';

// This invokes the "dynamic" find-code popup.
function sel_related(elem, codetype) {
 current_sel_name = elem ? elem.name : '';
 var url = '<?php echo $rootdir ?>/client_file/encounter/find_code_dynamic.php';
 if (codetype) url += '?codetype=' + encodeURIComponent(codetype);
 dlgopen(url, '_blank', 800, 500);
}

// This is for callback by the find-code popup.
// Appends to or erases the current list of related codes.
function set_related(codetype, code, selector, codedesc) {
 var f = document.forms[0];
 // frc will be the input element containing the codes.
 var frc = f[current_sel_name];
 var s = frc.value;
 if (code) {
  if (s.length > 0) {
   s  += ';';
  }
  s  += codetype + ':' + code;
 } else {
  s  = '';
 }
 frc.value = s;
 return '';
}

// This is for callback by the find-code popup.
// Deletes the specified codetype:code from the active input element.
function del_related(s) {
  var f = document.forms[0];
  my_del_related(s, f[current_sel_name], false);
}

// This is for callback by the find-code popup.
// Returns the array of currently selected codes with each element in codetype:code format.
function get_related() {
  var f = document.forms[0];
  if (current_sel_name) {
    return f[current_sel_name].value.split(';');
  }
  return new Array();
}

</script>

</head>

<body class="body_top">

<?php
if ($_POST['form_submit'] && !$alertmsg) {
    if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }

    if ($group_id) {
        $sets =
            "grp_subtitle = ?, "   .
            "grp_columns = ?";
        $sqlvars = array(
            $_POST['form_subtitle'],
            intval($_POST['form_columns']),
        );
    } else {
        $sets =
            "grp_title = ?, "      .
            "grp_subtitle = ?, "   .
            "grp_mapping = ?, "    .
            "grp_seq = ?, "        .
            "grp_activity = ?, "   .
            "grp_repeats = ?, "    .
            "grp_columns = ?, "    .
            "grp_size = ?, "       .
            "grp_issue_type = ?, " .
            "grp_aco_spec = ?, "   .
            "grp_services = ?, "   .
            "grp_products = ?, "   .
            "grp_diags = ?";
        $sqlvars = array(
            $_POST['form_title'],
            $_POST['form_subtitle'],
            $_POST['form_mapping'],
            intval($_POST['form_seq']),
            empty($_POST['form_activity']) ? 0 : 1,
            intval($_POST['form_repeats']),
            intval($_POST['form_columns']),
            intval($_POST['form_size']),
            $_POST['form_issue'],
            $_POST['form_aco'],
            empty($_POST['form_services']) ? '' : (empty($_POST['form_services_codes']) ? '*' : $_POST['form_services_codes']),
            empty($_POST['form_products']) ? '' : (empty($_POST['form_products_codes']) ? '*' : $_POST['form_products_codes']),
            empty($_POST['form_diags'   ]) ? '' : (empty($_POST['form_diags_codes'   ]) ? '*' : $_POST['form_diags_codes'   ]),
        );
    }

    if ($layout_id) {
      // They have edited an existing layout.
        $sqlvars[] = $layout_id;
        $sqlvars[] = $group_id;
        sqlStatement(
            "UPDATE layout_group_properties SET $sets " .
            "WHERE grp_form_id = ? AND grp_group_id = ?",
            $sqlvars
        );
    } else if (!$group_id) {
        // They want to add a new layout. New groups not supported here.
        $form_form_id = $_POST['form_form_id'];
        if (preg_match('/(LBF|LBT)[0-9A-Za-z_]+/', $form_form_id)) {
            $tmp = sqlQuery(
                "SELECT grp_form_id FROM layout_group_properties WHERE " .
                "grp_form_id = ? AND grp_group_id = ''",
                array($form_form_id)
            );
            if (empty($row)) {
                $sqlvars[] = $form_form_id;
                sqlStatement(
                    "INSERT INTO layout_group_properties " .
                    "SET $sets, grp_form_id = ?, grp_group_id = ''",
                    $sqlvars
                );
                $layout_id = $form_form_id;
            } else {
                $alertmsg = xl('This layout ID already exists');
            }
        } else {
            $alertmsg = xl('Invalid layout ID');
        }
    }

    // Close this window and redisplay the layout editor.
    //
    echo "<script language='JavaScript'>\n";
    if ($alertmsg) {
        echo " alert(" . js_escape($alertmsg) . ");\n";
    }
    echo " if (opener.refreshme) opener.refreshme(" . js_escape($layout_id) . ");\n";
    echo " window.close();\n";
    echo "</script></body></html>\n";
    exit();
}

$row = array(
    'grp_form_id'    => '',
    'grp_title'      => '',
    'grp_subtitle'   => '',
    'grp_mapping'    => 'Clinical',
    'grp_seq'        => '0',
    'grp_activity'   => '1',
    'grp_repeats'    => '0',
    'grp_columns'    => '4',
    'grp_size'       => '9',
    'grp_issue_type' => '',
    'grp_aco_spec'   => '',
    'grp_services'   => '',
    'grp_products'   => '',
    'grp_diags'      => '',
);

if ($layout_id) {
    $row = sqlQuery(
        "SELECT * FROM layout_group_properties WHERE " .
        "grp_form_id = ? AND grp_group_id = ?",
        array($layout_id, $group_id)
    );
    if (empty($row)) {
        die(xlt('This layout does not exist.'));
    }
}
?>

<form method='post' action='edit_layout_props.php?<?php echo "layout_id=" . attr_url($layout_id) . "&group_id=" . attr_url($group_id); ?>'>
<input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
<center>

<table border='0' width='100%'>
<?php if (empty($layout_id)) { ?>
 <tr>
  <td valign='top' width='1%' nowrap>
    <?php echo xlt('Layout ID'); ?>
  </td>
  <td>
   <input type='text' size='31' maxlength='31' name='form_form_id'
    value='' /><br />
    <?php echo xlt('Visit form ID must start with LBF. Transaction form ID must start with LBT.') ?>
  </td>
 </tr>
<?php } ?>

<?php if (empty($group_id)) { ?>
 <tr>
  <td valign='top' width='1%' nowrap>
    <?php echo xlt('Title'); ?>
  </td>
  <td>
   <input type='text' size='40' name='form_title' style='width:100%'
    value='<?php echo attr($row['grp_title']); ?>' />
  </td>
 </tr>
<?php } ?>

 <tr>
  <td valign='top' width='1%' nowrap>
    <?php echo xlt('Subtitle'); ?>
  </td>
  <td>
   <input type='text' size='40' name='form_subtitle' style='width:100%'
    value='<?php echo attr($row['grp_subtitle']); ?>' />
  </td>
 </tr>

<?php if (empty($group_id)) { ?>
 <tr>
  <td valign='top' width='1%' nowrap>
    <?php echo xlt('Category'); ?>
  </td>
  <td>
   <input type='text' size='40' name='form_mapping' style='width:100%'
    value='<?php echo attr($row['grp_mapping']); ?>' />
  </td>
 </tr>

 <tr>
  <td valign='top' width='1%' nowrap>
    <?php echo xlt('Active'); ?>
  </td>
  <td>
   <input type='checkbox' name='form_activity' <?php echo ($row['grp_activity']) ? "checked" : ""; ?> />
  </td>
 </tr>

 <tr>
  <td valign='top' width='1%' nowrap>
    <?php echo xlt('Sequence'); ?>
  </td>
  <td>
   <input type='text' size='4' name='form_seq'
    value='<?php echo attr($row['grp_seq']); ?>' />
  </td>
 </tr>

 <tr>
  <td valign='top' width='1%' nowrap>
    <?php echo xlt('Repeats'); ?>
  </td>
  <td>
   <input type='text' size='4' name='form_repeats'
    value='<?php echo attr($row['grp_repeats']); ?>' />
  </td>
 </tr>

<?php } ?>

 <tr>
  <td valign='top' nowrap>
    <?php echo xlt('Layout Columns'); ?>
  </td>
  <td>
   <select name='form_columns'>
<?php
  echo "<option value='0'>" . xlt('Default') . "</option>\n";
for ($cols = 2; $cols <= 10; ++$cols) {
    echo "<option value='" . attr($cols) . "'";
    if ($cols == $row['grp_columns']) {
        echo " selected";
    }
    echo ">" . text($cols) . "</option>\n";
}
?>
   </select>
  </td>
 </tr>

<?php if (empty($group_id)) { ?>
 <tr>
  <td valign='top' nowrap>
    <?php echo xlt('Font Size'); ?>
  </td>
  <td>
   <select name='form_size'>
    <?php
    echo "<option value='0'>" . xlt('Default') . "</option>\n";
    for ($size = 5; $size <= 15; ++$size) {
        echo "<option value='" . attr($size) . "'";
        if ($size == $row['grp_size']) {
            echo " selected";
        }
        echo ">" . text($size) . "</option>\n";
    }
    ?>
   </select>
  </td>
 </tr>

 <tr>
  <td valign='top' nowrap>
    <?php echo xlt('Issue Type'); ?>
  </td>
  <td>
   <select name='form_issue'>
    <option value=''></option>
    <?php
    $itres = sqlStatement(
        "SELECT type, singular FROM issue_types " .
        "WHERE category = ? AND active = 1 ORDER BY singular",
        array($GLOBALS['ippf_specific'] ? 'ippf_specific' : 'default')
    );
    while ($itrow = sqlFetchArray($itres)) {
        echo "<option value='" . attr($itrow['type']) . "'";
        if ($itrow['type'] == $row['grp_issue_type']) {
            echo " selected";
        }
        echo ">" . xlt($itrow['singular']) . "</option>\n";
    }
    ?>
   </select>
  </td>
 </tr>

 <tr>
  <td valign='top' nowrap>
    <?php echo xlt('Access Control'); ?>
  </td>
  <td>
   <select name='form_aco' style='width:100%'>
    <option value=''></option>
    <?php
    $gacl = new gacl_api();
  // collect and sort all aco objects
    $list_aco_objects = $gacl->get_objects(null, 0, 'ACO');
    ksort($list_aco_objects);
    foreach ($list_aco_objects as $seckey => $dummy) {
        if (empty($dummy)) {
            continue;
        }
        asort($list_aco_objects[$seckey]);
        $aco_section_data = $gacl->get_section_data($seckey, 'ACO');
        $aco_section_title = $aco_section_data[3];
        echo " <optgroup label='" . xla($aco_section_title) . "'>\n";
        foreach ($list_aco_objects[$seckey] as $acokey) {
            $aco_id = $gacl->get_object_id($seckey, $acokey, 'ACO');
            $aco_data = $gacl->get_object_data($aco_id, 'ACO');
            $aco_title = $aco_data[0][3];
            echo "  <option value='" . attr("$seckey|$acokey") . "'";
            if ("$seckey|$acokey" == $row['grp_aco_spec']) {
                echo " selected";
            }
            echo ">" . xlt($aco_title) . "</option>\n";
        }
        echo " </optgroup>\n";
    }
    ?>
   </select>
  </td>
 </tr>

 <tr>
  <td valign='top' width='1%' nowrap>
   <input type='checkbox' name='form_services' <?php echo ($row['grp_services']) ? "checked" : ""; ?> />
    <?php echo xlt('Show Services Section'); ?>
  </td>
  <td>
   <input type='text' size='40' name='form_services_codes' onclick='sel_related(this, "MA")' style='width:100%'
    value='<?php echo ($row['grp_services'] != '*') ? attr($row['grp_services']) : ""; ?>' />
  </td>
 </tr>

 <tr>
  <td valign='top' width='1%' nowrap>
   <input type='checkbox' name='form_products' <?php echo ($row['grp_products']) ? "checked" : ""; ?> />
    <?php echo xlt('Show Products Section'); ?>
  </td>
  <td>
   <input type='text' size='40' name='form_products_codes' onclick='sel_related(this, "PROD")' style='width:100%'
    value='<?php echo ($row['grp_products'] != '*') ? attr($row['grp_products']) : ""; ?>' />
  </td>
 </tr>

 <tr>
  <td valign='top' width='1%' nowrap>
   <input type='checkbox' name='form_diags' <?php echo ($row['grp_diags']) ? "checked" : ""; ?> />
    <?php echo xlt('Show Diagnoses Section'); ?>
  </td>
  <td>
   <input type='text' size='40' name='form_diags_codes' onclick='sel_related(this, "ICD10")' style='width:100%'
    value='<?php echo ($row['grp_diags'] != '*') ? attr($row['grp_diags']) : ""; ?>' />
  </td>
 </tr>

<?php } ?>

</table>

<p>
<input type='submit' name='form_submit' value='<?php echo xla('Submit'); ?>' />

&nbsp;
<input type='button' value='<?php echo xla('Cancel'); ?>' onclick='window.close()' />
</p>

</center>
</form>
<script language='JavaScript'>
<?php
if ($alertmsg) {
    echo " alert(" . js_escape($alertmsg) . ");\n";
    echo " window.close();\n";
}
?>
</script>
</body>
</html>
