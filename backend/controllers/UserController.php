<?php

namespace backend\controllers;

use Yii;
use common\components\User;
use backend\models\UserSearch;
use backend\controllers\AccomController;
use yii\web\NotFoundHttpException;
use common\components\Msg;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends AccomController
{

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'model' => new User(),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if ((new \backend\models\User)->validateUserUpdate($id)) {
            //Za sada obican korisnik ne moze da udje u update, mislio sam da se to nekako podeli, ali mogu napraviti i da moze, kazi kako hoces
            if (User::isUser()) {
                return $this->redirect(['profile']);
            }
            $model = $this->findModel($id);
            if (User::isAdmin()) {
                $model->scenario = 'adminUpdate';
            }
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Msg::success('Data succesefully updated!');
                return $this->redirect(['view', 'id' => $model->user_id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
        Msg::warning('This action is not allowed!');
        return $this->redirect(['index']);
    }

    public function actionSettings()
    {
        //Ovde bi bila podesavanja profila obicnog korisnik, update, jezik itd
        if (!User::isUser()) {
            return $this->redirect(['view?id=' .Yii::$app->user->identity->user_id]);
        }
        $model = $this->findModel(Yii::$app->user->identity->user_id);
        $model->scenario = 'userUpdate';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['profile']);
        } else {
            return $this->render('settings', ['data' => Yii::$app->user->identity]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if ((new \backend\models\User)->validateUserDelete($id)) {
            $this->findModel($id)->delete();
            Msg::success('User succesefully deleted!');
            return $this->redirect(['index']);
        } else {
            Msg::warning('This action is not allowed!');
            return $this->redirect(['index']);
        }
    }

    public function actionProfile()
    {
        if (!Yii::$app->user->isGuest) {
            $user = $this->findModel(Yii::$app->user->identity->user_id);
            return $this->render('profile', ['model' => $user]);
        }
        return $this->render('/site/error', ['name' => 'Profile Error', 'message' => 'You have to be logged in to see this page!']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
