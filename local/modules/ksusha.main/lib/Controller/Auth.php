<?php
namespace Ksusha\Main\Controller;

use Bitrix\Main\Engine\Controller as BitrixController;
use Bitrix\Main\Error;
use Bitrix\Main\Context;

class Auth extends BitrixController
{
    public function configureActions(): array
    {
        return [
            'login' => [
                'prefilters' => [],
                'postfilters' => [],
            ],
        ];
    }

    public function loginAction(): array
    {
        $request = Context::getCurrent()->getRequest();

        $login = trim($request->getPost('login'));
        $password = $request->getPost('password');

        if (empty($login) || empty($password)) {
            $this->addError(new Error('Логин и пароль обязательны'));
            return [];
        }

        $user = new \CUser;

        $authResult = $user->Login($login, $password, 'N');

        if ($authResult === true) {
            $userEntity = \CUser::GetByLogin($login)->Fetch();

            return [
                'name' => $userEntity['NAME'],
                'token' => $userEntity['UF_TOKEN'],
                'role' => $userEntity['UF_ROLE'],
            ];
        } else {
            $this->addError(new Error('Неправильный логин или пароль'));
            return [];
        }
    }
}
