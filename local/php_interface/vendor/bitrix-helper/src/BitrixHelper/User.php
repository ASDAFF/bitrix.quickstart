<?php

namespace BitrixHelper;

class User
{

	private $userInfo;

	private function setUserInfo()
	{
		//\BitrixHelper\Utils::Message($this->getUserId());
		$rsUser = $this->BitrixUser->GetByID($this->getUserId());
		//\BitrixHelper\Utils::Message($rsUser);
		$arUser = $rsUser->Fetch();
		//\BitrixHelper\Utils::Message($arUser);
		$arUser['FIO'] = trim($arUser['NAME'] . ' ' . $arUser['SECOND_NAME'] . ' ' . $arUser['LAST_NAME']);
		return $this->userInfo = $arUser;
	}

	public function getUserInfo()
	{
		if (empty($this->userInfo))
			$this->setUserInfo();
		return $this->userInfo;
	}

	private $BitrixUser;

	private function setBitrixUser()
	{
		global $USER;
		$this->BitrixUser = $USER;
	}

	private $userId;

	public function setUserId($userId = false)
	{
		if (!$userId) {
			$userId = $this->BitrixUser->GetID();
		}
		return $this->userId = $userId;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function __construct($userId = false)
	{
		$this->setBitrixUser();
		$this->setUserId($userId);
	}
}