<?php

namespace sintret\gii\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use sintret\gii\models\LogUpload;

/**
 * LogUploadSearch represents the model behind the search form about `app\models\LogUpload`.
 */
class LogUploadSearch extends LogUpload {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'userCreate', 'userUpdate'], 'integer'],
            [['title', 'filename', 'fileori', 'params', 'warning', 'type', 'updateDate', 'createDate'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = LogUpload::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'userCreate' => $this->userCreate,
            'userUpdate' => $this->userUpdate,
            'type' => $this->type
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'filename', $this->filename])
                ->andFilterWhere(['like', 'fileori', $this->fileori])
                ->andFilterWhere(['like', 'warning', $this->warning])
                ->andFilterWhere(['like', 'updateDate', $this->updateDate])
                ->andFilterWhere(['like', 'createDate', $this->createDate])
                ->andFilterWhere(['like', 'params', $this->params]);

        return $dataProvider;
    }

}
