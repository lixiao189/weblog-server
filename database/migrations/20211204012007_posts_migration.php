<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PostsMigration extends AbstractMigration
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
        $table = $this->table('posts');

        $table->addColumn(
            'sender_id', 'integer', [
                'comment' => '发送人的 ID'
            ]
        )->addColumn(
            'sender_name', 'string', [
                'comment' => '发送人的用户名',
            ]
        )->addColumn(
            'title', 'string', [
                'comment' => '帖子的标题',
            ]
        )->addColumn(
            'content', 'string', [
                'comment' => '帖子的内容',
            ]
        )->addTimestamps( // 添加 created_at 时间戳
            null, false
        );

        $table->create();
    }
}
