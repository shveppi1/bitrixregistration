<?php
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

$arResult["PARAMS_HASH"] = md5(serialize($arParams).$this->GetTemplateName());

$arParams["USE_CAPTCHA"] = (($arParams["USE_CAPTCHA"] != "N" && !$USER->IsAuthorized()) ? "Y" : "N");

$arParams["EVENT_NAME"] = trim($arParams["EVENT_NAME"]);
if($arParams["EVENT_NAME"] == '')
	$arParams["EVENT_NAME"] = "FEEDBACK_FORM";

$arParams["EMAIL_TO"] = trim($arParams["EMAIL_TO"]);
if($arParams["EMAIL_TO"] == '')
	$arParams["EMAIL_TO"] = COption::GetOptionString("main", "email_from");

if($arParams["OK_TEXT"] == '')
    $arParams["OK_TEXT"] = GetMessage("MF_OK_MESSAGE");

$iblock_id = (int)$arParams["IBLOCK_ID"] ? $arParams["IBLOCK_ID"] : 1;



if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["submit"] <> '' && isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response'])
{

    $secret = '6LcCQewoAAAAAGYJ3Kl2rfSJ1J21T2qNahEeY7Cy';
    $ip = $_SERVER['REMOTE_ADDR'];
    $response = $_POST['g-recaptcha-response'];
    $rsp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$ip");
    $arr = json_decode($rsp, TRUE);
    if ($arr['success']) {

        $arResult["ERROR_MESSAGE"] = array();
        if (check_bitrix_sessid()) {
            if (empty($arParams["REQUIRED_FIELDS"]) || !in_array("NONE", $arParams["REQUIRED_FIELDS"])) {
                if ((empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])) && mb_strlen($_POST["user_name"]) <= 1)
                    $arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_NAME");
                if ((empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])) && mb_strlen($_POST["MESSAGE"]) <= 3)
                    $arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_MESSAGE");
                if ((empty($arParams["REQUIRED_FIELDS"]) || in_array("PHONE", $arParams["REQUIRED_FIELDS"])) && mb_strlen($_POST["user_phone"]) <= 1)
                    $arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_PHONE");
                if ((empty($arParams["REQUIRED_FIELDS"]) || in_array("FILE", $arParams["REQUIRED_FIELDS"])) && isset($_FILES['user_file']) && mb_strlen($_FILES['user_file']['name']) <= 1 )
                    $arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_FILE");
            }
            if (mb_strlen($_POST["user_email"]) > 1 && !check_email($_POST["user_email"]))
                $arResult["ERROR_MESSAGE"][] = GetMessage("MF_EMAIL_NOT_VALID");
            if ($arParams["USE_CAPTCHA"] == "Y") {
                $captcha_code = $_POST["captcha_sid"];
                $captcha_word = $_POST["captcha_word"];
                $cpt = new CCaptcha();
                $captchaPass = COption::GetOptionString("main", "captcha_password", "");
                if ($captcha_word <> '' && $captcha_code <> '') {
                    if (!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
                        $arResult["ERROR_MESSAGE"][] = GetMessage("MF_CAPTCHA_WRONG");
                } else
                    $arResult["ERROR_MESSAGE"][] = GetMessage("MF_CAPTHCA_EMPTY");

            }
            if (empty($arResult["ERROR_MESSAGE"])) {


                $arFields = array(
                    "AUTHOR" => $_POST["user_name"],
                    "AUTHOR_PHONE" => $_POST["user_phone"],
                    "EMAIL_TO" => $arParams["EMAIL_TO"],
                    "TEXT" => $_POST["MESSAGE"],
                );

                $arLoadProductArray = array(
                    "MODIFIED_BY" => 1,
                    "IBLOCK_ID" => $iblock_id,
                    "NAME" => "Письмо от " . DATE('Y.m.d h:i:s'),
                    "ACTIVE" => "Y",
                    "DETAIL_TEXT" => implode("<br /> ", $arFields),
                );

                if(!empty($_FILES["user_file"]['name'])) {
                    $fileId = CFile::SaveFile($_FILES['user_file'],"tmp");

                    $arLoadProductArray['PROPERTY_VALUES'] = ['FILE' => CFile::MakeFileArray($fileId)];
                }

                CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields);
                $el = new CIBlockElement;
                $el->Add($arLoadProductArray);

                if($fileId)
                    CFile::Delete($fileId);

            }
        } else
            $arResult["ERROR_MESSAGE"][] = GetMessage("MF_SESS_EXP");

    }
}
elseif($_REQUEST["success"] == $arResult["PARAMS_HASH"])
{
    $arResult["OK_MESSAGE"] = $arParams["OK_TEXT"];
}



if($arParams["USE_CAPTCHA"] == "Y")
	$arResult["capCode"] =  htmlspecialcharsbx($APPLICATION->CaptchaGetCode());

$this->IncludeComponentTemplate();
