<?
use Bitrix\Main\Page\Asset;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Регистрация");

if(isset($_GET['qwe']) && strlen($_GET['qwe']) > 3):

    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/script.js");

    $user = new CUser;
    $arParams["SELECT"] = array("*");
    $filter = array('UF_REGISTRATION_KEY' => $_GET['qwe']);
    $cUser = $user::GetList(($by = "id"), ($order = "desc"), $filter, $arParams)->fetch();

?>

    <?php if($cUser && $cUser['ACTIVE'] == 'N'):?>
        <form method="POST" id="REG-STEP_SAVE" enctype="multipart/form-data">
            <input type="hidden" name="TYPE" value="SAVE_USER">
            <input type="hidden" name="USER_EMAIL" value="<?=$cUser['EMAIL']?>">
            <input type="hidden" name="USER_QWE" value="<?=$_GET['qwe']?>">

            <div class="bx-authform-formgroup-container">
                <div class="bx-authform-label-container"><span class="bx-authform-starrequired">*</span>
                    <?=GetMessage("IN_LOGIN");?>
                </div>
                <div class="bx-authform-input-container">
                    <input type="text" name="USER_LOGIN" maxlength="255" value="">
                </div>
            </div>

            <div class="bx-authform-formgroup-container">
                <div class="bx-authform-label-container"><span class="bx-authform-starrequired">*</span>
                    <?=GetMessage("IN_NAME");?>
                </div>
                <div class="bx-authform-input-container">
                    <input type="text" name="USER_NAME" maxlength="255" value="">
                </div>
            </div>

            <div class="bx-authform-formgroup-container">
                <div class="bx-authform-label-container"><span class="bx-authform-starrequired">*</span>
                    <?=GetMessage("IN_LASTNAME");?>
                </div>
                <div class="bx-authform-input-container">
                    <input type="text" name="USER_LASTNAME" maxlength="255" value="">
                </div>
            </div>

            <div class="bx-authform-formgroup-container">
                <div class="bx-authform-label-container"><span class="bx-authform-starrequired">*</span>
                    <?=GetMessage("IN_PHONE");?>
                </div>
                <div class="bx-authform-input-container">
                    <input type="text" name="USER_PHONE" maxlength="255" value="">
                </div>
            </div>

            <div class="bx-authform-formgroup-container">
                <div class="bx-authform-label-container"><span class="bx-authform-starrequired">*</span>
                    <?=GetMessage("IN_PASSWORD");?>
                </div>
                <div class="bx-authform-input-container">
                    <input type="password" name="USER_PASSWORD" maxlength="255" value="">
                </div>
            </div>

            <div class="bx-authform-formgroup-container">
                <div class="bx-authform-label-container"><span class="bx-authform-starrequired">*</span>
                    <?=GetMessage("IN_RPASSWORD");?>
                </div>
                <div class="bx-authform-input-container">
                    <input type="password" name="USER_RPASSWORD" maxlength="255" value="">
                </div>
            </div>
            <br />
            <div class="errortext">
            </div>

            <br />

            <div class="bx-authform-formgroup-container">
                <input type="submit" class="btn btn-primary" name="Register" value="<?=GetMessage("BTN_SUBMIT");?>">
            </div>

        </form>

    <?php endif; ?>



<?php
else: ?>

<div>
    <h3><?=GetMessage("INFO-T");?></h3>

    <strong>
        <?=GetMessage("INFO-ER", ['#URL#' => '<a href="https://test-1.s00000.ru/registratsiya/">заново</a>']);?>
    </strong>
</div>

<?php endif; ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>