<?php
namespace frontend\models;

use yii\base\Model;
use common\models\User;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $email;
    public $first_name;
    public $last_name;
    public $country;
    public $password;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'email'],
            ['email', 'required'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email has already been taken.'],

            ['first_name', 'trim'],
            ['first_name', 'required'],
            ['first_name', 'string', 'max' => 255],

            ['last_name', 'trim'],
            ['last_name', 'required'],
            ['last_name', 'string', 'max' => 255],

            ['country', 'trim'],
            ['country', 'required'],
            ['country', 'string', 'max' => 255],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        $user = new User();
        $user->email = $this->email;
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->country = $this->country;
        $user->role = $user::ROLE_USER;
        $user->created_at = Yii::$app->formatter->asTimestamp(date('d-m-Y'));
        $user->updated_at = Yii::$app->formatter->asTimestamp(date('d-m-Y'));
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save(true, ['email', 'first_name', 'last_name', 'country', 'role', 'password', 'auth_key', 'password_hash', 'password_reset_token', 'created_at', 'updated_at']) ? $user : null;
    }
}
