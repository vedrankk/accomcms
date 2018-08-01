<?php

namespace backend\controllers;

use Yii;
use backend\models\AccomodationNews;
use backend\models\AccomodationNewsSearch;
use yii\web\NotFoundHttpException;
use common\components\User;

/**
 * AccomodationNewsController implements the CRUD actions for AccomodationNews model.
 */
class AccomodationNewsController extends AccomController
{
    protected function defaultBehaviorActions() : array {
        return array_merge(['image-temp', 'image-temp-delete'], parent::defaultBehaviorActions());
    }
    
    /*
     * Uploads the image to a temp folder when writing the text
     */
    public function actionImageTemp()
    {
        //Gets the temp dir
        $path = AccomodationNews::createTempDirectoryIfNotExists();
        $fileName = AccomodationNews::createImageName($_FILES['file']['name']);
        $file = $path[0].'/' .$fileName;
        $imageFileType = pathinfo($file,PATHINFO_EXTENSION);
        //Validates the image, check the type and if it exists
        $validate = AccomodationNews::validateImage($file, $imageFileType, $_FILES['file']['tmp_name']);
        
        if($validate['status'] == false)
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $validate;
        }
        //If the image is uploaded, returns the src of the image
        if(move_uploaded_file($_FILES['file']['tmp_name'], $file))
        {
            return $path[1].$fileName;
        }
    }
    
    /*
     * Ajax call, when the user deletes the image from the editor, it is also deleted from the temp folder
     */
    public function actionImageTempDelete() : bool
    {
        if (Yii::$app->request->isAjax){
            $url = Yii::$app->request->post()['img_url'];
            //Gets the full url of the image
            $full_url = Yii::getAlias('@backend')."/web/".''.$url;
            if(unlink($full_url))
            {
                return true;
            }
            return false;
        }
        else{
            return $this->redirect(['index']);
        }
    }

    /**
     * Lists all AccomodationNews models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AccomodationNewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AccomodationNews model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AccomodationNews model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * If there are images in the text, creates the folder in the news dir and moves all the images from the temp dir, to the news dir
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AccomodationNews();

        if ($model->load(Yii::$app->request->post())) {
            //encodes the string as html
            $model->news_text = htmlspecialchars($model->news_text);
           
            
            if($model->save())
            {
                  //Replaces the URL of the images from news_temp to news
                  $model->news_text = AccomodationNews::formatContent($model->news_id, $model->news_text);
                  
                  //Tries to move the images from the temp folder to the primary folder
                  if(!AccomodationNews::moveImagesAndSave($model->news_id, $model))
                  { 
                      return $this->redirect('create');
                  }
                  //If the images are moved, the model is saved again with the replaced values of the img src
                  else
                  {
                      if($model->save())
                      {
                          return $this->redirect(['view', 'id' => $model->news_id]);
                      }
                      //If for some reason the the model cant be saved, the folder is than deleted
                      else
                      {
                          AccomodationNews::deleteFolder($model->news_id);
                          return $this->redirect('create');
                      }
                  }
            }
            else
            {
                 \common\components\Msg::warning(Yii::t('model/accomnews', 'internal_error'));
                return $this->redirect('create');
            }
            
            
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AccomodationNews model.
     * Almost the same as create, checks for images, moves them and saves the model corespondingly
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        
        $model = $this->findModel($id);
        $old_model = $model;
        if ($model->load(Yii::$app->request->post())) {
            $model->news_text = htmlspecialchars($model->news_text);
           
            
            if($model->save())
            {
                  $model->news_text = AccomodationNews::formatContent($model->news_id, $model->news_text);
                  
                  if(!AccomodationNews::moveImagesToNewsDir($model->news_id))
                  {
                      //If the images arent moved, the old model is than saved 
                      $old_model->save();
                      \common\components\Msg::warning(Yii::t('model/accomnews', 'internal_error'));
                      return $this->redirect(['update', 'id' => $model->news_id]);
                  }
                  else
                  {
                      if($model->save())
                      {
                          return $this->redirect(['view', 'id' => $model->news_id]);
                      }
                      else
                      {
                           \common\components\Msg::warning(Yii::t('model/accomnews', 'internal_error'));
                           return $this->redirect(['update', 'id' => $model->news_id]);
                      }
                  }
            }
            else
            {
                 \common\components\Msg::warning(Yii::t('model/accomnews', 'internal_error'));
                return $this->redirect(['update', 'id' => $model->news_id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AccomodationNews model, along with the folder
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        if(AccomodationNews::deleteFolder($model->news_id))
        {
            $model->delete();
        }
        else
        {
            \common\components\Msg::warning(Yii::t('model/accomnews', 'internal_error'));
        }
        

        return $this->redirect(['index']);
    }

    /**
     * Finds the AccomodationNews model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AccomodationNews the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AccomodationNews::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Not found'));
        }
    }
}
