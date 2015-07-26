<?php
use Phinx\Migration\AbstractMigration;

class Initial extends AbstractMigration
{
    public function up()
    {
        $this->table('uploads')
            ->addColumn('path', 'text')
            ->addColumn('extension', 'string')
            ->addColumn('model', 'string')
            ->addColumn('foreign_key', 'integer')
            ->addIndex(['model', 'foreign_key'])
            ->save();
    }

    public function down()
    {
        $this->dropTable('uploads');
    }
}
