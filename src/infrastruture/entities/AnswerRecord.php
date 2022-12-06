<?php

declare(strict_types=1);

namespace app\modules\poll\infrastruture\entities;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $sort
 * @property bool $can_be_commented
 * @property int $question_id
 * @property string $text
 * @property bool $deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @internal Avoid using this class outside of {@see PollRepository}
 */
final class AnswerRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'poll_questions';
    }

    public function rules(): array
    {
        return [
            ['question_id', 'exist', 'targetRelation' => 'question'],
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

    public function getQuestion(): ActiveQueryInterface
    {
        return $this->hasOne(QuestionRecord::class, ['id' => 'question_id']);
    }
}
