<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppointmentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
          'id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
          'user_id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
          'doctor_id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
          'date' => ['type'=>'DATE'],
          'time' => ['type'=>'VARCHAR','constraint'=>20],
          'status' => ['type'=>'VARCHAR','constraint'=>30, 'default'=>'pending'],
          'payment_method' => ['type'=>'VARCHAR','constraint'=>50, 'null'=>true],
          'payment_proof' => ['type'=>'VARCHAR','constraint'=>255, 'null'=>true],
          'created_at' => ['type'=>'DATETIME','null'=>true],
          'updated_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('doctor_id');
        $this->forge->createTable('appointments');
    }

    public function down()
    {
        $this->forge->dropTable('appointments');
    }
}
