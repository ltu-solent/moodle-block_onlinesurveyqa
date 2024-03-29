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
 * @copyright  2018 Soon Systems GmbH on behalf of evasys GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    require_once($CFG->dirroot . '/blocks/onlinesurveyqa/locallib.php');

    /*************************/
    /* Appearance settings.
    /*************************/

    // Heading.
    $settings->add(
        new admin_setting_heading('block_onlinesurveyqa/setting_heading_appearance',
            get_string('setting_heading_appearance', 'block_onlinesurveyqa', null, true),
            get_string('setting_heading_appearance_desc', 'block_onlinesurveyqa', null, true)
        )
    );


    // Block title.
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurveyqa/blocktitle',
                    get_string('setting_blocktitle', 'block_onlinesurveyqa', null, true),
                    get_string('setting_blocktitle_desc', 'block_onlinesurveyqa', null, true). ' '.
                            get_string('setting_blocktitle_multilangnote', 'block_onlinesurveyqa', null, true),
                    get_string('pluginname', 'block_onlinesurveyqa', null, true)
            )
    );


    // Display mode.
    $presentationoptions = array();
    $presentationoptions["brief"] = get_string('setting_presentation_brief', 'block_onlinesurveyqa', null, true);
    $presentationoptions["detailed"] = get_string('setting_presentation_detailed', 'block_onlinesurveyqa', null, true);
    $settings->add(
        new admin_setting_configselect('block_onlinesurveyqa/presentation',
            get_string('setting_presentation', 'block_onlinesurveyqa', null, true),
            get_string('setting_presentation_desc', 'block_onlinesurveyqa', null, true),
            "brief",
            $presentationoptions));
    unset($presentationoptions);


    // Hide empty block.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_onlinesurveyqa/survey_hide_empty',
            get_string('setting_survey_hide_empty', 'block_onlinesurveyqa', null, true),
            get_string('setting_survey_hide_empty_desc', 'block_onlinesurveyqa', null, true),
            0
        )
    );


    // Offer zoom.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_onlinesurveyqa/offer_zoom',
            get_string('setting_offer_zoom', 'block_onlinesurveyqa', null, true),
            get_string('setting_offer_zoom_desc', 'block_onlinesurveyqa', null, true),
            1
        )
    );


    // Show spinner.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_onlinesurveyqa/show_spinner',
            get_string('setting_show_spinner', 'block_onlinesurveyqa', null, true),
            get_string('setting_show_spinner_desc', 'block_onlinesurveyqa', null, true),
            1
        )
    );


    // Pop-up-Info active.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_onlinesurveyqa/survey_show_popupinfo',
            get_string('setting_survey_show_popupinfo', 'block_onlinesurveyqa', null, true),
            get_string('setting_survey_show_popupinfo_desc', 'block_onlinesurveyqa', null, true),
            0
        )
    );


    // Pop-up-Info title.
    $settings->add(
        new admin_setting_configtext(
            'block_onlinesurveyqa/survey_popupinfo_title',
            get_string('setting_survey_popupinfo_title', 'block_onlinesurveyqa', null, true),
            get_string('setting_survey_popupinfo_title_desc', 'block_onlinesurveyqa', null, true). ' '.
                    get_string('setting_blocktitle_multilangnote', 'block_onlinesurveyqa', null, true),
            get_string('setting_survey_popupinfo_title_default', 'block_onlinesurveyqa', null, true)
        )
    );


    // Pop-up-Info content.
    $settings->add(
        new admin_setting_confightmleditor(
            'block_onlinesurveyqa/survey_popupinfo_content',
            get_string('setting_survey_popupinfo_content', 'block_onlinesurveyqa', null, true),
            get_string('setting_survey_popupinfo_content_desc', 'block_onlinesurveyqa', null, true). ' '.
                    get_string('setting_blocktitle_multilangnote', 'block_onlinesurveyqa', null, true),
            get_string('setting_survey_popupinfo_content_default', 'block_onlinesurveyqa', null, true)
        )
    );


    /*************************/
    /* Communication settings.
    /*************************/

    // Heading.
    $settings->add(
        new admin_setting_heading('block_onlinesurveyqa/setting_heading_communication',
            get_string('setting_heading_communication', 'block_onlinesurveyqa', null, true),
            get_string('setting_heading_communication_desc', 'block_onlinesurveyqa', null, true)
        )
    );


    // Communication channel.
    $communicationoptions = array();
    $communicationoptions["SOAP"] = get_string('soap', 'block_onlinesurveyqa', null, true);
    $communicationoptions["LTI"] = get_string('lti', 'block_onlinesurveyqa', null, true);
    $settings->add(
            new admin_setting_configselect('block_onlinesurveyqa/connectiontype',
                    get_string('setting_communication_interface', 'block_onlinesurveyqa', null, true),
                    get_string('setting_communication_interface_desc', 'block_onlinesurveyqa', null, true),
                    "LTI",
                    $communicationoptions
            )
    );
    unset($communicationoptions);


    // User Identifier.
    $userdataoptions = array();
    $userdataoptions["email"] = get_string('email', 'core', null, true);
    $userdataoptions["username"] = get_string('username', 'core', null, true);
    $settings->add(
            new admin_setting_configselect('block_onlinesurveyqa/useridentifier',
                    get_string('setting_useridentifier', 'block_onlinesurveyqa', null, true),
                    get_string('setting_useridentifier_desc', 'block_onlinesurveyqa', null, true),
                    "email",
                    $userdataoptions
            )
    );
    unset($userdataoptions);


    // Custom field in evasys.
    $customfieldidoptions = array();
    $customfieldnr = get_string('setting_customfieldnumber', 'block_onlinesurveyqa', null, true);
    $customfieldidoptions[1] = $customfieldnr." 1";
    $customfieldidoptions[2] = $customfieldnr." 2";
    $customfieldidoptions[3] = $customfieldnr." 3";
    $settings->add(
            new admin_setting_configselect('block_onlinesurveyqa/customfieldnumber',
                    get_string('setting_customfieldnumberinevasys', 'block_onlinesurveyqa', null, true),
                    get_string('setting_customfieldnumberinevasys_desc', 'block_onlinesurveyqa', null, true),
                    "1",
                    $customfieldidoptions
            )
    );
    unset($customfieldidoptions);


    // Connection timeout in seconds.
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurveyqa/survey_timeout',
                    get_string('setting_survey_timeout', 'block_onlinesurveyqa', null, true),
                    get_string('setting_survey_timeout_desc', 'block_onlinesurveyqa', null, true),
                    0,
                    PARAM_INT
            )
    );


    /*************************/
    /* SOAP settings.
    /*************************/

    // Heading.
    $settings->add(
            new admin_setting_heading('block_onlinesurveyqa/setting_heading_soap',
                    get_string('setting_heading_soap', 'block_onlinesurveyqa', null, true),
                    get_string('setting_heading_soap_desc', 'block_onlinesurveyqa', null, true)
            )
    );


    // evasys server (SOAP).
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurveyqa/survey_server',
                    get_string('setting_survey_server', 'block_onlinesurveyqa', null, true),
                    get_string('setting_survey_server_desc', 'block_onlinesurveyqa', null, true),
                    '',
                    PARAM_RAW,
                    80
            )
    );


    // evasys path for online surveys (SOAP).
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurveyqa/survey_login',
                    get_string('setting_survey_login', 'block_onlinesurveyqa', null, true),
                    get_string('setting_survey_login_desc', 'block_onlinesurveyqa', null, true),
                    '',
                    PARAM_RAW,
                    80
            )
    );


    // evasys SOAP user.
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurveyqa/survey_user',
                    get_string('setting_survey_user', 'block_onlinesurveyqa', null, true),
                    get_string('setting_survey_user_desc', 'block_onlinesurveyqa', null, true),
                    '',
                    PARAM_RAW
            )
    );


    // evasys SOAP password.
    $settings->add(
            new admin_setting_configpasswordunmask(
                    'block_onlinesurveyqa/survey_pwd',
                    get_string('setting_survey_pwd', 'block_onlinesurveyqa', null, true),
                    get_string('setting_survey_pwd_desc', 'block_onlinesurveyqa', null, true),
                    ''
            )
    );


    // SOAP request at pageview.
    $settings->add(
            new admin_setting_configcheckbox(
                    'block_onlinesurveyqa/soap_request_eachtime',
                    get_string('setting_soap_request_eachtime', 'block_onlinesurveyqa', null, true),
                    get_string('setting_soap_request_eachtime_desc', 'block_onlinesurveyqa', null, true),
                    0
            )
    );


    /*************************/
    /* LTI settings.
    /*************************/

    // Heading.
    $settings->add(
            new admin_setting_heading('block_onlinesurveyqa/setting_heading_lti',
                    get_string('setting_heading_lti', 'block_onlinesurveyqa', null, true),
                    get_string('setting_heading_lti_desc', 'block_onlinesurveyqa', null, true)
            )
    );


    // URL of the LTI Provider.
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurveyqa/lti_url',
                    get_string('setting_survey_lti_url', 'block_onlinesurveyqa', null, true),
                    get_string('setting_survey_lti_url_desc', 'block_onlinesurveyqa', null, true),
                    '',
                    PARAM_RAW,
                    80
            )
    );


    // LTI password.
    $settings->add(
            new admin_setting_configpasswordunmask(
                    'block_onlinesurveyqa/lti_password',
                    get_string('setting_survey_lti_password', 'block_onlinesurveyqa', null, true),
                    get_string('setting_survey_lti_password_desc', 'block_onlinesurveyqa', null, true),
                    ''
            )
    );


    // Custom parameters.
    $settings->add(
            new admin_setting_configtextarea(
                    'block_onlinesurveyqa/lti_customparameters',
                    get_string('setting_lti_customparameters', 'block_onlinesurveyqa', null, true),
                    get_string('setting_lti_customparameters_desc', 'block_onlinesurveyqa', null, true),
                    '',
                    PARAM_RAW,
                    50,
                    6
            )
    );


    // Role mapping "Instructor".
    $choices = array();
    $roles = get_all_roles();
    $systemcontext = context_system::instance();
    $rolenames = role_fix_names($roles, $systemcontext, ROLENAME_ORIGINAL);
    if (!empty($rolenames)) {
        foreach ($rolenames as $key => $role) {
            if (!array_key_exists($role->id, $choices)) {
                $choices[$role->id] = $role->localname;
            }
        }
    }
    $settings->add(
            new admin_setting_configmultiselect(
                    'block_onlinesurveyqa/lti_instructormapping',
                    get_string('setting_lti_instructormapping', 'block_onlinesurveyqa', null, true),
                    get_string('setting_lti_instructormapping_desc', 'block_onlinesurveyqa', null, true),
                    array(3, 4),
                    $choices
            )
    );


    // Role mapping "Learner".
    $settings->add(
            new admin_setting_configmultiselect(
                    'block_onlinesurveyqa/lti_learnermapping',
                    get_string('setting_lti_learnermapping', 'block_onlinesurveyqa', null, true),
                    get_string('setting_lti_learnermapping_desc', 'block_onlinesurveyqa', null, true),
                    array(5),
                    $choices
            )
    );
    unset($roles);
    unset($rolenames);
    unset($choices);


    /*************************/
    /* Expert settings.
    /*************************/

    // Heading.
    $settings->add(
        new admin_setting_heading('block_onlinesurveyqa/setting_heading_expert',
            get_string('setting_heading_expert', 'block_onlinesurveyqa', null, true),
            get_string('setting_heading_expert_desc', 'block_onlinesurveyqa', null, true)
        )
    );


    // Debug.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_onlinesurveyqa/survey_debug',
            get_string('setting_survey_debug', 'block_onlinesurveyqa', null, true),
            get_string('setting_survey_debug_desc', 'block_onlinesurveyqa', null, true),
            0
        )
    );


    // Additional CSS for iframe.
    $settings->add(
        new admin_setting_configtextarea(
            'block_onlinesurveyqa/additionalcss',
            get_string('setting_additionalcss', 'block_onlinesurveyqa', null, true),
            get_string('setting_additionalcss_desc', 'block_onlinesurveyqa', null, true),
            '',
            PARAM_RAW,
            50,
            6
        )
    );


    // Learner regular expression.
    $settings->add(
        new admin_setting_configtext(
            'block_onlinesurveyqa/lti_regex_learner',
            get_string('setting_lti_regex_learner', 'block_onlinesurveyqa', null, true),
            get_string('setting_lti_regex_learner_desc', 'block_onlinesurveyqa', null, true),
            BLOCK_ONLINESURVEYQA_LTI_REGEX_LEARNER_DEFAULT,
            PARAM_RAW,
            80
        )
    );


    // Instructor regular expression.
    $settings->add(
        new admin_setting_configtext(
            'block_onlinesurveyqa/lti_regex_instructor',
            get_string('setting_lti_regex_instructor', 'block_onlinesurveyqa', null, true),
            get_string('setting_lti_regex_instructor_desc', 'block_onlinesurveyqa', null, true),
            BLOCK_ONLINESURVEYQA_LTI_REGEX_INSTRUCTOR_DEFAULT,
            PARAM_RAW,
            80
        )
    );
}
