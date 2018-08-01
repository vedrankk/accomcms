<?php
namespace common\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;
use yii\web\Cookie;
use yii\web\UrlManager as BaseUrlManager;

class UrlManager extends BaseUrlManager
{
    
    private $lang_param = 'lang';
    private $languages = [];
    
    public function init() {
        $this->languages = WebsiteLang::getLangCodes();
        parent::init();
    }
    /**
     * @inheritdoc
     */
    public function parseRequest($request)
    {
        $pathInfo = $request->getPathInfo();

        if (empty($pathInfo))
        {
            Yii::$app->getResponse()->redirect($this->createUrl([]))->send();
            exit();
            return true;
        }
        
        $match = [];
        $lang = '';
        if (preg_match('/^([a-z]{2,3}(\-[a-z]{1,3})?)(\/|$)/', $pathInfo, $match)) {
            $lang = $match[1];
        }
    
        if (WebsiteLang::setCurrentLang($lang) == $lang) {
            //remove lang part from route and continue
            $request->setPathInfo(substr($pathInfo, strlen($lang)));
            Yii::$app->language = $lang;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        return parent::parseRequest($request);
    }
    
    /**
     * @inheritdoc
     */
    public function createUrl($params)
    {
        $lang_code =  '';

        if (isset($params[$this->lang_param])) {
            $lang_code = $params[$this->lang_param];
            unset($params[$this->lang_param]);
        }
        
        if ($lang_code !== false) {
            $lang_code = in_array($lang_code, $this->languages) ? $lang_code : WebsiteLang::getCurrentLangCode();
            $p = isset($params[0]) ? $params[0] : '';
            $p = sprintf('%s/%s', $lang_code, $p);
            if (isset($params['lang_code'])) {
                $p = str_replace($params['lang_code'], '', $p);
                unset($params['lang_code']);
                $p = str_replace('//', '/', $p);
            }
            $params[0] = $p;
        }
  
        return parent::createUrl($params);
    }
}
