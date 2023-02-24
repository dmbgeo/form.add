<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;
if (!$this->__template)  $this->InitComponentTemplate();

if (isset($_REQUEST['AJAX_MODE']) && $_REQUEST['AJAX_MODE'] == 'Y') {
    $content = ob_get_contents();
    $APPLICATION->RestartBuffer();
    $arResult['PARAMS'] = $arParams;
    $result = [
        'html' => explode('<!-- form_continer -->', $content)[1],
        'data' => $arResult,
    ];

    echo json_encode($result);
    exit;
}
