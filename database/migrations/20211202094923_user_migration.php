<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UserMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('users'); // 创建数据表

        // 添加列
        $table->addColumn(
            'username', 'string', [
                'comment' => '用户名'
            ]
        )->addColumn(
            'password', 'string', [
                'comment' => '密码'
            ]
        )->addIndex(
            ['username'],
            ['unique' => true],
        );

        $table->create();
    }
}
