<?php
namespace Ksusha\Main\Controller;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\Controller as BitrixContoller;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Ksusha\Main\Helper\Iblock;
use Bitrix\Main\Loader;

class Movies extends BitrixContoller
{
    public function configureActions(): array
    {
        return [
            'getMovies' => [
                'prefilters' => [],
                'postfilters' => [],
            ],
            'getMovie' => [
                'prefilters' => [],
                'postfilters' => [],
            ],
            'getMovieReviews' => [
                'prefilters' => [],
                'postfilters' => [],
            ],
        ];
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     * @throws LoaderException
     */
    public function getMoviesAction(): array
    {
        Loader::includeModule('iblock');
        return Iblock::getMoviesInfoByIdsBriefly([]);
    }

    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getMovieAction(): array
    {
        $elementId = $this->request->get('movieId');
        Loader::includeModule('iblock');
        return Iblock::getMovieByIdDetailed($elementId);
    }

    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getMovieReviewsAction(): array
    {
        $elementId = $this->request->get('movieId');
        Loader::includeModule('iblock');
        return Iblock::getMovieReviewsById($elementId);
    }
}
