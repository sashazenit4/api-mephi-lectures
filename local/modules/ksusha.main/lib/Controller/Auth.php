<?php
namespace Ksusha\Main\Controller;

use Bitrix\Main\Engine\Controller as BitrixContoller;
use Bitrix\Main\Error;

class Auth extends BitrixContoller
{
    public function configureActions(): array
    {
        return [
            'login' => [
                'prefilters' => [],
                'postfilters' => [],
            ]
        ];
    }

    public function loginAction(): array
    {
        $authorization = $this->request->getHeader('authorization');
        $authorization = explode(' ', $authorization);
        $authorization = base64_decode($authorization[1]);
        $login = explode(':', $authorization)[0];
        $password = explode(':', $authorization)[1];
        $userName = $login;
        $userPassword = $password;

        $user = new \CUser;
        $authResult = $user->Login($userName, $userPassword);
        if ($authResult === true) {
            $userEntity = \CUser::GetByLogin($userName)->Fetch();
            return [
                'name' => $userEntity['NAME'],
                'token' => $userEntity['UF_TOKEN'],
                'role' => $userEntity['UF_ROLE'],
                'password' => $userPassword,
            ];
        } else {
            $this->addError(new Error('Неправильный логин или пароль'));
            return [];
        }
    }
}
