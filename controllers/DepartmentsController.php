<?php

namespace app\controllers;

use Yii;

use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\bootstrap\ActiveForm;

use kartik\icons\Icon;

use app\models\Departments;
use app\models\search\DepartmentsSearch;

/**
 * DepartmentsController implements the CRUD actions for Departments model.
 */
class DepartmentsController extends Controller
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
     * Lists all Departments models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepartmentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Departments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param null $form
     * @param null $returnUrl
     * @return mixed
     */
    public function actionCreate($form = null, $returnUrl = null)
    {

        $returnUrl = \Yii::$app->request->get('returnUrl', ['index']);
        $model = new Departments();

        // Обработка формы через Ajax запрос
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
            return $this->redirect($returnUrl);
        }

        /** Формирование формы ввода для \yii\bootstrap\Modal */
        if ($form === Departments::FORM_TYPE_AJAX) {
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
     * Updates an existing Departments model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param null $form
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id,$form = null, $returnUrl = null)
    {
        $returnUrl = \Yii::$app->request->get('returnUrl', ['index']);
        $model = $this->findModel($id);

        // Обработка формы через Ajax запрос
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect($returnUrl);
        }

        /** Формирование формы ввода для \yii\bootstrap\Modal */
        if ($form === Departments::FORM_TYPE_AJAX) {
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
     * Deletes an existing Departments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);


        // Запретить удаление отдела, если в нем есть сотрудники
        if(isset($model->countStaff)){
            \Yii::$app->session->setFlash('error',   '<h4>'.\Yii::t('app','Unsuccessful').'</h4><hr class="kv-alert-separator" /><p> '.\Yii::t('app', 'It is forbidden to remove a department with employees').'</p>');
            return $this->redirect(['index']);
        }

        if ($model->delete()) {
            \Yii::$app->session->setFlash('success', '<h4>'.\Yii::t('app','Successful').  '</h4><hr class="kv-alert-separator" /><p> '.\Yii::t('app', 'The data is delete').'</p>');
        } else {
            \Yii::$app->session->setFlash('error',   '<h4>'.\Yii::t('app','Unsuccessful').'</h4><hr class="kv-alert-separator" /><p> '.\Yii::t('app', 'The data is not delete').'</p>');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Departments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Departments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Departments::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
