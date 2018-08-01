<?php

namespace backend\components;

use common\components\Msg;
use Yii;
use backend\models\Domains;
use backend\models\Templates;
use backend\models\Services;
use backend\models\AccomServices;
use backend\models\Accomodation;
use backend\models\Emails;

class Creation
{
    const ACCOM_ID = 'accom_id';
    
    /*
     * If there is an Accomodation id in the memory, gets the accomodation, if not creates a new model
     */
    public static function newModel()
    {
        $accomId = self::getAccomIdFromMemory();
        if($accomId !== 0)
        {
            $new = Accomodation::find()->where(['accomodation_id' => $accomId])->one();
            return !empty($new) ? $new : new Accomodation();
        }
        else{
            return new Accomodation();
        }
    }
    
    /*
     * Sets the new model with the default data and saves it
     */
    public static function setNewAccomodationModel($model) : bool
    {
        $model->lang_id = 1;
        $model->user_id = Yii::$app->user->identity->user_id;
        $model->parent_id = null;
        $model->published = 0;
        return $model->save() ? self::newModelSuccess($model->accomodation_id) : self::returnError('There was an error saving');
    }
    
    /*
     * Sets the flash message
     */
    private static function returnError(string $msg) : bool
    {
        Msg::error($msg);
        return false;
    }
    
    /*
     * Sets the cookie and session for the current accomodation
     */
    private static function newModelSuccess(int $id) : bool
    {
        Yii::$app->session->set(self::ACCOM_ID, $id);
        self::setNewAccomIdCookie($id);
        return true;
    }
    
    /*
     * Creates a new cookie
     */
    private static function setNewAccomIdCookie(int $id)
    {
        $cookies = Yii::$app->response->cookies;
        
        $cookies->add(new \yii\web\Cookie([
            'name' => self::ACCOM_ID,
            'value' => $id,
            'expire' => time()+86400*365
        ]));
    }
    
    /*
     * Finishes the creation and removes the id from memory
     */
    public static function finalize() : bool
    {
        $accomId = self::getAccomIdFromMemory();
        $accomodation = \backend\models\Accomodation::find()->where(['accomodation_id' => $accomId])->one();
        if($accomodation->save())
        {
            $cookies = Yii::$app->response->cookies;
            $cookies->remove(self::ACCOM_ID);
            Yii::$app->session->remove(self::ACCOM_ID);
            return true;
        }
        return false;
    }
    
    /*
     * Check if there is a domain set for the current accomodation
     */
    public static function validateDomainExists() : bool
    {
        if(Yii::$app->session->has(self::ACCOM_ID))
        {
            return empty(\backend\models\AccomodationDomain::find()->where(['accomodation_id' => Yii::$app->session->get(self::ACCOM_ID)])->one()) ? true : false;
        }
        elseif(Yii::$app->request->cookies->has(self::ACCOM_ID))
        {
            return empty(\backend\models\AccomodationDomain::find()->where(['accomodation_id' => Yii::$app->request->cookies->get(self::ACCOM_ID)->value])->one()) ? true : false;
        }
        else{
            return true;
        }
    }
    
    /*
     * Sets a new accomodation domain
     */
    public static function setNewAccomodationDomain(string $domain) : array
    {
        $accom_id = self::getAccomIdFromMemory();
        if($accom_id !== 0)
        {
            $newDomain = new Domains();
            $newDomain->domain_url = $domain;
            if($newDomain->save())
            {
                $newAccomDomain = new \backend\models\AccomodationDomain();
                $newAccomDomain->accomodation_id = $accom_id;
                $newAccomDomain->domain_id = $newDomain->domain_id;
                if($newAccomDomain->save())
                {
                    return ['status' => true];
                }
                else{
                    $newDomain->delete();
                    return ['status' => false, 'msg' => 'Error assigning domain. Please try again.'];
                }
            }
            else{
                return ['status' => false, 'msg' => 'Error saving domain. Please try again.'];
            }
        }
        else{
            return ['status' => false, 'msg' => ''];
        }
    }
    
