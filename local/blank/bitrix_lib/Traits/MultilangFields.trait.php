<?php

namespace Cpeople\Traits;

trait MultilangFields
{
    static $sites;

    public function getLangPropValue($key)
    {
        return $this->getPropValue($this->getCurrentLanguageKey() . '_' . $key);
    }

    public function getLangPropText($key)
    {
        return $this->getPropText($this->getCurrentLanguageKey() . '_' . $key);
    }

    public function getLangPropTextUnesc($key)
    {
        return html_entity_decode($this->getLangPropText($key));
    }

    public function getLangPropDescription($key)
    {
        return $this->getPropDescription($this->getCurrentLanguageKey() . '_' . $key);
    }

    private function fetchSites()
    {
        if (!isset($this->sites))
        {
            $res = \CSite::GetList();
            self::$sites = $res->arResult;
        }
    }

    private function getSiteById($id)
    {
        $this->fetchSites();

        $retval = false;

        foreach (self::$sites as $site)
        {
            if ($site['LID'] == $id)
            {
                $retval = $site;
            }
        }

        return $retval;
    }

    private function getCurrentSite()
    {
        return $this->getSiteById(SITE_ID);
    }

    private function getCurrentLanguageKey()
    {
        $currentSite = $this->getCurrentSite();
        return strtoupper($currentSite['LANGUAGE_ID']);
    }

    public function getLangActiveFromDate($format)
    {
        return lang_date_nominative($format, strtotime($this->data['ACTIVE_FROM']), $this->getCurrentLanguageKey());
    }

    public function getLangActiveToDate($format)
    {
        return lang_date_nominative($format, strtotime($this->data['DATE_ACTIVE_TO']), $this->getCurrentLanguageKey());
    }
}