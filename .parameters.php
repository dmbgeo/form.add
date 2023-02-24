<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @global array $arCurrentValues */
/** @var array $templateProperties */

use Bitrix\Main\Loader;

if (!Loader::includeModule('form'))
	return;

$arForms = [[0 => GetMessage("NOT_SELECTED")]];
$dbForm  = CForm::GetList('s_sort', 'asc', ['ACTIVE' => 'Y']);
while ($arForm = $dbForm->Fetch()) {
	$arForms[$arForm['ID']] = '[' . $arForm['ID'] . ']' . $arForm['NAME'];
}



$arFields = [];
if (isset($arCurrentValues['FORM_ID'])) {


	$rsQuestions = CFormField::GetList(
		$arCurrentValues['FORM_ID'],
		"ALL",
		"s_id",
		"desc",
		["ACTIVE" => "Y"]
	);
	while ($arQuestion  = $rsQuestions->Fetch()) {

		$arFields[$arQuestion['ID']] = ' [' . $arQuestion['ID'] . '] ' .  $arQuestion['TITLE'];
	}
}





$arComponentParameters = array(
	"GROUPS" => array(
		"FORM" => array(
			"NAME" => GetMessage("FORM_GROUP"),
		),
		"FORM_FIELDS" => array(
			"NAME" => GetMessage("FORM_FIELDS_GROUP"),
		),
		"FORM_FIELDS_LINK" => array(
			"NAME" => GetMessage("FORM_FIELDS_LINK_GROUP"),
		),
		"FORM_FIELDS_MASK" => array(
			"NAME" => GetMessage("FORM_FIELDS_MASK_GROUP"),
		),
		"FORM_FIELDS_VALIDATE" => array(
			"NAME" => GetMessage("FORM_FIELDS_VALIDATE_GROUP"),
		),
	),
	"PARAMETERS" => array(
		"FORM_ID" => array(
			"NAME" => GetMessage("FORM_ID"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arForms,
			"ADDITIONAL_VALUES" => "N",
			"PARENT" => "FORM",
			'REFRESH' => 'Y',
		),

		"SEND_SUCCESS" => array(
			"NAME" => GetMessage('SEND_SUCCESS'),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"PARENT" => "FORM",
		),

		"SEND_ERROR" => array(
			"NAME" => GetMessage('SEND_ERROR'),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"PARENT" => "FORM",
		),

		"SUCCESS_REDIRECT_URL" => array(
			"NAME" => GetMessage('SUCCESS_REDIRECT_URL'),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"PARENT" => "FORM",
		),

		"ERROR_REDIRECT_URL" => array(
			"NAME" => GetMessage('ERROR_REDIRECT_URL'),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"PARENT" => "FORM",
		),

		"FORM_FIELDS_ID" => array(
			"NAME" => GetMessage("FORM_FIELDS_ID"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arFields,
			"PARENT" => "FORM_FIELDS",
			'SIZE' => count($arFields) < 5 ? 5 : count($arFields),
			'REFRESH' => 'Y',
		),

		"FORM_IBLOCK_ELEMENT" => array(
			"NAME" => GetMessage("FORM_IBLOCK_ELEMENT"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arFields,
			"PARENT" => "FORM_FIELDS",
		),

		"IBLOCK_ELEMENT" => array(
			"NAME" => GetMessage("IBLOCK_ELEMENT"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"PARENT" => "FORM_FIELDS",
		),
	),
);


$arRules = [
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
$arComponentParameters['PARAMETERS']["IS_POPUP_FORM"] = array(
	"NAME" => GetMessage('IS_POPUP_FORM'),
	"TYPE" => "CHECKBOX",
	"MULTIPLE" => "N",
	"DEFAULT" => "Y",
	"PARENT" => "FORM",
);


$arComponentParameters['PARAMETERS']["IS_POPUP_RESULT"] = array(
	"NAME" => GetMessage('IS_POPUP_RESULT'),
	"TYPE" => "CHECKBOX",
	"MULTIPLE" => "N",
	"DEFAULT" => "Y",
	"PARENT" => "FORM",
);



$arComponentParameters['PARAMETERS']["POPUP_ID"] = array(
	"NAME" => GetMessage('POPUP_ID'),
	"TYPE" => "STRING",
	"MULTIPLE" => "N",
	"DEFAULT" => "",
	"PARENT" => "FORM",
);
$arComponentParameters['PARAMETERS']["POPUP_RESULT_ID"] = array(
	"NAME" => GetMessage('POPUP_RESULT_ID'),
	"TYPE" => "STRING",
	"MULTIPLE" => "N",
	"DEFAULT" => "",
	"PARENT" => "FORM",
);

if (isset($arFields[$filedId])) {
	$arComponentParameters['PARAMETERS']['FORM_FIELDS_LINK_' . $filedId] = array(
		"NAME" => $arFields[$filedId],
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"DEFAULT" => "",
		"PARENT" => "FORM",
	);
}
if (isset($arCurrentValues['FORM_FIELDS_ID']) &&  count($arCurrentValues['FORM_FIELDS_ID']) > 0) {

	foreach ($arCurrentValues['FORM_FIELDS_ID'] as $filedId) {
		if (isset($arFields[$filedId])) {
			$arComponentParameters['PARAMETERS']['FORM_FIELDS_LINK_' . $filedId] = array(
				"NAME" => $arFields[$filedId],
				"TYPE" => "STRING",
				"MULTIPLE" => "N",
				"DEFAULT" => "",
				"PARENT" => "FORM_FIELDS_LINK",
			);
			$arComponentParameters['PARAMETERS']['FORM_FIELDS_MASK_' . $filedId] = array(
				"NAME" => $arFields[$filedId],
				"TYPE" => "STRING",
				"MULTIPLE" => "N",
				"DEFAULT" => "",
				"PARENT" => "FORM_FIELDS_MASK",
			);
		}
	}

	foreach ($arCurrentValues['FORM_FIELDS_ID'] as $filedId) {
		if (isset($arFields[$filedId])) {
			$arComponentParameters['PARAMETERS']['FORM_FIELDS_VALIDATION_' . $filedId] = array(
				"NAME" => $arFields[$filedId],
				"TYPE" => "LIST",
				"MULTIPLE" => "Y",
				"VALUES" => $arRules,
				"PARENT" => "FORM_FIELDS_VALIDATE",
				'SIZE' => count($arRules) < 5 ? 5 : count($arRules),
				'REFRESH' => 'Y',
			);
		}
	}



	foreach ($arCurrentValues['FORM_FIELDS_ID'] as $filedId) {
		$arComponentParameters['GROUPS']['FORM_FIELDS_VALIDATE_VALUE_GROUP_' . $filedId] = [
			"NAME" => GetMessage("FORM_FIELDS_VALIDATE_VALUE_GROUP", ['#FIELD#' => $arFields[$filedId]]),
		];
		foreach ($arRules as $arRule => $arRuleName) {
			if (isset($arCurrentValues['FORM_FIELDS_VALIDATION_' . $filedId]) && in_array($arRule, $arCurrentValues['FORM_FIELDS_VALIDATION_' . $filedId])) {
				$arComponentParameters['PARAMETERS']['FORM_FIELDS_VALIDATION_VALUE_' . $filedId . '_' . $arRule] = array(
					"NAME" => $arRuleName,
					"TYPE" => "STRING",
					"MULTIPLE" => "N",
					"DEFAULT" => "",
					"PARENT" => 'FORM_FIELDS_VALIDATE_VALUE_GROUP_' . $filedId,
				);
			}
		}
	}


	foreach ($arCurrentValues['FORM_FIELDS_ID'] as $filedId) {

		$arComponentParameters['GROUPS']['FORM_FIELDS_ERROR_MESSAGE_GROUP_' . $filedId] = [
			"NAME" => GetMessage("FORM_FIELDS_ERROR_MESSAGE_GROUP", ['#FIELD#' => $arFields[$filedId]]),
		];
		foreach ($arRules as $arRule => $arRuleName) {
			if (isset($arCurrentValues['FORM_FIELDS_VALIDATION_' . $filedId]) && in_array($arRule, $arCurrentValues['FORM_FIELDS_VALIDATION_' . $filedId])) {
				$arComponentParameters['PARAMETERS']['FORM_FIELDS_ERROR_MESSAGE_' . $filedId . '_' . $arRule] = array(
					"NAME" =>  $arRuleName,
					"TYPE" => "STRING",
					"MULTIPLE" => "N",
					"DEFAULT" => "",
					"PARENT" => 'FORM_FIELDS_ERROR_MESSAGE_GROUP_' . $filedId,
				);
			}
		}
	}
}
