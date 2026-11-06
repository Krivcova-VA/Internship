<?php

namespace Only\Site\Agents;


class Iblock
{
    public static function clearOldLogs()
    {
        if (!\CModule::IncludeModule("iblock")) {
            return '\Only\Site\Agents\Iblock::clearOldLogs();';
        }
        $res = \CIBlock::GetList(
            [],
            ["CODE" => "LOG", "CHECK_PERMISSIONS" => "N"]
        );
        $logIblock = $res->Fetch();
        if (!$logIblock) {
            return '\Only\Site\Agents\Iblock::clearOldLogs();';
        }
        $res = \CIBlockElement::GetList(
            ["DATE_ACTIVE_FROM" => "DESC", "ID" => "DESC"],
            [ "IBLOCK_ID" => $logIblock["ID"], "CHECK_PERMISSIONS" => "N"],
            false,
            false,
            ["ID"]
        );
        $count = 0;
        while ($element = $res->Fetch()) {
            $count++;
            if ($count > 10) {
                \CIBlockElement::Delete($element["ID"]);
            }
        }
        return '\Only\Site\Agents\Iblock::clearOldLogs();';
    }

    public static function example()
    {
        global $DB;
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $iblockId = \Only\Site\Helpers\IBlock::getIblockID('QUARRIES_SEARCH', 'SYSTEM');
            $format = $DB->DateFormatToPHP(\CLang::GetDateFormat('SHORT'));
            $rsLogs = \CIBlockElement::GetList(['TIMESTAMP_X' => 'ASC'], [
                'IBLOCK_ID' => $iblockId,
                '<TIMESTAMP_X' => date($format, strtotime('-1 months')),
            ], false, false, ['ID', 'IBLOCK_ID']);
            while ($arLog = $rsLogs->Fetch()) {
                \CIBlockElement::Delete($arLog['ID']);
            }
        }
        return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
    }
}
