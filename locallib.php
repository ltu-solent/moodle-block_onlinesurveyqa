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
 * Plugin "Evaluations (evasys) QAHE" - Local library
 *
 * @package    block_onlinesurveyqa
 * @copyright  2018 Soon Systems GmbH on behalf of evasys GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('BLOCK_ONLINESURVEYQA_COMM_SOAP', "SOAP");
define('BLOCK_ONLINESURVEYQA_COMM_LTI', "LTI");
define('BLOCK_ONLINESURVEYQA_DEFAULT_TIMEOUT', 15);

define('BLOCK_ONLINESURVEYQA_LTI_REGEX_LEARNER_DEFAULT', '/<(p){1}(.){0,}[\s]{0,}(data-participated="false"){1}[\s]{0,}/');
define('BLOCK_ONLINESURVEYQA_LTI_REGEX_INSTRUCTOR_DEFAULT',
        '/<(div){1}[\s]{1,}(class=){1}["|\']{1}[a-z]{0,}[\s]{0,}(response-box){1}[\s]{0,}[a-z]{0,}[\s]{0,}["|\']{1}>/');

define('BLOCK_ONLINESURVEYQA_PRESENTATION_BRIEF', "brief");
define('BLOCK_ONLINESURVEYQA_PRESENTATION_DETAILED', "detailed");

/**
 * Request surveys for the current user according to email or username and displays the result.
 * @param string $config block settings of "block_onlinesurveyqa"
 * @param string $moodleusername username for SOAP request
 * @param string $moodleemail email for SOAP request
 * @param int $modalzoom indicates if the modal list popup is open or not
 * @return string
 */
