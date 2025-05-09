<?php
namespace Ksusha\Main\Helper;

use Bitrix\Highloadblock as HL;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

class Hlblock
{
    public const GENRES_CODE_CODE = 'genres';

    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getGenresInfoByIds(array $elementIds): array
    {
        $genresHlBlock = HL\HighloadBlockTable::getList([
            'filter' => [
                'NAME' => self::GENRES_CODE_CODE,
            ],
        ])->fetch();
        $genresEntity = HL\HighloadBlockTable::compileEntity($genresHlBlock);
        $genresEntityClass = $genresEntity->getDataClass();

        return $genresEntityClass::getList([
            'filter' => [
                'ID' => $elementIds,
            ],
        ])->fetchAll();
    }

    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getGenresInfoByCodes(array $elementCodes): array
    {
        $genresHlBlock = HL\HighloadBlockTable::getList([
            'filter' => [
                'NAME' => self::GENRES_CODE_CODE,
            ],
        ])->fetch();
        $genresEntity = HL\HighloadBlockTable::compileEntity($genresHlBlock);
        $genresEntityClass = $genresEntity->getDataClass();

        return $genresEntityClass::getList([
            'filter' => [
                'UF_XML_ID' => $elementCodes,
            ],
        ])->fetchAll();
    }
}
