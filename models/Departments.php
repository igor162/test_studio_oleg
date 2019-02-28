<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "departments".
 *
 * @property int $id
 * @property string $name Название отдела
 * @property int $created_at Создано
 * @property int $created_by Создал
 * @property int $updated_at Обновил
 * @property int $updated_by Обновлено
 *
 * @property DepStaff $maxWage
 * @property DepStaff $countStaff
 * @property DepStaff[] $depStaff
 * @property User $createdBy
 * @property User $updatedBy
 */
class Departments extends \yii\db\ActiveRecord
{

    const FORM_TYPE_AJAX = 'ajaxForm'; // Вид формы при отправки

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'departments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 45],

            [['name'], 'unique', 'targetAttribute' => ['name'],   'message' => \Yii::t('app', 'This «{attribute}» has been added',    ['attribute' => \Yii::t('app', 'Department')] )],    // проверка на уникальные элементы

            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'count_staff' => Yii::t('app', 'Number of employees'),
            'max_wage' => Yii::t('app', 'Max wage'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }


    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findByAll()
    {
        $query = self::find()->all();

        return $query;

    }

    /**
     * Вернуть кол-во сотрудников в отделе
     * @return mixed
     */
    public function getCountStaff()
    {

        $data = Departments::find()
            ->select(['COUNT(dep_staff.staff_id) AS count_staff'])
            ->innerJoin('dep_staff', 'dep_staff.dep_id = departments.id')->where(['departments.id' => $this->id])
            ->groupBy('departments.id')
            ->asArray()->one();

        return $data['count_staff'];

    }

    /**
     * Вернуть максимальную заработная плату в отделе
     * @return mixed
     */
    public function getMaxWage()
    {

        $data = Departments::find()
            ->select(['MAX(st.wage) AS max_wage'])

            ->innerJoin( DepStaff::tableName().' as `depStaff` on `depStaff`.`dep_id` = '.Departments::tableName().'.`id`') // Связь с таблицей отделов сотрудника
            ->innerJoin( Staff::tableName().' as `st` on `st`.`id` = depStaff.`staff_id`') // Связь с таблицей сотрудников

            ->where([Departments::tableName().'.id' => $this->id])
            ->groupBy('depStaff.dep_id')
            ->asArray()->one();

        return $data['max_wage'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepStaff()
    {
        return $this->hasMany(DepStaff::className(), ['dep_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}
