<?php

namespace app\controllers;

use app\models\DepStaff;
use Yii;

use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\bootstrap\ActiveForm;

use app\models\Staff;
use app\models\search\StaffSearch;



/**
 * StaffController implements the CRUD actions for Staff model.
 */
class StaffController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Staff models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StaffSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Creates a new Staff model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param null $form
     * @return mixed
     */
    public function actionCreate($form = null)
    {
        $returnUrl = \Yii::$app->request->get('returnUrl', ['index']);

        $model = new Staff();

        // Обработка формы через Ajax запрос
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($valid = $model->validate()) {

                // Начало транзакции для SQL запроса
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save()) {
                        if(!$model->updateDepartment()) {
                            $transaction->rollBack();
                            throw new \Exception(\Yii::t('app', 'Failed to save «{attribute}»', ['attribute' => \Yii::t('app', 'communication with departments')]));   // Присвоение ошибки
                        }

                        \Yii::$app->session->setFlash('success', '<h4>' .\Yii::t('app', 'Successful') . '</h4><hr class="kv-alert-separator" /><p> ' . \Yii::t('app', 'The data is saved') . '.</p>');
                        $transaction->commit();
                        return $this->redirect($returnUrl);
                    }

                    $transaction->rollBack();
                    throw new \Exception(\Yii::t('app', 'When processing your request an error occurred.'));   // Присвоение ошибки

                } catch (\Exception $e) {
                    \Yii::$app->session->setFlash('error', '<h4>' .  \Yii::t('app', 'Unsuccessful') . '</h4><hr class="kv-alert-separator" /><p> ' . $e->getMessage(). '.</p>');
                    $transaction->rollBack();
                    return $this->redirect($returnUrl);
                }

            }

        }

        /** Формирование формы ввода для \yii\bootstrap\Modal */
        if ($form === Staff::FORM_TYPE_AJAX) {
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        }
        /** Формирование формы ввода для POST or GET */
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Staff model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param null $form
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id,$form = null)
    {

        $returnUrl = \Yii::$app->request->get('returnUrl', ['index']);
        $model = $this->findModel($id);

        // Присвоение свзанных отделов к сотруднику
        $model->departments_data  = !empty($model->depStaff) ? ArrayHelper::getColumn($model->depStaff, 'dep_id') : null;

        $odlDepartments = $model->departments_data; // Массив прошлых связей с отделами сотрудника

        // Обработка формы через Ajax запрос
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {

            $postDepartments = $model->departments_data; // Массив связей с отделами сотрудника переданных через POST
            $deleteIdDepartments = !empty($odlDepartments) ? array_diff($odlDepartments, $postDepartments) : null; // Массив для удаления связей с отделами сотрудника
            $newIdDepartments = !empty($odlDepartments) ? array_diff($postDepartments, $odlDepartments) : $model->departments_data; // Массив новых связей с отделами сотрудника

            if ($valid = $model->validate()) {

                // Начало транзакции для SQL запроса
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    // Удаление связей с отделами сотрудника
                    if (!empty($deleteIdDepartments)) {
                        if (!($del = DepStaff::deleteAll(['staff_id' => $model->id, 'dep_id' => $deleteIdDepartments]))) {
                            $transaction->rollBack();
                            throw new \Exception(\Yii::t('app', 'Failed to remove «{attribute}»', ['attribute' => \Yii::t('app', 'communication with departments')]));
                        }
                    }

                    // Обновление новых связей с отделами сотрудника
                    if(!empty($newIdDepartments)) {
                        $model->departments_data  = $newIdDepartments; // Перенос связей в модель

                        if(!$model->updateDepartment()) {
                            $transaction->rollBack();
                            throw new \Exception(\Yii::t('app', 'Failed to save «{attribute}»', ['attribute' => \Yii::t('app', 'communication with departments')]));   // Присвоение ошибки
                        }
                    }

                    // Обновление данных сотрудника
                    if(!$model->save()){
                        $transaction->rollBack();
                        throw new \Exception(\Yii::t('app', 'When processing your request an error occurred.'));   // Присвоение ошибки
                    }

                    \Yii::$app->session->setFlash('success', '<h4>' .\Yii::t('app', 'Successful') . '</h4><hr class="kv-alert-separator" /><p> ' . \Yii::t('app', 'The data is saved') . '.</p>');
                    $transaction->commit();
                    return $this->redirect($returnUrl);

                } catch (\Exception $e) {
                    \Yii::$app->session->setFlash('error', '<h4>' .  \Yii::t('app', 'Unsuccessful') . '</h4><hr class="kv-alert-separator" /><p> ' . $e->getMessage(). '.</p>');
                    $transaction->rollBack();
                    return $this->redirect($returnUrl);
                }
            }
        }


        /** Формирование формы ввода для \yii\bootstrap\Modal */
        if ($form === Staff::FORM_TYPE_AJAX) {
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        }
        /** Формирование формы ввода для POST or GET */
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Staff model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->delete()) {
            \Yii::$app->session->setFlash('success', '<h4>'.\Yii::t('app','Successful').  '</h4><hr class="kv-alert-separator" /><p> '.\Yii::t('app', 'The data is delete').'</p>');
        } else {
            \Yii::$app->session->setFlash('error',   '<h4>'.\Yii::t('app','Unsuccessful').'</h4><hr class="kv-alert-separator" /><p> '.\Yii::t('app', 'The data is not delete').'</p>');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Staff model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Staff the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Staff::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