    public static function setNewEmailModel(int $accom_id, string $email, string $title) : array
    {
        $newEmail = new Emails();
        $newEmail->accomodation_id = $accom_id;
        $newEmail->email = $email;
        $newEmail->title = $title;
        $newEmail->lang_id = 1;
        $newEmail->parent_id = null;
        if($newEmail->save())
        {
            return ['status' => true];
        }
        else{
             return ['status' => false, 'msg' => 'Something went wrong. Please try again.'];
        }
    }
    
    /*
     * Gets the saved id from session or cookie
     */
    public static function getAccomIdFromMemory() : int
    {
        if(Yii::$app->session->has(self::ACCOM_ID))
        {
            return Yii::$app->session->get(self::ACCOM_ID);
        }
        elseif(Yii::$app->request->cookies->has(self::ACCOM_ID))
        {
            $id = Yii::$app->request->cookies->get(self::ACCOM_ID)->value;
            Yii::$app->session->set(self::ACCOM_ID, $id);
            return $id;
        }
        else{
            return 0;
        }
    }
    
    /*
     * Gets the details for the current template
     * Creates the path for the image to be shown
     */
    public static function getTemplateDetails($id) : array
    {
        $template_data = Templates::find()->where(['template_id' => $id])->asArray()->one();
        if(!empty($template_data)){
            $image_path = Templates::returnTemplateImagePath($template_data['name'], Templates::explodeVersion($template_data['version'])[0]);
            $image_name = file_exists($template_data['path'].'screenshot.jpg') ? 'screenshot.jpg' : 'screenshot.png';
            $template_data['preview'] = $image_path.$image_name;
        }
        
        if(file_exists($template_data['path'].'preview/'))
        {
            
            $di = new \RecursiveDirectoryIterator($template_data['path'].'preview/', \FilesystemIterator::SKIP_DOTS);
            $ri = new \RecursiveIteratorIterator($di, \RecursiveIteratorIterator::CHILD_FIRST);
            
            foreach($ri as $file)
            {
                if(!$file->isDir())
                {
                    $template_data['image_data'][] = $image_path.'preview/'. $file->getFilename();
                }
            }
        }
        
        return $template_data;
    }
    
    /*
     * Deletes the accomodation with the id from memory, and removes the id from session nad cookie
     */
    public static function reset() 
    {
        $accomodation = \backend\models\Accomodation::find()->where(['accomodation_id' => self::getAccomIdFromMemory()])->one();
        if(!empty($accomodation))
        {
            \backend\models\AccomodationDomain::deleteAll(['accomodation_id' => $accomodation['accomodation_id']]);
            \backend\models\AccomodationTemplate::deleteAll(['accomodation_id' => $accomodation['accomodation_id']]);
            AccomServices::deleteAll(['accomodation_id' => $accomodation['accomodation_id']]);
            \backend\models\AccomLanguages::deleteAll(['accomodation_id' => $accomodation['accomodation_id']]);
            Emails::deleteAll(['accomodation_id' => $accomodation['accomodation_id']]);
            
            $accomodation->delete();
            $cookies = Yii::$app->response->cookies;
            $cookies->remove(self::ACCOM_ID);
            Yii::$app->session->remove(self::ACCOM_ID);
        }
    }
    
    /*
     * Sets the template for the current accomodation
     */
    public static function setTemplate(int $accom_id, int $template_id) : array
    {
        if(!self::accomodationHasTemplate($accom_id))
        {
            $accom_template = new \backend\models\AccomodationTemplate();
            $accom_template->accomodation_id = $accom_id;
            $accom_template->template_id = $template_id;
            if($accom_template->save())
            {
                return ['status' => true];
            }
            else{
                return ['status' => false, 'msg' => 'Error saving. Please try again!'];
            }
        }
        else{
            return ['status' => false, 'msg' => 'How did you even get here'];
        }
    }
    
    /*
     * Checks if the accomodation has a template set
     */
    public static function accomodationHasTemplate(int $accom_id) : bool
    {
        return !empty(\backend\models\AccomodationTemplate::find()->where(['accomodation_id' => $accom_id])->asArray()->one()) ? true : false;
    }
    
