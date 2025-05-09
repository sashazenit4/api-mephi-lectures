<?php
namespace Ksusha\Main\Controller;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\Controller as BitrixContoller;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Ksusha\Main\Helper\Hlblock;
use Bitrix\Main\Loader;

class Genres extends BitrixContoller
{
    public function configureActions(): array
    {
        return [
            'getGenres' => [
                'prefilters' => [],
                'postfilters' => [],
            ],
        ];
    }

    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getGenresAction(): array
    {
        $sort = [];
        $limit = false;
        if (true === $this->request->get('top')) {
            $sort = [
                'UF_SORT' => 'ASC',
            ];
            $limit = 8;
        }
        $params = [
            'order' => $sort,
            'limit' => $limit
        ];
        Loader::includeModule('highloadblock');
        $rawGenres = Hlblock::getGenresInfoByIds([], $params);
        $genres = [];
        $server = Application::getInstance()->getContext()->getServer();
        foreach ($rawGenres as $genre) {
            $genres[] = [
                'id' => $genre['ID'],
                'name' => $genre['UF_NAME'],
                'icon' => $server->getHttpHost() . \CFile::GetPath($genre['UF_FILE']),
            ];
        }

        return $genres;
    }
}
