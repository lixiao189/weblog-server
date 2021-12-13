<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CommentsMigration extends AbstractMigration
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
        $table = $this->table('comments');

        $table->addColumn(
            'post_id', 'integer', [
                'comment' => '所评论的帖子的 ID'
            ]
        )->addColumn(
            'content', 'string', [
                'comment' => '帖子的评论内容'
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
            'at_id', 'integer', [
                'comment' => '被 At 的用户的 ID',
                'null' => true,
            ]
        )->addColumn(
            'at_name', 'string', [
                'comment' => '被 At 的用户的用户名',
                'null' => true,
            ]
        )->addTimestamps(
            null, false
        )->addIndex(['created_at'], [
            'order' => ['created_at' => 'DESC']
        ]);

        $table->create();
    }
}
