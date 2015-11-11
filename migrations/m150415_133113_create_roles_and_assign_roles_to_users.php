<?php

use yii\db\Schema;
use yii\db\Migration;
use app\models\users\Users;

class m150415_133113_create_roles_and_assign_roles_to_users extends Migration
{
    public function up()
    {
        $rbac = \Yii::$app->authManager;

        $user = $rbac->createRole('user');
        $user->description = 'Regular site user';
        $rbac->add($user);

        $admin = $rbac->createRole('administrator');
        $admin->description = 'Administrator';
        $rbac->add($admin);

        $rbac->addChild($admin, $user);

        $users = Users::find()->all();

        foreach($users as $one) {

            if($one->id == 1)
                $rbac->assign($admin,$one->id);
            else
                $rbac->assign($user, $one->id);
        }
    }

    public function down()
    {
        echo "m150415_133113_create_roles_and_assign_roles_to_users cannot be reverted.\n";

        return false;
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
