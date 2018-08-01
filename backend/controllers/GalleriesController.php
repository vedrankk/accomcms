<?php

namespace backend\controllers;

use Yii;
use \backend\models\Galleries;
use backend\models\GalleryImages;

/**
 * GalleriesController implements the CRUD actions for Galleries model.
 */
class GalleriesController extends LangDbController
{
    
    public function defaultBehaviorActions() : array
    {
        return array_merge(self::translateBehaviorActions(), ['view-galleries', 'gallery-details', 'gallery-upload', 'upload-images', 'delete-image', 'delete-gallery']);
    }
    
    /**
     * Crates the gallery folder if it does not exists and uploads the images to the folder, also saves to the gallery_images table
     * @param $_POST['id'] -> Gallery ID
     * @param $_FILES Image for upload
     * @return array
     */
    public function actionUploadImages()
    {
        $gallery_id = $_POST['id'];
        //Gets the gallery info
        $gallery_data = Galleries::findOne(['gallery_id' => $gallery_id]);
        //Gets the parent accomodation
        $accomodation_name = \backend\models\Accomodation::find()->where(['accomodation_id' => $gallery_data['accomodation_id']])->asArray()->one()['name'];
        //Gets the gallery directory, if it does not exist, creates it *galleries/md5(accom_name)/md5(gallery_name)*
        $path = Galleries::getGalleryDirectory($accomodation_name, $gallery_data['gallery_name']) .'\\';
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $fileName = Galleries::cleanImageName($_FILES['files']['name'][0]);
        
        $file = $path.$fileName;
        
        $imageFileType = pathinfo($file,PATHINFO_EXTENSION);
        //Validates image type and checks if the image with the same name already exists
        $validate = Galleries::validateImage($file, $imageFileType, $_FILES['files']['tmp_name'][0]);
        
        if($validate['status'] == false)
        {
            return ['files' => [['name' => $validate['msg']]]];
        }
        //Tries to upload the files, if true, enters the data into the table and returns an array response
        if(move_uploaded_file($_FILES['files']['tmp_name'][0], $file))
        {
            return GalleryImages::insertNew($gallery_data['gallery_id'], $fileName, $gallery_data['gallery_name'], $accomodation_name, $_FILES, $file);
        }    
    }
    
    /*
     * @param $im_id -> Image ID
     * @param $gal_name -> Gallery Name
     * @param $ac_name -> Accomodation Name
     * @return boolean
     */
    public function actionDeleteImage($im_id, $gal_name, $ac_name)
    {
        //Finds the image in the table
        $model = GalleryImages::find()->where(['image_id' => $im_id])->one();
        //Finds the path where the image is stored
        $path = Galleries::getGalleryDirectory($ac_name, $gal_name) .'\\'.$model->image_name;
        if(file_exists($path)){
            if(unlink($path) && $model->delete())
            {
                return true;
            }
            else{
                return false;
            }
        }
    }
    
    /*
     * Deletes the gallery and all the images from the table and the folder
     * @param $id -> Gallery ID
     */
    public function actionDeleteGallery()
    {
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post()['id'];
            
            $gallery = Galleries::findOne(['gallery_id' => $id]);
            //If the Gallery has a translation, returns false
            if(!empty($gallery->find()->where(['parent_id' => $id])->one()))
            {
                \common\components\Msg::warning(Yii::t('app', "has_translation"));
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['status' => false];
            }
            $accomodation_id = $gallery->accomodation_id;
            $db_lang = $gallery->dblang_id;
            //Gets the gallery dir
            $dir = Galleries::getGalleryDirectory($gallery->accomodation->name, $gallery->gallery_name);
            //If deleting all images from the table is succesefullt, deletes all the images from the folder
            if(GalleryImages::deleteAll(['gallery_id' => $id])){
                Galleries::deleteAllFromDir($dir);
            }
            //Deletes entry from table and deletes the folder
            if($gallery->delete())
            {
                rmdir($dir);
                return $this->redirect(Galleries::redirectOnDelete($accomodation_id, $db_lang));
            }
        }
        else{
            return $this->redirect('index');
        }
    }
    
    
    public function actionCreate()
    {
        $model = $this->findModel();
        $model->setScenario($model::SCENARIO_CREATE);
        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            //If the data is saved to the table, creates the gallery directory
            if(Galleries::createGalleryDirectory($model->gallery_name, $model->accomodation->name))
            {
                return $this->redirect(['index']);
            }
        } else {
            return $this->render('form', [
                'model' => $model,
                'parent_id' => $model->parent_id
            ]);
        }
    }
    
    /*
     * Displays all galleries for the current accomdoation ( $id )
     * @param $id -> Accomodation ID
     */
    public function actionViewGalleries(int $id)
    {
        //If the lang is default, searches for the 'original' rows
        if(Galleries::isDefault())
        {
            $model['data'] = Galleries::find()->where(['accomodation_id' => $id])->asArray()->all();
        }
        //If the db lang is not default, searches for translated rows for the current gallery
        else
        {
            $model['data'] = Galleries::getTranslatedRows($id);
        }
        //If there are no entries for the current accomdoation, redirects to index
        if(empty($model['data']))
        {
            \common\components\Msg::warning(Yii::t('model/galleries', 'no_galleries'));
            return $this->redirect(['index', 'db_lang' => Galleries::getLangParam()]);
        }
        
        $model['accom_name'] = \backend\models\Accomodation::find()->where(['accomodation_id' => $id])->asArray()->one()['name'];
        
        return yii\base\Controller::render('viewGalleries', [
            'model' => $model
        ]);
    }
    
    /*
     * Details for the gallery, shows all images
     * @param $id -> Gallery ID
     */
    public function actionGalleryDetails(int $id)
    {
        //Looks for db_lang in the URL and based on that gets the orignal or the translated value
        if(Galleries::isDefault())
        {
            $model = Galleries::find()->where(['gallery_id' => $id])->asArray()->one();
        }
        else
        {
            $model = Galleries::getTranslatedGallery($id);
        }
        //If there is nothing in the table, redirects to index
        if(empty($model))
        {
            \common\components\Msg::warning(Yii::t('model/galleries', 'gallery_not_exist'));
            return $this->redirect(['index', 'db_lang' => Galleries::getLangParam()]);
        }
        //If we are viewing the translaed value, gets the original gallery name, so it can prperly create the image URL
        $gallery_name = $model['parent_id'] == null ? $model['gallery_name'] : Galleries::getGalleryName($model['parent_id']);
        
        $accomodation_name = Galleries::getAccomodationName($id);
        //Creates the URL for the images
        $url = Galleries::imageUrl($accomodation_name, $gallery_name).'/';
        //Finds all images from the table
        $images = GalleryImages::find()->where(['gallery_id' => $id])->asArray()->all();
        
        return yii\base\Controller::render('galleryDetails', [
            'model' => $model,
            'images' => $images,
            'url' => $url,
            'accomodation_name' => $accomodation_name,
        ]);
    }
    
    /*
     * Renders the gallery upload page with the GalleryImages model
     */
    public function actionGalleryUpload(int $id)
    {
        
        $model = new \backend\models\GalleryImages();
        $gallery = Galleries::find()->where(['gallery_id' => $id])->asArray()->one();
        return yii\base\Controller::render('galleryUpload', ['model' => $model, 'gallery' => $gallery]);
    }
}
