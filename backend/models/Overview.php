<?php

namespace backend\models;

use backend\models\Emails;
use backend\models\AccomLanguages;
use backend\models\AccomServices;
use Yii;

class Overview
{
    /*
     * Gets the accomodation data from the accomodation table
     * @param $id - Accomodation ID
     * @param $lang_id - Lang ID from URL
     * If the accomodation has a translation for the current languagge, returns the values from the translated row
     * @return array
     */
    public static function getAccomodationData(int $id, int $lang_id) : array
    {
        $data = Yii::$app->db->createCommand("SELECT a.name, a.description, a.address, b.facebook, b.twitter, b.youtube "
                                                . "FROM accomodation a, accomodation b "
                                                . "WHERE a.parent_id = :parent_id AND a.lang_id = :lang AND a.parent_id = b.accomodation_id"
                                                )
                                                ->bindValues([':parent_id' => $id, ':lang' => $lang_id])->queryAll();
        if(empty($data))
        {
            $data = [Accomodation::find()->where(['accomodation_id' => $id])->asArray()->one()];
        }
        return $data;
    }
    
    /*
     * Get's the languages for this accomodation
     * @param $id - Accomodation Id
     * @param $lang_id - Lang ID from URL
     * If the language has a translation on the curernt language, get's that translation
     * @return array
     */
    public static function getAccomodationLanguages(int $id, int $lang_id) : array
    {
         $accomTrLangs = Yii::$app->db->createCommand(
                                                "SELECT a.*, b.name, b.parent_id "
                                                . "FROM accom_languages a, languages_db b "
                                                . "WHERE a.accomodation_id = :accom_id AND a.lang_id = b.parent_id AND b.lang_id = :lang_id"
                                                )
                                                ->bindValues([':accom_id' => $id, ':lang_id' => $lang_id])->queryAll();
         $accomLangs = AccomLanguages::find()
                                     ->select('accom_languages.*, languages_db.name, languages_db.lg_id')
                                     ->where(['accomodation_id' => $id])
                                     ->leftJoin('languages_db', 'accom_languages.lang_id = languages_db.lg_id')
                                     ->asArray()->all();
         return self::filterLangTranslations($accomTrLangs, $accomLangs);
    }
    
    /*
     * Filters the accomodation languages
     * @param $a Languages that have a translation on the current language
     * @param $b All languages that this accomodation has on english
     * Filters out the matching keys
     * @return array
     */
    private static function filterLangTranslations(array $a, array $b) : array
    {
         foreach($a as $key => $val)
        {
            foreach($b as $key_b => $val_b)
            {
                if($val['parent_id'] == $val_b['lg_id'])
                {
                    unset($b[$key_b]);
                }
            }
        }
        return array_merge($a, $b);
    }
    
    /*
     * Get's the services for the current accomodation
     * @param $id -accomodation ID
     * @param $lang_id - Lang ID from URL
     * If the service has a translation on the current language, get's that translated value
     * @return array
     */
    public static function getAccomodationServices(int $id, int $lang_id) : array
    {
        $accomServices = AccomServices::find()
                                       ->select('accom_services.*, services.name')
                                       ->leftJoin('services', 'accom_services.services_id = services.services_id')
                                       ->where(['accom_services.accomodation_id' => $id])->asArray()->all();
        $allServices = \backend\models\Services::find()->where(['parent_id' => null])->asArray()->all();
        return self::filterServices($accomServices, $allServices);
    }
    
    /*
     * Filters services. 
     * @param $a Services that the current accomodation has
     * @param $b All existing service
     * Filters out the matching keys
     * @return array
     */
    private static function filterServices(array $a, array $b) : array
    {
         foreach($a as $key => $val)
        {
            foreach($b as $key_b => $val_b)
            {
                if($val['services_id'] == $val_b['services_id'])
                {
                    unset($b[$key_b]);
                    $a[$key]['checked'] = 1;
                }
            }
        }
        return array_merge($a, $b);
    }
    
    /*
     * Gets the accomodation model. If there is a lang ID in the URL tries to find the translation first.
     * If the accomodation does not have a translation for the current language, gets the original row.
     * @param $data array
     * @param $data['lang'] - Lang ID from URL
     * @param $data ['id'] - Accomodation ID
     * @return array
     */
    public function accomDataInfoFindModel(array $data)
    {
        if(isset($data['lang']) && $data['lang'] != 1)
        {
            $model = Accomodation::find()->where(['parent_id' => $data['id'], 'lang_id' => $data['lang']])->one();
        }
            
        if(empty($model))
        {
            $model = Accomodation::findOne(['accomodation_id' => $data['id']]);
        }
        return $model;
    }
    
    
    
    /*
     * Filters the translation values for the emails(TEST)
     */
    public function filterEmailTranslation(array $a, array $b) : array
    {
        foreach($a as $key => $val)
        {
            foreach($b as $key_b => $val_b)
            {
                if($val['parent_id'] == $val_b['emails_id'])
                {
                    unset($b[$key_b]);
                }
            }
        }
        return array_merge($a, $b);
    }
    
    
    
    /*
     * Loops through the data sent from Ajax request for .
     * If the value is empty or equeal to the value currently in the DB nothing happens
     * Otherwise, the value in the model is changed and it sets the $change to 1 to indicate there is an edited value
     */
    public function checkAccomodationData(array $data, $model) : array
    {
        $change = 0;
        foreach($data as $key => $val)
        {
            if($key == 'accom_id' || $key == 'lang_id'){continue;}
            if(!empty($val) && $val != $model->{$key})
            {
                $model->{$key} = $val;
                $change = 1;
            }
        }
        return [$model, $change];
    }
    
