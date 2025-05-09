<?php
namespace Ksusha\Main\Controller;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\Controller as BitrixContoller;
use Bitrix\Main\Error;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Ksusha\Main\Helper\Hlblock;
use Ksusha\Main\Helper\Iblock;
use Bitrix\Main\UserTable;

class User extends BitrixContoller
{
    public function configureActions(): array
    {
        return [
            'getUser' => [
                'prefilters' => [],
                'postfilters' => [],
            ]
        ];
    }

    public function getUserAction(): array
    {
        $authorization = $this->request->getHeader('authorization');
        $authorization = explode(' ', $authorization);
        $token = $authorization[1];
        if ($token) {
            return $this->getUserByToken($token);
        } else {
            $this->addError(new Error('Токен не был передан'));
            return [];
        }
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private function getUserByToken(string $token): array
    {
        $user = UserTable::getList([
            'filter' => [
                'UF_TOKEN' => $token,
            ],
            'select' => [
                'UF_GENRES',
                'NAME',
                'UF_ROLE',
                'EMAIL',
                'UF_MOVIES',
            ],
        ])->Fetch();
        if (!$user) {
            $this->addError(new Error('Пользователь с таким токеном не найден'));
            return [];
        }

        return [
            'name' => $user['NAME'],
            'email' => $user['EMAIL'],
            'role' => $user['UF_ROLE'],
            'genres' => array_column(Hlblock::getGenresInfoByIds($user['UF_GENRES']), 'UF_NAME'),
            'movies' => Iblock::getMoviesInfoByIdsBriefly($user['UF_MOVIES']),
        ];
    }
}
