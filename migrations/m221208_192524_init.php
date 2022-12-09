<?php

use yii\db\Migration;

/**
 * Class m221208_192524_init
 */
class m221208_192524_init extends Migration
{
    public function up()
    {
        $this->createTable(
            'polls',
            [
                'id' => $this->primaryKey(),
                'title' => $this->string()->notNull(),
                'published_from' => $this->integer()->notNull(),
                'published_to' => $this->integer()->notNull(),
                'user_ids' => 'JSON NOT NULL',
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
                'created_by' => $this->integer()->notNull(),
                'updated_by' => $this->integer()->notNull(),
            ]
        );
        $this->createIndex('idx_published_from__published_to', 'polls', ['published_from', 'published_to']);

        $this->createTable(
            'poll_questions',
            [
                'id' => $this->primaryKey(),
                'poll_id' => $this->integer()->notNull(),
                'text' => $this->text()->notNull(),
                'deleted' => $this->tinyInteger()->notNull(),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
                'created_by' => $this->integer()->notNull(),
                'updated_by' => $this->integer()->notNull(),
            ]
        );
        $this->createIndex('idx_poll_id__deleted', 'poll_questions', ['poll_id', 'deleted']);

        $this->createTable(
            'poll_answers',
            [
                'id' => $this->primaryKey(),
                'sort' => $this->integer()->notNull(),
                'can_be_commented' => $this->tinyInteger()->notNull(),
                'question_id' => $this->integer()->notNull(),
                'text' => $this->integer()->notNull(),
                'deleted' => $this->tinyInteger()->notNull(),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
                'created_by' => $this->integer()->notNull(),
                'updated_by' => $this->integer()->notNull(),
            ]
        );
        $this->createIndex('idx_question_id__deleted', 'poll_answers', ['question_id', 'deleted']);

        $this->createTable(
            'poll_client_answers',
            [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer()->notNull(),
                'license_id' => $this->integer()->notNull(),
                'poll_id' => $this->integer()->notNull(),
                'rejection' => $this->tinyInteger()->notNull(),
            ],
        );
        $this->createIndex('idx_user_id__poll_id', 'poll_client_answers', ['poll_id', 'user_id']);

        $this->createTable(
            'poll_client_question_answers',
            [
                'id' => $this->primaryKey(),
                'question_id' => $this->integer()->notNull(),
                'answer_id' => $this->integer()->notNull(),
                'client_answer_id' => $this->integer()->notNull(),
            ]
        );
        $this->createIndex('idx_client_answer_id', 'poll_client_question_answers', ['client_answer_id']);
    }

    public function down()
    {
        $this->dropTable('polls');
        $this->dropTable('poll_questions');
        $this->dropTable('poll_answers');
        $this->dropTable('poll_client_answers');
        $this->dropTable('poll_client_question_answers');
    }
}
