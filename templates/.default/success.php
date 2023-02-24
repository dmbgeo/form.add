<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();



$this->setFrameMode(true);
global $APPLICATION;


?>

<!-- form_continer -->
<? if (!empty($arResult['RESULT_ID'])) : ?>
    <div class="popup__title"><?= GetMessage('TITLE'); ?></div>
    <div class="feedback">
        <p><?= GetMessage('TEXT'); ?></p>
        <div class="feedback__ok">
            <img src="<?= SITE_TEMPLATE_PATH; ?>/assets/img/ico-ok.svg" alt="">
        </div>
        <button class="btn-close-popup" data-fancybox-close><?= GetMessage('BUTTON') ?></button>
    </div>
<? endif; ?>
<!-- form_continer -->