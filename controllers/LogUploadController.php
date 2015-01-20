<?php

namespace app\controllers;

use Yii;
use sintret\gii\models\LogUpload;
use sintret\gii\models\LogUploadSearch;
use sintret\gii\components\Util;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * LogUploadController implements the CRUD actions for LogUpload model.
 */
class LogUploadController extends Controller {
    
    public $userClassName;

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->userClassName === null) {
            $this->userClassName = Yii::$app->getUser()->identityClass;
            $this->userClassName = $this->userClassName ? : 'common\models\User';
        }
    }

    /**
     * Lists all LogUpload models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new LogUploadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Util::logSave(['artist','song']);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LogUpload model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new LogUpload model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new LogUpload();

        if ($model->loadWithFiles(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Well done! successfully to save data!  ');
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing LogUpload model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->loadWithFiles(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Well done! successfully to Update data!  ');
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing LogUpload model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Well done! successfully to deletedc d data!  ');

        return $this->redirect(['index']);
    }

    /**
     * Finds the LogUpload model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LogUpload the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = LogUpload::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSample() {

        //$objPHPExcel = new \PHPExcel();
        $template = Yii::$app->util->templateExcel();
        $model = new LogUpload;
        $date = date('YmdHis');
        $name = $date . LogUpload;
        //$attributes = $model->attributeLabels();
        $models = LogUpload::find()->all();
        $excelChar = Yii::$app->util->excelChar();
        $not = Yii::$app->util->excelNot();

        foreach ($model->attributeLabels() as $k => $v) {
            if (!in_array($k, $not)) {
                $attributes[$k] = $v;
            }
        }

        $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load(Yii::getAlias($template));

        return $this->render('sample', ['models' => $models, 'attributes' => $attributes, 'excelChar' => $excelChar, 'not' => $not, 'name' => $name, 'objPHPExcel' => $objPHPExcel]);
    }

    public function actionParsing() {
        $model = new Upload;

        if (Yii::$app->request->isPost) {
            $model->fileori = UploadedFile::getInstance($model, 'fileori');

            if ($model->validate()) {
                $model->fileori->saveAs(Yii::getAlias(Upload::$imagePath) . $model->fileori->baseName . '.' . $model->fileori->extension);
            }
        }

        return $this->render('parsing', ['model' => $model]);
    }

}
