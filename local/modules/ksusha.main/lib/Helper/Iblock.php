<?php
namespace Ksusha\Main\Helper;

use Bitrix\Iblock\Elements\ElementMoviesTable;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

class Iblock
{
    private const MOVIES_IBLOCK_API_CODE = 'films';

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getMoviesInfoByIdsBriefly(array $moviesIds): array
    {
        $className = sprintf('\Bitrix\Iblock\Elements\Element%sTable', self::MOVIES_IBLOCK_API_CODE);
        if (!class_exists($className)) {
            return [];
        }
        $rawMovies = $className::getList([
            'filter' => [
                'ID' => $moviesIds,
            ],
            'select' => [
                '*',
                'YEAR',
                'GENRES',
                'RATING',
            ],
        ])->fetchCollection();
        $movies = [];

        $server = Application::getInstance()->getContext()->getServer();

        foreach ($rawMovies as $rawMovie) {
            $genresCollection = $rawMovie->getGenres();
            $genres = [];
            foreach ($genresCollection as $genre) {
                $genres[] = $genre->getValue();
            }
            $genresInfo = Hlblock::getGenresInfoByCodes($genres);
            $genres = array_column($genresInfo, 'UF_NAME');
            $movies[] = [
                'id' => $rawMovie->getId(),
                'title' => $rawMovie->getName(),
                'year' => $rawMovie->getYear()?->getValue(),
                'image' =>  $server->getHttpHost() . \CFile::GetPath($rawMovie->getPreviewPicture()),
                'genres' => $genres,
                'rating' => round($rawMovie->getRating()?->getValue(), 2),
            ];
        }
        return $movies;
    }
}