function block_onlinesurveyqa_get_soap_content($config = null, $moodleusername = '', $moodleemail = '', $modalzoom = 0) {
    global $SESSION;

    $surveyurl = 'indexstud.php?type=html&user_tan=';

    if (empty($config)) {
        $config = get_config("block_onlinesurveyqa");
    }

    $connectiontype = $config->connectiontype;
    $surveyurl = $config->survey_login.$surveyurl;
    $wsdl = $config->survey_server;
    $soapuser = $config->survey_user;
    $soappassword = $config->survey_pwd;
    $debugmode = $config->survey_debug;

    $hideempty = $config->survey_hide_empty;
    $offerzoom = $config->offer_zoom;

    $timeout = isset($config->survey_timeout) ? $config->survey_timeout : BLOCK_ONLINESURVEYQA_DEFAULT_TIMEOUT;

    // Parse wsdlnamespace from the wsdl url.
    preg_match('/\/([^\/]+\.wsdl)$/', $wsdl, $matches);

    $soapcontentstr = '';

    if (count($matches) == 2) {
        $wsdlnamespace = $matches[1];

        $soapconfigobj = new stdClass();
        $soapconfigobj->connectiontype = $connectiontype;
        $soapconfigobj->wsdl = $wsdl;
        $soapconfigobj->timeout = $timeout;
        $soapconfigobj->debugmode = $debugmode;
        $soapconfigobj->soapuser = $soapuser;
        $soapconfigobj->soappassword = $soappassword;
        $soapconfigobj->wsdlnamespace = $wsdlnamespace;
        $soapconfigobj->useridentifier = $config->useridentifier;
        $soapconfigobj->moodleemail = $moodleemail;
        $soapconfigobj->moodleusername = $moodleusername;
        $soapconfigobj->customfieldnumber = $config->customfieldnumber;
        $soapconfigobj->coursecode = '';

        $result = new stdClass();

        $soaprequesteachtime = $config->soap_request_eachtime;

        // Get surveys if no surveys in SESSION or debug mode for the block is enabled.
        if (!isset($SESSION->block_onlinesurveyqa_surveykeys) || $debugmode || $soaprequesteachtime) {
            $result = block_onlinesurveyqa_get_surveys($soapconfigobj);
            $SESSION->block_onlinesurveyqa_surveykeys = $result->surveys;

            $SESSION->block_onlinesurveyqa_error = $result->error;
        }

        if (isset($SESSION->block_onlinesurveyqa_error)) {
            $result->error = $SESSION->block_onlinesurveyqa_error;
        }

        if (is_object($SESSION->block_onlinesurveyqa_surveykeys)) {
            if (!is_array($SESSION->block_onlinesurveyqa_surveykeys->OnlineSurveyKeys)) {
                $SESSION->block_onlinesurveyqa_surveykeys->OnlineSurveyKeys = array(
                                $SESSION->block_onlinesurveyqa_surveykeys->OnlineSurveyKeys
                );
            }

            $count = count($SESSION->block_onlinesurveyqa_surveykeys->OnlineSurveyKeys);

            $count2 = 0;

            $surveysfound = false;
            foreach ($SESSION->block_onlinesurveyqa_surveykeys->OnlineSurveyKeys as $surveykey) {
                if (!empty($surveykey->TransactionNumber) && ($surveykey->TransactionNumber != null &&
                        $surveykey->TransactionNumber !== 'null')) {
                    $surveysfound = true;

                    $count2++;
                }
            }

            if ($hideempty && $count2 > 0) {
                $soapcontentstr .= block_onlinesurveyqa_viewscript();
            }

            if (!$offerzoom && $count2 > 0 && !$modalzoom) {
                $soapcontentstr .= block_onlinesurveyqa_surveybuttonscript();
            }

            if ($count2 > 0 && !$modalzoom) {
                $soapcontentstr .= block_onlinesurveyqa_highlightscript($count2);
            } else if ($count2 == 0 && !$modalzoom) {
                $soapcontentstr .= block_onlinesurveyqa_donthighlightscript();
            }

            if ($config->presentation == BLOCK_ONLINESURVEYQA_PRESENTATION_BRIEF && !$modalzoom) {

                $soapcontentstr .= block_onlinesurveyqa_createsummary($count2);

                // Surveys found.
                if ($count2 && $surveysfound) {
                    if (!empty($config->survey_show_popupinfo)) {
                        $soapcontentstr .= '<script language="JavaScript">'.
                                'if (typeof window.parent.evasysGeneratePopupinfo == "function") { '.
                                'window.parent.evasysGeneratePopupinfo(); }</script>';
                    }
                }

            } else {

                // Surveys found.
                if ($count && $surveysfound) {
                    $soapcontentstr .= '<ul class="block_onlinesurveyqa_survey_list">';

                    $cnt = 0;
                    foreach ($SESSION->block_onlinesurveyqa_surveykeys->OnlineSurveyKeys as $surveykey) {
                        if ($surveykey->TransactionNumber !== 'null') {
                            $cnt++;

                            $soapcontentstr .= '<li class="survey">';
                            $soapcontentstr .= "<a id=\"surveylink_".$cnt."\" ".
                                    "href=\"$surveyurl" . "{$surveykey->TransactionNumber}\" ".
                                    "target=\"_blank\">$surveykey->CourseName</a>";
                            $soapcontentstr .= '</li>';

                        }
                    }
                    $soapcontentstr .= '</ul>';

                    if (!empty($config->survey_show_popupinfo)) {
                        $soapcontentstr .= '<script language="JavaScript">'.
                                'if (typeof window.parent.evasysGeneratePopupinfo == "function") { '.
                                'window.parent.evasysGeneratePopupinfo(); }</script>';
                    }
                } else {
                    $soapcontentstr = '<div class="block_onlinesurveyqa_info">'.
                            get_string('surveys_exist_not', 'block_onlinesurveyqa').'</div>';
                }
            }
        } else if (empty($SESSION->block_onlinesurveyqa_surveykeys)) {
            $soapcontentstr = '<div class="block_onlinesurveyqa_info">'.get_string('surveys_exist_not', 'block_onlinesurveyqa').
                    '</div>';
        }

        if (isset($result->error) && !empty($result->error)) {
            $soapcontentstr = get_string('error_occured', 'block_onlinesurveyqa', $result->error);
        }

        // TODO: Check, was hier angezeigt werden soll.
        if ($debugmode && isset($result->warning) && !empty($result->warning)) {
            $soapcontentstr = get_string('error_warning_message', 'block_onlinesurveyqa', $result->warning) ."<br>" . $soapcontentstr;
        }
    } else {
        if ($debugmode) {
            $soapcontentstr = get_string('error_wsdl_namespace', 'block_onlinesurveyqa');
        }
    }

    echo $soapcontentstr;
}

