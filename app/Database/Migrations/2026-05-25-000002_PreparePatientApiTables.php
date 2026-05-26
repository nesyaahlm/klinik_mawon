<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PreparePatientApiTables extends Migration
{
    public function up()
    {
        $this->prepareDoctors();
        $this->prepareAppointments();
        $this->prepareProfiles();
        $this->prepareDoctorSchedule();
    }

    public function down()
    {
        // This migration may extend tables already used by the web application.
        // Its rollback is intentionally non-destructive.
    }

    private function prepareDoctors(): void
    {
        if (! $this->db->tableExists('doctors')) {
            $this->forge->addField([
                'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'name'           => ['type' => 'VARCHAR', 'constraint' => 255],
                'specialization' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'phone'          => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'email'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'photo'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'schedule'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'created_at'     => ['type' => 'DATETIME', 'null' => true],
                'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('doctors');

            return;
        }

        $this->addMissingColumns('doctors', [
            'specialization' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'phone'          => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'photo'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'schedule'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
    }

    private function prepareAppointments(): void
    {
        if (! $this->db->tableExists('appointments')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'doctor_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'date'        => ['type' => 'DATE'],
                'time'        => ['type' => 'VARCHAR', 'constraint' => 20],
                'keluhan'     => ['type' => 'TEXT', 'null' => true],
                'status'      => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'waiting'],
                'no_antrian'  => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
                'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->addKey('doctor_id');
            $this->forge->createTable('appointments');

            return;
        }

        $this->addMissingColumns('appointments', [
            'keluhan'    => ['type' => 'TEXT', 'null' => true],
            'no_antrian' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ]);
    }

    private function prepareProfiles(): void
    {
        if ($this->db->tableExists('profiles')) {
            return;
        }

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'address'    => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->createTable('profiles');
    }

    private function prepareDoctorSchedule(): void
    {
        if ($this->db->tableExists('doctor_schedule')) {
            return;
        }

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'doctor_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'day'        => ['type' => 'VARCHAR', 'constraint' => 20],
            'start_time' => ['type' => 'TIME'],
            'end_time'   => ['type' => 'TIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('doctor_id');
        $this->forge->createTable('doctor_schedule');
    }

    private function addMissingColumns(string $table, array $fields): void
    {
        foreach ($fields as $field => $definition) {
            if (! $this->db->fieldExists($field, $table)) {
                $this->forge->addColumn($table, [$field => $definition]);
            }
        }
    }
}
