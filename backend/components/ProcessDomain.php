<?php

namespace backend\components;

use Yii;

class ProcessDomain
{
    private $domain;
    
    public function __construct($domain_name)
    {
        $this->domain = $domain_name;
    }
    
    public function getDomainName()
    {
        return $this->domain;
    }
}