/**
 * Returns a string with HTML code for the compact view.
 *
 * @param int $surveycount number of surveys
 * @return string
 */
function block_onlinesurveyqa_createsummary($surveycount) {
    $offerzoom = get_config('block_onlinesurveyqa', 'offer_zoom');
    if ($surveycount == 0 && $offerzoom == false) {
        $contentstr = "<div id=\"block_onlinesurveyqa_area\" class=\"block_onlinesurveyqa_area\">";

        $contentstr .= "<div class=\"block_onlinesurveyqa_circle\" >";
        $contentstr .= "<span class=\"block_onlinesurveyqa_number\">";
        $contentstr .= "<i class=\"fa fa-check\"></i>";
        $contentstr .= "</span>";
        $contentstr .= "</div>";

        $contentstr .= '<div class="block_onlinesurveyqa_text">' . get_string('surveys_exist_not', 'block_onlinesurveyqa') . '</div>';

        $contentstr .= "</div>";
    } else if ($surveycount == 0 && $offerzoom == true) {
        $contentstr = "<div id=\"block_onlinesurveyqa_area\" class=\"block_onlinesurveyqa_area block_onlinesurveyqa_offerzoom\" ".
            "onClick=\"parent.document.getElementById('block_onlinesurveyqa_surveys_content').click(parent.document);\">";

        $contentstr .= "<div class=\"block_onlinesurveyqa_circle\" >";
        $contentstr .= "<span class=\"block_onlinesurveyqa_number\">";
        $contentstr .= "<i class=\"fa fa-check\"></i>";
        $contentstr .= "</span>";
        $contentstr .= "<div class=\"block_onlinesurveyqa_compact_magnifier\">";
        $contentstr .= "<i class=\"fa fa-search-plus\"></i>";
        $contentstr .= "</div>";
        $contentstr .= "</div>";

        $contentstr .= '<div class="block_onlinesurveyqa_text">' . get_string('surveys_exist_not', 'block_onlinesurveyqa') . '</div>';

        $contentstr .= "</div>";
    } else {
        if ($surveycount > 0 && $surveycount <= 3) {
            $surveycountclass = 'block_onlinesurveyqa_surveycount_'.$surveycount;
        }
        if ($surveycount > 3) {
            $surveycountclass = 'block_onlinesurveyqa_surveycount_gt3';
        }

        $contentstr = "<div id=\"block_onlinesurveyqa_area\" ".
                "class=\"block_onlinesurveyqa_area block_onlinesurveyqa_surveysexist ".$surveycountclass."\" ".
                "onClick=\"parent.document.getElementById('block_onlinesurveyqa_surveys_content').click(parent.document);\">";

        $contentstr .= "<div class=\"block_onlinesurveyqa_circle\" >";
        $contentstr .= "<span class=\"block_onlinesurveyqa_number\">";
        $contentstr .= $surveycount;
        $contentstr .= "</span>";
        $contentstr .= "<div class=\"block_onlinesurveyqa_compact_magnifier\">";
        $contentstr .= "<i class=\"fa fa-search-plus\"></i>";
        $contentstr .= "</div>";
        $contentstr .= "</div>";

        $contentstr .= '<div class="block_onlinesurveyqa_text">' . get_string('surveys_exist', 'block_onlinesurveyqa') . '</div>';

        $contentstr .= "</div>";
    }

    return $contentstr;
}

/**
 * Returns a string with a <script> tag which shows the previously hidden block.
 *
 * @return string
 */
function block_onlinesurveyqa_viewscript() {
    return '<script language="JavaScript">'."\n".
            '   var hiddenelements = parent.document.getElementsByClassName("block_onlinesurveyqa");'."\n".
            '   for (var i = 0; i < hiddenelements.length; i++) {'."\n".
            '       hiddenelements[i].style.display = "block";'."\n".
            '   }'."\n".
            '</script>';
}

