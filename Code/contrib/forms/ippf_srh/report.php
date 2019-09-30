<?php
// Copyright (C) 2009 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

require_once("../../globals.php");
require_once($GLOBALS["srcdir"] . "/api.inc");

// This function is invoked from printClientForms in report.inc
// when viewing a "comprehensive client report".  Also from
// interface/client_file/encounter/forms.php.
//
function ippf_srh_report($pid, $encounter, $cols, $id)
{
    require_once($GLOBALS["srcdir"] . "/options.inc.php");
    echo "<table>\n";
    display_layout_rows('SRH', sqlQuery("SELECT * FROM form_ippf_srh WHERE id = ?", array($id)));
    echo "</table>\n";
}
