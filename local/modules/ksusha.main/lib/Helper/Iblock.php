<?php

namespace Ksusha\Main\Helper;

use Bitrix\Iblock\Elements\ElementMoviesTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;

class Iblock
{
    private const MOVIES_IBLOCK_API_CODE = 'films';
    private const REVIEWS_IBLOCK_API_CODE = 'reviews';
    private const MAIN_PAGE_IBLOCK_API_CODE = 'mainPage';

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
                'ID',
                'NAME',
                'PREVIEW_PICTURE',
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
                'image' => $server->getHttpHost() . \CFile::GetPath($rawMovie->getPreviewPicture()),
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
                'NAME',
                'ID',
                'PREVIEW_PICTURE',
                'DETAIL_TEXT',
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

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getMovieReviewsById(int $elementId): array
    {
        $className = sprintf('\Bitrix\Iblock\Elements\Element%sTable', self::REVIEWS_IBLOCK_API_CODE);
        if (!class_exists($className)) {
            return [];
        }
        $filter = [
            'FILM.VALUE' => $elementId,
        ];
        $rawReviews = $className::getList([
            'filter' => $filter,
            'select' => [
                'ID',
                'ACTIVE_FROM',
                'NAME',
                'PROS',
                'CONS',
                'QUOTE',
                'RATE',
                'AUTHOR',
                'DETAIL_TEXT',
            ],
        ])->fetchCollection();

        $reviews = [];
        foreach ($rawReviews as $rawReview) {
            $proses = [];
            foreach ($rawReview->getPros() as $pros) {
                $proses[] = $pros?->getValue();
            }
            $conses = [];
            foreach ($rawReview->getCons() as $cons) {
                $conses[] = $cons?->getValue();
            }
            $authorInfo = UserTable::getList([
                'filter' => [
                    '=ID' => $rawReview->getAuthor()?->getValue() ?? 0,
                ],
                'select' => [
                    'NAME',
                    'LAST_NAME',
                    'SECOND_NAME',
                    'UF_ROLE',
                ],
            ])->fetch();
            $reviews[] = [
                'id' => $rawReview->getId(),
                'title' => $rawReview->getName(),
                'rating' => $rawReview->getRate()?->getValue(),
                'content' => $rawReview->getDetailText(),
                'pros' => $proses,
                'cons' => $conses,
                'quote' => $rawReview->getQuote()?->getValue(),
                'date' => $rawReview->getActiveFrom()->format('Y-m-d'),
                'author' => trim(sprintf('%s %s %s', $authorInfo['LAST_NAME'], $authorInfo['NAME'], $authorInfo['SECOND_NAME'])),
                'authorRole' => $authorInfo['UF_ROLE'],
            ];
        }

        return $reviews;
    }

    public static function getMainPageBlocks(): array
    {
        $className = sprintf('\Bitrix\Iblock\Elements\Element%sTable', self::MAIN_PAGE_IBLOCK_API_CODE);
        if (!class_exists($className)) {
            return [];
        }
        $rawMainPageBlocks = $className::getList([
            'select' => [
                'ID',
                'NAME',
                'ACTIVE_FROM',
                'PREVIEW_TEXT',
                'CATEGORY',
                'PREVIEW_PICTURE',
                'BUTTON_TEXT',
            ],
            'order' => [
                'ID' => 'ASC',
            ],
        ])->fetchCollection();
        $mainPageBlocks = [];
        $server = Application::getInstance()->getContext()->getServer();
        foreach ($rawMainPageBlocks as $rawMainPageBlock) {
            $fileInfo = \CFile::GetByID($rawMainPageBlock->getPreviewPicture())->Fetch();
            $fileDescription = $fileInfo['DESCRIPTION'];
            $filePath = \CFile::GetPath($rawMainPageBlock->getPreviewPicture());
            $mainPageBlocks[] = [
                'id' => $rawMainPageBlock->getId(),
                'title' => $rawMainPageBlock->getName(),
                'description' => $rawMainPageBlock->getPreviewText(),
                'actionText' => $rawMainPageBlock->getButtonText()?->getValue(),
                'imageUrl' => $server->getHttpHost() . $filePath,
                'altText' => $fileDescription,
                'publishDate' => $rawMainPageBlock->getActiveFrom()?->format('Y-m-d'),
            ];
        }

        return $mainPageBlocks;
    }

    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function postReview(string $token, int $filmId, array $queryParams): bool
    {
        $userId = 0;
        if ($token !== '') {
            $userId = (int)UserTable::getList([
                'filter' => [
                    'UF_TOKEN' => $token
                ],
                'select' => [
                    'ID'
                ],
            ])->fetch()['ID'];
        }
        if ($userId <= 0) {
            return false;
        }

        $iblockId = IblockTable::getList([
            'filter' => [
                'CODE' => self::REVIEWS_IBLOCK_API_CODE,
            ],
            'cache' => [
                'ttl' => 360000,
            ],
        ])->fetch()['ID'];

        $fields = [
            'IBLOCK_ID' => $iblockId,
            'NAME' => $queryParams['title'],
            'DETAIL_TEXT',
            'PREVIEW_TEXT' => $queryParams['content'],
            'ACTIVE_FROM' => new DateTime(),
            'PROPERTY_VALUES' => [
                'PROS' => $queryParams['pros'],
                'CONS' => $queryParams['cons'],
                'QUOTE' => $queryParams['quote'],
                'RATE' => $queryParams['rating'],
                'AUTHOR' => $userId,
                'FILM' => $filmId,
            ],
        ];
        $model = new \CIBlockElement;
        $model->Add($fields);
        return true;
    }
}