/**
 * Returns a string with a <script> tag which shows the previously hidden 'zoom survey list' button.
 *
 * @return string
 */
function block_onlinesurveyqa_surveybuttonscript() {
    return '<script language="JavaScript">'."\n".
            '   var hiddenelements = parent.document.getElementsByClassName("block_onlinesurveyqa_allsurveys");'."\n".
            '   for (var i = 0; i < hiddenelements.length; i++) {'."\n".
            '       hiddenelements[i].style.display = "block";'."\n".
            '   }'."\n".
            '</script>';
}

/**
 * Returns a string with a <script> tag which adds a class to indicate that surveys exist.
 *
 * @param int $surveycount The number of open surveys.
 * @return string
 */
function block_onlinesurveyqa_highlightscript($surveycount) {
    if ($surveycount > 0 && $surveycount <= 3) {
        $surveycountclass = 'block_onlinesurveyqa_surveycount_'.$surveycount;
    }
    if ($surveycount > 3) {
        $surveycountclass = 'block_onlinesurveyqa_surveycount_gt3';
    }

    return '<script language="JavaScript">'."\n".
            '   var parentelements = parent.document.getElementsByClassName("block_onlinesurveyqa");'."\n".
            '   for (var i = 0; i < parentelements.length; i++) {'."\n".
            '       parentelements[i].classList.add("block_onlinesurveyqa_surveysexist");'."\n".
            '       parentelements[i].classList.add("'.$surveycountclass.'");'."\n".
            '   }'."\n".
            '</script>';
}

/**
 * Returns a string with a <script> tag which removes a class to indicate that no surveys exist.
 *
 * @return string
 */
function block_onlinesurveyqa_donthighlightscript() {
    return '<script language="JavaScript">'."\n".
            '   var parentelements = parent.document.getElementsByClassName("block_onlinesurveyqa");'."\n".
            '   for (var i = 0; i < parentelements.length; i++) {'."\n".
            '       parentelements[i].classList.remove("block_onlinesurveyqa_surveysexist");'."\n".
            '   }'."\n".
            '</script>';
}

/**
 * Perform SOAP request for surveys of a user according to user email or username.
 *
 * @param object $soapconfigobj Object containing data for SOAP request.
 * @return object Object containing surveys if present and errors or warnings of the onlinesurveyqa_soap_client
 */
function block_onlinesurveyqa_get_surveys($soapconfigobj) {
    $retval = new stdClass();
    $retval->error = null;
    $retval->warning = null;
    $retval->surveys = false;
    try {
        // Check connectiontype for SOAP.
        if ($soapconfigobj->connectiontype == 'SOAP') {
            require_once('onlinesurveyqa_soap_client.php');

            $client = new onlinesurveyqa_soap_client( $soapconfigobj->wsdl,
                    array(
                                    'trace' => 1,
                                    'feature' => SOAP_SINGLE_ELEMENT_ARRAYS,
                                    'connection_timeout' => $soapconfigobj->timeout),
                    $soapconfigobj->timeout,
                    $soapconfigobj->debugmode
                    );

            $header = array(
                            'Login' => $soapconfigobj->soapuser,
                            'Password' => $soapconfigobj->soappassword
            );

            if (is_object($client)) {
                if ($client->haswarning) {
                    $retval->warning = $client->warnmessage;
                }

                $soapheader = new SoapHeader($soapconfigobj->wsdlnamespace, 'Header', $header);
                $client->__setSoapHeaders($soapheader);
            } else {
                $retval->error = block_onlinesurveyqa_handle_error("SOAP client configuration error");
                return $result;
            }

            if (!empty($soapconfigobj->useridentifier)) {
                if ($soapconfigobj->useridentifier == 'email') {
                    if ($soapconfigobj->moodleemail) {
                        $retval->surveys = $client->GetPswdsByParticipant($soapconfigobj->moodleemail);
                    }
                } else if ($soapconfigobj->useridentifier == 'username') {
                    $retval->surveys = $client->GetPswdsByParticipant($soapconfigobj->moodleusername,
                            $soapconfigobj->coursecode, $soapconfigobj->customfieldnumber);
                }
            }
        }
    } catch (Exception $e) {
        $retval->error = block_onlinesurveyqa_handle_error($e);
        return $retval;
    }
    return $retval;
}

