<?php

namespace app\models\search;

use app\models\Departments;
use app\models\DepStaff;
use app\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Staff;

/**
 * StaffSearch represents the model behind the search form of `app\models\Staff`.
 */
class StaffSearch extends Staff
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'wage', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['first_name', 'last_name', 'patronymic', 'gender', 'departments_data'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
    public function search($params)
    {
        $query = Staff::find()
//            ->joinWith(['depStaff'])
            ->leftJoin( DepStaff::tableName().' as `depStaff` on `depStaff`.`staff_id` = '.Staff::tableName().'.`id`')       // Связи отделов с сотрудниками
            ->leftJoin( Departments::tableName().' as `dep` on `dep`.`id` = `depStaff`.`dep_id`')       // Связь отдела

            ->leftJoin( User::tableName().' as `create`    on `create`   .`id`    = '.Staff::tableName().'.`created_by`')       // Создал
            ->leftJoin( User::tableName().' as `update`    on `update`   .`id`    = '.Staff::tableName().'.`updated_by`');      // Обновил

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 20
            ]
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'id',
                'first_name',
                'last_name',
                'patronymic',
                'gender',
                'wage',
                'created_at',
                'updated_at',
                'departments_data' =>
                    [
                        'asc'  => [ '`dep`.name' => SORT_ASC ],
                        'desc' => [ '`dep`.name' => SORT_DESC ],
                        'default' => SORT_ASC
                    ],
                'created_by' =>
                    [
                        'asc'       => [ 'create.username' => SORT_ASC ],
                        'desc'      => [ 'create.username' => SORT_DESC ],
                        'default'   => SORT_ASC
                    ],
                'updated_by' =>
                    [
                        'asc'       => [ 'update.username' => SORT_ASC ],
                        'desc'      => [ 'update.username' => SORT_DESC ],
                        'default'   => SORT_ASC
                    ],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            Staff::tableName().'.id' => $this->id,
            'wage' => $this->wage,
            'DATE_FORMAT(FROM_UNIXTIME('.Staff::tableName().'.created_at),"%d-%m-%Y")' => $this->created_at,
            'DATE_FORMAT(FROM_UNIXTIME('.Staff::tableName().'.updated_at),"%d-%m-%Y")' => $this->updated_at,
            'gender' => $this->gender,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'patronymic', $this->patronymic])
            ->andFilterWhere(['like', '`dep`.name', $this->departments_data])
            ->andFilterWhere(['like', 'create.username', $this->created_by])
            ->andFilterWhere(['like', 'update.username', $this->updated_by]);

        return $dataProvider;
    }
}
