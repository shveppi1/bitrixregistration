<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

\Bitrix\Main\UI\Extension::load("ui.bootstrap4");
/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<div class="mb-4">
	<?if(!empty($arResult["ERROR_MESSAGE"]))
	{
		foreach($arResult["ERROR_MESSAGE"] as $v)
			ShowError($v);
	}
	if($arResult["OK_MESSAGE"] <> '')
	{
		?><div class="alert alert-success"><?=$arResult["OK_MESSAGE"]?></div><?
	}
	?>
	<form action="<?=POST_FORM_ACTION_URI?>" method="POST" enctype="multipart/form-data">
		<?=bitrix_sessid_post()?>

		<div class="form-group">
			<label for="mainFeedback_name"><?=GetMessage("MFT_NAME");?><?
				if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?><span class="mf-control-required">*</span><?endif;?></label>
			<input
				type="text"
				id="mainFeedback_name"
				name="user_name"
				class="form-control"
				value="<?=$arResult["AUTHOR_NAME"]?>"
				<?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])): ?>required<?endif?>
			/>
		</div>

		<div class="form-group">
			<label for="mainFeedback_phone">Телефон
				<?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("PHONE", $arParams["REQUIRED_FIELDS"])):?><span class="mf-control-required">*</span><?endif?>
			</label>
			<input
				type="text"
				if="mainFeedback_phone"
				name="user_phone"
				class="form-control"
				<?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("PHONE", $arParams["REQUIRED_FIELDS"])):?>required<?endif?>
			/>
		</div>


		<div class="form-group">
			<label for="mainFeedback_message"><?=GetMessage("MFT_MESSAGE")?><?
				if(empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])):?><span class="mf-control-required">*</span><?endif?></label>
			<textarea class="form-control" id="mainFeedback_message" name="MESSAGE" rows="5"><?=$arResult["MESSAGE"]?></textarea>
		</div>

		<div class="form-group">
			<label for="mainFeedback_file">Файл<?
				if(empty($arParams["REQUIRED_FIELDS"]) || in_array("FILE", $arParams["REQUIRED_FIELDS"])):?><span class="mf-control-required">*</span><?endif?></label>
			<input
				type="file"
				if="mainFeedback_file"
				name="user_file"
				class="form-control"
			/>
		</div>

		<br/>
		<div class="g-recaptcha" data-sitekey="6LcCQewoAAAAAMJwArGVHspN2p6NxJVowY9yePdJ"></div>
		<br/>

		<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
		<input type="submit" name="submit"  value="<?=GetMessage("MFT_SUBMIT")?>" class="btn btn-primary">
	</form>
</div>