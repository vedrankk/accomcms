<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "gallery_images".
 *
 * @property integer $image_id
 * @property integer $gallery_id
 * @property string $image_name
 *
 * @property Galleries $gallery
 */
class GalleryImages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gallery_images';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gallery_id', 'image_name'], 'required'],
            [['gallery_id'], 'integer'],
            [['image_name'], 'string', 'max' => 150],
            [['gallery_id'], 'exist', 'skipOnError' => true, 'targetClass' => Galleries::className(), 'targetAttribute' => ['gallery_id' => 'gallery_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'image_id' => 'Image ID',
            'gallery_id' => 'Gallery ID',
            'image_name' => 'Image Name',
        ];
    }
    
    /*
     * Inserts new image in the galleryImages table, and uploads to the folder
     * @param $gallery_id -> Gallery ID
     * @param $img_name -> Name of the image for upload
     * @param $gallery_name -> Gallery Name
     * @param $accomodation_name -> Accomodation Name
     * @param $files -> $_FILES
     * @param $file -> Full image path
     * @return array
     */
    public function insertNew(int $gallery_id, string $img_name, string $gallery_name, string $accomodation_name, array $files, string $file) : array
    {
        $model = new self();
        $model->gallery_id = $gallery_id;
        $model->image_name = $img_name;
        if($model->save())
        {
            $files['files']['name'][0] = $img_name;
            return ['files' =>[Galleries::createReturnArray($model->image_id, $gallery_name, $accomodation_name, $files, $file)]];
        }
        else{
            unlink($file);
            return ['files' => [['name' => Yii::t('model/galleries', 'upload_error')]]];
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGallery()
    {
        return $this->hasOne(Galleries::className(), ['gallery_id' => 'gallery_id']);
    }
}
