<?php

namespace sintret\gii\controllers;

use Yii;
use sintret\gii\models\Notification;
use sintret\gii\models\NotificationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use sintret\gii\models\LogUpload;

/**
 * NotificationController implements the CRUD actions for Notification model.
 */
class NotificationController extends Controller {
    public $userClassName;

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'sample'],
                        'roles' => ['viewer']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'create', 'parsing'],
                        'roles' => ['editor']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['admin']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['admin']
                    ],
                ],
            ],
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
     * Lists all Notification models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new NotificationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Notification model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Notification model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Notification();

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
     * Updates an existing Notification model.
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
     * Deletes an existing Notification model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Well done! successfully to deleted data!  ');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Notification model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Notification the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Notification::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSample() {

        //$objPHPExcel = new \PHPExcel();
        $template = Yii::$app->util->templateExcel();
        $model = new Notification;
        $date = date('YmdHis');
        $name = $date . Notification;
        //$attributes = $model->attributeLabels();
        $models = Notification::find()->all();
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
        $model = new LogUpload;
        $date = date('Ymdhis') . Yii::$app->user->identity->id;

        if (Yii::$app->request->isPost) {
            $model->fileori = UploadedFile::getInstance($model, 'fileori');

            if ($model->validate()) {
                $fileOri = Yii::getAlias(LogUpload::$imagePath) . $model->fileori->baseName . '.' . $model->fileori->extension;
                $filename = Yii::getAlias(LogUpload::$imagePath) . $date . '.' . $model->fileori->extension;
                $model->fileori->saveAs($filename);
            }
            $params = Yii::$app->util->excelParsing(Yii::getAlias($filename));
            $model->params = \yii\helpers\Json::encode($params);

            $top = ['userUpdate', 'userCreate', 'createDate'];
            $now = date('Y-m-d H:i:s');
            $userId = Yii::$app->user->identity->id;

            $num = 0;
            $fields = [];
            $values = [];
            if ($params)
                foreach ($params as $v) {
                    foreach ($v as $key => $val) {
                        if ($num == 0) {
                            $fields[$key] = $val;
                            $max = $key;
                        }

                        if ($num >= 3) {
                            $values[$num] = $v;
                            if ($key == 8) {
                                $values[$num] = $v;
                                $values[$num][$max + 1] = $userId;
                                $values[$num][$max + 2] = $userId;
                                $values[$num][$max + 3] = $now;
                            }
                        }
                    }
                    $num++;
                }
            $connection = Yii::$app->db;
            $connection->createCommand()->batchInsert('notification', array_merge($fields, $top), $values)->execute();

            $model->title = 'notification';
            $model->fileori = $fileOri;
            $model->filename = $filename;

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Well done! successfully to Parsing data, see log on log upload menu!  ');
            }
        }

        return $this->render('parsing', ['model' => $model]);
    }

}
