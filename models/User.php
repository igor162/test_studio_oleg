<?php
/**
 * Created by PhpStorm.
 * User: ig
 * Date: 25.02.19
 * Time: 12:14
 */

namespace app\models;

use Yii;

use yii\db\ActiveRecord;

use yii\behaviors\TimestampBehavior;

use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * Class User
 * @package app\models
 *
 *
 * @property integer $updated_at
 * @property User[] $statusList
 */
class User extends ActiveRecord implements IdentityInterface
{

    /*
     * Пример в бд
     [
            'id' => '1',
            'username' => 'admin',
//            'password' => 'admin',
            'auth_key' => 'xw6XkIK9Rq6mbDdfuB9NRrkboJgcEQt0',
            'password_hash' => '$2y$13$69CbnnCZbhFTFrgB6V8TqummASMfnnOuWJPxInQuA/Ox2dX.evQje',
            'email' => 'admin@admin.com',
            'status' => "Actual",
            'created_at' => time(),
    ],
    [
    'id' => '2',
    'username' => 'demo',
    //            'password' => 'demo',
    'auth_key' => 'zDXON4kLfA_-nlvp_isKo90HJAeFHX5g',
    'password_hash' => '$2y$13$vcBiop.4T7lJLgYxfK8JcevByoAzHkd1T0oK2YBgjZjouW.ISlDeq',
    'email' => 'demo@demo.com',
    'status' => "Actual",
    'created_at' => time(),
    ]
    */

    public  $password,          // Пароль
            $password_repeat;   // Повторный ввод пароля

    /**
     * Статус в системе Пользователя
     */
    const   STATUS_ACTUAL        = 'Actual',     // 'Актуальный'
            STATUS_BLOCKED       = 'Blocked',    // 'Заблокированный'
            STATUS_DELETED       = 'Deleted';    // 'Удаленный'
    /**
     * Сценарии использования модуля "Пользователя"
     */
    const   SCENARIO_UPDATE_USER  = 'update',
            SCENARIO_RESET_PASS   = 'reset',
            SCENARIO_ADD_USER     = 'create',
            SCENARIO_SEARCH_USER  = 'search';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'status' ], 'required', 'on' => self::SCENARIO_UPDATE_USER],
            [['username', 'email', 'status', 'password', 'password_repeat' ], 'required', 'on' => self::SCENARIO_ADD_USER],

            [['id', 'created_at', 'updated_at'], 'integer'],

            [['username', 'email', 'password', 'password_repeat', 'auth_key', 'password_hash', 'password_reset_token'], 'trim'], // обрезать пробелы

            [['password_repeat'], 'compare', 'compareAttribute' => 'password', 'message' => \Yii::t('app','New passwords do not match')],   // проверка на введенные пароли

            [['status',], 'default', 'value' => self::STATUS_ACTUAL ],
            [['status',], 'in', 'range' => array_keys($this->statusList) ],

            [['email'],     'email'],
            [['email'],     'unique', 'targetClass' => User::className(),                                               'message' => \Yii::t('app', 'This «{attribute}» is already in use', ['attribute' => \Yii::t('app', 'E-mail')] )],  // проверка на уникальные «E-mail» в системе
            [['username'],  'unique', 'targetClass' => User::className(),                                               'message' => \Yii::t('app', 'This «{attribute}» is already in use', ['attribute' => \Yii::t('app', 'Username')] )],// проверка на уникальные «Пользователя» в системе
            [['username', 'email'], 'unique', 'targetAttribute' => ['username', 'email'],   'message' => \Yii::t('app', 'This «{attribute}» has been added',    ['attribute' => \Yii::t('app', 'User')] )],    // проверка на уникальные элементы

            [['username', 'email', 'password_hash', 'password_reset_token'],    'string', 'max' => 100],
            [['auth_key'],                                                      'string', 'max' => 32],
            [['username'],                      'string',  'min' => 4, 'max' => 100],
            [['password', 'password_repeat'],   'string',  'min' => 6, 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                    => Yii::t('app', 'ID - User'),
            'username'              => Yii::t('app', 'Login'),
            'email'                 => Yii::t('app', 'E-mail'),
            'confirmed_reg'         => Yii::t('app', 'Confirmed By'),
            'auth_key'              => Yii::t('app', 'Auth Key'),
            'password'              => Yii::t('app', 'Password'),
            'password_repeat'       => Yii::t('app', 'Confirm password'),
            'password_hash'         => Yii::t('app', 'Hash password'),
            'password_reset_token'  => Yii::t('app', 'Token reset password'),
            'status'                => Yii::t('app', 'Status'),
            'created_at'            => Yii::t('app', 'Created At'),
            'updated_at'            => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTUAL]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException(\Yii::t('app', '«{attribute}» is not implemented.', ['attribute' => 'findIdentityByAccessToken']));
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTUAL]);
    }

    /**
     * Статус пользователя в системе
     * @return array
     */
    public function getStatusList()
    {
        return [
            self::STATUS_ACTUAL     => Yii::t('app', self::STATUS_ACTUAL),
            self::STATUS_DELETED    => Yii::t('app', self::STATUS_DELETED),
            self::STATUS_BLOCKED    => Yii::t('app', self::STATUS_BLOCKED),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}