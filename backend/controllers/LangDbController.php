<?php

namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\base\Exception;
use common\components\Msg;
use yii\filters\VerbFilter;

/**
 * LanguagesDbController implements the CRUD actions
 */
class LangDbController extends AccomController
{
    private $render_file_path = '/_shared/languages/';
  
    /**
     * Search for grid
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $controller = ucfirst(Yii::$app->controller->id) .'Search';
        $controller = 'backend\models\\' . str_replace('-', '', ucwords($controller, '-'));
  
        if (class_exists($controller)) {
            $searchModel = new $controller();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'model' => $this->findModel(),
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    /**
     * Creates a new record
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = $this->findModel();
        $model->setScenario($model::SCENARIO_CREATE);
        return $this->saveOrRender($model, ['view', 'id' => $model->primaryKey]);
    }

    /**
     * Updates an existing record.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);
        $model->setScenario($model::SCENARIO_UPDATE);
        return $this->saveOrRender($model, ['view', 'id' => $model->primaryKey]);
    }
    
   /**
    * Adds a translation for record specified in $db_lang
    * @param integer $parent_id
    * @param integer $db_lang
    * @return mixed
    */
    public function actionAddTranslation(int $db_lang, int $parent_id)
    {
        $model = $this->findModel();
        $model->setScenario($model::SCENARIO_ADD_TRANSLATION);
        return $this->saveOrRender($model, ['view', 'id' => $model->primaryKey], $db_lang, $parent_id);
    }
    
    /**
     * Update translation
     *
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdateTranslation(int $id)
    {
        $model = $this->findModel($id);
        $model->setScenario($model::SCENARIO_UPDATE_TRANSLATION);
        
        if (!is_null($model) && !$model->validateLangInUrl($model)) {
            throw new NotFoundHttpException('Not found');
        }
        
        return $this->saveOrRender($model, ['view', 'id' => $model->primaryKey]);
    }
    
    /**
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView(int $id)
    {
        $model = $this->findModel($id);
        if (!is_null($model) && !$model->validateLangInUrl($model)) {
            throw new NotFoundHttpException('Not found');
        }

        $model::getOneWithParent($model);
        return $this->render('view', [
            'model' => $model,
        ]);
    }
    
    /**
     * Delete original record
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);

        if (is_null($model)) {
            throw new NotFoundHttpException(Yii::t('app', 'Not Found'));
        }
        
        try {
            if (!$model->validateDelete($id) || !$model->delete()) {
                throwException(Yii::t('app', 'Something went wrong'));
            }
            
            Msg::success(Yii::t('app', 'delete_success'));
        } catch (Exception $e) {
            $msg = $e->getCode() == 23000 ? Yii::t('app', 'row_exists') : $e->getMessage();
            Msg::error($msg);
        }

        return $this->redirect($model->formatRefferer());
    }
    
    /**
     * Delete translation
     *
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDeleteTranslation(int $id)
    {
        $model = $this->findModel($id);

        if (!is_null($model) && !$model->validateLangInUrl($model)) {
            throw new NotFoundHttpException(Yii::t('app', 'Not found'));
        }
        try {
            $model->validateDeleteTranslation($model);
            if (!$model->validateDeleteTranslation($model) || !$model->delete()) {
                throwException(Yii::t('app', "Something went wrong"));
            }
            
            Msg::success(Yii::t('app', 'delete_success'));
        } catch (Exception $e) {
            Msg::error($e->getMessage());
        }
      
        return $this->redirect(['index', 'db_lang' => $model->dblang_id]);
    }
    /**
     * @inherit
     */
    public function render($view, $params = [])
    {
        $default_params = $this->defaultRenderParams();
        $params = array_merge($default_params, $params);
        return parent::render($this->render_file_path . $view, $params);
    }
    
    /**
     * Finds the Languages model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LangDb - parent the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id = null) : \backend\models\LangDb
    {
        $controller = 'backend\models\\' . str_replace('-', '', ucwords(Yii::$app->controller->id, '-'));
      
        if (class_exists($controller)) {
            if (is_null($id)) {
                return new $controller();
            } elseif (($model = $controller::findOne($id)) !== null) {
                return $model;
            }
        }
        
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    /**
     * Render form with default parameters
     *
     * @param LangDB $model
     * @param int $db_lang
     * @param int $parent_id
     * @return string
     */
    protected function renderForm($model, int $db_lang = null, int $parent_id = null) : string
    {
        return $this->render('form', ['model' => $model, 'parent_id' => $parent_id, 'db_lang' => $db_lang]);
    }

    /**
     * Save form and redirect to view or render again form with errors messages
     *
     * @param LangDb $model
     * @param array $params
     * @param int $db_lang
     * @param int $parent_id
     * @return string
     */
    protected function saveOrRender($model, array $params, int $db_lang = null, int $parent_id = null)
    {
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (empty($params['id'])) {
                $params['id'] = $model->primaryKey;
            }
            $params = array_merge(['db_lang' => $model->dblang_id], $params);
            return $this->redirect($params);
        } else {
            return $this->renderForm($model, $db_lang, $parent_id);
        }
    }

    /**
     * Get default template variables
     *
     * @return array
     */
    protected function defaultRenderParams() : array
    {
        $model = $this->findModel();
        return ['db_lang' => $model->dblang_id];
    }
}
