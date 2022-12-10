<?php

declare(strict_types=1);

namespace app\modules\poll\infrastruture\entities;

use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $user_id
 * @property int $license_id
 * @property int $poll_id
 * @property bool $rejection
 *
 * @property PollRecord $poll
 */
final class ClientAnswerRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'poll_client_answers';
    }

    public function rules(): array
    {
        return [
            ['user_id', 'unique', 'targetAttribute' => ['user_id', 'license_id', 'poll_id']],
            ['rejection', 'default', 'value' => false],
            ['poll_id', 'exist', 'targetRelation' => 'poll'],
        ];
    }

    public function getPoll(): ActiveQueryInterface
    {
        return $this->hasOne(PollRecord::class, ['id' => 'poll_id']);
    }
}
