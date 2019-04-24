<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

/**
 * Представляет обработчик для команды на добавление товара в избранное.
 */
class AddToFavoritesHandler extends BaseHandler
{
    private $productId;

    public function __construct($request)
    {
        $this->productId = $request->getPost("id");
    }

    public function Execute() {
        require_once __DIR__ . "/FavoritesManager.php";
        $favoritesManager = new FavoritesManager();
        return $favoritesManager->AddToFavorites($this->productId);
    }
}