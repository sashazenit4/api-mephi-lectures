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
        $filter = [];
        if (!empty($moviesIds)) {
            $filter['ID'] = $moviesIds;
        }
        $rawMovies = $className::getList([
            'filter' => $filter,
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

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getMovieByIdDetailed(int $elementId): array
    {
        $className = sprintf('\Bitrix\Iblock\Elements\Element%sTable', self::MOVIES_IBLOCK_API_CODE);
        if (!class_exists($className)) {
            return [];
        }
        $filter = [
            '=ID' => $elementId,
        ];
        $rawMovies = $className::getList([
            'filter' => $filter,
            'select' => [
                '*',
                'YEAR',
                'GENRES',
                'RATING',
                'NAME_EN',
                'DIRECTOR',
                'SCREEN_WRITER',
                'COUNTRY',
                'ACTORS',
            ],
        ])->fetchCollection();

        $server = Application::getInstance()->getContext()->getServer();
        $movie = [];
        foreach ($rawMovies as $rawMovie) {
            $genresCollection = $rawMovie->getGenres();
            $genres = [];
            foreach ($genresCollection as $genre) {
                $genres[] = $genre->getValue();
            }
            $genresInfo = Hlblock::getGenresInfoByCodes($genres);
            $genres = array_column($genresInfo, 'UF_NAME');
            $actorsCollection = $rawMovie->getActors();
            $actors = [];
            foreach ($actorsCollection as $actor) {
                $actors[] = $actor->getValue();
            }
            $movie = [
                'id' => $rawMovie->getId(),
                'title' => [
                    'ru' => $rawMovie->getName(),
                    'en' => $rawMovie->getNameEn()?->getValue(),
                ],
                'rating' => $rawMovie->getRating()?->getValue(),
                'details' => [
                    'year' => $rawMovie->getYear()?->getValue(),
                    'director' => $rawMovie->getDirector()?->getValue(),
                    'screenwriter' => $rawMovie->getScreenWriter()?->getValue(),
                    'genres' => $genres,
                    'country' => $rawMovie->getCountry()?->getValue(),
                    'description' => $rawMovie->getDetailText(),
                ],
                'poster' => $server->getHttpHost() . \CFile::GetPath($rawMovie->getPreviewPicture()),
                'actors' => $actors,
            ];
        }

        return $movie;
    }
}
