<?php
//подключаем ядро битрикса и проверям текущий пользователь админ
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
if (!$USER->IsAdmin()) {
    LocalRedirect('/');
}
//подключаем модуль инфоблоков 
\Bitrix\Main\Loader::includeModule('iblock');
$row = 1;
$IBLOCK_ID = 6;

$el = new CIBlockElement;
$arProps = [];



$rsProp = CIBlockPropertyEnum::GetList(
    ["SORT" => "ASC", "VALUE" => "ASC"],
    ['IBLOCK_ID' => $IBLOCK_ID]
);
while ($arProp = $rsProp->Fetch()) {
    $key = trim($arProp['VALUE']);
    $arProps[$arProp['PROPERTY_CODE']][$key] = $arProp['ID'];
}

//функция для нахождения конкретного id свойства из списка
function getEnumId($propertyCode, $value, $arProps)
{
    $value = mb_strtolower(trim((string)$value), 'UTF-8');
    if ($value === '') {
        return null;
    }
    foreach ($arProps[$propertyCode] ?? [] as $name => $id) {
        $name = mb_strtolower(trim($name), 'UTF-8');
        if ($name === $value) {
            return $id;
        }
    }
    return null;
}

//удаляем все элементы перед загрузкой новых
$rsElements = CIBlockElement::GetList([], ['IBLOCK_ID' => $IBLOCK_ID], false, false, ['ID']);
while ($element = $rsElements->GetNext()) {
    CIBlockElement::Delete($element['ID']);
}

//заполняем свойства элемента полученными данными, создаем новый элемент
if (($handle = fopen("vacancy.csv", "r")) !== false) {
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        if ($row == 1) {
            $row++;
            continue;
        }
        $row++;

        $PROP['ACTIVITY'] = getEnumId('ACTIVITY', $data[9], $arProps);
        $PROP['FIELD'] = getEnumId('FIELD', $data[11], $arProps);
        $PROP['OFFICE'] = getEnumId('OFFICE', $data[1], $arProps);
        $PROP['LOCATION'] = getEnumId('LOCATION', $data[2], $arProps);
        $PROP['REQUIRE'] = $data[4];
        $PROP['DUTY'] = $data[5];
        $PROP['CONDITIONS'] = $data[6];
        $PROP['EMAIL'] = $data[12];
        $PROP['DATE'] = date('d.m.Y');
        $PROP['TYPE'] = getEnumId('TYPE', $data[8], $arProps);
        $PROP['SALARY_TYPE'] = '';
        $PROP['SALARY_VALUE'] = $data[7];
        $PROP['SCHEDULE'] = getEnumId('SCHEDULE', $data[10], $arProps);

        foreach (['REQUIRE', 'DUTY', 'CONDITIONS'] as $key) {
            $value = trim($PROP[$key]);
            $value = str_replace('\n', '', $value);
            if (stripos($value, '•') !== false) {
                $value = explode('•', $value);
                array_splice($value, 0, 1);
                foreach ($value as &$str) {
                    $str = trim($str);
                }
                unset($str);
            } 
            $PROP[$key] = $value;
        }


        if ($PROP['SALARY_VALUE'] == '-') {
            $PROP['SALARY_VALUE'] = '';
        } elseif ($PROP['SALARY_VALUE'] == 'по договоренности') {
            $PROP['SALARY_VALUE'] = '';
            $PROP['SALARY_TYPE'] = getEnumId(
                'SALARY_TYPE', 'Договорная', $arProps
            );
        } else {
            $arSalary = explode(' ', $PROP['SALARY_VALUE']);
            if ($arSalary[0] == 'от' || $arSalary[0] == 'до') {
                $PROP['SALARY_TYPE'] = getEnumId(
                    'SALARY_TYPE',
                    $arSalary[0] == 'от' ? 'ОТ' : 'ДО',
                    $arProps
                );
                array_splice($arSalary, 0, 1);
                $PROP['SALARY_VALUE'] = implode(' ', $arSalary);
            } else {
                $PROP['SALARY_TYPE'] = getEnumId(
                    'SALARY_TYPE', '=', $arProps
                );
            }
        }

        $arLoadProductArray = [
            "MODIFIED_BY" => $USER->GetID(),
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => $IBLOCK_ID,
            "PROPERTY_VALUES" => $PROP,
            "NAME" => $data[3],
            "ACTIVE" => end($data) ? 'Y' : 'N',
        ];

        if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
            echo "Добавлен элемент с ID : " . $PRODUCT_ID . "<br>";
        } else {
            echo "Error: " . $el->LAST_ERROR . '<br>';
        }
    }
    fclose($handle);
}


