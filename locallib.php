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
<<<<<<< HEAD
 * Plugin "Evaluations (evasys)" - Local library
 *
 * @package    block_onlinesurvey
 * @copyright  2018 Soon Systems GmbH on behalf of evasys GmbH
=======
 * Plugin "Evaluations (EvaSys)" - Local library
 *
 * @package    block_onlinesurveyqa
 * @copyright  2018 Soon Systems GmbH on behalf of Electric Paper Evaluationssysteme GmbH
>>>>>>> Initial commit
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

<<<<<<< HEAD
define('BLOCK_ONLINESURVEY_COMM_SOAP', "SOAP");
define('BLOCK_ONLINESURVEY_COMM_LTI', "LTI");
define('BLOCK_ONLINESURVEY_DEFAULT_TIMEOUT', 15);

define('BLOCK_ONLINESURVEY_LTI_REGEX_LEARNER_DEFAULT', '/<(p){1}(.){0,}[\s]{0,}(data-participated="false"){1}[\s]{0,}/');
define('BLOCK_ONLINESURVEY_LTI_REGEX_INSTRUCTOR_DEFAULT',
        '/<(div){1}[\s]{1,}(class=){1}["|\']{1}[a-z]{0,}[\s]{0,}(response-box){1}[\s]{0,}[a-z]{0,}[\s]{0,}["|\']{1}>/');

define('BLOCK_ONLINESURVEY_PRESENTATION_BRIEF', "brief");
define('BLOCK_ONLINESURVEY_PRESENTATION_DETAILED', "detailed");

/**
 * Request surveys for the current user according to email or username and displays the result.
 * @param string $config block settings of "block_onlinesurvey"
=======
define('BLOCK_onlinesurveyqa_COMM_SOAP', "SOAP");
define('BLOCK_onlinesurveyqa_COMM_LTI', "LTI");
define('BLOCK_onlinesurveyqa_DEFAULT_TIMEOUT', 15);

define('BLOCK_onlinesurveyqa_LTI_REGEX_LEARNER_DEFAULT', '/<(p){1}(.){0,}[\s]{0,}(data-participated="false"){1}[\s]{0,}/');
define('BLOCK_onlinesurveyqa_LTI_REGEX_INSTRUCTOR_DEFAULT',
        '/<(div){1}[\s]{1,}(class=){1}["|\']{1}[a-z]{0,}[\s]{0,}(response-box){1}[\s]{0,}[a-z]{0,}[\s]{0,}["|\']{1}>/');

define('BLOCK_onlinesurveyqa_PRESENTATION_BRIEF', "brief");
define('BLOCK_onlinesurveyqa_PRESENTATION_DETAILED', "detailed");

/**
 * Request surveys for the current user according to email or username and displays the result.
 * @param string $config block settings of "block_onlinesurveyqa"
>>>>>>> Initial commit
 * @param string $moodleusername username for SOAP request
 * @param string $moodleemail email for SOAP request
 * @param int $modalzoom indicates if the modal list popup is open or not
 * @return string
 */
