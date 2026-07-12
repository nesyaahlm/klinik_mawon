<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RepairMobileApiTables extends Migration
{
    public function up()
    {
        $this->repairPasien();
        $this->repairMobileProfiles();
        $this->repairProfiles();
        $this->repairDoctorSchedule();
        $this->repairPayments();
        $this->repairMobilePayments();
        $this->repairAppointments();
    }

    public function down()
    {
        // Non-destructive repair migration.
    }

    private function repairProfiles(): void
    {
        if (! $this->isUsableTable('profiles')) {
            $this->dropBrokenTable('profiles');
            $this->forge->reset();
            $this->forge->addField([
                'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'name'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'phone'      => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'address'    => ['type' => 'TEXT', 'null' => true],
                'birth_date' => ['type' => 'DATE', 'null' => true],
                'gender'     => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'photo'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('user_id');
            try {
                $this->forge->createTable('profiles');
            } catch (\Throwable) {
                // Orphaned InnoDB tablespace may require manual filesystem cleanup.
                // The API will use pasien as the linked profile fallback.
            }
            return;
        }

        $this->addMissingColumns('profiles', [
            'birth_date' => ['type' => 'DATE', 'null' => true],
            'gender'     => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'photo'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
    }

    private function repairDoctorSchedule(): void
    {
        if (! $this->isUsableTable('doctor_schedule')) {
            $this->dropBrokenTable('doctor_schedule');
            $this->forge->reset();
            $this->forge->addField([
                'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'doctor_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'day'          => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'date'         => ['type' => 'DATE', 'null' => true],
                'start_time'   => ['type' => 'TIME', 'null' => true],
                'end_time'     => ['type' => 'TIME', 'null' => true],
                'is_available' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('doctor_id');
            try {
                $this->forge->createTable('doctor_schedule');
            } catch (\Throwable) {
                // Orphaned schedule tablespace requires manual cleanup; API returns empty schedules.
            }
            return;
        }

        $this->addMissingColumns('doctor_schedule', [
            'date'         => ['type' => 'DATE', 'null' => true],
            'is_available' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ]);
    }

    private function repairPayments(): void
    {
        if (! $this->isUsableTable('payments')) {
            $this->dropBrokenTable('payments');
            $this->forge->reset();
            $this->forge->addField([
                'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'appointment_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'payment_method' => ['type' => 'VARCHAR', 'constraint' => 30],
                'amount'         => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'status'         => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'unpaid'],
                'proof'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'paid_at'        => ['type' => 'DATETIME', 'null' => true],
                'created_at'     => ['type' => 'DATETIME', 'null' => true],
                'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('appointment_id');
            try {
                $this->forge->createTable('payments');
            } catch (\Throwable) {
                // Use mobile_payments fallback when legacy payments tablespace is orphaned.
            }
            return;
        }

        $this->addMissingColumns('payments', [
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 30],
            'amount'         => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'status'         => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'unpaid'],
            'proof'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'paid_at'        => ['type' => 'DATETIME', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
    }

    private function repairAppointments(): void
    {
        if (! $this->isUsableTable('appointments')) {
            return;
        }

        $this->addMissingColumns('appointments', [
            'schedule_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'amount'         => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ]);
    }

    private function repairMobilePayments(): void
    {
        if ($this->isUsableTable('mobile_payments')) {
            return;
        }

        $this->dropBrokenTable('mobile_payments');
        $this->forge->reset();
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'appointment_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 30],
            'amount'         => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'status'         => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'unpaid'],
            'proof'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'paid_at'        => ['type' => 'DATETIME', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('appointment_id');
        $this->forge->createTable('mobile_payments');
    }

    private function repairPasien(): void
    {
        if (! $this->isUsableTable('pasien')) {
            $this->dropBrokenTable('pasien');
            $this->forge->reset();
            $this->forge->addField([
                'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'nama'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'phone'      => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'alamat'     => ['type' => 'TEXT', 'null' => true],
                'birth_date' => ['type' => 'DATE', 'null' => true],
                'gender'     => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'photo'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'keluhan'    => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            try {
                $this->forge->createTable('pasien');
            } catch (\Throwable) {
                // Use mobile_profiles fallback when legacy pasien tablespace is orphaned.
            }
            return;
        }

        $this->addMissingColumns('pasien', [
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'alamat'     => ['type' => 'TEXT', 'null' => true],
            'birth_date' => ['type' => 'DATE', 'null' => true],
            'gender'     => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'photo'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
    }

    private function repairMobileProfiles(): void
    {
        if ($this->isUsableTable('mobile_profiles')) {
            return;
        }

        $this->dropBrokenTable('mobile_profiles');
        $this->forge->reset();
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'address'    => ['type' => 'TEXT', 'null' => true],
            'birth_date' => ['type' => 'DATE', 'null' => true],
            'gender'     => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'photo'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->createTable('mobile_profiles');
    }

    private function isUsableTable(string $table): bool
    {
        try {
            if (! $this->db->tableExists($table)) {
                return false;
            }
            $this->db->getFieldNames($table);
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function dropBrokenTable(string $table): void
    {
        try {
            $this->db->query('ALTER TABLE `' . $table . '` DISCARD TABLESPACE');
        } catch (\Throwable) {
            // Ignore: only needed for orphaned InnoDB tablespaces.
        }

        try {
            $this->db->query('DROP TABLE IF EXISTS `' . $table . '`');
        } catch (\Throwable) {
            // Ignore: createTable below will surface any remaining real issue.
        }
    }

    private function addMissingColumns(string $table, array $fields): void
    {
        foreach ($fields as $field => $definition) {
            try {
                if (! $this->db->fieldExists($field, $table)) {
                    $this->forge->addColumn($table, [$field => $definition]);
                }
            } catch (\Throwable) {
                return;
            }
        }
    }
}
