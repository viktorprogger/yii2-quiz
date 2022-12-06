<?php

declare(strict_types=1);

namespace app\modules\poll\infrastruture\entities;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $title
 * @property int $published_from
 * @property int $published_to
 * @property int[] $user_ids
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property QuestionRecord[] $questions
 *
 * @internal Avoid using this class outside of {@see PollRepository}
 */
final class PollRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'polls';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    public function getQuestions(): ActiveQuery
    {
        return $this->hasMany(QuestionRecord::class, ['poll_id' => 'id'])->where(['questions.deleted' => false]);
    }
}
