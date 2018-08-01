<?php
namespace backend\controllers;

use backend\models\Accomodation;
use backend\models\Emails;
use backend\models\AccomLanguages;
use backend\models\AccomServices;
use backend\models\Overview;
use Yii;

class OverviewController extends AccomController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    public function actionAccomData()
    {
         if (Yii::$app->request->isAjax) {
             $id = Yii::$app->request->post()['id'];
             $lang_id = Yii::$app->request->post()['lang'];
             
             $data = Overview::getAccomodationData($id, $lang_id);
             $emailData = Emails::find()->where(['accomodation_id' => $id])->asArray()->all();
             
             //DEO ZA PREVEDENE VREDNOSTI IZ EMAILA. EMAIL DATA SE TRENUTNO KORISTI U JS
//             $email = Yii::$app->db->createCommand("SELECT a.email, b.parent_id, b.title, b.emails_id "
//                                                . "FROM emails a, emails b "
//                                                . "WHERE a.accomodation_id = :accom_id AND b.lang_id = :lang_id AND a.emails_id = b.parent_id"
//                                                )
//                                                ->bindValues([':accom_id' => $id, ':lang_id' => $lang_id])->queryAll();
             
//             $email = Emails::find()->where(['accomodation_id' => $id, 'lang_id' => $lang_id])->asArray()->all();
              
              
              \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
              return [
                'data' => $data[0],
                'emailData' => $emailData,
                'accomLangs' => Overview::getAccomodationLanguages($id, $lang_id),
                'services' => Overview::getAccomodationServices($id, $lang_id),
//                  'mail' => $email,
//                  'final' => Overview::filterEmailTranslation($email, $emailData)
              ];
         }
         else{
             return $this->redirect('index');
         }
         
    }
    
     /*
     * Saves the accomodation data sent from the ajax request.
     */
    public function actionSaveAccomodationData()
    {
        if (Yii::$app->request->isAjax) {
            $result = [];
            $data = Yii::$app->request->post();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if($data['lang_id'] != 1){
                return ['code' => 222, 'msg' => Yii::t('model/overview', 'edit_translate')];
            }
            $model = Accomodation::findOne(['accomodation_id' => $data['accom_id']]);
            $model = Overview::checkAccomodationData($data, $model);
            if($model[1] == 1){
                $result = Overview::trySaveModel($model[0], 'Succesefully edited!');
            }
            
            $result ['modelData'] = Overview::getAccomodationData($data['accom_id'], $data['lang_id'])[0];
            $result['msg'] = isset($result['msg']) ? $result['msg'] : Yii::t('model/overview', 'no_data_changed');
            return $result;
        }
        else{
             return $this->redirect('index');
         }
    }
    
    /*
     * Deletes the selected social network value
     * @return array
     */
    public function actionDeleteAccomodationSocialData()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $model = Accomodation::findOne(['accomodation_id' => $data['accom_id']]);
            $model = Overview::detectAccomodationSocialDelete($data['delete'], $model);
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return Overview::trySaveModel($model, 'Successfully deleted!');
        }
    }
    
    /*
     * Edits the email for the current accomodation
     * @param $id of the specific email row
     */
    public function actionAccomEmailEdit()
    {
         if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $model = Emails::findOne(['emails_id' => $data['id']]);
            if(!empty($model))
            {
                $model->email = $data['email'];
                return $model->save() ? 111 : 222;
            }
            else{
                return 222;
            }
         }
         else{
             return $this->redirect('index');
         }
    }
    
    /*
     * Deletes the email for the current acoomodation
     * If there is a translation, it can't be deleted
     */
    public function actionAccomEmailDelete()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $model = Emails::findOne(['emails_id' => $data['id']]);
            $children = Emails::find()->where(['parent_id' => $data['id']])->count();
            if($children == 0){
               \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
               return Overview::accomEmailDelete($model);
            }
            else
            {
                return Yii::t('model/overview', "has_translation");
            }
         }
         else{
             return $this->redirect('index');
         }
    }
    
    /*
     * Adds a new email for the accomodation
     * If there are 5 emails in the table, you can't add more.
     * @param $accom_id current accomodation id
     * @param $title email title
     * @param $email email
     */
    public function actionAccomEmailNew()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $accomEmailinDb = Emails::find()->select('COUNT(*)')->where(['accomodation_id' => $data['accom_id']])->count();
            $msg = '';
            $insertId = 0;
            if($accomEmailinDb >= 5)
            {
                $msg =  Yii::t('model/overview', 'too_much_emails');
            }
            else
            {
                $newEmail = Overview::accomNewEmail($data);
                $msg = $newEmail[0];
                $insertId = $newEmail[1];
            }
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['msg' => $msg, 'key' => $insertId];
         }
         else{
             return $this->redirect('index');
         }
    }
    
    /*
     * Deletes a lang for the current accomodation
     * If there is only one language in the database you can't delete it
     * If the language is the default, another default language is selected and the old value is deleted
     * @param id of accomodation
     */
    public function actionAccomLangsDelete()
    {
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post()['id'];
            $model = AccomLanguages::findOne(['accom_languages_id' => $id]);
            $accom_id = $model->accomodation_id;
            $langsInDb = AccomLanguages::find()->where(['accomodation_id' => $accom_id])->count();
            
            if($langsInDb == 1)
            {
                return Yii::t('model/overview', "There has to be at least one language!");
            }
            
            if($model->default_lang_id == 1)
            {
                $newDefaultLanguage = AccomLanguages::find()->where(['accomodation_id' => $accom_id])->andWhere(['<>', 'accom_languages_id', $id])->one();
                $newDefaultLanguage->default_lang_id = 1;
                
                return Overview::trySaveNewDefaultLang($newDefaultLanguage, $model);
            }
            else{
                $model->delete();
                return Yii::t('model/overview', 'Language Deleted.');
            }
        }
         else{
             return $this->redirect('index');
         }
    }
    
    /*
     * Tries to add a service for the current accomodation OR to delete an existing service
     */
    public function actionAccomServicesChange()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $service = AccomServices::findOne(['services_id' => $data['id'], 'accomodation_id' => $data['accom_id']]);
            if(!empty($service))
            { 
                return Overview::tryDeleteService($service);
            }
            else{
                return Overview::tryAddService($data);
            }
        }
         else{
             return $this->redirect('index');
         }
    }
    
}
