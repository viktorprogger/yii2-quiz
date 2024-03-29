<?php

declare(strict_types=1);

namespace app\modules\poll\infrastruture\entities;

use app\modules\poll\domain\entities\clientAnswer\ClientAnswerChange;
use app\modules\poll\domain\entities\poll\Answer;
use app\modules\poll\domain\entities\poll\Poll;
use app\modules\poll\domain\entities\poll\PollChange;
use app\modules\poll\domain\entities\poll\Question;
use app\modules\poll\domain\entities\PollRepositoryInterface;
use app\modules\poll\domain\exceptions\DomainDataCorruptionException;
use app\modules\poll\domain\exceptions\EntityNotFoundException;
use app\modules\poll\domain\exceptions\NotImplementedException;
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
    public function create(PollChange $poll): Poll
    {
        /** @var Transaction $transaction */
        $transaction = $this->connection->beginTransaction();

        try {
            $pollRecord = new PollRecord();
            $pollRecord->setAttributes(
                [
                    'title' => $poll->getTitle(),
                    'published_from' => $poll->getPublishedFrom()->getTimestamp(),
                    'published_to' => $poll->getPublishedTo()->getTimestamp(),
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
                            'sort' => $answer->getSort(),
                            'can_be_commented' => $answer->canBeCommented(),
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

        return $this->populate($pollRecord);
    }

    public function update(int $id, PollChange $poll): Poll
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
        $now = time();
        $query = PollRecord::find()
            ->alias('poll')
            ->with('questions', 'questions.answers')
            ->leftJoin(ClientAnswerRecord::tableName() . ' ca', 'ca.poll_id = poll.id')
            ->andWhere(['<=', 'published_from', $now])
            ->andWhere(['>', 'published_to', $now])
            ->andWhere(['ca.id' => null])
            ->orderBy(['published_from' => SORT_ASC]);

        $record = (clone $query)
            ->andWhere(new Expression("JSON_CONTAINS(user_ids, '$userId')"));
        $record = $record->one();
        if ($record === null) {
            $record = $query
                ->andWhere(['user_ids' => '[]'])
                ->one();
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
        $this->checkCanBeAnswered($answer->getPollId(), $answer->getUserId());

        /** @var Transaction $transaction */
        $transaction = $this->connection->beginTransaction();

        try {
            $clientAnswerRecord = new ClientAnswerRecord();
            $clientAnswerRecord->setAttributes(
                [
                    'user_id' => $answer->getUserId(),
                    'license_id' => $answer->getLicenseId(),
                    'poll_id' => $answer->getPollId(),
                ],
                false
            );

            $this->save($clientAnswerRecord);

            $answered = [];
            foreach ($answer->getAnswers() as $questionAnswer) {
                $questionRecord = QuestionRecord::findOne($questionAnswer->getQuestionId());
                if ($questionRecord === null || $questionRecord->poll_id !== $answer->getPollId()) {
                    throw new DomainDataCorruptionException("Question #{$questionAnswer->getQuestionId()} doesn't belong to poll #{$answer->getPollId()}");
                }

                $answerRecord = AnswerRecord::findOne($questionAnswer->getAnswerId());
                if ($answerRecord === null || $answerRecord->question_id !== $questionAnswer->getQuestionId()) {
                    throw new DomainDataCorruptionException("Answer #{$questionAnswer->getAnswerId()} doesn't belong to question #{$questionAnswer->getQuestionId()}");
                }

                if ($questionAnswer->getComment() !== '' && (bool) $answerRecord->can_be_commented === false) {
                    throw new DomainDataCorruptionException("Answer #{$questionAnswer->getAnswerId()} can't be commented");
                }

                $questionAnswerRecord = new QuestionAnswerRecord();
                $questionAnswerRecord->setAttributes(
                    [
                        'question_id' => $questionAnswer->getQuestionId(),
                        'answer_id' => $questionAnswer->getAnswerId(),
                        'client_answer_id' => $clientAnswerRecord->id,
                        'comment' => $questionAnswer->getComment(),
                    ],
                    false
                );

                $this->save($questionAnswerRecord);
                $answered[] = $questionAnswer->getQuestionId();
            }

            $questionList = $this->connection
                ->createCommand(
                    'SELECT id FROM ' . QuestionRecord::tableName() . ' WHERE poll_id = :poll',
                    ['poll' => $answer->getPollId()]
                )
                ->queryColumn();

            if (count(array_diff($questionList, $answered)) > 0) {
                throw new DomainDataCorruptionException("Not all questions are answered");
            }
        } catch (Throwable $exception) {
            $transaction->rollBack();

            throw $exception;
        }

        $transaction->commit();
    }

    public function addRejection(int $pollId, int $userId, $licenseId): void
    {
        $this->checkCanBeAnswered($pollId, $userId);
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
            (array) $record->user_ids,
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
                (string) $record->text,
                (bool) $record->can_be_commented,
            );
        }

        return $result;
    }

    private function checkCanBeAnswered(int $pollId, int $userId): void
    {
        $pollRecord = PollRecord::findOne($pollId);
        if ($pollRecord === null || (!$pollRecord->user_ids->isEmpty() && !in_array($userId, $pollRecord->user_ids->toArray(), true))) {
            throw new DomainDataCorruptionException('User is not allowed to answer to this poll');
        }
    }
}
