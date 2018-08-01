<?php

namespace backend\components;

class SmartyControl extends \Smarty
{
    public function setVars($data, $path)
    {
        foreach($data as $key => $val)
        {
            if($key == AllAccomodationData::ACCOMODATION_DATA){continue;}
            $this->assign($key, $val);
        }
        
        $this->setAccomData($data[AllAccomodationData::ACCOMODATION_DATA]);
        $this->assign('path', $path);
    }
    
    private function setAccomData($data)
    {
        foreach($data as $key => $val)
        {
            $this->assign(sprintf('accom_%s', $key), $val);
        }
    }
}
