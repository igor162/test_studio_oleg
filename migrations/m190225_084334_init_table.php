<?php

use yii\db\Migration;

use app\models\User;
/**
 * Class m190225_084334_init_table
 */
class m190225_084334_init_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Создать таблицу users
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique()->comment('Логин'),
            'auth_key' => $this->string(32)->notNull()->comment('Ключ авторизации'),
            'password_hash' => $this->string()->notNull()->comment('Хэш пароля'),
            'password_reset_token' => $this->string()->unique()->comment('Токен восстановления'),
            'email' => $this->string()->notNull()->unique()->comment('E-mail пользователя'),

            'status' => "ENUM('Actual','Blocked','Deleted') NOT NULL DEFAULT 'Actual' COMMENT 'Статус \"Пользователя\" в системе'",
            'created_at' => $this->integer()->notNull()->comment('Создано'),
            'updated_at' => $this->integer()->notNull()->comment('Обновлено'),
        ], $tableOptions);

        $user = new User(
            [
                'id' => '1',
                'username' => 'admin',
//            'password' => 'admin',
                'auth_key' => 'admin1234sdfasW#41',
                'password_hash' => '101-$user',
                'email' => '101-token',
                'status' => "Actual",
                'created_at' => time(),
            ]
        );

        $user  = User::findOne(1);
        $user->setPassword('admin');    // Создать хэш пароля
        $user->generateAuthKey();               // Создать ключ авторизации

        $this->insert('{{%user}}',[
            'id' => '1',
            'username' => 'admin',
//            'password' => 'admin',
            'auth_key' => 'xw6XkIK9Rq6mbDdfuB9NRrkboJgcEQt0',
            'password_hash' => '$2y$13$69CbnnCZbhFTFrgB6V8TqummASMfnnOuWJPxInQuA/Ox2dX.evQje',
            'email' => 'admin@admin.com',
            'status' => "Actual",
            'created_at' => time(),
        ]);
        $this->insert('{{%user}}',[
            'id' => '2',
            'username' => 'demo',
//            'password' => 'demo',
            'auth_key' => 'zDXON4kLfA_-nlvp_isKo90HJAeFHX5g',
            'password_hash' => '$2y$13$vcBiop.4T7lJLgYxfK8JcevByoAzHkd1T0oK2YBgjZjouW.ISlDeq',
            'email' => 'demo@demo.com',
            'status' => "Actual",
            'created_at' => time(),
        ]);
/*        // Создать таблицу staff
        $this->createTable('{{%staff}}', [
            'id' => $this->primaryKey()->,
            'username' => $this->string()->notNull()->unique()->comment('Логин'),
            'auth_key' => $this->string(32)->notNull()->comment('Ключ авторизации'),
            'password_hash' => $this->string()->notNull()->comment('Хэш пароля'),
            'password_reset_token' => $this->string()->unique()->comment('Токен восстановления'),
            'email' => $this->string()->notNull()->unique()->comment('E-mail пользователя'),

            'status' => "ENUM('Actual','Blocked','Deleted') NOT NULL DEFAULT 'Actual' COMMENT 'Статус \"Пользователя\" в системе'",
            'created_at' => $this->integer()->notNull()->comment('Создано'),
            'updated_at' => $this->integer()->notNull()->comment('Обновлено'),
        ], $tableOptions);*/
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190225_084334_init_table cannot be reverted.\n";

        return false;
    }
    */
}
