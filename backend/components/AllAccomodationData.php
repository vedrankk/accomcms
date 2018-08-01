<?php

namespace backend\components;

use backend\models\Accomodation;
use backend\models\AccomServices;
use backend\models\Galleries;
use backend\models\Emails;
use backend\models\AccomodationNews;
use backend\models\GalleryImages;
use backend\models\AccomLanguages;
use backend\models\Services;
use backend\models\LanguagesDb;

class AllAccomodationData extends CashControl
{
    private $accomodation_id;
    
    const ACCOMODATION_DATA = 'accomodation';
    const SERVICES_DATA = 'services';
    const GALLERIES_DATA = 'galleries';
    const EMAILS_DATA = 'emails';
    const NEWS_DATA = 'news';
    const LANG_DATA = 'langs';
    
    public function __construct($accomodation_id)
    {
        $this->accomodation_id = $accomodation_id;
        $this->setCache($this->setCacheString());
    }
    
    public function getAllData() : array
    {
        $basic_data = $this->getBasicData();
        $services_data = $this->getServicesData();
        $galleries = $this->getGalleriesData($basic_data['name']);
        $emails_data = $this->getEmailsData();
        $news_data = $this->getNewsData();
        $lang_data = $this->getLanguagesData();
        return [
            self::ACCOMODATION_DATA => $basic_data,
            self::SERVICES_DATA => $services_data,
            self::GALLERIES_DATA => $galleries,
            self::EMAILS_DATA => $emails_data,
            self::NEWS_DATA => $news_data,
            self::LANG_DATA => $lang_data
            ];
    }
    
    /*
     * Sets the crypted string for the setCashe function
     * @param int $id -> Accomodation ID
     */
    private function setCacheString() : string
    {
        return md5(static::CASH_NAME_CRYPT.$this->accomodation_id.static::CASH_NAME_CRYPT);
    }
    
    /*
     * Sets the string that the cash data will be saved with
     * @param string $key -> The key for the crypt
     */
    private function casheCryptString(string $key) : string
    {
        return md5(sprintf('%s_%s', md5(static::CASH_CRYPT.$this->accomodation_id.static::CASH_CRYPT), $key));
    }
    
    /*
     * Retrieves the data from the cash based on the key
     * @param string $key -> The key for the data
     */
    private function retrieveData(string $key)
    {
        return $this->retrieve($this->casheCryptString($key));
    }
    
    /*
     * Stores the data in the cash
     * @param string $key -> The key for the crypt
     * @param array $data -> The data to be saved
     */
    private function storeData(string $key, array $data)
    {
        $this->store($this->casheCryptString($key), $data);
    }
    
    /*
     * Gets the basic accomodation data from the cashe or database
     */
    private function getBasicData() : array
    {
        $data = $this->retrieveData(static::BASIC_SAVE_STRING);
        if($data === null)
        {
            $data = Accomodation::find()->where(['accomodation_id' => $this->accomodation_id])->asArray()->one();
            $this->storeData(static::BASIC_SAVE_STRING, $data);
        }
        return $data;
    }
    
    /*
     * Gets services data from the cashe or database and procceses it
     */
    private function getServicesData() : array
    {
        $data = $this->retrieveData(static::SERVICES_SAVE_STRING);
        if($data === null)
        {
            $services =  AccomServices::find()->where(['accomodation_id' => $this->accomodation_id])->asArray()->all();
            $data = $this->proccessServiceData($services);
            $this->storeData(static::SERVICES_SAVE_STRING, $data);
        }
        return $data;
    }
    
    /*
     * Find the right service names for the ID-s
     * @param array $services_data -> Array of values, services for this accomodation
     */
    private function proccessServiceData(array $services_data) : array
    {
        $services = [];
        foreach($services_data as $key => $val)
        {
            $services[] = Services::find()->where(['services_id' => $val['services_id']])->asArray()->one();
        }
        return $services;
    }
    
    /*
     * Gets gallery data from the cashe or database
     */
    private function getGalleriesData(string $accom_name) : array
    {
        $data = $this->retrieveData(static::GALLERIES_SAVE_STRING);
        if($data === null)
        {
            $galleries =  Galleries::find()->where(['accomodation_id' => $this->accomodation_id])->asArray()->all();
            $data =  $this->proccessGalleriesData($galleries, $accom_name);
            $this->storeData(static::GALLERIES_SAVE_STRING, $data);
        }
        return $data;
    }
    
    /*
     * Proccesses galllery data, gets all the images for every gallery
     * @param array $gallery_data -> Basic gallery data
     * @param string $accom_name -> Accomodation name, needed for crypt
     */
    private function proccessGalleriesData(array $gallery_data, string $accom_name) : array
    {
        foreach($gallery_data as $key => $val)
        {
            $imgPath = Galleries::imageUrl($accom_name, $val['gallery_name']);
            $images = GalleryImages::find()->where(['gallery_id' => $val['gallery_id']])->asArray()->all();
            foreach($images as $i_key => $i_val)
            {
                $i_val['image_name'] = $imgPath.'\\'.$i_val['image_name'];
                $images[$i_key] = $i_val;
            }
            $gallery_data[$key]['images'] = $images;
        }
        return $gallery_data;
    }
    
    /*
     * Gets the bemail data from the cashe or database
     */
    private function getEmailsData() : array
    {
        $data = $this->retrieveData(static::EMAILS_SAVE_STRING);
        if($data === null)
        {
            $data = Emails::find()->where(['accomodation_id' => $this->accomodation_id])->asArray()->all();
            $this->storeData(static::EMAILS_SAVE_STRING, $data);
        }
        return $data;
    }
    
    /*
     * Gets the news data from the cashe or database
     */
    private function getNewsData() : array
    {
        $data = $this->retrieveData(static::NEWS_SAVE_STRING);
        if($data === null)
        {
            $data = AccomodationNews::find()->where(['accomodation_id' => $this->accomodation_id])->asArray()->all();
            $this->storeData(static::NEWS_SAVE_STRING, $data);
        }
        return $data;
    }
    
    /*
     * Gets the languages data from the cashe or database
     */
    private function getLanguagesData() : array
    {
        $data = $this->retrieveData(static::LANGS_SAVE_STRING);
        if($data === null)
        {
            $langs =  AccomLanguages::find()->where(['accomodation_id' => $this->accomodation_id])->asArray()->all();
            $data =  $this->proccessLanguageData($langs);
            $this->storeData(static::LANGS_SAVE_STRING, $data);
        }
        return $data;
    }
    
    /*
     * Gets the language values based on the array
     * @param array $lang_data -> Accomodation Languages
     */
    private function proccessLanguageData(array $lang_data) : array
    {
        $langs = [];
        foreach($lang_data as $key => $val)
        {
            $langs[] = LanguagesDb::find()->where(['lg_id' => $val['lang_id']])->asArray()->one();
        }
        return $langs;
    }
}