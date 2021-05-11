<?php
namespace Helper\Favorite;

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

class FavoritesManager
{
    public function __construct()
    {
        Loader::includeModule('sale');
        Loader::includeModule('catalog');
    }

    public function AddToFavorites($productId) {
        global $USER;
        
        if ($productId) {
            if($USER->IsAuthorized()) {
                return $this->AddFavoritesToUserField($productId);
            } else {
                return $this->AddFavoritesToCookie($productId);
            }
        }
    }
    
    public function RemoveFromFavorites($productId) {
        global $USER;
        
        if ($productId) {
            if ($USER->IsAuthorized()) {
                return $this->RemoveFavoritesFromUserField($productId, $USER);
            } else {
                return $this->RemoveFavoritesFromCookie($productId);
            }
        }
    }

    /**
     * @param $productId
     * @return bool
     */
    private function AddFavoritesToCookie($productId)
    {
        $cookie = Application::getInstance()->getContext()->getRequest()->getCookie("Favorites");

        $arElements = unserialize($cookie);
        
        if (!in_array($productId, $arElements) || !$arElements) {
            $arElements[] = $productId;
            $cookie = new Cookie("Favorites", serialize($arElements));
            $this->UpdateCookie($cookie);
        }
        return true;
    }

    /**
     * @param $productId
     * @return bool
     */
    private function AddFavoritesToUserField($productId)
    {
        global $USER;
        $userId = $USER->GetID();
        $arUser = $this->GetUserData($userId);

        if (is_array($arUser)) {
            $arElements = $arUser['UF_FAVORITES'];
            
            if (in_array($productId, $arElements) == false) {
                $arElements[] = (int)$productId;
                $USER->Update($userId, ["UF_FAVORITES" => $arElements]);

            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * @param $productId
     * @return bool
     */
    private function RemoveFavoritesFromCookie($productId)
    {
        $cookie = Application::getInstance()->getContext()->getRequest()->getCookie("Favorites");

        $arElements = unserialize($cookie);
        
        if (in_array($productId, $arElements) || !$arElements) {
            $key = array_search($productId, $arElements);
            unset($arElements[$key]);
            $cookie = new Cookie("Favorites", serialize($arElements));
            $this->UpdateCookie($cookie);
        }
        return true;
    }

    /**
     * @param $productId
     * @return bool
     */
    private function RemoveFavoritesFromUserField($productId)
    {
        global $USER;
        $userId = $USER->GetID();
        $arUser = $this->GetUserData($userId);

        if (is_array($arUser)) {
            $arElements = $arUser['UF_FAVORITES'];

            if (in_array($productId, $arElements)) {
                $key = array_search($productId, $arElements);
                unset($arElements[$key]);
                $USER->Update($userId, ["UF_FAVORITES" => $arElements]);
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * @param $userId
     * @return mixed
     */
    private function GetUserData($userId)
    {
        $res = \CUser::GetByID($userId);
        $arUser = $res->Fetch();
        return $arUser;
    }

    /**
     * Добаляет куки из параметра и обноволяет контекст.
     *
     * @param Cookie $cookie
     * @return void
     */
    private function UpdateCookie(Cookie $cookie) : void {
        $application = Application::getInstance();
        $context = $application->getContext();

        $cookie->setDomain($context->getServer()->getHttpHost());
        $cookie->setHttpOnly(false);

        $context->getResponse()->addCookie($cookie);

        $context->getResponse()->flush("");
    }
}