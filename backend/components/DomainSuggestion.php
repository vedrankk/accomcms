<?php

namespace backend\components;

use Yii;
use common\components\Msg;
use backend\models\Domains;
use backend\models\AccomodationDomain;

class DomainSuggestion
{
    private $url;
    private $individualWords;
    private $suggestions;
    private $final;
    
    const DOMAINS = ['com', 'org', 'blog', 'info', 'dev', 'rs'];
    const SYMBOLS_FOR_CHECKING = ['.', '-', '_'];
    const ACCOMODATION_ADDONS = ['hotel', 'motel', 'hostel', 'rooms'];
    
    public function __construct($domain)
    {
        $this->url = $domain;
        $this->sanitizeUrl();
    }
    
    private function sanitizeUrl()
    {
        $this->sanitizeUrlString();
        $this->replaceMultipleSymbols();
        $this->getIndividualWordsFromDomainString($this->removeDomainFromUrl($this->url));
    }
    
    public  function returnSuggestedDomains(){
        $this->addInitalUrlToSuggesstions();
        $this->suggestBasedOnIndividualWords();
        $this->getFinalSuggestions();
        return $this->final;
    }
    
    private function getFinalSuggestions()
    {
        shuffle($this->suggestions);
        $keys = array_rand($this->suggestions, 10);
        foreach($keys as $key)
        {
            $this->final[] = $this->suggestions[$key];
        }
    }
    
    private function checkInitalUrl() : bool
    {
        return preg_match('/([\w-])+([\.])([a-z]+)/', $this->url) ? true : false;
    }
    
    private function suggestBasedOnIndividualWords()
    {
        $this->suggestions = $this->addDomains();
    }
    
    private function addDomains()
    {
        $return = [];
        foreach(self::DOMAINS as $domain)
        {
            $return[] = implode('', $this->individualWords).'.'.$domain;
            if(sizeof($this->individualWords) > 1)
            {
                $return[] = implode('-', $this->individualWords).'.'.$domain;
            }
        }
        if(!preg_match('/(rooms|hotel|motel|hostel)/', $this->individualWords[0]))
        {
            foreach(self::DOMAINS as $domain)
            {
                foreach(self::ACCOMODATION_ADDONS as $addon)
                {
                    $return[] = $addon.implode('', $this->individualWords).'.'.$domain;
                    $return[] = $addon. '-' .implode('', $this->individualWords).'.'.$domain;
                    $return[] = $addon. '-' .implode('-', $this->individualWords).'.'.$domain;
                }
            }
        }
        
        return $return;
    }
    
    private function domainAddons($addon)
    {
        
    }
    
    private function addInitalUrlToSuggesstions()
    {
        if($this->checkInitalUrl() && $this->checkIfDomainAvaliable($this->url))
        {
            $this->final['inital'] = ['domain' => $this->url];
        }
    }
    
    private function sanitizeUrlString()
    {
        $this->url = preg_replace('/(http:\/\/|https:\/\/|www.)/', '', $this->url);
        $this->url = preg_replace('/(_)/', '-', $this->url);
    }
    
    private function validateUrlString() : bool
    {
        return preg_match('/^[\w. -]+$/', $this->url) ? true : false;
    }
    
    private function checkIfDomainAvaliable(string $domain) : bool
    {
        $domain = Domains::find()->where(['domain_url' => $domain])->asArray()->one();
        if(!empty($domain))
        {
            return empty(AccomodationDomain::find()->where(['domain_id' => $domain['domain_id']])->asArray()->one()) ? true : false;
        }
        return true;
    }
    
    private function getIndividualWordsFromDomainString($url)
    {
        preg_match_all("/[\w]+/",$url, $matches, PREG_PATTERN_ORDER);
        $this->individualWords = $matches[0];
    }
    
    private function replaceMultipleSymbols()
    {
        foreach(self::SYMBOLS_FOR_CHECKING as $key => $val)
        {
            $this->url = preg_replace(sprintf('/(\%s)\1+/', $val), $val, $this->url);
        }
    }
    
    private function removeDomainFromUrl(string $url) : string
    {
        foreach(self::DOMAINS as $domain)
        {
            $url = preg_replace(sprintf('/\.%s/', $domain), '', $url);
        }
        return $url;
    }
    
    private function stringHasDomain($string) : bool
    {
        $has = false;
        foreach(self::DOMAINS as $domain)
        {
            if(preg_match(sprintf('/\.%s/', $domain), $string))
            {
                $has = true;
                break;
            }
        }
        return $has;
    }
}

