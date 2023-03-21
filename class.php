<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Loader;

class CBitrixBasketComponent extends CBitrixComponent
{

    public function __construct($component = null)
    {
        parent::__construct($component);
    }




    public function onPrepareComponentParams($params)
    {

        $params['RULES'] = [
            'required' => GetMessage('RULE_NAME_required'),
            'remote' => GetMessage('RULE_NAME_remote'),
            'minlength' => GetMessage('RULE_NAME_minlength'),
            'maxlength' => GetMessage('RULE_NAME_maxlength'),
            'rangelength' => GetMessage('RULE_NAME_rangelength'),
            'min' => GetMessage('RULE_NAME_min'),
            'max' => GetMessage('RULE_NAME_max'),
            'range' => GetMessage('RULE_NAME_range'),
            'step' => GetMessage('RULE_NAME_step'),
            'email' => GetMessage('RULE_NAME_email'),
            'url' => GetMessage('RULE_NAME_url'),
            'date' => GetMessage('RULE_NAME_date'),
            'dateISO' => GetMessage('RULE_NAME_dateISO'),
            'number' => GetMessage('RULE_NAME_number'),
            'digits' => GetMessage('RULE_NAME_digits'),
            'equalTo' => GetMessage('RULE_NAME_equalTo'),
            'remote_form' => GetMessage('RULE_NAME_remote_form'),
        ];

        if (empty($params['POPUP_RESULT_ID'])) {
            $params['POPUP_RESULT_ID'] = $params['POPUP_ID'];
        }
        if (empty($params['POPUP_CORE'])) {
            $params['POPUP_CORE'] = 'fancybox';
        }
        return $params;
    }


