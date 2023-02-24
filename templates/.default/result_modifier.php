<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


use \Bitrix\Main\Loader;

Loader::includeModule('iblock');
$dbElem = \CIBlockElement::GetList([], [
    "SECTION_ID" => getSectionIdByPath(IBLOCKS['CONTENT'], "/" . LANGUAGE_ID . '/index_from_invest'),
    "IBLOCK_ID" => IBLOCKS['CONTENT'],
], false, false, ['ID', 'NAME', 'SECTION_ID']);
$arResult['CUSTOM_FIELD']['ITEMS'] = [];
$arResult['CUSTOM_FIELD']['DESCRIPTION'] = false;
while ($arElem = $dbElem->Fetch()) {

    if (!$arResult['CUSTOM_FIELD']['DESCRIPTION']) {
        $arResult['CUSTOM_FIELD']['DESCRIPTION'] = \CIBlockSection::GetList([], [
            "ID" => getSectionIdByPath(IBLOCKS['CONTENT'], "/" . LANGUAGE_ID . '/index_from_invest'),
            "IBLOCK_ID" => IBLOCKS['CONTENT'],
        ], false, ['DESCRIPTION'])->Fetch()['DESCRIPTION'] ?? "";

    }
    $arResult['CUSTOM_FIELD']['ITEMS'][$arElem['ID']] = $arElem['NAME'];
}
