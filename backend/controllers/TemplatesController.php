<?php

namespace backend\controllers;
use Yii;
use \backend\models\Templates;
use backend\models\Accomodation;
use backend\models\AccomodationTemplate;

/**
 * TemplatesController implements the CRUD actions for Templates model.
 */
class TemplatesController extends LangDbController
{
    /*
     * Uploads the zip file with the template
     */
    public function actionCreate()
    {
        $model = $this->findModel();
        if ($model->load(Yii::$app->request->post()))
        {
            $files = $_FILES['Templates'];
            //Uploads the zip to the temp path and returns the path
            $filePath = Templates::zipToTemp($files['type']['path'], $files['name']['path'], $files['tmp_name']['path']);
            if($filePath)
            {
                //Component for extracting the zip
                $archiveControl = new \backend\components\template\ArchiveControl($filePath);
                if($archiveControl->extract())
                {
                    //Component for validating the unziped files
                    $templateValidator = new \backend\components\template\TemplateCreator($model, Templates::tempPath(), Templates::zipCryptName($files['name']['path']));
                    return $templateValidator->newTemplate() ? $this->redirect('index') : $this->redirect('create');
                }
                return $this->redirect('create');
            }
            return $this->redirect('create');
        }
        return \yii\base\Controller::render('upload', ['model' => $this->findModel()]);
    }

    public function actionUpdate(int $id)
    {
        $model = Templates::find()->where(['template_id' => $id, 'parent_id' => null])->one();

        if (is_null($model)) {
            throw new \yii\web\NotFoundHttpException(Yii::t('app', 'Not Found'));
        }

        if(!empty($model) && $model->load(Yii::$app->request->post()))
        {
            $files = $_FILES['Templates'];
            if($filePath = Templates::zipToTemp($files['type']['path'], $files['name']['path'], $files['tmp_name']['path']))
            {
                $archiveControl = new \backend\components\template\ArchiveControl($filePath);
                if($archiveControl->extract())
                {
                     $templateValidator = new \backend\components\template\TemplateCreator($model, Templates::tempPath(), Templates::zipCryptName($files['name']['path']));
                     return $templateValidator->updateTemplate() ? $this->redirect('index') : $this->redirect(['update', 'id' => $model->template_id, 'db_lang' => $model->lang_id]);
                }
            }
            else{
                \yii\helpers\FileHelper::removeDirectory(Templates::tempPath());
               return $this->redirect(['update', 'id' => $model->template_id, 'db_lang' => $model->lang_id]);
            }
        }
        else{
            return \yii\base\Controller::render('updateTemplate', ['model' => $this->findModel()]);
        }
    }

    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);
        $name = $model->name;
        
        if (is_null($model)) {
            throw new NotFoundHttpException(Yii::t('app', 'Not Found'));
        }

        try {
            if($model->validateDelete($id) && \backend\models\TemplateVersions::deleteAll(['template_id' => $id]) && $model->delete())
            {
                \yii\helpers\FileHelper::removeDirectory(Templates::getRootTemplateDir(Templates::templateMainDirName($model->name)));
                Templates::removeOptionsTable(\backend\components\template\xml\XMLOptions::createTableName($name));
            }
            else{
                throwException(Yii::t('app', 'Something went wrong'));
            }

            \common\components\Msg::success(Yii::t('app', 'delete_success'));
        } catch (Exception $e) {
            $msg = $e->getCode() == 23000 ? Yii::t('app', 'row_exists') : $e->getMessage();
            \common\components\Msg::error($msg);
        }

        return $this->redirect($model->formatRefferer());
    }

    public function actionTestTemplate(string $url)
    {
        $domain = \backend\models\Domains::find()->where(['domain_url' => $url])->asArray()->one();
        if(!empty($domain))
        {
            $accomodation = \backend\models\AccomodationDomain::find()->where(['domain_id' => $domain['domain_id']])->asArray()->one();
            if(!empty($accomodation))
            {
                $accomodationData = Accomodation::find()->where(['accomodation_id' => $accomodation['accomodation_id']])->asArray()->one();
                $accomodationTemplate = AccomodationTemplate::find()->where(['accomodation_id' => $accomodation['accomodation_id']])->asArray()->one();
                $template = Templates::find()->where(['template_id' => $accomodationTemplate['template_id']])->asArray()->one();
                $data = new \backend\components\AllAccomodationData($accomodation['accomodation_id']);
                $allData = $data->getAllData();
                
                $version = Templates::explodeVersion($template['version']);
                $pathUrl = sprintf('%s/templates/%s/%s/', Yii::getAlias('@web'), Templates::templateMainDirName($template['name']), $version[0]);
                
                
                $smarty = new \backend\components\SmartyControl();
                $smarty->setVars($allData, $pathUrl);
                $smarty->display($template['path'].'index.php');
            }
        }

    }
}
