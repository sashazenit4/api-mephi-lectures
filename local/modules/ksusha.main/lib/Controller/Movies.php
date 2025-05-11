<?php
namespace Ksusha\Main\Controller;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\Controller as BitrixContoller;
use Bitrix\Main\Error;
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
            'getMainPage' => [
                'prefilters' => [],
                'postfilters' => [],
            ],
            'postMovieReview' => [
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

    /**
     * @throws LoaderException
     */
    public function getMainPageAction(): array
    {
        Loader::includeModule('iblock');
        return Iblock::getMainPageBlocks();
    }

    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws SystemException
     */
    public function postMovieReviewAction(): array
    {
        $authorization = $this->request->getHeader('authorization');
        $authorization = explode(' ', $authorization);
        $token = $authorization[1];
        Loader::includeModule('iblock');
        $elementId = $this->request->get('movieId');
        if (!Iblock::postReview($token, $elementId, $this->request->getPostList()->toArray())) {
            $this->addError(new Error('Не удалось добавить отзыв'));
        }

        return [];
    }
}
