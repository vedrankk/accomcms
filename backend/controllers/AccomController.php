<?php

namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use common\components\User;
use common\components\AccessControl;
use common\components\AccessRule;

/**
 * LanguagesDbController implements the CRUD actions
 */
class AccomController extends \yii\web\Controller
{
    public $behavior = null;
    
    public function init()
    {
        $this->behavior = User::ROLE_ADMIN;
        parent::init();
    }

    public function behaviors()
    {
        switch ($this->behavior) {
            case User::ROLE_SUPERADMIN:
                $user_behavior = $this->behaviorsSuperAdmin();
                break;
            case User::ROLE_ADMIN:
                $user_behavior = $this->behaviorsAdmin();
                break;
            default:
                $user_behavior = $this->behaviorsUser();
                break;
                
        }

        return array_merge($this->behaviorsShared(), $user_behavior, $this->behaviorsCustom());
    }
    
    protected function behaviorsCustom() : array
    {
        return [];
    }
    
    protected function behaviorsShared() : array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete_translation' => ['POST'],
                ],
            ],
            
        ];
    }
    
    protected function defaultBehaviorActions() : array
    {
        return ['create', 'update', 'delete', 'index', 'view'];
    }
    
    protected function translateBehaviorActions() : array
    {
        return ['create', 'update', 'delete', 'index', 'view', 'delete_translation', 'update_translation'];
    }
    
    protected function behaviorsUser() : array
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
               'only' => self::defaultBehaviorActions(),
               'rules' => [
                    [
                        'actions' => self::defaultBehaviorActions(),
                        'allow' => true,
                        'roles' => [User::ROLE_USER, User::ROLE_SUPERADMIN, User::ROLE_ADMIN, ['@']],
                    ]],
            ]];
    }

    protected function behaviorsAdmin() : array
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
               'only' => self::defaultBehaviorActions(),
               'rules' => [
                    [
                        'actions' => self::defaultBehaviorActions(),
                        'allow' => true,
                        'roles' => [User::ROLE_SUPERADMIN, User::ROLE_ADMIN, ['@']],
                    ]],
            ]];
    }

    protected function behaviorsSuperAdmin() : array
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
               'only' => self::defaultBehaviorActions(),
               'rules' => [
                    [
                        'actions' => self::defaultBehaviorActions(),
                        'allow' => true,
                        'roles' => [User::ROLE_SUPERADMIN, ['@']],
                    ]],
            ]];
    }
}
