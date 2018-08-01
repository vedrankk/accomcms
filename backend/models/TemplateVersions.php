<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "template_versions".
 *
 * @property integer $version_id
 * @property integer $template_id
 * @property string $version
 * @property string $version_description
 *
 * @property Templates $template
 */
class TemplateVersions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'template_versions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id', 'version'], 'required'],
            [['template_id'], 'integer'],
            [['version_description'], 'string'],
            [['version'], 'string', 'max' => 10],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Templates::className(), 'targetAttribute' => ['template_id' => 'template_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'version_id' => 'Version ID',
            'template_id' => 'Template ID',
            'version' => 'Version',
            'version_description' => 'Version Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Templates::className(), ['template_id' => 'template_id']);
    }
}
