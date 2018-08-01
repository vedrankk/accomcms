<?php

namespace backend\models;

use Yii;
use \common\models\User as Users;

/**
 * This is the model class for table "user".
 *
 * @property integer $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $role
 * @property string $country
 * @property string $lang
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const USER_ACTIVE = 10;
    const USER_INACTIVE = 0;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    // /**
    //  * @inheritdoc
    //  */
    // public function rules()
    // {
    //     return [
    //         [['first_name', 'last_name', 'country', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at'], 'required'],
    //         [['role', 'lang'], 'string'],
    //         [['status', 'created_at', 'updated_at'], 'integer'],
    //         [['first_name', 'last_name', 'country', 'lang', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
    //         [['auth_key'], 'string', 'max' => 32],
    //         [['email'], 'unique'],
    //         [['email'], 'email'],
    //         [['password_reset_token'], 'unique'],
    //     ];
    // }

    // public function scenarios()
    // {
    //     $scenarios = parent::scenarios();
    //     $scenarios['adminUpdate'] = ['first_name', 'last_name', 'country', 'lang'];
    //     $scenarios['userUpdate'] = ['first_name', 'last_name', 'country', 'lang', 'email'];
    //     return $scenarios;
    // }

    // /**
    //  * @inheritdoc
    //  */
    // public function attributeLabels()
    // {
    //     return [
    //         'user_id' => Yii::t('yii','User ID'),
    //         'first_name' => Yii::t('yii', 'First Name'),
    //         'last_name' => Yii::t('yii','Last Name'),
    //         'role' => Yii::t('yii','User Role'),
    //         'country' => Yii::t('yii','Country'),
    //         'lang' => Yii::t('yii','Language'),
    //         'auth_key' => Yii::t('yii','Auth Key'),
    //         'password_hash' => Yii::t('yii','Password Hash'),
    //         'password_reset_token' => Yii::t('yii','Password Reset Token'),
    //         'email' => Yii::t('yii','Email'),
    //         'status' => Yii::t('yii','Status'),
    //         'created_at' => Yii::t('yii','Created At'),
    //         'updated_at' => Yii::t('yii','Updated At'),
    //     ];
    // }

    public static function formatDate($timestamp)
    {
        return Yii::$app->formatter->asDate($timestamp);
    }

    public static function formatStatus($status)
    {
        return $status == self::USER_ACTIVE ? 'Active' : 'Inactive';
    }
    /**
    *Validates that admins can't update superadmin
    *@param integer $id
    *@return boolean
    */
    private function validateUpdateAndDelete(int $id) : bool
    {
        if (!\common\components\User::isSuperAdmin()) {
            foreach (self::getSuperAdmins() as $key => $value) {
                if ($value['user_id'] == $id) {
                    return false;
                }
            }
            return true;
        }
        return true;
    }

    private function userRoleDelete($id)
    {
        return \common\components\User::isUser() && Yii::$app->user->identity->user_id != $id ? false : true;
    }

    protected function getSuperAdmins() : array
    {
        return Users::find()->where(['role' => Users::ROLE_SUPERADMIN])->asArray()->all();
    }

    public function validateUserUpdate($id)
    {
        return self::validateUpdateAndDelete($id);
    }
    public function validateUserDelete($id)
    {
        return self::validateUpdateAndDelete($id) && self::userRoleDelete($id) ? true : false;
    }
}
