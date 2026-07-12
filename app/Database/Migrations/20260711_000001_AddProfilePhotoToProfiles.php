<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfilePhotoToProfiles extends Migration
{
    public function up()
    {
        // Only add the photo column if the profiles table already exists.
        if ($this->db->tableExists('profiles')) {
            try {
                // add photo column if not exists
                if (! $this->db->fieldExists('photo', 'profiles')) {
                    $fields = [
                        'photo' => [
                            'type' => 'VARCHAR',
                            'constraint' => 255,
                            'null' => true,
                        ],
                    ];
                    $this->forge->addColumn('profiles', $fields);
                }
            } catch (\Throwable $e) {
                // Could not inspect columns (table might be corrupted or engine missing)
                // Skip altering the table to avoid migration failure.
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('profiles')) {
            try {
                if ($this->db->fieldExists('photo', 'profiles')) {
                    $this->forge->dropColumn('profiles', 'photo');
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }
}
