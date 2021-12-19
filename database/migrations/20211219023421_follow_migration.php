<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class FollowMigration extends AbstractMigration
{
    /**
     * 用户关注列表
     */
    public function change(): void
    {
        $table = $this->table('follow'); // 创建数据表

        // 添加列
        $table->addColumn(
            'user_id', 'integer', [
                'comment' => '用户名'
            ]
        )->addColumn(
            'follower_id', 'integer', [
                'comment' => '该用户对应的关注者'
            ]
        )->addIndex(['user_id'])->addIndex(['follower_id']);

        $table->create();
    }
}
