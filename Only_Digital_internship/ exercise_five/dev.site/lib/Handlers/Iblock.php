<?php

namespace Only\Site\Handlers;

class Iblock
{
    public static function addLog(&$arFields)
    {
        if (empty($arFields["RESULT"]) || $arFields["ID"] <= 0) {
            return;
        }

        if (!\CModule::IncludeModule("iblock")) {
            return;
        }

        $res = \CIBlock::GetList([], ["CODE" => "LOG"]);
        $logIblock = $res->Fetch();

        if (!$logIblock) {
            return;
        }

        $logIblockId = $logIblock["ID"];
        $sourceIblockId = $arFields["IBLOCK_ID"];

        if ($sourceIblockId == $logIblockId) { // Обходим изменения LOG
            return;
        }

        // Получаем имя исходного инфоблока
        $res = \CIBlock::GetByID($sourceIblockId);
        $sourceIblock = $res->Fetch();

        if (!$sourceIblock) {
            return;
        }

        $iblockName = $sourceIblock["NAME"];
        $iblockCode = $sourceIblock["CODE"];

        $res = \CIBlockSection::GetList(
            [],
            ["IBLOCK_ID" => $logIblockId, "CODE" => $iblockCode],
            false,
            ["ID"]
        );

        $section = $res->Fetch();

        if ($section) { // Создан ли такой раздел в LOG
            $sectionId = $section["ID"];
        } else { // Нет, создаём раздел
            $section = new \CIBlockSection();

            $sectionId = $section->Add([
                "IBLOCK_ID" => $logIblockId,
                "NAME" => $iblockName,
                "CODE" => $iblockCode,
                "ACTIVE" => "Y"
            ]);

            if (!$sectionId) {
                return;
            }
        }

        // Получаем элемент
        $res = \CIBlockElement::GetByID($arFields["ID"]);
        $element = $res->Fetch();

        if (!$element) {
            return;
        }

        $description = $iblockName;

        if ($element["IBLOCK_SECTION_ID"] > 0) {
            $res = \CIBlockSection::GetNavChain(
                $sourceIblockId,
                $element["IBLOCK_SECTION_ID"]
            );

            while ($parentSection = $res->Fetch()) {
                $description .= " -> " . $parentSection["NAME"];
            }
        }

        $description .= " -> " . $element["NAME"];

        $logCode = $sourceIblockId . "_" . $element["ID"];

        $logFields = [
            "IBLOCK_ID" => $logIblockId,
            "IBLOCK_SECTION_ID" => $sectionId,
            "NAME" => (string)$element["ID"],
            "CODE" => $logCode,
            "ACTIVE" => "Y",
            "ACTIVE_FROM" => \ConvertTimeStamp(time(), "FULL"),
            "PREVIEW_TEXT" => $description,
            "PREVIEW_TEXT_TYPE" => "text"
        ];

        // Проверяем, есть ли уже такая запись
        $res = \CIBlockElement::GetList(
            [],
            ["IBLOCK_ID" => $logIblockId, "CODE" => $logCode],
            false,
            false,
            ["ID"]
        );

        $oldLog = $res->Fetch();
        $logElement = new \CIBlockElement();

        if ($oldLog) {
            $logElement->Update($oldLog["ID"], $logFields); // Изменяем запись
        } else {
            $logElement->Add($logFields); // Создаём новую
        }
    }

