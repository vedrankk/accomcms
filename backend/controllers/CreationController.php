<?php

namespace backend\controllers;

use Yii;

use backend\models\Accomodation;
use backend\models\Domains;
use backend\models\AccomodationDomain;
use backend\models\AccomLanguages;
use backend\models\AccomServices;
use backend\models\Templates;
use backend\models\Emails;

use backend\components\Creation;

class CreationController extends AccomController
{
    public function actionIndex()
    {
        $model = Creation::newModel();
        if($model->load(Yii::$app->request->post()))
        {
            return Creation::setNewAccomodationModel($model) ? $this->redirect(['choose-domain']) : $this->redirect(['index']);
        }
        return $this->render('index', ['model' => $model]);
    }
    
    public function actionChooseDomain()
    {
        if(Creation::getAccomIdFromMemory() === 0)
        {
            return $this->redirect('index');
        }
        if(!Creation::validateDomainExists())
        {
            return $this->redirect('choose-template');
        }
        
        return $this->render('domain', ['model' => new Domains()]);
    }
    
    public function actionGetDomainSuggestions()
    {
        if (Yii::$app->request->isAjax) {
           \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
           if(preg_match('/([a-zA-Z-]+[\.]+[\.a-z]{1,4}$)/', Yii::$app->request->post()['domainUrl']))
           {
               $domain = Domains::find()->where(['domain_url' => Yii::$app->request->post()['domainUrl']])->asArray()->one();
               if(!empty($domain))
               {
                   return !empty(AccomodationDomain::find()->where(['domain_id' => $domain['domain_id']])->asArray()->one()) ? ['status' => true, 'msg' => 'Valid URL, procced?'] : ['status' => false, 'msg' => 'Domain taken'];
               }
               else{
                   return ['status' => true, 'msg' => 'Valid URL, procced?'];
               }
           }
           else{
               return ['status' => false, 'msg' => 'Invalid url format'];
           }
          
        }
    }
    
    public function actionSaveDomain()
    {
        if(Yii::$app->request->isAjax)
        {
           $domain =  Yii::$app->request->post()['domainUrl'];
           \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
           if(preg_match('/([a-zA-Z-]+[\.]+[\.a-z]{1,4}$)/', $domain))
           {
               return Creation::setNewAccomodationDomain($domain);
           }
           else{
               return ['status' => false, 'msg' => 'Invalid format. (exaple.examp)'];
           }
        }
    }
    
    public function actionChooseTemplate()
    {
        $accom_id = Creation::getAccomIdFromMemory();
        if($accom_id === 0)
        {
            return $this->redirect('index');
        }
        if(Creation::accomodationHasTemplate($accom_id))
        {
            return $this->redirect('choose-services');
        }
        
        return $this->render('template', ['model' => new Templates(), 'templates' => Creation::getTemplateData()]);
    }
    
    public function actionTemplateDetails()
    {
        if(Yii::$app->request->isAjax)
        {
            $template_data = Creation::getTemplateDetails(Yii::$app->request->post()['template_id']);
            
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if(!empty($template_data))
            {
                return $template_data;
            }
            else{
                return ['status' => false];
            }
        }
    }
    
    public function actionSaveTemplate(int $id)
    {
        $accom_id = Creation::getAccomIdFromMemory();
        if($accom_id !== 0)
        {
            $setTemplate = Creation::setTemplate($accom_id, $id);
            if($setTemplate['status'])
            {
                return $this->redirect('choose-services');
            }
            else{
                Msg::error($setTemplate['msg']);
                return $this->refresh();
            }
        }
        else{
            return $this->redirect('index');
        }
    }
    
    public function actionChooseServices()
    {
        $model = new AccomServices();
        if($model->load(Yii::$app->request->post()))
        {
            return Creation::addNewMultiple($model->services_id, '\backend\models\AccomServices', 'services_id', 'accom_services_id') ? $this->redirect('choose-languages') : $this->refresh();
        }
        
        return $this->render('services', ['services' => Creation::getAvaliableServices(), 'model' => $model]);
    }
    
    public function actionChooseLanguages()
    {
        if(!empty(AccomLanguages::find()->where(['accomodation_id' => Creation::getAccomIdFromMemory()])->asArray()->one()))
        {
            return $this->redirect('choose-emails');
        }
        $model = new AccomLanguages();
        if($model->load(Yii::$app->request->post()))
        {
           return Creation::addNewMultiple($model->lang_id, '\backend\models\AccomLanguages', 'lang_id', 'accom_languages_id', ['first' =>['default_lang_id' => 1]]) ? $this->redirect('choose-emails') : $this->refresh();
        }
        return $this->render('languages', ['model' => $model]);
    }
    
    public function actionChooseEmails()
    {
        if(!empty(Emails::find()->where(['accomodation_id' => Creation::getAccomIdFromMemory()])->asArray()->one()))
        {
            return $this->redirect('finalize');
        }
        
        
        return $this->render('emails', ['model' => new Emails()]);
    }
    
    public function actionSaveEmail()
    {
        if(Yii::$app->request->isAjax)
        {
            $email = Yii::$app->request->post()['email'];
            $title = Yii::$app->request->post()['title'];
            if(preg_match('/^[a-zA-Z]+[a-zA-Z0-9\.!#$%+-?_|]+[@]+[a-z]+[\.]+[a-z]+$/', $email))
            {
                $accom_id = Creation::getAccomIdFromMemory();
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                if(!empty(Emails::find()->where(['email' => $email])->asArray()->one()))
                {
                    return ['status' => false, 'msg' => 'This email already exists in our database!'];
                }
                else{
                    return Creation::setNewEmailModel($accom_id, $email, $title);
                }
            }
        }
    }
    
    public function actionFinalize()
    {
        if(!Yii::$app->request->cookies->has(Creation::ACCOM_ID))
        {
            return $this->redirect('index');
        }
        $accomId = Creation::getAccomIdFromMemory();
        
        return $this->render('finalize', ['accomodation' => Accomodation::find()->where(['accomodation_id' => $accomId])->asArray()->one(),
                                          'domain' => Creation::getDomain($accomId), 
                                          'services' => Creation::getServices($accomId), 
                                          'languages' => Creation::getLanguages($accomId),
                                          'emails' => Creation::getEmails($accomId)]);
    }
    
    public function actionSave()
    {
        if(!Yii::$app->request->cookies->has(Creation::ACCOM_ID))
        {
            return $this->redirect('index');
        }
        return Creation::finalize() ? $this->redirect('index') : $this->redirect('finalize');
    }
    
    public function actionReset()
    {
        if(!Yii::$app->request->cookies->has(Creation::ACCOM_ID))
        {
            return $this->redirect('index');
        }
        Creation::reset();
        return $this->redirect('index');
    }
    
    public function actionTemplatePreview($id)
    {
        $template = Templates::find()->where(['template_id' => $id])->asArray()->one();
        
        $data = new \backend\components\AllAccomodationData(4);
        
        $allData = $data->getAllData();
        
        $version = Templates::explodeVersion($template['version']);
                $pathUrl = sprintf('%s/templates/%s/%s/', Yii::getAlias('@web'), Templates::templateMainDirName($template['name']), $version[0]);
                
                $smarty = new \backend\components\SmartyControl();
                $smarty->setVars($allData, $pathUrl);
                $smarty->display($template['path'].'index.php');
    }
}

