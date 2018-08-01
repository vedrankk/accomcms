<?php

namespace backend\controllers;

use Yii;
use backend\models\AccomLanguages;
use backend\models\AccomLanguagesSearch;
use yii\web\NotFoundHttpException;
use common\components\Msg;
use common\components\User;
/**
 * AccomLanguagesController implements the CRUD actions for AccomLanguages model.
 */
class AccomLanguagesController extends AccomController
{
    
    public function defaultBehaviorActions() : array {
        return ['accom-langs', 'accom-langs-create', 'create', 'index', 'view', 'delete', 'make-default'];
    }
    
    public function actionAccomLangs()
    {
       if (Yii::$app->request->isAjax) {
         $id = Yii::$app->request->post()['id'];
         $langs = AccomLanguages::removeExistingLangs($id);
         
         \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
         return [
             'langs' => array_values($langs)
         ];
       }
    }
    
    /*
     * Creates a new Accomodation Language
     */
    public function actionAccomLangsCreate()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $langs = $data['langs'];
            foreach($langs as $key => $val)
            {
                if(AccomLanguages::langExists($data['accomId'], $val['lang']))
                {
                    Msg::warning(Yii::t('model/accomlanguages', 'val_exists'));
                    continue;
                }
                else if($val['default'] == 1 && AccomLanguages::hasDefault($data['accomId']))
                {
                    Msg::warning(Yii::t('model/accomlanguages', 'has_default'));
                    $langs[$key]['default'] = 0;
                }
                
                $model = new AccomLanguages();
                AccomLanguages::addNewLang($model, $data['accomId'], $val['lang'], $langs[$key]['default']);
            }
            return $this->redirect(['index']);
        }
    }

    /**
     * Lists all AccomLanguages models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AccomLanguagesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AccomLanguages model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $completeModel = $this->findAccomodations($id);
        if(empty($completeModel)){
            Msg::warning('There are no values for this accomodation!');
            return $this->redirect(['index']);
        }
        return $this->render('view', [
            'model' => $this->findAccomodations($id),
            'completeModel' => $this->findAccomodations($id),
        ]);
    }

    /**
     * Creates a new AccomLanguages model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $msg = '';
        $model = new AccomLanguages();

        if ($model->load(Yii::$app->request->post())) {
            if(AccomLanguages::rowExists($model))
            {
                Msg::warning('Row already exists!');
                return $this->redirect(['create']);
            }
            
            if(AccomLanguages::defaultLangIdExists($model))
            {
                Msg::warning('This language already has a default ID!');
                return $this->redirect(['create']);
            }
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->accom_languages_id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AccomLanguages model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $accom_id = $model->accomodation_id;
        $isLast = AccomLanguages::find()->where(['accomodation_id' => $model->accomodation_id])->count();
        if($isLast == 1)
        {
            $model->delete();
            return $this->redirect(['index']);
        }
        if($model->default_lang_id == 1)
        {
            $accom_id = $model->accomodation_id;
            
            $defaultModel = AccomLanguages::find()->where(['accomodation_id' => $accom_id])->andWhere(['<>', 'accom_languages_id', $id])->one();
            $defaultModel->default_lang_id = 1;
            if($defaultModel->save())
            {
                $model->delete();
                return $this->redirect(['view', 'id' => $accom_id]);
            }
        }
        $model->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }
    
    /*
     * Makes the clicked language the default one, and removes the default value from the current default langauge.
     */
    public function actionMakeDefault($id){
        $model = $this->findModel($id);
        if($model->default_lang_id == 1)
        {
            return $this->redirect(Yii::$app->request->referrer);
        }
        if($currentDefault = AccomLanguages::find()->where(['accomodation_id' => $model->accomodation_id, 'default_lang_id' => 1])->one())
        {
            $currentDefault->default_lang_id = 0;
            $currentDefault->save();
        }
        $model->default_lang_id = 1;
        $model->save();
        return $this->redirect(Yii::$app->request->referrer);
    }
    

    /**
     * Finds the AccomLanguages model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AccomLanguages the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AccomLanguages::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    protected function findAccomodations($id)
    {
        $model = AccomLanguages::find()
                ->select('accom_languages.*, accomodation.name, languages_db.name as lang_name')
                ->where(['accom_languages.accomodation_id' => $id])
                ->leftJoin('accomodation', 'accom_languages.accomodation_id = accomodation.accomodation_id')
                ->leftJoin('languages_db', 'accom_languages.lang_id = languages_db.lg_id')
                ->asArray()
                ->all();
        return $model;
    }
}
