<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateRegimeActiviteTable extends Migration
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
            'regime_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'activite_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['regime_id', 'activite_id']);
        $this->forge->addUniqueKey(['regime_id', 'activite_id']);

        $this->forge->addForeignKey('regime_id', 'regimes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('activite_id', 'activites', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('regime_activite');
    }

    public function down()
    {
        $this->forge->dropTable('regime_activite');
    }
}