/**
 * Helper function that returns an error string
 * @param Array|object|string $err
 * @return string human readable representation of an error
 */
function block_onlinesurveyqa_handle_error($err) {
    $error = '';
    if (is_array($err)) {
        // Configuration validation error.
        if (!$err[0]) {
            $error = $err[1];
        }
    } else if (is_string($err)) {
        // Simple error message.
        $error = $err;
    } else {
        // Error should be an exception.
        $error = block_onlinesurveyqa_print_exceptions($err);
    }
    return $error;
}

/**
 * Helper function for exceptions
 * @param object $e should be an exception
 * @return string formatted error message of the excetion
 */
function block_onlinesurveyqa_print_exceptions($e) {
    if (get_class($e) == "SoapFault") {
        $msg = "{$e->faultstring}";

        $context = context_system::instance();
        if (has_capability('block/onlinesurveyqa:view_debugdetails', $context)) {
            $detail = '';
            if (isset($e->detail) && !empty($e->detail)) {
                $detail = $e->detail;
                if (is_object($detail) && isset($detail->tSoapfault)) {
                    $detail = $detail->tSoapfault;
                    if (isset($detail->sDetails)) {
                        $detail = $detail->sDetails;
                    }
                }

                $msg .= "<br>".$detail;
            }
        }
    } else {
        $msg = $e->getMessage();
    }

    return $msg;
}

/**
 * Request surveys via LTI for the current user according to email or username and displays the result.
 * This functions uses functions of '/mod/lti/locallib.php'.
 * Performs a second request via curl to check the result for learner content in order to include code to display popupinfo dialog -
 * if option is selected in the settings.
 * @param string $config block settings of "block_onlinesurveyqa"
 * @param string $context context for LTI request - not yet supported by LTI provider
 * @param string $course course for LTI request - not yet supported by LTI provider
 * @param int $modalzoom indicates if the modal list popup is open or not
 * @return string
 */
function block_onlinesurveyqa_get_lti_content($config = null, $context = null, $course = null, $modalzoom = 0) {
    global $CFG, $SESSION;

    require_once($CFG->dirroot.'/mod/lti/locallib.php');

    if (empty($config)) {
        $config = get_config("block_onlinesurveyqa");
    }

    $courseid = (!empty($course->id)) ? $course->id : 1;

    list($endpoint, $parameter) = block_onlinesurveyqa_get_launch_data($config, $context, $course);

    $debuglaunch = $config->survey_debug;

    $surveycount = 0;

    // Check for learner content in LTI result.
    try {
        $content2 = block_onlinesurveyqa_lti_post_launch_html_curl($parameter, $endpoint, $config);
    } catch (Exception $e) {
        $lticontentstr = $e->getMessage();
        echo $lticontentstr;
        return '';
    }

    // Search in $content2 for e.g.: <div class="cell participate centered">.
    // If match found and survey_show_popupinfo is set, add code to generate popup.
    if (!empty($content2)) {
        if (isset($config->lti_regex_learner) && !empty($config->lti_regex_learner)) {
            $re = $config->lti_regex_learner;

            // No regex in config -> use default regex.
        } else {
            $re = BLOCK_ONLINESURVEYQA_LTI_REGEX_LEARNER_DEFAULT;
        }

        if (!empty($re)) {
            $surveycount = preg_match_all($re, $content2, $matches, PREG_SET_ORDER, 0);

            $SESSION->block_onlinesurveyqa_curl_checked = true;

            if (!empty($matches) && !empty($config->survey_show_popupinfo)) {
                // Check to display dialog is (also) done in JS function "evasysGeneratePopupinfo".
                echo '<script language="JavaScript">if (typeof window.parent.evasysGeneratePopupinfo == "function") { '.
                        'window.parent.evasysGeneratePopupinfo(); }</script>';
            }
        }

        if (isset($config->lti_regex_instructor) && !empty($config->lti_regex_instructor)) {
            $reinstructor = $config->lti_regex_instructor;

            // No regex in config -> use default regex.
        } else {
            $reinstructor = BLOCK_ONLINESURVEYQA_LTI_REGEX_INSTRUCTOR_DEFAULT;
        }
        if (!empty($reinstructor)) {
            $surveycount += preg_match_all($reinstructor, $content2, $matches, PREG_SET_ORDER, 0);
        }
    }

    $lticontentstr = '';

    if ($config->survey_hide_empty && $surveycount > 0 && !$modalzoom) {
        $lticontentstr .= block_onlinesurveyqa_viewscript();
    }

    if (!$config->offer_zoom && $surveycount > 0 && !$modalzoom) {
        $lticontentstr .= block_onlinesurveyqa_surveybuttonscript();
    }

    if ($surveycount > 0 && !$modalzoom) {
        $lticontentstr .= block_onlinesurveyqa_highlightscript($surveycount);
    } else if ($surveycount == 0 && !$modalzoom) {
        $lticontentstr .= block_onlinesurveyqa_donthighlightscript();
    }

    if ($config->presentation == BLOCK_ONLINESURVEYQA_PRESENTATION_BRIEF && !$modalzoom) {
        $lticontentstr .= block_onlinesurveyqa_createsummary($surveycount);
    } else {
        if (empty($context)) {
            $context = context_system::instance();
        }
        if (empty($debuglaunch) || has_capability('block/onlinesurveyqa:view_debugdetails', $context)) {
            $lticontentstr .= lti_post_launch_html($parameter, $endpoint, $debuglaunch);

            if ($debuglaunch && has_capability('block/onlinesurveyqa:view_debugdetails', $context)) {
                $debuglaunch = false;
                // $lti_content_str2 = lti_post_launch_html($parameter, $endpoint, $debuglaunch);
                // echo "$lti_content_str2 <br><br>";
            }
        } else {
            $lticontentstr = get_string('error_debugmode_missing_capability', 'block_onlinesurveyqa');
        }
    }

    echo $lticontentstr;
}