    /*
     * Gets the data for all the templates avaliable
     */
    public static function getTemplateData() : array
    {
        $starterTemplates = Templates::find()->asArray()->all();
        foreach($starterTemplates as $key => $val)
        {
            $image_path = Templates::returnTemplateImagePath($val['name'], Templates::explodeVersion($val['version'])[0]);
            $image_name = file_exists($val['path'].'screenshot.jpg') ? 'screenshot.jpg' : 'screenshot.png';
            $starterTemplates[$key]['image'] = $image_path.$image_name;
        }
        return $starterTemplates;
    }
    
    /*
     * Gets all the services that are avaliable for this accomodation. If there are some set, those are excluded
     */
    public static function getAvaliableServices() : array
    {
        $accom_id = self::getAccomIdFromMemory();
        $allServices = Services::find()->where('parent_id IS NULL')->asArray()->all();
        $accomServices = AccomServices::find()->where(['accomodation_id' => $accom_id])->asArray()->all();
        
        foreach($allServices as $key => $val)
        {
            foreach($accomServices as $a_key => $a_val)
            {
                if($val['services_id'] == $a_val['services_id'])
                {
                    unset($allServices[$key]);
                }
            }
        }
        return \yii\helpers\ArrayHelper::map($allServices, 'services_id', 'name');
    }
    
    /*
     * Function for multiple addition of data to the model. Used for services and languages
     */
    public static function addNewMultiple(array $data, $class, string $property, string $model_id, array $options = []) : bool
    {
        $accom_id = Creation::getAccomIdFromMemory();
        $successefulEntry = [];
        $success = true;
        $firstDone = false;
        foreach($data as $key => $val)
        {
            $model = new $class();
            $model->accomodation_id = $accom_id;
            $model->$property = $val;
            
            if(!empty($options))
            {
                $model = self::checkOptionsForMultipleAdd($model, $options, $firstDone);
                $firstDone = true;
            }
            
            if($model->save())
            {
                $successefulEntry[] = $model->$model_id;
            }
            else{
                $success = false;
                break;
            }
        }
        
        if(!$success)
        {
            return self::multipleAddFailed($successefulEntry, $class, $model_id);
        }
        return true;
    }
    
    /*
     * Checks if there are some custom options to be set in the model
     */
    private static function checkOptionsForMultipleAdd($model, array $options, bool $firstDone)
    {
        if(isset($options['first']) && !$firstDone)
        {
            foreach($options['first'] as $key => $val)
            {
                $model->$key = $val;
            }
        }
        if(isset($options['all']))
        {
            foreach($options['all'] as $key => $val)
            {
                $model->$key = $val;
            }
        }
        return $model;
    }
    
    /*
     * If some fo the saving failed, rolls back the changes
     */
    private static function multipleAddFailed(array $successefulEntry, $class, string $model_id) : bool
    {
        foreach($successefulEntry as $key => $val)
        {
            $model_for_delete = $class::find()->where([$model_id => $val])->one();
            $model_for_delete->delete();
        }
        return false;
    }
    
    /*
     * Gets the domain for the accomodation
     */
    public static function getDomain(int $accomId) : array
    {
        $domainId = \backend\models\AccomodationDomain::find()->where(['accomodation_id' => $accomId])->asArray()->one()['domain_id'];
        return Domains::find()->where(['domain_id' => $domainId])->asArray()->one();
    }
    
    /*
     * Gets the services and their names
     */
    public static function getServices(int $accomId) : array
    {
        $accomServices = AccomServices::find()->where(['accomodation_id' => $accomId])->asArray()->all();
        $services = [];
        foreach($accomServices as $key => $val)
        {
            $services[] = Services::find()->where(['services_id' => $val['services_id']])->asArray()->one();
        }
        return $services;
    }
    
    /*
     * Gets the languages set for the accomodation
     */
    public static function getLanguages(int $accomId) : array
    {
        $accomLangs = \backend\models\AccomLanguages::find()->where(['accomodation_id' => $accomId])->asArray()->all();
        $langs = [];
        foreach($accomLangs as $key => $val)
        {
            $langs[] = \backend\models\LanguagesDb::find()->where(['lang_id' => $val['lang_id']])->asArray()->one();
        }
        return $langs;
    }
    
    /*
     * Gets the emails set
     */
    public static function getEmails(int $accomId) : array
    {
        return \backend\models\Emails::find()->where(['accomodation_id' => $accomId, 'parent_id' => null])->asArray()->all();
    }
    
}