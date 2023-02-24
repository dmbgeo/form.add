<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();



$this->setFrameMode(true);
global $APPLICATION;


?>
<!-- form_continer -->
<div class="popup__title"><?= GetMessage('FORM_TITLE'); ?></div>
<div class="feedback">
    <p><?= GetMessage('FORM_TEXT'); ?></p>
    <form class="form" id="<?= $arResult['VIEW']['FORM_ID']; ?>" method="post" action="<?= $APPLICATION->GetCurPage(); ?>">
        <?= bitrix_sessid_post(); ?>
        <input type="hidden" value="<?= strtoupper(LANGUAGE_ID); ?>" name="utm">
        <div class="form__row">
            <div class="form__row__item">
                <div class="form-input">
                    <div class="form-input"><input type="text" class="input" placeholder="<?= GetMessage('INPUT_NAME'); ?>" name="name"></div>
                </div>
            </div>
            <div class="form__row__item">
                <div class="form-input">
                    <input type="text" class="input" name="phone">
                </div>
            </div>
        </div>
        <button class="btn-send"><?= GetMessage('SEND_FORM'); ?></button>
        <div class="form-agree"><?= GetMessage('FORM_POLICY', ['#LINK#' => SITE_DIR . 'policy/']); ?></a>.</div>
    </form>
</div>

<!-- form_continer -->