/**
 * Return the endpoint and parameter for lti request based on the block settings.
 * This function uses '/mod/lti/locallib.php'.
 * @param string $config block settings of "block_onlinesurveyqa"
 * @param string $context optional context for LTI request - not yet supported by LTI provider
 * @param string $course optional course for LTI request - not yet supported by LTI provider
 * @return multitype:string
 */
function block_onlinesurveyqa_get_launch_data($config = null, $context = null, $course = null) {
    global $CFG, $PAGE;

    require_once($CFG->dirroot.'/mod/lti/locallib.php');

    if (empty($config)) {
        $config = get_config("block_onlinesurveyqa");
    }
    // Default the organizationid if not specified.
    if (empty($config->lti_tool_consumer_instance_guid)) {
        $urlparts = parse_url($CFG->wwwroot);
        $config->lti_tool_consumer_instance_guid = $urlparts['host'];
    }

    $key = '';
    if (!empty($config->lti_password)) {
        $secret = $config->lti_password;
    } else if (is_array($config) && !empty($config['lti_password'])) {
        $secret = $config['lti_password'];
    } else {
        $secret = '';
    }

    $endpoint = !empty($config->lti_url) ? $config->lti_url : $config['lti_url'];
    $endpoint = trim($endpoint);

    // If the current request is using SSL and a secure tool URL is specified, use it.
    if (lti_request_is_using_ssl() && !empty($config->securetoolurl)) {
        $endpoint = trim($config->securetoolurl);
    }

    // If SSL is forced, use the secure tool url if specified. Otherwise, make sure https is on the normal launch URL.
    if (isset($config->forcessl) && ($config->forcessl == '1')) {
        if (!empty($config->securetoolurl)) {
            $endpoint = trim($config->securetoolurl);
        }

        $endpoint = lti_ensure_url_is_https($endpoint);
    } else {
        if (!strstr($endpoint, '://')) {
            $endpoint = 'http://' . $endpoint;
        }
    }

    $orgid = $config->lti_tool_consumer_instance_guid;

    if (empty($course)) {
        $course = $PAGE->course;
    }

    $allparams = block_onlinesurveyqa_build_request_lti($config, $course);

    if (!isset($config->id)) {
        $config->id = null;
    }
    $requestparams = $allparams;
    $requestparams = array_merge($requestparams, lti_build_standard_message($config, $orgid, false));
    $customstr = '';
    if (isset($config->lti_customparameters)) {
        $customstr = $config->lti_customparameters;
    }

    // The function 'lti_build_custom_parameters' expects some parameters that are not part of the block setting -
    // so we build "dummys".
    $toolproxy = new stdClass();
    $tool = new stdClass();
    $tool->ltiversion = LTI_VERSION_1;
    $tool->parameter = '';
    $tool->enabledcapability = array();
    $instance = null;
    $instructorcustomstr = null;

    $requestparams = array_merge($requestparams, lti_build_custom_parameters($toolproxy, $tool, $instance, $allparams, $customstr,
            $instructorcustomstr, false));

    $target = 'iframe';
    if (!empty($target)) {
        $requestparams['launch_presentation_document_target'] = $target;
    }

    // Consumer key currently not used -> $key can be '' -> check "(true or !empty(key))".
    if ((true or !empty($key)) && !empty($secret)) {
        $parms = lti_sign_parameters($requestparams, $endpoint, "POST", $key, $secret);

        $endpointurl = new \moodle_url($endpoint);
        $endpointparams = $endpointurl->params();

        // Strip querystring params in endpoint url from $parms to avoid duplication.
        if (!empty($endpointparams) && !empty($parms)) {
            foreach (array_keys($endpointparams) as $paramname) {
                if (isset($parms[$paramname])) {
                    unset($parms[$paramname]);
                }
            }
        }
    } else {
        // If no key and secret, do the launch unsigned.
        $returnurlparams['unsigned'] = '1';
        $parms = $requestparams;
    }

    return array($endpoint, $parms);
}

