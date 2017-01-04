<?php

use yii\db\Migration;
use app\models\countries\Countries;
use app\migratemodels\Country;

class m170104_091353_populate_new_countries_table extends Migration
{
    public function up()
    {
        $oldCountries = Countries::find()->all();

        foreach ($oldCountries as $one)
        {
            $newCountry = new Country();
            $newCountry->id = $one->id;
            $newCountry->country = $one->country;
            $newCountry->save(false);
        }
    }

    public function down()
    {
        $this->dropTable('{{%country}}');
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
