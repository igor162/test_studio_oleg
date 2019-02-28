<?php

namespace app\models;

use app\widgets\Helper;
use Yii;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;


/**
 * This is the model class for table "staff".
 *
 * @property int $id
 * @property string $first_name Имя
 * @property string $last_name Фамиля
 * @property string $patronymic Отчество
 * @property string $gender Пол
 * @property array $departments_data массив отделов
 * @property int $wage Заработная плата
 * @property int $created_at Создано
 * @property int $created_by Создал
 * @property int $updated_at Обновил
 * @property int $updated_by Обновлено
 * @property string $full_name


 *
// * @property Staff $fullName
 * @property DepStaff[] $depStaff
 * @property Staff[] $departmentsList
 * @property Staff $departmentsString
 * @property Staff[] $genderList
 * @property Staff $genderOne
 * @property User $createdBy
 * @property User $updatedBy
 */
class Staff extends \yii\db\ActiveRecord
{

    public $departments_data; // Массив выбранный отделов
    public $full_name; // ФИО


    const FORM_TYPE_AJAX = 'ajaxForm'; // Вид формы при отправки

    /**
     * Гендорная принадлежность
     */
    const   GENDER_MALE     = 'Male',       // Мужской
            GENDER_FEMALE   = 'Female';     // Женский

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%staff}}';
    }

    /**
     * Действие при Создании/Изменении в базе
     * @inheritdoc
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' =>
                    [
                        ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                        ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                    ],
                'value' => time(),  // Атрибут типа даты
            ],
            [
                'class' => BlameableBehavior::className(),
                'attributes' =>
                    [
                        ActiveRecord::EVENT_BEFORE_INSERT => ['created_by'],
                        ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_by'],
                    ],
                'value' => empty( \Yii::$app->user->id) ? NULL : \Yii::$app->user->identity->getId(), // Атрибут пользователя
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name','last_name','patronymic','gender','wage'], 'required'],
            [['departments_data'], 'required', 'message' => Yii::t('app', 'Select at least one department' )],

            [['first_name','last_name','patronymic','gender',], 'unique', 'targetAttribute' => ['first_name','last_name','patronymic','gender',],   'message' => \Yii::t('app', 'This «{attribute}» has been added',    ['attribute' => \Yii::t('app', 'Employee')] )],    // проверка на уникальные элементы

            [['gender'], 'in', 'range' => array_keys($this->genderList)],

            [['wage', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['first_name', 'last_name', 'patronymic'], 'string', 'max' => 45],

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
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'patronymic' => Yii::t('app', 'Patronymic'),
            'gender' => Yii::t('app', 'Gender'),
            'wage' => Yii::t('app', 'Wage'),
            'departments_data' => Yii::t('app', 'Departments'),
            'fullName'          => Yii::t('app', 'Full name'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function findByAll()
    {
        $query = self::find()->all()

            ;
        return $query;

    }

    /**
     * Функция вывода полного имени пользователя
     * @return string
     */
    public function getFullName()
    {
        return empty($this->last_name) && empty($this->first_name) && empty($this->patronymic) ? null : Html::encode($this->last_name.' '.$this->first_name.' '.$this->patronymic);
    }

    /**
     * Обновление отделов сотрудника
     * @return bool
     */
    public function updateDepartment()
    {
        // Поиск отделов согласно поступившему массиву выбранных отделов
       $departments = Departments::findAll($this->departments_data);

        foreach ($departments as $vl) {

            try {
                $dep_staff = New DepStaff();
                $dep_staff->staff_id = $this->id;
                $dep_staff->dep_id = $vl->id;

                // Проверка на сохранение данных в базу
                if(!$dep_staff->save()){ throw new \Exception('Error Save DepStaff'); }

            } catch (\Exception $ex) { return false; }
        }

        return true;
    }

    /**
     * Вывод отдел(ов) в котором(ых) работает сотрудник
     * @return null|string
     */
    public function getDepartmentsString()
    {
        if(!empty($depStaff = $this->depStaff)){
            $html = null;

            // Классы для цвета кнопок
            $color = [
              'btn-success',
              'btn-warning',
              'btn-danger',
              'btn-info',
              'btn-primary',
            ];

            $counter = 0; // Счетчик позиций цвета кнопки
            foreach ($depStaff as $val){
                if($counter > count($color)){ $counter = 0;} // Обнулить счетчик
                    $html .= Html::a('#' . Html::encode($val->dep->name), ['/departments', 'id' => Html::encode($val->id), 'returnUrl' => Helper::getReturnUrl()], ['class'=> 'label '. $color[$counter],'title' => \Yii::t('app', 'Change a «{attribute}»', ['attribute' => Yii::t('app', 'Department')]), ]).' ';
                $counter++;
            }
            return $html ;

        }
    }

    /**
     * Вывод отдел(ов) в котором(ых) работает сотрудник
     * @return null|string
     */

    /**
     * @return array|bool
     */
    public static function convertTab()
    {
        $arrayStaff = self::findByAll();
        $arrayDepartments = Departments::findByAll();

        if(empty($arrayStaff)) return false;
        if(empty($arrayDepartments)) return false;

        $m = ArrayHelper::getColumn($arrayDepartments,'id'); // Массив с данными отделами
        array_unshift($m, null); // Добавить в начало массива пустое значение

        $colName = array(); // Массив с названием колонок
        $colID = array(); // Массив с ID колонок

        // Заполнение массива
        foreach ($arrayDepartments as $val)
        {
            $colID[] = $val->id;
            $colName[] = $val->name;
        }
        // Добавить в начало массива пустое значение для первой колонки таблицы
        array_unshift($colName, '#');
        array_unshift($colID, null);


        // Создание массива с колонками таблицы, где первый элемент - это Отделы
        $data = array($colName);

        $i = 1; // id строки

        foreach ($arrayStaff as $val){
            $data[$i] = [ $val->fullName ];


            // Проверка на наличие связей с отделами сотрудника
            if(!empty($val->depStaff)){

                $dep = array(); // Массив связей с отделами

                // Поиск связей с отделами сотрудника
                foreach ($val->depStaff as $valDep){
                    $depId = $valDep->dep_id;
                    $key = array_search($depId, $colID);
                    $dep[$key] = true; // Записать найденную связь
                }

                // Отдельное формирование массива строки таблицы
                foreach ($colID as $id => $vl){
                    if($id === 0){
                        $dep[$id] = $data[$i][$id]; // Записать ФИО сотрудника в первую ячейку
                        continue;
                    }

                    if(isset($dep[$id])){
                        $dep[$id] = true; // Записать найденной связи с отделом сотрудника
                        continue;
                    }
                    $dep[$id] = false; // Записать отсутствующей связи с отделом сотрудника
                }

                ksort($dep); // Сортировка массива по ключам
                $data[$i] = $dep; // Запись всех колонок таблицы в строку
            }else{
                // Запись пустых значений в массив, при отсутствии отделов у сотрудника
                foreach ($colID as $id => $vl){
                    if($id === 0) continue;
                    $data[$i][$id] = false;
                }
            }
            $i++;
        }

        return $data;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepStaff()
    {
        return $this->hasMany(DepStaff::className(), ['staff_id' => 'id']);
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

    /**
     * Список отделов
     * @return array
     */
    public function getDepartmentsList()
    {
        return ArrayHelper::map(\app\models\Departments::find()->all(), 'id', 'name');
    }

    /**
     * Список гендорной принадлежности
     * @return array
     */
    public function getGenderList()
    {
        return [
            self::GENDER_MALE   => Yii::t('app', self::GENDER_MALE),
            self::GENDER_FEMALE => Yii::t('app', self::GENDER_FEMALE),
        ];
    }

    /**
     * Найти гендорную принадлежность сотрудника из массива
     * @return Staff|string
     */
    public function getGenderOne()
    {
        $data = $this->genderList;
        return isset($data[$this->gender]) ? $data[$this->gender] : '---';
    }
}
