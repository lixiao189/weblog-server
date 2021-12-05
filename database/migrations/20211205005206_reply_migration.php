<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ReplyMigration extends AbstractMigration
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
        $table = $this->table('replies');

        $table->addColumn(
            'comment_id', 'integer', [
                'comment' => '所回复的评论的 ID'
            ]
        )->addColumn(
            'content', 'string', [
                'comment' => '回复的评论内容'
            ]
        )->addColumn(
            'sender_id', 'integer', [
                'comment' => '该评论的发送者 id'
            ]
        )->addColumn(
            'sender_name', 'string', [
                'comment' => '该评论的的发送者 username'
            ]
        )->addColumn(
            'receiver_id', 'integer', [
                'comment' => '接受者用户 ID',
            ]
        )->addColumn(
            'receiver_name', 'string', [
                'comment' => '接收者 username'
            ]
        )->addTimestamps(
            null, false
        );

        $table->create();
    }
}
