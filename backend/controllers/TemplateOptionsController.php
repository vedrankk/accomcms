<?php

namespace backend\controllers;

use Yii;
use backend\models\Accomodation;

class TemplateOptionsController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $accomodation = Accomodation::find()->where(['user_id' => Yii::$app->user->identity->user_id])->asArray()->one();
        $templateId = \backend\models\AccomodationTemplate::find()->where(['accomodation_id' => $accomodation['accomodation_id']])->asArray()->one()['template_id'];
        $template = \backend\models\Templates::find()->where(['template_id' => $templateId])->asArray()->one();
        
        $xmlValidator = new \backend\components\template\xml\XMLValidator($template['path']);
        
        if($xmlValidator->xmlFileExists() && $xmlValidator->validateOptionsContent())
        {
            $xml = $xmlValidator->getXmlData();
        }
        else{
            \common\components\Msg::error('There was an error in the template. If it continues to occur, please contact the admins at: mail@mail.com');
            return $this->redirect(['/']);
        }
        
        if(!empty(Yii::$app->request->post()))
        {
            \backend\components\template\xml\XMLOptions::saveFromPost(Yii::$app->request->post(), $template['name'], $accomodation['accomodation_id']);
            return $this->refresh();
        }
        
        $widget = new \backend\components\template\TemplateOptionsWidget($xml, $template['name'], $accomodation['accomodation_id']);
        
        return $this->render('index', ['accomodation' => $accomodation, 'template' => $template, 'formContent' => $widget->display()]);
    }
}

