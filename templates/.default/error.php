<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();



$this->setFrameMode(true);
global $APPLICATION;


?>

<!-- form_continer -->

<?
foreach ($arResult['MESSAGES'] as $arMessage)
    echo ShowError($arMessage);
?>

<!-- form_continer -->