<<<<<<< HEAD
function block_onlinesurvey_get_soap_content($config = null, $moodleusername = '', $moodleemail = '', $modalzoom = 0) {
=======
function block_onlinesurveyqa_get_soap_content($config = null, $moodleusername = '', $moodleemail = '', $modalzoom = 0) {
>>>>>>> Initial commit
    global $SESSION;

    $surveyurl = 'indexstud.php?type=html&user_tan=';

    if (empty($config)) {
<<<<<<< HEAD
        $config = get_config("block_onlinesurvey");
=======
        $config = get_config("block_onlinesurveyqa");
>>>>>>> Initial commit
    }

    $connectiontype = $config->connectiontype;
    $surveyurl = $config->survey_login.$surveyurl;
    $wsdl = $config->survey_server;
    $soapuser = $config->survey_user;
    $soappassword = $config->survey_pwd;
    $debugmode = $config->survey_debug;

    $hideempty = $config->survey_hide_empty;
    $offerzoom = $config->offer_zoom;

<<<<<<< HEAD
    $timeout = isset($config->survey_timeout) ? $config->survey_timeout : BLOCK_ONLINESURVEY_DEFAULT_TIMEOUT;
=======
    $timeout = isset($config->survey_timeout) ? $config->survey_timeout : BLOCK_onlinesurveyqa_DEFAULT_TIMEOUT;
>>>>>>> Initial commit

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
<<<<<<< HEAD
        if (!isset($SESSION->block_onlinesurvey_surveykeys) || $debugmode || $soaprequesteachtime) {
            $result = block_onlinesurvey_get_surveys($soapconfigobj);
            $SESSION->block_onlinesurvey_surveykeys = $result->surveys;

            $SESSION->block_onlinesurvey_error = $result->error;
        }

        if (isset($SESSION->block_onlinesurvey_error)) {
            $result->error = $SESSION->block_onlinesurvey_error;
        }

        if (is_object($SESSION->block_onlinesurvey_surveykeys)) {
            if (!is_array($SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys)) {
                $SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys = array(
                                $SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys
                );
            }

            $count = count($SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys);
=======
        if (!isset($SESSION->block_onlinesurveyqa_surveykeys) || $debugmode || $soaprequesteachtime) {
            $result = block_onlinesurveyqa_get_surveys($soapconfigobj);
            $SESSION->block_onlinesurveyqa_surveykeys = $result->surveys;

            $SESSION->block_onlinesurveyqa_error = $result->error;
        }

        if (isset($SESSION->block_onlinesurveyqa_error)) {
            $result->error = $SESSION->block_onlinesurveyqa_error;
        }

        if (is_object($SESSION->block_onlinesurveyqa_surveykeys)) {
            if (!is_array($SESSION->block_onlinesurveyqa_surveykeys->onlinesurveyqaKeys)) {
                $SESSION->block_onlinesurveyqa_surveykeys->onlinesurveyqaKeys = array(
                                $SESSION->block_onlinesurveyqa_surveykeys->onlinesurveyqaKeys
                );
            }

            $count = count($SESSION->block_onlinesurveyqa_surveykeys->onlinesurveyqaKeys);
>>>>>>> Initial commit

            $count2 = 0;

            $surveysfound = false;
<<<<<<< HEAD
            foreach ($SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys as $surveykey) {
=======
            foreach ($SESSION->block_onlinesurveyqa_surveykeys->onlinesurveyqaKeys as $surveykey) {
>>>>>>> Initial commit
                if (!empty($surveykey->TransactionNumber) && ($surveykey->TransactionNumber != null &&
                        $surveykey->TransactionNumber !== 'null')) {
                    $surveysfound = true;

                    $count2++;
                }
            }

            if ($hideempty && $count2 > 0) {
<<<<<<< HEAD
                $soapcontentstr .= block_onlinesurvey_viewscript();
            }

            if (!$offerzoom && $count2 > 0 && !$modalzoom) {
                $soapcontentstr .= block_onlinesurvey_surveybuttonscript();
            }

            if ($count2 > 0 && !$modalzoom) {
                $soapcontentstr .= block_onlinesurvey_highlightscript($count2);
            } else if ($count2 == 0 && !$modalzoom) {
                $soapcontentstr .= block_onlinesurvey_donthighlightscript();
            }

            if ($config->presentation == BLOCK_ONLINESURVEY_PRESENTATION_BRIEF && !$modalzoom) {

                $soapcontentstr .= block_onlinesurvey_createsummary($count2);
=======
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

            if ($config->presentation == BLOCK_onlinesurveyqa_PRESENTATION_BRIEF && !$modalzoom) {

                $soapcontentstr .= block_onlinesurveyqa_createsummary($count2);
>>>>>>> Initial commit

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
<<<<<<< HEAD
                    $soapcontentstr .= '<ul class="block_onlinesurvey_survey_list">';

                    $cnt = 0;
                    foreach ($SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys as $surveykey) {
=======
                    $soapcontentstr .= '<ul class="block_onlinesurveyqa_survey_list">';

                    $cnt = 0;
                    foreach ($SESSION->block_onlinesurveyqa_surveykeys->onlinesurveyqaKeys as $surveykey) {
>>>>>>> Initial commit
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
<<<<<<< HEAD
                    $soapcontentstr = '<div class="block_onlinesurvey_info">'.
                            get_string('surveys_exist_not', 'block_onlinesurvey').'</div>';
                }
            }
        } else if (empty($SESSION->block_onlinesurvey_surveykeys)) {
            $soapcontentstr = '<div class="block_onlinesurvey_info">'.get_string('surveys_exist_not', 'block_onlinesurvey').
=======
                    $soapcontentstr = '<div class="block_onlinesurveyqa_info">'.
                            get_string('surveys_exist_not', 'block_onlinesurveyqa').'</div>';
                }
            }
        } else if (empty($SESSION->block_onlinesurveyqa_surveykeys)) {
            $soapcontentstr = '<div class="block_onlinesurveyqa_info">'.get_string('surveys_exist_not', 'block_onlinesurveyqa').
>>>>>>> Initial commit
                    '</div>';
        }

        if (isset($result->error) && !empty($result->error)) {
<<<<<<< HEAD
            $soapcontentstr = get_string('error_occured', 'block_onlinesurvey', $result->error);
=======
            $soapcontentstr = get_string('error_occured', 'block_onlinesurveyqa', $result->error);
>>>>>>> Initial commit
        }

        // TODO: Check, was hier angezeigt werden soll.
        if ($debugmode && isset($result->warning) && !empty($result->warning)) {
<<<<<<< HEAD
            $soapcontentstr = get_string('error_warning_message', 'block_onlinesurvey', $result->warning) ."<br>" . $soapcontentstr;
        }
    } else {
        if ($debugmode) {
            $soapcontentstr = get_string('error_wsdl_namespace', 'block_onlinesurvey');
=======
            $soapcontentstr = get_string('error_warning_message', 'block_onlinesurveyqa', $result->warning) ."<br>" . $soapcontentstr;
        }
    } else {
        if ($debugmode) {
            $soapcontentstr = get_string('error_wsdl_namespace', 'block_onlinesurveyqa');
>>>>>>> Initial commit
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
<<<<<<< HEAD
function block_onlinesurvey_createsummary($surveycount) {
    $offerzoom = get_config('block_onlinesurvey', 'offer_zoom');
    if ($surveycount == 0 && $offerzoom == false) {
        $contentstr = "<div id=\"block_onlinesurvey_area\" class=\"block_onlinesurvey_area\">";

        $contentstr .= "<div class=\"block_onlinesurvey_circle\" >";
        $contentstr .= "<span class=\"block_onlinesurvey_number\">";
=======
function block_onlinesurveyqa_createsummary($surveycount) {
    $offerzoom = get_config('block_onlinesurveyqa', 'offer_zoom');
    if ($surveycount == 0 && $offerzoom == false) {
        $contentstr = "<div id=\"block_onlinesurveyqa_area\" class=\"block_onlinesurveyqa_area\">";

        $contentstr .= "<div class=\"block_onlinesurveyqa_circle\" >";
        $contentstr .= "<span class=\"block_onlinesurveyqa_number\">";
>>>>>>> Initial commit
        $contentstr .= "<i class=\"fa fa-check\"></i>";
        $contentstr .= "</span>";
        $contentstr .= "</div>";

<<<<<<< HEAD
        $contentstr .= '<div class="block_onlinesurvey_text">' . get_string('surveys_exist_not', 'block_onlinesurvey') . '</div>';

        $contentstr .= "</div>";
    } else if ($surveycount == 0 && $offerzoom == true) {
        $contentstr = "<div id=\"block_onlinesurvey_area\" class=\"block_onlinesurvey_area block_onlinesurvey_offerzoom\" ".
            "onClick=\"parent.document.getElementById('block_onlinesurvey_surveys_content').click(parent.document);\">";

        $contentstr .= "<div class=\"block_onlinesurvey_circle\" >";
        $contentstr .= "<span class=\"block_onlinesurvey_number\">";
        $contentstr .= "<i class=\"fa fa-check\"></i>";
        $contentstr .= "</span>";
        $contentstr .= "<div class=\"block_onlinesurvey_compact_magnifier\">";
=======
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
>>>>>>> Initial commit
        $contentstr .= "<i class=\"fa fa-search-plus\"></i>";
        $contentstr .= "</div>";
        $contentstr .= "</div>";

<<<<<<< HEAD
        $contentstr .= '<div class="block_onlinesurvey_text">' . get_string('surveys_exist_not', 'block_onlinesurvey') . '</div>';
=======
        $contentstr .= '<div class="block_onlinesurveyqa_text">' . get_string('surveys_exist_not', 'block_onlinesurveyqa') . '</div>';
>>>>>>> Initial commit

        $contentstr .= "</div>";
    } else {
        if ($surveycount > 0 && $surveycount <= 3) {
<<<<<<< HEAD
            $surveycountclass = 'block_onlinesurvey_surveycount_'.$surveycount;
        }
        if ($surveycount > 3) {
            $surveycountclass = 'block_onlinesurvey_surveycount_gt3';
        }

        $contentstr = "<div id=\"block_onlinesurvey_area\" ".
                "class=\"block_onlinesurvey_area block_onlinesurvey_surveysexist ".$surveycountclass."\" ".
                "onClick=\"parent.document.getElementById('block_onlinesurvey_surveys_content').click(parent.document);\">";

        $contentstr .= "<div class=\"block_onlinesurvey_circle\" >";
        $contentstr .= "<span class=\"block_onlinesurvey_number\">";
        $contentstr .= $surveycount;
        $contentstr .= "</span>";
        $contentstr .= "<div class=\"block_onlinesurvey_compact_magnifier\">";
=======
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
>>>>>>> Initial commit
        $contentstr .= "<i class=\"fa fa-search-plus\"></i>";
        $contentstr .= "</div>";
        $contentstr .= "</div>";

<<<<<<< HEAD
        $contentstr .= '<div class="block_onlinesurvey_text">' . get_string('surveys_exist', 'block_onlinesurvey') . '</div>';
=======
        $contentstr .= '<div class="block_onlinesurveyqa_text">' . get_string('surveys_exist', 'block_onlinesurveyqa') . '</div>';
>>>>>>> Initial commit

        $contentstr .= "</div>";
    }

    return $contentstr;
}

/**
 * Returns a string with a <script> tag which shows the previously hidden block.
 *
 * @return string
 */
<<<<<<< HEAD
function block_onlinesurvey_viewscript() {
    return '<script language="JavaScript">'."\n".
            '   var hiddenelements = parent.document.getElementsByClassName("block_onlinesurvey");'."\n".
=======
function block_onlinesurveyqa_viewscript() {
    return '<script language="JavaScript">'."\n".
            '   var hiddenelements = parent.document.getElementsByClassName("block_onlinesurveyqa");'."\n".
>>>>>>> Initial commit
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
<<<<<<< HEAD
function block_onlinesurvey_surveybuttonscript() {
    return '<script language="JavaScript">'."\n".
            '   var hiddenelements = parent.document.getElementsByClassName("block_onlinesurvey_allsurveys");'."\n".
=======
function block_onlinesurveyqa_surveybuttonscript() {
    return '<script language="JavaScript">'."\n".
            '   var hiddenelements = parent.document.getElementsByClassName("block_onlinesurveyqa_allsurveys");'."\n".
>>>>>>> Initial commit
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
<<<<<<< HEAD
function block_onlinesurvey_highlightscript($surveycount) {
    if ($surveycount > 0 && $surveycount <= 3) {
        $surveycountclass = 'block_onlinesurvey_surveycount_'.$surveycount;
    }
    if ($surveycount > 3) {
        $surveycountclass = 'block_onlinesurvey_surveycount_gt3';
    }

    return '<script language="JavaScript">'."\n".
            '   var parentelements = parent.document.getElementsByClassName("block_onlinesurvey");'."\n".
            '   for (var i = 0; i < parentelements.length; i++) {'."\n".
            '       parentelements[i].classList.add("block_onlinesurvey_surveysexist");'."\n".
=======
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
>>>>>>> Initial commit
            '       parentelements[i].classList.add("'.$surveycountclass.'");'."\n".
            '   }'."\n".
            '</script>';
}

/**
 * Returns a string with a <script> tag which removes a class to indicate that no surveys exist.
 *
 * @return string
 */
<<<<<<< HEAD
function block_onlinesurvey_donthighlightscript() {
    return '<script language="JavaScript">'."\n".
            '   var parentelements = parent.document.getElementsByClassName("block_onlinesurvey");'."\n".
            '   for (var i = 0; i < parentelements.length; i++) {'."\n".
            '       parentelements[i].classList.remove("block_onlinesurvey_surveysexist");'."\n".
=======
function block_onlinesurveyqa_donthighlightscript() {
    return '<script language="JavaScript">'."\n".
            '   var parentelements = parent.document.getElementsByClassName("block_onlinesurveyqa");'."\n".
            '   for (var i = 0; i < parentelements.length; i++) {'."\n".
            '       parentelements[i].classList.remove("block_onlinesurveyqa_surveysexist");'."\n".
>>>>>>> Initial commit
            '   }'."\n".
            '</script>';
}

/**
 * Perform SOAP request for surveys of a user according to user email or username.
 *
 * @param object $soapconfigobj Object containing data for SOAP request.
<<<<<<< HEAD
 * @return object Object containing surveys if present and errors or warnings of the onlinesurvey_soap_client
 */
function block_onlinesurvey_get_surveys($soapconfigobj) {
=======
 * @return object Object containing surveys if present and errors or warnings of the onlinesurveyqa_soap_client
 */
function block_onlinesurveyqa_get_surveys($soapconfigobj) {
>>>>>>> Initial commit
    $retval = new stdClass();
    $retval->error = null;
    $retval->warning = null;
    $retval->surveys = false;
    try {
        // Check connectiontype for SOAP.
        if ($soapconfigobj->connectiontype == 'SOAP') {
<<<<<<< HEAD
            require_once('onlinesurvey_soap_client.php');

            $client = new onlinesurvey_soap_client( $soapconfigobj->wsdl,
=======
            require_once('onlinesurveyqa_soap_client.php');

            $client = new onlinesurveyqa_soap_client( $soapconfigobj->wsdl,
>>>>>>> Initial commit
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
<<<<<<< HEAD
                $retval->error = block_onlinesurvey_handle_error("SOAP client configuration error");
=======
                $retval->error = block_onlinesurveyqa_handle_error("SOAP client configuration error");
>>>>>>> Initial commit
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
<<<<<<< HEAD
        $retval->error = block_onlinesurvey_handle_error($e);
=======
        $retval->error = block_onlinesurveyqa_handle_error($e);
>>>>>>> Initial commit
        return $retval;
    }
    return $retval;
}

/**
 * Helper function that returns an error string
 * @param Array|object|string $err
 * @return string human readable representation of an error
 */
<<<<<<< HEAD
function block_onlinesurvey_handle_error($err) {
=======
function block_onlinesurveyqa_handle_error($err) {
>>>>>>> Initial commit
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
<<<<<<< HEAD
        $error = block_onlinesurvey_print_exceptions($err);
=======
        $error = block_onlinesurveyqa_print_exceptions($err);
>>>>>>> Initial commit
    }
    return $error;
}

/**
 * Helper function for exceptions
 * @param object $e should be an exception
 * @return string formatted error message of the excetion
 */
<<<<<<< HEAD
function block_onlinesurvey_print_exceptions($e) {
=======
function block_onlinesurveyqa_print_exceptions($e) {
>>>>>>> Initial commit
    if (get_class($e) == "SoapFault") {
        $msg = "{$e->faultstring}";

        $context = context_system::instance();
<<<<<<< HEAD
        if (has_capability('block/onlinesurvey:view_debugdetails', $context)) {
=======
        if (has_capability('block/onlinesurveyqa:view_debugdetails', $context)) {
>>>>>>> Initial commit
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
<<<<<<< HEAD
 * @param string $config block settings of "block_onlinesurvey"
=======
 * @param string $config block settings of "block_onlinesurveyqa"
>>>>>>> Initial commit
 * @param string $context context for LTI request - not yet supported by LTI provider
 * @param string $course course for LTI request - not yet supported by LTI provider
 * @param int $modalzoom indicates if the modal list popup is open or not
 * @return string
 */
<<<<<<< HEAD
function block_onlinesurvey_get_lti_content($config = null, $context = null, $course = null, $modalzoom = 0) {
=======
function block_onlinesurveyqa_get_lti_content($config = null, $context = null, $course = null, $modalzoom = 0) {
>>>>>>> Initial commit
    global $CFG, $SESSION;

    require_once($CFG->dirroot.'/mod/lti/locallib.php');

    if (empty($config)) {
<<<<<<< HEAD
        $config = get_config("block_onlinesurvey");
=======
        $config = get_config("block_onlinesurveyqa");
>>>>>>> Initial commit
    }

    $courseid = (!empty($course->id)) ? $course->id : 1;

<<<<<<< HEAD
    list($endpoint, $parameter) = block_onlinesurvey_get_launch_data($config, $context, $course);
=======
    list($endpoint, $parameter) = block_onlinesurveyqa_get_launch_data($config, $context, $course);
>>>>>>> Initial commit

    $debuglaunch = $config->survey_debug;

    $surveycount = 0;

    // Check for learner content in LTI result.
    try {
<<<<<<< HEAD
        $content2 = block_onlinesurvey_lti_post_launch_html_curl($parameter, $endpoint, $config);
=======
        $content2 = block_onlinesurveyqa_lti_post_launch_html_curl($parameter, $endpoint, $config);
>>>>>>> Initial commit
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
<<<<<<< HEAD
            $re = BLOCK_ONLINESURVEY_LTI_REGEX_LEARNER_DEFAULT;
=======
            $re = BLOCK_onlinesurveyqa_LTI_REGEX_LEARNER_DEFAULT;
>>>>>>> Initial commit
        }

        if (!empty($re)) {
            $surveycount = preg_match_all($re, $content2, $matches, PREG_SET_ORDER, 0);

<<<<<<< HEAD
            $SESSION->block_onlinesurvey_curl_checked = true;
=======
            $SESSION->block_onlinesurveyqa_curl_checked = true;
>>>>>>> Initial commit

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
<<<<<<< HEAD
            $reinstructor = BLOCK_ONLINESURVEY_LTI_REGEX_INSTRUCTOR_DEFAULT;
        }
        if (!empty($reinstructor)) {
            $surveycount += preg_match_all($reinstructor, $content2, $matches, PREG_SET_ORDER, 0);
=======
            $reinstructor = BLOCK_onlinesurveyqa_LTI_REGEX_INSTRUCTOR_DEFAULT;
        }
        if (empty($matches) && !empty($reinstructor)) {
            $surveycount = preg_match_all($reinstructor, $content2, $matches, PREG_SET_ORDER, 0);
>>>>>>> Initial commit
        }
    }

    $lticontentstr = '';

    if ($config->survey_hide_empty && $surveycount > 0 && !$modalzoom) {
<<<<<<< HEAD
        $lticontentstr .= block_onlinesurvey_viewscript();
    }

    if (!$config->offer_zoom && $surveycount > 0 && !$modalzoom) {
        $lticontentstr .= block_onlinesurvey_surveybuttonscript();
    }

    if ($surveycount > 0 && !$modalzoom) {
        $lticontentstr .= block_onlinesurvey_highlightscript($surveycount);
    } else if ($surveycount == 0 && !$modalzoom) {
        $lticontentstr .= block_onlinesurvey_donthighlightscript();
    }

    if ($config->presentation == BLOCK_ONLINESURVEY_PRESENTATION_BRIEF && !$modalzoom) {
        $lticontentstr .= block_onlinesurvey_createsummary($surveycount);
=======
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

    if ($config->presentation == BLOCK_onlinesurveyqa_PRESENTATION_BRIEF && !$modalzoom) {
        $lticontentstr .= block_onlinesurveyqa_createsummary($surveycount);
>>>>>>> Initial commit
    } else {
        if (empty($context)) {
            $context = context_system::instance();
        }
<<<<<<< HEAD
        if (empty($debuglaunch) || has_capability('block/onlinesurvey:view_debugdetails', $context)) {
            $lticontentstr .= lti_post_launch_html($parameter, $endpoint, $debuglaunch);

            if ($debuglaunch && has_capability('block/onlinesurvey:view_debugdetails', $context)) {
=======
        if (empty($debuglaunch) || has_capability('block/onlinesurveyqa:view_debugdetails', $context)) {
            $lticontentstr .= lti_post_launch_html($parameter, $endpoint, $debuglaunch);

            if ($debuglaunch && has_capability('block/onlinesurveyqa:view_debugdetails', $context)) {
>>>>>>> Initial commit
                $debuglaunch = false;
                // $lti_content_str2 = lti_post_launch_html($parameter, $endpoint, $debuglaunch);
                // echo "$lti_content_str2 <br><br>";
            }
        } else {
<<<<<<< HEAD
            $lticontentstr = get_string('error_debugmode_missing_capability', 'block_onlinesurvey');
=======
            $lticontentstr = get_string('error_debugmode_missing_capability', 'block_onlinesurveyqa');
>>>>>>> Initial commit
        }
    }

    echo $lticontentstr;
}

/**
 * Return the endpoint and parameter for lti request based on the block settings.
 * This function uses '/mod/lti/locallib.php'.
<<<<<<< HEAD
 * @param string $config block settings of "block_onlinesurvey"
=======
 * @param string $config block settings of "block_onlinesurveyqa"
>>>>>>> Initial commit
 * @param string $context optional context for LTI request - not yet supported by LTI provider
 * @param string $course optional course for LTI request - not yet supported by LTI provider
 * @return multitype:string
 */
<<<<<<< HEAD
function block_onlinesurvey_get_launch_data($config = null, $context = null, $course = null) {
=======
function block_onlinesurveyqa_get_launch_data($config = null, $context = null, $course = null) {
>>>>>>> Initial commit
    global $CFG, $PAGE;

    require_once($CFG->dirroot.'/mod/lti/locallib.php');

    if (empty($config)) {
<<<<<<< HEAD
        $config = get_config("block_onlinesurvey");
=======
        $config = get_config("block_onlinesurveyqa");
>>>>>>> Initial commit
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

<<<<<<< HEAD
    $allparams = block_onlinesurvey_build_request_lti($config, $course);
=======
    $allparams = block_onlinesurveyqa_build_request_lti($config, $course);
>>>>>>> Initial commit

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
<<<<<<< HEAD
 * @param object $config block settings of "block_onlinesurvey"
 * @param object $course course that is used for some context attributes
 * @return multitype:string NULL
 */
function block_onlinesurvey_build_request_lti($config, $course) {
    global $USER;

    $roles = block_onlinesurvey_get_ims_roles($USER, $config);
=======
 * @param object $config block settings of "block_onlinesurveyqa"
 * @param object $course course that is used for some context attributes
 * @return multitype:string NULL
 */
function block_onlinesurveyqa_build_request_lti($config, $course) {
    global $USER;

    $roles = block_onlinesurveyqa_get_ims_roles($USER, $config);
>>>>>>> Initial commit

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
<<<<<<< HEAD
 * @param object $config block settings of "block_onlinesurvey"
 * @return string A role string suitable for passing with an LTI launch
 */
function block_onlinesurvey_get_ims_roles($user, $config) {
=======
 * @param object $config block settings of "block_onlinesurveyqa"
 * @return string A role string suitable for passing with an LTI launch
 */
function block_onlinesurveyqa_get_ims_roles($user, $config) {
>>>>>>> Initial commit
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
<<<<<<< HEAD
function block_onlinesurvey_lti_post_launch_html_curl($parameter, $endpoint, $config) {
=======
function block_onlinesurveyqa_lti_post_launch_html_curl($parameter, $endpoint, $config) {
>>>>>>> Initial commit

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
<<<<<<< HEAD
    $fieldsstring = rtrim($fieldsstring, '&');

    $curl = new curl;
    $timeout = isset($config->survey_timeout) ? $config->survey_timeout : BLOCK_ONLINESURVEY_DEFAULT_TIMEOUT;
=======
    rtrim($fieldsstring, '&');

    $curl = new curl;
    $timeout = isset($config->survey_timeout) ? $config->survey_timeout : BLOCK_onlinesurveyqa_DEFAULT_TIMEOUT;
>>>>>>> Initial commit
    $curloptions = array(
        'RETURNTRANSFER' => 1,
        'FRESH_CONNECT' => true,
        'TIMEOUT' => $timeout,
    );
    $ret = $curl->post($endpoint, $fieldsstring, $curloptions);

    if ($errornumber = $curl->get_errno()) {
<<<<<<< HEAD
        $msgoutput = get_string('error_survey_curl_timeout_msg', 'block_onlinesurvey');

        $context = context_system::instance();
        if (has_capability('block/onlinesurvey:view_debugdetails', $context)) {
=======
        $msgoutput = get_string('error_survey_curl_timeout_msg', 'block_onlinesurveyqa');

        $context = context_system::instance();
        if (has_capability('block/onlinesurveyqa:view_debugdetails', $context)) {
>>>>>>> Initial commit
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
