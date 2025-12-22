<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDoctorsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
          'id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
          'name' => ['type'=>'VARCHAR','constraint'=>255],
          'specialty' => ['type'=>'VARCHAR','constraint'=>255],
          'schedule' => ['type'=>'VARCHAR','constraint'=>255, 'null'=>true],
          'photo' => ['type'=>'VARCHAR','constraint'=>255, 'null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('doctors');
    }

    public function down()
    {
        $this->forge->dropTable('doctors');
    }
}