/**
 * Builds array of parameters for the LTI request
 * @param object $config block settings of "block_onlinesurveyqa"
 * @param object $course course that is used for some context attributes
 * @return multitype:string NULL
 */
function block_onlinesurveyqa_build_request_lti($config, $course) {
    global $USER;

    $roles = block_onlinesurveyqa_get_ims_roles($USER, $config);

    $requestparams = array(
                    'user_id' => $USER->id,
                    'lis_person_sourcedid' => $USER->idnumber,
                    'roles' => $roles,
                    'context_id' => $course->id,
                    'context_label' => $course->shortname,
                    'context_title' => $course->fullname,
    );
    if ($course->format == 'site') {
        $requestparams['context_type'] = 'Group';
    } else {
        $requestparams['context_type'] = 'CourseSection';
        $requestparams['lis_course_section_sourcedid'] = $course->idnumber;
    }

    // E-mail address is evaluated in EVERY case, even if it is decided to use the Username instead.
    $requestparams['lis_person_contact_email_primary'] = $USER->email;

    if (strpos($roles, 'Learner') !== false) {
        if ($config->useridentifier == 'email') {
            $requestparams['custom_learner_lms_identifier'] = 'lis_person_contact_email_primary';
            $requestparams['lis_person_contact_email_primary'] = $USER->email;
        } else if ($config->useridentifier == 'username') {
            $requestparams['custom_learner_lms_identifier'] = 'ext_user_username';
            $requestparams['ext_user_username'] = $USER->username;
            $requestparams['custom_learner_provider_identifier'] = "custom".$config->customfieldnumber;
        }
    }
    if (strpos($roles, 'Instructor') !== false) {
        // $requestparams['custom_instructor_lms_identifier'] = 'ext_user_username';
        // $requestparams['ext_user_username'] = $USER->username;

        if ($config->useridentifier == 'email') {
            $requestparams['custom_instructor_lms_identifier'] = 'lis_person_contact_email_primary';
            $requestparams['lis_person_contact_email_primary'] = $USER->email;
        } else if ($config->useridentifier == 'username') {
            $requestparams['custom_instructor_lms_identifier'] = 'ext_user_username';
            $requestparams['ext_user_username'] = $USER->username;
            // $requestparams['custom_instructor_provider_identifier'] = "custom".$config->customfieldnumber;
        }
    }

    return $requestparams;
}