    /*
     * Detects which social network is supposed to be deleted
     * @param $field - social network name
     * @param @model - Accomodation model
     * @return object
     */
    public function detectAccomodationSocialDelete(string $field, $model)
    {
        switch($field)
        {
            case 'facebook':
                $model->facebook = '';
            break;
        
            case 'twitter':
                $model->twitter = '';
            break;
        
            case 'youtube':
                $model->youtube = '';
            break;
        }
        return $model;
    }
    
    /*
     * Tries to save the new default accomodation language in the case we are deleting the current default language
     * @param $defaultLangModel - New default accomodation language
     * @param $model - Language that is to be deleted
     * @return string;
     */
    public function trySaveNewDefaultLang($defaultLangModel, $model) : string
    {
        if($defaultLangModel->save())
        {
            $model->delete();
            return Yii::t('model/overview', 'Language Deleted.');
        }
        else{
            return Yii::t('model/overview', 'Something went wrong!');
        }
    }
    
    /*
     * Tries to edit the accomodation data(address, name, description)
     * @param $data['address'] / $data['name'] / $data['description'] - Data values
     * @return int
     */
    public function accomDataEdit(array $data, $model) : array
    {
        if(isset($data['address']))
        {
                
            $model->address = $data['address'];
            return self::trySaveModel($model, 'Succesefully edited!');
        }
            
        if(isset($data['name']))
        {
             $model->name = $data['name'];
             return self::trySaveModel($model, 'Succesefully edited!');
        }
            
        if(isset($data['description']))
        {
             $model->description = $data['description'];
             return self::trySaveModel($model, 'Succesefully edited!');
        }
        return ['code' => 222, 'msg' => Yii::t('model/overview', 'Something went wrong!')];
    }
    
    /*
     * Creates a new email for the current acomodation
     * @param $data['title'] - Email Title
     * @param $data['email'] - Email
     * @param $data['accom_id'] - Current accomodation ID
     * @return array
     */
    public function accomNewEmail(array $data) : array
    {
        $model = new Emails();
        $model->title = $data['title'];
        $model->email = $data['email'];
        $model->accomodation_id = $data['accom_id'];
        $msg =  self::trySaveModel($model, 'Succesefully added!', 'email_invalid');
        $insertId = $model->primaryKey;
        return [$msg, $insertId];
    }
    
    /*
     * Tries to edit the accomodation social data(facebook, youtube, twitter)
     * @param $data['facebook'] / $data['youtube'] / $data['twitter'] - Social data values
     * @return int
     */
    public function accomDataSocialEdit($model, array $data) : array
    {
         if(isset($data['facebook']))
         {
             $model->facebook = $data['facebook'];
             return self::trySaveModel($model, 'Succesefully added!');
         }
            
         if(isset($data['youtube']))
         {
             $model->youtube = $data['youtube'];
             return self::trySaveModel($model, 'Succesefully added!');
         }
            
         if(isset($data['twitter']))
         {
             $model->twitter = $data['twitter'];
             return self::trySaveModel($model, 'Succesefully added!');
         }
         return ['code' => 222, 'msg' => Yii::t('model/overview', 'Something went wrong!')];
    }
    
    /*
     * Tries to remove a value for the social network of the current accomodation
     * @param $data['facebook'] / $data['youtube'] / $data['twitter'] int
     * @return int
     */
    public function accomDataSocialDelete($model, array $data) : array
    {
         if(isset($data['facebook']))
        {
            $model->facebook = '';
            return self::trySaveModel($model, 'Successfully deleted!');
        }
            
        if(isset($data['youtube']))
        {
            $model->youtube = '';
            return self::trySaveModel($model, 'Successfully deleted!');
        }
            
        if(isset($data['twitter']))
        {
            $model->twitter = '';
            return self::trySaveModel($model, 'Successfully deleted!');
        }
        return ['code' => 222, 'msg' => Yii::t('model/overview', 'Something went wrong!')];
    }
    
    public function accomEmailDelete($model) : array
    {
        if(!empty($model))
        {
            return self::tryDeleteModel($model, 'Successfully deleted!');
        }
        return ['code' => 222, 'msg' => Yii::t('model/overview', 'Something went wrong!')];
    }
    
    public function trySaveModel($model, $success_message, $error_message = '')
    {
        $error_message = $error_message != '' ? $error_message : "Something went wrong!";
        return $model->save() ? ['code' => 111, 'msg' => Yii::t('model/overview', $success_message)] : ['code' => 222, 'msg' => Yii::t('model/overview', $error_message)];
    }
    
    public function tryDeleteModel($model, $success_message)
    {
        return $model->delete() ? ['code' => 111, 'msg' => Yii::t('model/overview', $success_message)] : ['code' => 222, 'msg' => Yii::t('model/overview', 'Something went wrong!')];
    }
    
    /*
     * Deletes a service for the current accomodation
     * @param $service - Service Model
     * @return string
     */
    public static function tryDeleteService($service)
    {
        if($service->delete())
        {
            return Yii::t('model/overview', 'Successefully deleted!');
        }
        else{
            return Yii::t('model/overview', 'Something went wrong!');
        }
        
    }
    
    /*
     * Tries to add a new Service for the current accomodation
     * @param $data['id'] - service ID
     * @param $data['accom_id'] - Accomodation ID
     * @return string
     */
    public static function tryAddService($data)
    {
        $model = new AccomServices();
        $model->services_id = $data['id'];
        $model->accomodation_id = $data['accom_id'];
        if($model->save())
        {
            return Yii::t('model/overview', 'Succesefully added!');
        }
        else{
            return Yii::t('model/overview', 'Something went wrong!');
        }
    }
}