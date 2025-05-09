<?php
namespace Ksusha\Main\Events;

use Bitrix\Main\EventResult;
use Bitrix\Main\Security\Random;

class Main
{
    private const TOKEN_LENGTH = 12;
    public static function onAfterUserLoginHandler(array $params): EventResult
    {
        $userId = $params['USER_ID'];
        if (\CUser::GetByID($userId)->Fetch()['UF_TOKEN'] === '') {
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
