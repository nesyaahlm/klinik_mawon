<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixAppointmentsAutoIncrement extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('appointments') || ! $this->db->fieldExists('id', 'appointments')) {
            return;
        }

        $this->forge->modifyColumn('appointments', [
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
        ]);
    }

    public function down()
    {
        // Non-destructive migration for legacy API compatibility.
    }
}
