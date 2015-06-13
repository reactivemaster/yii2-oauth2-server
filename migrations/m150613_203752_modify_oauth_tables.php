<?php

use yii\db\Schema;
use yii\db\Migration;

class m150613_203752_modify_oauth_tables extends Migration
{
    public function primaryKey($columns) {
        return 'PRIMARY KEY (' . $this->db->getQueryBuilder()->buildColumns($columns) . ')';
    }

    public function up()
    {
        $transaction = $this->db->beginTransaction();
        try {
            $this->dropTable('{{%oauth_users}}');
            $this->addColumn('{{%oauth_scopes}}', 'id', Schema::TYPE_PK . ' first');
            $this->addColumn('{{%oauth_public_keys}}', 'id', Schema::TYPE_BIGPK . ' first');
            $transaction->commit();
        } catch (Exception $e) {
            echo 'Exception: ' . $e->getMessage() . '\n';
            $transaction->rollback();
            return false;
        }
    }

    public function down()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $transaction = $this->db->beginTransaction();
        try {
            $this->createTable('{{%oauth_users}}', [
                'username' => Schema::TYPE_STRING . '(255) NOT NULL',
                'password' => Schema::TYPE_STRING . '(2000) DEFAULT NULL',
                'first_name' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
                'last_name' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
                $this->primaryKey('username'),
            ], $tableOptions);
            $this->dropColumn('{{%oauth_scopes}}', 'id');
            $this->dropColumn('{{%oauth_public_keys}}', 'id');
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            echo $e->getMessage();
            echo "\n";
            echo get_called_class() . ' cannot be reverted.';
            echo "\n";

            return false;
        }
    }
}
