<?
use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Регистрация");

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/script.js");


?>

    <form method="POST" id="REG-STEP_ONE" enctype="multipart/form-data">
        <input type="hidden" name="AUTH_FORM" value="Y">
        <input type="hidden" name="TYPE" value="REG-STEP_ONE">

        <div class="bx-authform-formgroup-container">
            <div class="bx-authform-label-container"><span class="bx-authform-starrequired">*</span><?=GetMessage("IN_EMAIL");?></div>
            <div class="bx-authform-input-container">
                <input type="text" name="USER_EMAIL" maxlength="255" value="">
            </div>
        </div>

        <br />

        <div class="bx-authform-formgroup-container">
            <input type="submit" class="btn btn-primary" name="Register" value="<?=GetMessage("BTN_SUBMIT");?>">
        </div>

    </form>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>