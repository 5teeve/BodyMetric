<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRegimesAchetesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'regime_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'prix_paye' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
            ],
            'date_achat' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'duree_jours' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 30,
            ],
            'date_fin' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['actif', 'termine', 'annule'],
                'default' => 'actif',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('regime_id');
        $this->forge->addKey(['user_id', 'regime_id']);
        $this->forge->addUniqueKey(['user_id', 'regime_id']);

        // Foreign keys
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('regime_id', 'regimes', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('regimes_achetes');
    }

    public function down()
    {
        $this->forge->dropTable('regimes_achetes');
    }
}