    function OnBeforeIBlockElementAddHandler(&$arFields)
    {
        $iQuality = 95;
        $iWidth = 1000;
        $iHeight = 1000;

        /*
         * Получаем пользовательские свойства
         */
        $dbIblockProps = \Bitrix\Iblock\PropertyTable::getList(array(
            'select' => array('*'),
            'filter' => array('IBLOCK_ID' => $arFields['IBLOCK_ID'])
        ));

        /*
         * Выбираем только свойства типа ФАЙЛ (F)
         */
        $arUserFields = [];

        while ($arIblockProps = $dbIblockProps->Fetch()) {
            if ($arIblockProps['PROPERTY_TYPE'] == 'F') {
                $arUserFields[] = $arIblockProps['ID'];
            }
        }

        /*
         * Перебираем и масштабируем изображения
         */
        foreach ($arUserFields as $iFieldId) {
            foreach ($arFields['PROPERTY_VALUES'][$iFieldId] as &$file) {
                if (!empty($file['VALUE']['tmp_name'])) {
                    $sTempName = $file['VALUE']['tmp_name'] . '_temp';

                    $res = \CAllFile::ResizeImageFile(
                        $file['VALUE']['tmp_name'],
                        $sTempName,
                        array("width" => $iWidth, "height" => $iHeight),
                        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                        false,
                        $iQuality
                    );

                    if ($res) {
                        rename($sTempName, $file['VALUE']['tmp_name']);
                    }
                }
            }
        }

        if ($arFields['CODE'] == 'brochures') {
            $RU_IBLOCK_ID = \Only\Site\Helpers\IBlock::getIblockID('DOCUMENTS', 'CONTENT_RU');
            $EN_IBLOCK_ID = \Only\Site\Helpers\IBlock::getIblockID('DOCUMENTS', 'CONTENT_EN');

            if ($arFields['IBLOCK_ID'] == $RU_IBLOCK_ID || $arFields['IBLOCK_ID'] == $EN_IBLOCK_ID) {
                \CModule::IncludeModule('iblock');
                $arFiles = [];

                foreach ($arFields['PROPERTY_VALUES'] as $id => &$arValues) {
                    $arProp = \CIBlockProperty::GetByID($id, $arFields['IBLOCK_ID'])->Fetch();

                    if ($arProp['PROPERTY_TYPE'] == 'F' && $arProp['CODE'] == 'FILE') {
                        $key_index = 0;

                        while (isset($arValues['n' . $key_index])) {
                            $arFiles[] = $arValues['n' . $key_index++];
                        }
                    } elseif ($arProp['PROPERTY_TYPE'] == 'L' && $arProp['CODE'] == 'OTHER_LANG' && $arValues[0]['VALUE']) {
                        $arValues[0]['VALUE'] = null;

                        if (!empty($arFiles)) {
                            $OTHER_IBLOCK_ID = $RU_IBLOCK_ID == $arFields['IBLOCK_ID'] ? $EN_IBLOCK_ID : $RU_IBLOCK_ID;

                            $arOtherElement = \CIBlockElement::GetList(
                                [],
                                [
                                    'IBLOCK_ID' => $OTHER_IBLOCK_ID,
                                    'CODE' => $arFields['CODE']
                                ],
                                false,
                                false,
                                ['ID']
                            )->Fetch();

                            if ($arOtherElement) {
                                \CIBlockElement::SetPropertyValues(
                                    $arOtherElement['ID'],
                                    $OTHER_IBLOCK_ID,
                                    $arFiles,
                                    'FILE'
                                );
                            }
                        }
                    } elseif ($arProp['PROPERTY_TYPE'] == 'E') {
                        $elementIds = [];

                        foreach ($arValues as &$arValue) {
                            if ($arValue['VALUE']) {
                                $elementIds[] = $arValue['VALUE'];
                                $arValue['VALUE'] = null;
                            }
                        }

                        if (!empty($arFiles && !empty($elementIds))) {
                            $rsElement = \CIBlockElement::GetList(
                                [],
                                [
                                    'IBLOCK_ID' => \Only\Site\Helpers\IBlock::getIblockID(
                                        'PRODUCTS',
                                        'CATALOG_' . $RU_IBLOCK_ID == $arFields['IBLOCK_ID'] ? '_RU' : '_EN'
                                    ),
                                    'ID' => $elementIds
                                ],
                                false,
                                false,
                                ['ID', 'IBLOCK_ID', 'NAME']
                            );

                            while ($arElement = $rsElement->Fetch()) {
                                \CIBlockElement::SetPropertyValues(
                                    $arElement['ID'],
                                    $arElement['IBLOCK_ID'],
                                    $arFiles,
                                    'FILE'
                                );
                            }
                        }
                    }
                }
            }
        }
    }
}
