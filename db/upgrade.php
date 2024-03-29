<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin "Evaluations (evasys) QAHE"
 *
 * @package    block_onlinesurveyqa
 * @copyright  2020 Alexander Bias on behalf of evasys GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade steps for this plugin
 * @param int $oldversion the version we are upgrading from
 * @return boolean
 */
function xmldb_block_onlinesurveyqa_upgrade($oldversion) {

    // From now on, the setting "block_onlinesurveyqa|setting_survey_server" uses SOAP API Version 61 instead of Version 51.
    if ($oldversion < 2020010903) {
        // Check if the setting is set in this Moodle instance.
        $oldsetting = get_config('block_onlinesurveyqa', 'survey_server');
        if (!empty($oldsetting) && strpos($oldsetting, 'soapserver-v51.wsdl') !== false) {

            // Replace the version in the setting.
            $newsetting = str_replace('soapserver-v51.wsdl', 'soapserver-v61.wsdl', $oldsetting);

            // Write the setting back to the DB.
            set_config('survey_server', $newsetting, 'block_onlinesurveyqa');

            // Show an info message that the SOAP API version has been changed automatically.
            $message = get_string('upgrade_notice_2020010900', 'block_onlinesurveyqa',
                    array ('old' => $oldsetting, 'new' => $newsetting));
            echo html_writer::tag('div', $message, array('class' => 'alert alert-info'));
        }

        // Remember upgrade savepoint.
        upgrade_plugin_savepoint(true, 2020010903, 'block', 'onlinesurveyqa');
    }

    // The setting 'additionalclass' was removed.
    if ($oldversion < 2020052200) {
        // Check if the setting is set in this Moodle instance.
        $oldsetting = get_config('block_onlinesurveyqa', 'additionalclass');
        if (!empty($oldsetting)) {

            // Remove the setting in the DB.
            unset_config('additionalclass', 'block_onlinesurveyqa');
        }

        // Remember upgrade savepoint.
        upgrade_plugin_savepoint(true, 2020052200, 'block', 'onlinesurveyqa');
    }

    // Re-branding of the evasys brand.
    if ($oldversion < 2020060404) {
        // Check if the blocktitle is set in this Moodle instance.
        $blocktitlesetting = get_config('block_onlinesurveyqa', 'blocktitle');
        if (!empty($blocktitlesetting)) {

            // If the blocktitle contains the substring 'EvaSys' (case-sensitive).
            if (strpos($blocktitlesetting, 'EvaSys') !== false) {
                // Replace the substring with 'evasys' (case-sensitive).
                $newblocktitle = str_replace('EvaSys', 'evasys', $blocktitlesetting);

                // Write the setting back to the DB.
                set_config('blocktitle', $newblocktitle, 'block_onlinesurveyqa');
            }
        }

        // Remember upgrade savepoint.
        upgrade_plugin_savepoint(true, 2020060404, 'block', 'onlinesurveyqa');
    }

    return true;
}
