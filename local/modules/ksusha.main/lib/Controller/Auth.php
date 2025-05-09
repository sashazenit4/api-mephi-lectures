<?php
namespace Ksusha\Main\Controller;

use Bitrix\Main\Engine\Controller as BitrixContoller;

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
        if ($user->Login($userName, $userPassword)){
            $userEntity = \CUser::GetByLogin($userName)->Fetch();
            return [
                'status' => true,
                'name' => $userEntity['NAME'],
                'token' => $userEntity['UF_TOKEN'],
                'role' => $userEntity['UF_ROLE'],
            ];
        } else {
            return [
                'status' => false,
                'error_messages' => [
                    'Неправильный логин или пароль',
                ],
            ];
        }
    }
}
