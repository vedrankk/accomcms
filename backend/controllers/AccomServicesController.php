<?php

namespace backend\controllers;

use Yii;
use backend\models\AccomServices;
use backend\models\AccomServicesSearch;
use yii\web\NotFoundHttpException;
use common\components\Msg;
use common\components\User;

/**
 * AccomServicesController implements the CRUD actions for AccomServices model.
 */
class AccomServicesController extends AccomController
{

    /**
     * Lists all AccomServices models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AccomServicesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AccomServices model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => \backend\models\Accomodation::findOne(['accomodation_id' => $id]),
            'completeModel' => $this->findCompleteModel($id)
        ]);
    }

    /**
     * Creates a new AccomServices model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $msg = '';
        $model = new AccomServices();
       
        if ($model->load(Yii::$app->request->post())) {
            foreach ($model->services_id as $key => $val)
            {
                if(AccomServices::find()->where(['accomodation_id' => $model->accomodation_id, 'services_id' => $val])->asArray()->one())
                {
                    $msg .= $model->createWarningMessage($model->accomodation_id, $val);
                    continue;
                }
                $insertModel = new AccomServices();
                $insertModel->accom_services_id = $model->accom_services_id;
                $insertModel->accomodation_id = $model->accomodation_id;
                $insertModel->services_id = $val;
                $insertModel->save();
            }
            if($msg != ''){
                Msg::warning($msg);
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AccomServices model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $msg = '';
        $model = $this->findModel($id);

         if ($model->load(Yii::$app->request->post())) {
            foreach ($model->services_id as $key => $val)
            {
                if(AccomServices::find()->where(['accomodation_id' => $model->accomodation_id, 'services_id' => $val])->asArray()->one())
                {
                    $msg .= $model->createWarningMessage($model->accomodation_id, $val);
                    continue;
                }
                $insertModel = new AccomServices();
                $insertModel->accom_services_id = $model->accom_services_id;
                $insertModel->accomodation_id = $model->accomodation_id;
                $insertModel->services_id = $val;
                $insertModel->save();
            }
            if($msg != ''){
                Msg::warning($msg);
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AccomServices model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the AccomServices model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AccomServices the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AccomServices::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    private function findCompleteModel($id)
    {
        if (($id) !== null) {
            $fullModel = AccomServices::find()->select('accom_services.*, services.name as service_name')->where(['accom_services.accomodation_id' => $id])->leftJoin('services', 'accom_services.services_id = services.services_id')->asArray()->all();
            return $fullModel;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
