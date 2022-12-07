<?php

declare(strict_types=1);

namespace app\modules\poll\infrastruture\entities;

use app\modules\poll\domain\entities\clientAnswer\ClientAnswerChange;
use app\modules\poll\domain\entities\exceptions\DomainDataCorruptionException;
use app\modules\poll\domain\entities\exceptions\EntityNotFoundException;
use app\modules\poll\domain\entities\exceptions\NotImplementedException;
use app\modules\poll\domain\entities\poll\Answer;
use app\modules\poll\domain\entities\poll\Poll;
use app\modules\poll\domain\entities\poll\PollChange;
use app\modules\poll\domain\entities\poll\Question;
use app\modules\poll\domain\entities\PollRepositoryInterface;
use DateTimeImmutable;
use Throwable;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Exception;
use yii\db\Expression;
use yii\db\Transaction;

final class PollRepository implements PollRepositoryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Throwable
     * @throws Exception
     */
    public function create(PollChange $poll): void
    {
        /** @var Transaction $transaction */
        $transaction = $this->connection->beginTransaction();

        try {
            $pollRecord = new PollRecord();
            $pollRecord->setAttributes(
                [
                    'title' => $poll->getTitle(),
                    'published_from' => $poll->getPublishedFrom(),
                    'published_to' => $poll->getPublishedTo(),
                    'user_ids' => $poll->getUserIds(),
                ],
                false
            );

            $this->save($pollRecord);

            foreach ($poll->getQuestions() as $question) {
                $questionRecord = new QuestionRecord();
                $questionRecord->setAttributes(
                    [
                        'poll_id' => $pollRecord->id,
                        'text' => $question->getText(),
                    ],
                    false
                );
                $this->save($questionRecord);

                foreach ($question->getAnswers() as $answer) {
                    $answerRecord = new AnswerRecord();
                    $answerRecord->setAttributes(
                        [
                            'question_id' => $questionRecord->id,
                            'text' => $answer->getText(),
                        ],
                        false
                    );
                    $this->save($answerRecord);
                }
            }
        } catch (Throwable $exception) {
            $transaction->rollBack();

            throw $exception;
        }

        $transaction->commit();
    }

    public function update(int $id, PollChange $poll): void
    {
        $record = PollRecord::find()->with('questions', 'questions.answers')->andWhere(['poll.deleted' => false])->one(
        );
        if ($record === null) {
            throw new EntityNotFoundException("Poll id #$id not found");
        }

        // TODO
        throw new NotImplementedException();
    }

    public function getActiveForUser(int $userId): ?Poll
    {
        $query = PollRecord::find()
            ->with('questions', 'questions.answers')
            ->leftJoin(ClientAnswerRecord::tableName() . ' ua', 'ua.poll_id = poll.id')
            ->andWhere(['ua.id' => null])
            ->andWhere(['deleted' => false])
            ->orderBy(['published_at' => SORT_ASC]);

        $record = $query
            ->andWhere(new Expression("JSON_CONTAINS('user_ids', '$userId')"))
            ->one();
        if ($record === null) {
            $record = $query->one();
        }

        /** @var PollRecord|null $record */
        if ($record !== null) {
            return $this->populate($record);
        }

        return null;
    }

    /**
     * @throws Throwable
     */
    public function addAnswer(ClientAnswerChange $answer): void
    {
        /** @var Transaction $transaction */
        $transaction = $this->connection->beginTransaction();

        try {
            $answerRecord = new ClientAnswerRecord();
            $answerRecord->setAttributes(
                [
                    'user_id' => $answer->getUserId(),
                    'license_id' => $answer->getLicenseId(),
                    'poll_id' => $answer->getPollId(),
                ],
                false
            );

            $this->save($answerRecord);

            foreach ($answer->getAnswers() as $questionAnswer) {
                $questionAnswerRecord = new QuestionAnswerRecord();
                $questionAnswerRecord->setAttributes(
                    [
                        'question_id' => $questionAnswer->getQuestionId(),
                        'answer_id' => $questionAnswer->getAnswerId(),
                        'client_answer_id' => $answerRecord->id,
                    ],
                    false
                );

                $this->save($questionAnswerRecord);
            }
        } catch (Throwable $exception) {
            $transaction->rollBack();

            throw $exception;
        }

        $transaction->commit();
    }

    public function addRejection(int $pollId, int $userId, $licenseId): void
    {
        $answerRecord = new ClientAnswerRecord();
        $answerRecord->setAttributes(
            [
                'user_id' => $userId,
                'license_id' => $licenseId,
                'poll_id' => $pollId,
                'rejection' => true,
            ],
            false
        );

        $this->save($answerRecord);
    }

    private function save(ActiveRecord $record): void
    {
        if (!$record->save()) {
            $errors = $record->getFirstErrors();

            throw new DomainDataCorruptionException(reset($errors));
        }
    }

    private function populate(PollRecord $record): Poll
    {
        return new Poll(
           $record->id,
           $record->title,
           (new DateTimeImmutable())->setTimestamp($record->published_from),
           (new DateTimeImmutable())->setTimestamp($record->published_to),
           $record->user_ids,
            ...$this->populateQuestions(...$record->questions)
        );
    }

    /**
     * @return Question[]
     */
    private function populateQuestions(QuestionRecord ...$records): array
    {
        $result = [];
        foreach ($records as $record) {
            $result[] = new Question(
               $record->id,
               $record->text,
                ...$this->populateAnswers(...$record->answers)
            );
        }

        return $result;
    }

    /**
     * @return Answer[]
     */
    private function populateAnswers(AnswerRecord ...$records): array
    {
        $result = [];
        foreach ($records as $record) {
            $result[] = new Answer(
                $record->id,
                $record->sort,
                $record->text,
                $record->can_be_commented,
            );
        }

        return $result;
    }
}
