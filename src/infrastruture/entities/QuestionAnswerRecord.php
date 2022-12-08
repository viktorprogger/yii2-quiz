<?php

declare(strict_types=1);

namespace app\modules\poll\infrastruture\entities;

use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $question_id
 * @property int $answer_id
 * @property int $client_answer_id
 *
 * @property QuestionRecord $question
 * @property AnswerRecord $answer
 * @property ClientAnswerRecord $clientAnswer
 *
 * @property PollRecord $poll
 *
 */
final class QuestionAnswerRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'poll_client_question_answers';
    }

    public function rules(): array
    {
        return [
            ['question_id', 'exist', 'targetRelation' => 'question'],
            ['answer_id', 'exist', 'targetRelation' => 'answer'],
            ['client_answer_id', 'exist', 'targetRelation' => 'clientAnswer'],
            ['question_id', 'unique', 'targetAttribute' => ['question_id', 'client_answer_id']],
        ];
    }

    public function getQuestion(): ActiveQueryInterface
    {
        return $this->hasOne(QuestionRecord::class, ['id' => 'question_id']);
    }

    public function getAnswer(): ActiveQueryInterface
    {
        return $this->hasOne(AnswerRecord::class, ['id' => 'question_id']);
    }

    public function getClientAnswerRecord(): ActiveQueryInterface
    {
        return $this->hasOne(ClientAnswerRecord::class, ['id' => 'question_id']);
    }
}
