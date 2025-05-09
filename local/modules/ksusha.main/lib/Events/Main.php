<?php
namespace Ksusha\Main\Events;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\EventResult;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Security\Random;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;

class Main
{
    private const TOKEN_LENGTH = 12;

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function onAfterUserLoginHandler(array $params): EventResult
    {
        $userId = $params['USER_ID'];
        if (UserTable::GetByID($userId)->Fetch()['UF_TOKEN'] === '') {
            $user = new \CUser;
            $user->Update($userId, [
                'UF_TOKEN' => Random::getString(self::TOKEN_LENGTH),
            ]);
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
                'UF_TOKEN' => '',
            ]);
        }

        return new EventResult(EventResult::SUCCESS);
    }
}
