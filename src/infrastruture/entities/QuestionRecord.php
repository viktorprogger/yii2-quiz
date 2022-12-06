<?php

declare(strict_types=1);

namespace app\modules\poll\infrastruture\entities;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $poll_id
 * @property string $text
 * @property bool $deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property AnswerRecord[] $answers
 * @property PollRecord $poll
 *
 * @internal Avoid using this class outside of {@see PollRepository}
 */
final class QuestionRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'poll_questions';
    }

    public function rules(): array
    {
        return [
            ['poll_id', 'exist', 'targetRelation' => 'poll'],
            ['deleted', 'default', false],
        ];
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    public function getPoll(): ActiveQueryInterface
    {
        return $this->hasOne(PollRecord::class, ['id' => 'poll_id']);
    }

    public function getAnswers(): ActiveQueryInterface
    {
        return $this->hasMany(AnswerRecord::class, ['question_id' => 'id'])->where(['answers.deleted' => false]);
    }
}
