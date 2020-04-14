<?php
namespace Helper;

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

global $USER;

/**
 * Представляет механизм для подписки и отписки пользователей.
 */
class FollowService
{
    /**
     * Пользователь, с которым работает сервис.
     *
     * @var CUser
     */
    private $user;

    /**
     * Код пользовательского поля в которой хранятся товары, на которые подписался, авторизированный пользователь.
     *
     * @var string
     */
    private static $UserFieldCode = "UF_FOLLOW_PRODUCTS";

    /**
     * Название переменной куки в которой хранятся товары, на которые подписался, не авторизированный пользователь.
     *
     * @var string
     */
    private static $CookieFieldCode = "FollowProducts";

    /**
     * Инициализирует объект класса FollowService.
     *
     * @param CUser $user
     * @return void
     */
    public function __construct(CUser $user)
    {
        $this->user = $user;
        Loader::includeModule('sale');
        Loader::includeModule('catalog');
    }

    /**
     * Подписывает текущего пользоватльзователя на продукт.
     *
     * @param integer $productId
     * @return void
     */
    public function FollowProduct(int $productId): void
    {
        if($this->user->IsAuthorized()) {
            $this->SaveFollowProductToUserField($productId);
        } else {
            $this->SaveFollowProductToCookie($productId);
        }
    }

    /**
     * Отписывает текущего пользоватльзователя на продукт.
     *
     * @param integer $productId
     * @return void
     */
    public function UnfollowProduct(int $productId): void
    {
        if($this->user->IsAuthorized()) {
            $this->DropFollowProductFromUserField($productId);
        } else {
            $this->DropFollowProductFromCookie($productId);
        }
    }

    /**
     * Сохраняет подписку в поле пользователя.
     *
     * @param integer $productId
     * @return void
     */
    private function SaveFollowProductToUserField(int $productId): void {
        if ($productId <= 0) {
            throw new InvalidArgumentException("Не корректный ид товара.");
        }
        $userId = $this->user->GetID();
        $res = CUser::GetByID($userId);
        $userFields = $res->Fetch();
        
        if (is_array($userFields)) {
            $products = $userFields[self::$UserFieldCode];

            if (!in_array($productId, $products)) {
                $products[] = $productId;
                $this->user->Update($userId, [self::$UserFieldCode => $products]);
            }
        }
    }

    /**
     * Сохраняет подписку в куки.
     *
     * @param integer $productId
     * @return void
     */
    private function SaveFollowProductToCookie(int $productId): void {
        if ($productId <= 0) {
            throw new InvalidArgumentException("Не корректный ид товара.");
        }

        $cookie = Application::getInstance()->getContext()->getRequest()->getCookie(self::$CookieFieldCode);

        $products = unserialize($cookie);
        
        if (!in_array($productId, $products) || !$products) {
            $products[] = $productId;
            $cookie = new Cookie(self::$CookieFieldCode, serialize($products));
            $this->UpdateCookie($cookie);
        }
    }

    
    /**
     * Отписывает от оповещения о наличии товара, с записью в поле пользователя.
     *
     * @param integer $productId
     * @return void
     */
    private function DropFollowProductFromUserField(int $productId): void {
        if ($productId <= 0) {
            throw new InvalidArgumentException("Не корректный ид товара.");
        }
        $userId = $this->user->GetID();
        $res = CUser::GetByID($userId);
        $userFields = $res->Fetch();
        
        if (is_array($userFields)) {
            $products = $userFields[self::$UserFieldCode];

            if (in_array($productId, $products)) {
                $key = array_search($productId, $products);
                unset($products[$key]);
                $this->user->Update($userId, [self::$UserFieldCode => $products]);
            }
        }
    }

    /**
     * Отписывает от оповещения о наличии товара, с записью в куки.
     *
     * @param integer $productId
     * @return void
     */
    private function DropFollowProductFromCookie(int $productId): void {
        if ($productId <= 0) {
            throw new InvalidArgumentException("Не корректный ид товара.");
        }

        $cookie = Application::getInstance()->getContext()->getRequest()->getCookie(self::$CookieFieldCode);
        $products = unserialize($cookie);
        
        if (in_array($productId, $products) || $products) {
            $key = array_search($productId, $products);
            unset($products[$key]);
            $cookie = new Cookie(self::$CookieFieldCode, serialize($products));
            $this->UpdateCookie($cookie);
        }
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
