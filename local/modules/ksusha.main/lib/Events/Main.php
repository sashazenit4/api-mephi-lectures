<?php
namespace Ksusha\Main\Events;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\EventResult;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Security\Random;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use Bitrix\Main\Diag\Debug;

class Main
{
    private const TOKEN_LENGTH = 24;

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function onAfterUserLoginHandler(array $params): EventResult
    {
        $userId = $params['USER_ID'];
        if (is_null(UserTable::GetByID($userId)->Fetch()['UF_TOKEN'])) {
            $user = new \CUser;
            $res = $user->Update($userId, [
                'UF_TOKEN' => Random::getString(self::TOKEN_LENGTH, true),
            ]);
            Debug::dumpToFile($res);
        }

        return new EventResult(EventResult::SUCCESS);
    }
    public static function OnAfterUserLogoutHandler(array $params): EventResult
    {
        $userId = $params['USER_ID'];
        $logoutSuccess = $params['SUCCESS'];
        if ($logoutSuccess) {
            $user = new \CUser;
            $user->Update($userId, [
                'UF_TOKEN' => null,
            ]);
        }

        return new EventResult(EventResult::SUCCESS);
    }
}