    public function executeComponent()
    {

        if (Loader::includeModule('form')) {
            $arForm = CForm::GetByID($this->arParams['FORM_ID'])->Fetch();
            if ($arForm) {
                $this->arResult['FORM'] = $arForm;
                $rsFields = CFormField::GetList(
                    $this->arParams['FORM_ID'],
                    "ALL",
                    "s_id",
                    "asc",
                    ["ACTIVE" => "Y"]
                );



                if ($this->arParams['IBLOCK_ELEMENT']) {
                    if (Loader::includeModule('iblock')) {
                        $IblockElement = CIBlockElement::GetByID($this->arParams['IBLOCK_ELEMENT'])->GetNextElement();
                        if ($IblockElement) {
                            $this->arResult['IBLOCK_ELEMENT'] = $IblockElement->GetFields();
                            $this->arResult['IBLOCK_ELEMENT']['GROUPS'] = $IblockElement->GetGroups();
                            $this->arResult['IBLOCK_ELEMENT']['PROPERTIES'] = $IblockElement->GetProperties();
                        }
                    }
                }

                while ($arField  = $rsFields->Fetch()) {
                    $rsAnswer = \CFormAnswer::GetList($arField['ID']);


                    while ($arAnswer = $rsAnswer->Fetch()) {
                        $arField['ANSWER'][intVal($arAnswer['ID'])] = $arAnswer;
                    }
                    $arAnswer = reset($arField['ANSWER']);
                    $code = 'form_' . $arAnswer['FIELD_TYPE'] . '_';
                    switch ($arAnswer['FIELD_TYPE']) {
                        case "text":
                            $code .= $arAnswer['ID'];
                            break;
                        case "textarea":
                            $code .= $arAnswer['ID'];
                            break;
                        case "password":
                            $code .= $arAnswer['ID'];
                            break;
                        case "date":
                            $code .= $arAnswer['ID'];
                            break;
                        case "radio":
                            $code .= $arAnswer['ID'];
                            break;
                        case "dropdown":
                            $code .= $arField['SID'];
                            break;
                        case "checkbox":
                            $code .= $arField['SID'] . '[]';
                            break;
                        case "multiselect":
                            $code .= $arField['SID'] . '[]';
                            break;
                        case "file":
                            $code .= $arAnswer['ID'];
                            break;
                        case "image":
                            $code .= $arAnswer['ID'];
                            break;
                        case "hidden    ":
                            $code .= $arAnswer['ID'];
                            break;
                    }
                    $arField['CODE'] = $code;
                    $arField['LINK'] = !empty($this->arParams['FORM_FIELDS_LINK_' . $arField['ID']]) ? $this->arParams['FORM_FIELDS_LINK_' . $arField['ID']] : $arField['CODE'];
                    $arField['VALUE'] = !empty($_REQUEST[$arField['LINK']]) ? $_REQUEST[$arField['LINK']] : (!empty($_REQUEST[$arField['CODE']]) ? $_REQUEST[$arField['CODE']] : '');
                    $arField['VALUE_TEXT'] = $arField['VALUE'];
                    if (!empty($arField['VALUE']) && count($arField['ANSWER']) > 1 &&  isset($arField['ANSWER'][intVal($arField['VALUE'])])) {
                        $arField['VALUE_TEXT'] = $arField['ANSWER'][intVal($arField['VALUE'])]['MESSAGE'];
                    }

                    if ($arField['ID'] == $this->arParams['FORM_IBLOCK_ELEMENT'] && !empty($this->arResult['IBLOCK_ELEMENT'])) {
                        $arField['VALUE'] =  '[' . $this->arResult['IBLOCK_ELEMENT']['ID'] . '] ' . $this->arResult['IBLOCK_ELEMENT']['NAME'];
                    }
                    $this->arResult['FIELDS'][intVAl($arField['ID'])] = $arField;
                }

                $this->setValidation();

                $this->arResult['ERRORS'] = [];

                $this->arResult['VIEW']['FORM_ID'] = 'form_' . $this->arParams['FORM_ID'] . '_' . $this->generateRandomString(rand(10, 30));
                $this->arResult['VIEW']['POPUP_ID'] = $this->arParams['POPUP_ID'];
                $this->arResult['VIEW']['POPUP_RESULT_ID'] = $this->arParams['POPUP_RESULT_ID'];

                if (!isset($_REQUEST['AJAX_MODE']) && $_REQUEST['AJAX_MODE'] != 'Y') {
                    $this->arResult['VIEW']['CONTAINER_ID'] = 'container_' . $this->arParams['FORM_ID'] . '_' . $this->generateRandomString(rand(10, 30));
                }
                if (!empty($_POST['ACTION']) && $_POST['ACTION'] == 'SEND') {
                    $this->addResult();
                }


                if (!empty($this->arResult['ERRORS'])) {
                    $this->includeComponentTemplate('error');
                } elseif (!empty($this->arResult['RESULT_ID'])) {
                    $this->includeComponentTemplate('success');
                } else {
                    $this->includeComponentTemplate();
                }
            } else {
                echo ShowError(GetMessage('FORM_NOT_FOUND'));
            }
        } else {
            echo ShowError(GetMessage('COMPONENT_FORM_NOT_FOUND'));
        }
    }
    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function setValidation()
    {
        $this->arResult['VALIDATION'] = [
            'RULES' => [],
            'MESSAGES' => [],
            'MASKS' => [],
        ];
        foreach ($this->arResult['FIELDS'] as $filedId => $arField) {

            if (count($this->arParams['FORM_FIELDS_VALIDATION_' . $filedId]) > 0) {
                foreach ($this->arParams['FORM_FIELDS_VALIDATION_' . $filedId] as $arRule) {
                    if (!empty($this->arParams['FORM_FIELDS_VALIDATION_VALUE_' . $filedId . '_' . $arRule])) {
                        $arRuleParam = $this->arParams['FORM_FIELDS_VALIDATION_VALUE_' . $filedId . '_' . $arRule];
                        $this->arResult['VALIDATION']['RULES'][$arField['LINK']][$arRule] = $this->isJson($arRuleParam) ? json_decode($arRuleParam) : $arRuleParam;
                    }
                    if (!empty($this->arParams['FORM_FIELDS_ERROR_MESSAGE_' . $filedId . '_' . $arRule])) {
                        $this->arResult['VALIDATION']['MESSAGES'][$arField['LINK']][$arRule] = $this->arParams['FORM_FIELDS_ERROR_MESSAGE_' . $filedId . '_' . $arRule];
                    }
                    if (!empty($this->arParams['FORM_FIELDS_MASK_' . $filedId])) {
                        $this->arResult['VALIDATION']['MASKS'][$arField['LINK']][$arRule] = $this->arParams['FORM_FIELDS_MASK_' . $filedId];
                    }
                }
            }
        }
    }

    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    private function addResult()
    {
        $event = new \Bitrix\Main\Event("dmbgeo.form", "OnBeforeAddResult", [$this->arResult, $this->arParams]);
        $event->send();
        foreach ($event->getResults() as $eventResult) {
            if ($eventResult->getType() == \Bitrix\Main\EventResult::ERROR) // если обработчик вернул ошибку, ничего не делаем 
                continue;
            $this->arResult = array_merge($this->arResult, $eventResult->getParameters());
        }

        if (empty($this->arResult['ERRORS'])) {
            $arValues = [];
            $arEventValues = [];
            foreach ($this->arResult['FIELDS'] as $arField) {
                if (!empty($arField['VALUE'])) {
                    $arValues[$arField['CODE']] = $arField['VALUE'];
                    $arEventValues[$arField['LINK']] = $arField['VALUE_TEXT'];
                }
            }
            $this->arResult['VALUES'] = $arValues;
            $this->arResult['EVENT_VALUES'] = $arEventValues;
            $this->arResult['RESULT_ID'] = CFormResult::Add($this->arResult['FORM']['ID'], $arValues);
            if ($this->arResult['RESULT_ID']) {
                CFormCRM::onResultAdded($this->arResult['FORM']['ID'], $this->arResult['RESULT_ID']);
                CFormResult::SetEvent($this->arResult['RESULT_ID']);
                CFormResult::Mail($this->arResult['RESULT_ID']);

                $event = new \Bitrix\Main\Event("dmbgeo.form", "OnAfterAddResult", [$this->arResult, $this->arParams]);
                $event->send();
                foreach ($event->getResults() as $eventResult) {
                    if ($eventResult->getType() == \Bitrix\Main\EventResult::ERROR) // если обработчик вернул ошибку, ничего не делаем 
                        continue;
                    $this->arResult = array_merge($this->arResult, $eventResult->getParameters());
                }
            } else {
                global $strError;
                $this->arResult['ERRORS'][] = $strError;
            }
        }
    }
}
