<?php
/**
 * This file is part of OpenEMR.
 *
 * @link https://github.com/openemr/openemr/tree/master
 * @license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace OpenEMR\Events\ClientSelect;

use OpenEMR\Events\AbstractBoundFilterEvent;

/**
 * Event object for creating custom client filters for client select (New/Search) results
 *
 * @package OpenEMR\Events
 * @subpackage PatinetSelect
 * @author Ken Chapple <ken@mi-squared.com>
 * @copyright Copyright (c) 2019 Ken Chapple <ken@mi-squared.com>
 */
class ClientSelectFilterEvent extends AbstractBoundFilterEvent
{
    /**
     * The customWhereFilter event occurs in the client_select.php script that generates
     * results for the legacy client new/search dialog. Subscribe to this event and set a customWhereFilter to
     * alter the results of the client finder query
     */
    const EVENT_HANDLE = 'clientSelect.customFilter';
}
