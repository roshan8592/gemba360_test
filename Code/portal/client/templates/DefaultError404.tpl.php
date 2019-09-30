<?php
/**
 * DefaultError404.tpl.php
 *
 * @package   OpenEMR
 * @link      https://www.open-emr.org
 * @author    Jerry Padgett <sjpadgett@gmail.com>
 * @copyright Copyright (c) 2016-2017 Jerry Padgett <sjpadgett@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

    $this->assign('title', xlt("Client Portal") . " | " . xlt("File Not Found"));
    $this->assign('nav', 'home');

    $this->display('_Header.tpl.php');
?>

<div class="container">

    <h1><?php echo xlt('Oh Snap!'); ?></h1>

    <!-- this is used by app.js for scraping -->
    <!-- ERROR The page you requested was not found /ERROR -->

    <p><?php echo xlt('The page you requested was not found. Please check that you typed the URL correctly.'); ?></p>

</div> <!-- /container -->

<?php
    $this->display('_Footer.tpl.php');
?>