/**
 * Gets the LTI role string for the specified user according to lti rolemappings
 *
 * @param object $user user object
 * @param object $config block settings of "block_onlinesurveyqa"
 * @return string A role string suitable for passing with an LTI launch
 */
function block_onlinesurveyqa_get_ims_roles($user, $config) {
    global $DB;

    $roles = array();

    // Check if user has "mapped" roles.
    $isinstructor = false;
    $ltimapping = $config->lti_instructormapping;
    if (!empty($ltimapping)) {
        try {
            $ltimapping = explode(',', $ltimapping);
            list($sql, $params) = $DB->get_in_or_equal($ltimapping, SQL_PARAMS_NAMED, 'lti_mapping');
            $params['userid'] = $user->id;
            $isinstructor = $DB->record_exists_select('role_assignments', "userid = :userid and roleid $sql", $params);
        } catch (Exception $e) {
            error_log("error check user roles for 'instructor': ".$e->getMessage());
        }
    }
    $islearner = false;
    $ltimapping = $config->lti_learnermapping;
    if (!empty($ltimapping)) {
        try {
            $ltimapping = explode(',', $ltimapping);
            list($sql, $params) = $DB->get_in_or_equal($ltimapping, SQL_PARAMS_NAMED, 'lti_mapping');
            $params['userid'] = $user->id;
            $islearner = $DB->record_exists_select('role_assignments', "userid = :userid and roleid $sql", $params);
        } catch (Exception $e) {
            error_log("error check user roles for 'learner': ".$e->getMessage());
        }
    }

    if (!empty($isinstructor)) {
        array_push($roles, 'Instructor');
    }
    if (!empty($islearner)) {
        array_push($roles, 'Learner');
    }

    // User has NO role in moodle -> use role mapping for learner.
    if (empty($roles)) {
        array_push($roles, 'Learner');
    }

    if (is_siteadmin($user)) {
        array_push($roles, 'urn:lti:sysrole:ims/lis/Administrator', 'urn:lti:instrole:ims/lis/Administrator');
    }

    return join(',', $roles);
}

/**
 * Fetches the LTI content on the server for analyzing the survey list server-side.
 *
 * @param array $parameter parameter for LTI request
 * @param string $endpoint endpoint for LTI request
 * @param object $config the plugin configuration
 * @return string result of the curl LTI request
 */
function block_onlinesurveyqa_lti_post_launch_html_curl($parameter, $endpoint, $config) {

    // Set POST variables.
    $fields = array();

    // Construct html for the launch parameters.
    foreach ($parameter as $key => $value) {
        $key = htmlspecialchars($key);
        $value = htmlspecialchars($value);
        if ( $key != "ext_submit" ) {
            $fields[$key] = urlencode($value);
        }
    }
    // Url-ify the data for the POST.
    $fieldsstring = '';
    foreach ($fields as $key => $value) {
        $fieldsstring .= $key.'='.$value.'&';
    }
    $fieldsstring = rtrim($fieldsstring, '&');

    $curl = new curl;
    $timeout = isset($config->survey_timeout) ? $config->survey_timeout : BLOCK_ONLINESURVEYQA_DEFAULT_TIMEOUT;
    $curloptions = array(
        'RETURNTRANSFER' => 1,
        'FRESH_CONNECT' => true,
        'TIMEOUT' => $timeout,
    );
    $ret = $curl->post($endpoint, $fieldsstring, $curloptions);

    if ($errornumber = $curl->get_errno()) {
        $msgoutput = get_string('error_survey_curl_timeout_msg', 'block_onlinesurveyqa');

        $context = context_system::instance();
        if (has_capability('block/onlinesurveyqa:view_debugdetails', $context)) {
            if (!empty($msgoutput)) {
                $msgoutput .= "<br><br>"."curl_errno $errornumber: $ret"; // Variable $ret now contains the error string.
            }
        }

        if (in_array($errornumber, array(CURLE_OPERATION_TIMEDOUT, CURLE_OPERATION_TIMEOUTED))) {
            throw new Exception("$msgoutput");
        }
    }

    return $ret;
}
