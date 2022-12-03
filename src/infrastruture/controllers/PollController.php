<?php

declare(strict_types=1);

namespace app\modules\poll\infrastruture\controllers;

use app\modules\poll\domain\entities\clientAnswer\ClientAnswer;
use app\modules\poll\domain\entities\clientAnswer\ClientAnswerChange;
use app\modules\poll\domain\entities\clientAnswer\QuestionAnswer;
use app\modules\poll\domain\entities\poll\AnswerChange;
use app\modules\poll\domain\entities\poll\Poll;
use app\modules\poll\domain\entities\poll\PollChange;
use app\modules\poll\domain\entities\poll\QuestionChangeCustom;
use app\modules\poll\domain\entities\poll\QuestionChangeInterface;
use app\modules\poll\domain\entities\poll\QuestionChangeRated;
use app\modules\poll\domain\entities\PollRepositoryInterface;
use DateTimeImmutable;
use InvalidArgumentException;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\web\User;

final class PollController extends Controller
{
    private PollRepositoryInterface $pollRepository;

    public function __construct($id, $module, PollRepositoryInterface $pollRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->pollRepository = $pollRepository;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors() ?? [];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        return $behaviors;
    }

    public function actionGetQuiz(): Poll
    {
        return $this->pollRepository->getActiveForUser(Yii::$app->user->id);
    }

    /**
     * Quiz creation endpoint
     *
     * @param array $poll The following JSON structure:
     * ```yaml
     * title: string, # required
     * publishedFrom: int, # timestamp, required
     * publishedTo: int, # timestamp, required
     * questions: [
     *   {
     *     type: string, # enum<custom|rated>, required
     *     text: string, # required
     *     answers: [ # required for type === custom, ignored for type === rated
     *       {
     *         text: string, # required,
     *         sort: int, # required; the less value is, the higher this answer will be positioned
     *         canBeCommented: bool, # required; user can comment the answer when value is true
     *       }
     *     ],
     *   }
     * ]
     * ```
     *
     * @return Poll
     *
     * @throws BadRequestHttpException
     */
    public function actionCreate(array $poll): Poll
    {
        return $this->pollRepository->create($this->createQuizFromArray($poll));
    }

    /**
     * Quiz update endpoint
     *
     * @param int $id Id of the poll to update
     * @param array $poll The following JSON structure:
     * ```yaml
     * title: string, # required
     * publishedFrom: int, # timestamp, required
     * publishedTo: int, # timestamp, required
     * questions: [
     *   {
     *     type: string, # enum<custom|rated>, required
     *     text: string, # required
     *     answers: [ # required for type === custom, ignored for type === rated
     *       {
     *         text: string, # required,
     *         sort: int, # required; the less value is, the higher this answer will be positioned
     *         canBeCommented: bool, # required; user can comment the answer when value is true
     *       }
     *     ],
     *   }
     * ]
     * ```
     *
     * @return Poll
     *
     * @throws BadRequestHttpException
     */
    public function actionUpdate(int $id, array $poll): Poll
    {
        return $this->pollRepository->update($id, $this->createQuizFromArray($poll));
    }

    /**
     * @param int $pollId Id of an answered poll
     * @param array $answers Array of answers in a such form: [questionId => int, answerId => int]. All the keys are
     *     required
     * @param User $user Current user component, injected via DI container
     * @param Response $response Current response object, injected via DI container
     *
     * @return Response
     *
     * @throws BadRequestHttpException
     */
    public function actionAnswer(int $pollId, array $answers, User $user, Response $response): Response
    {
        $answerCollection = [];
        foreach ($answers as $index => $definition) {
            if (!isset($definition['questionId']) || !is_int($definition['questionId'])) {
                throw new BadRequestHttpException("QuestionId must be an integer, error in answer #$index");
            }
            if (!isset($definition['answerId']) || !is_int($definition['answerId'])) {
                throw new BadRequestHttpException("AnswerId must be an integer, error in answer #$index");
            }

            $answerCollection[] = new QuestionAnswer($definition['questionId'], $definition['answerId']);
        }

        $clientAnswer = new ClientAnswerChange(
            $pollId,
            $user->getId(),
            $user->getLiscenseId(), // FIXME
            ...$answerCollection
        );
        $this->pollRepository->addAnswer($clientAnswer);

        return $response->setStatusCode(201);
    }

    /**
     * @param int $pollId Id of the poll to reject
     * @param User $user Current user component, injected via DI container
     * @param Response $response Current response object, injected via DI container
     *
     * @return Response
     */
    public function actionReject(int $pollId, User $user, Response $response): Response
    {
        $this->pollRepository->addRejection($pollId, $user->getId(), $user->getLicenseId()); // FIXME

        return $response->setStatusCode(201);
    }

    private function createQuizFromArray(array $poll): PollChange
    {
        if (!isset($poll['title']) || !is_string($poll['title'])) {
            throw new BadRequestHttpException('Quiz title must be a string');
        }

        if (!isset($poll['publishedFrom']) || !is_int($poll['publishedFrom'])) {
            throw new BadRequestHttpException('Quiz publish start datetime must be a valid timestamp');
        }

        if (!isset($poll['publishedTo']) || !is_int($poll['publishedTo'])) {
            throw new BadRequestHttpException('Quiz publish end datetime must be a valid timestamp');
        }

        $questions = [];
        try {
            foreach ($poll['questions'] as $index => $definition) {
                $questions[] = $this->createQuestionFromArray($definition);
            }
        } catch (InvalidArgumentException $exception) {
            throw new BadRequestHttpException("Question #$index structure is invalid", 0, $exception);
        }

        return new PollChange(
               $poll['title'],
               (new DateTimeImmutable())->setTimestamp($poll['publishedFrom']),
               (new DateTimeImmutable())->setTimestamp($poll['publishedTo']),
            ...$questions
        );
    }

    private function createQuestionFromArray(array $question): QuestionChangeInterface
    {
        if (!isset($question['title']) || !is_string($question['title'])) {
            throw new InvalidArgumentException('Quiz title must be a string');
        }

        switch ($question['type']) {
            case 'rated':
                if (!isset($question['maximum']) || !is_int($question['maximum']) || $question['maximum'] < 2) {
                    throw new InvalidArgumentException('Maximum rating must be an integer above 1');
                }
                
                if (!isset($question['dontCommentSince']) || !is_int($question['dontCommentSince']) || $question['dontCommentSince'] < 1) {
                    throw new InvalidArgumentException('dontCommentSince must be a positive integer');
                }

                return new QuestionChangeRated($question['title'], $question['maximum'], $question['dontCommentSince']);
            case 'custom':
                $answers = [];
                try {
                    foreach ($question['answers'] ?? [] as $index => $definition) {
                        $answers[] = $this->createAnswerFromArray($definition);
                    }
                } catch (InvalidArgumentException $exception) {
                    throw new InvalidArgumentException("Question #$index structure is invalid", 0, $exception);
                }

                return new QuestionChangeCustom($question['title'], ...$answers);
            default:
                throw new InvalidArgumentException('Question type must be either rated or custom');
        }
    }

    private function createAnswerFromArray(array $definition): AnswerChange
    {
        if (!isset($definition['sort']) || !is_int($definition['sort'])) {
            throw new InvalidArgumentException('sort must be an integer');
        }

        if (!isset($definition['text']) || !is_string($definition['text']) || $definition['text'] === '') {
            throw new InvalidArgumentException('text must be a non-empty string');
        }

        if (!isset($definition['canBeCommented'])) {
            throw new InvalidArgumentException('canBeCommented field is required');
        }
        $definition['canBeCommented'] = (bool) $definition['canBeCommented'];

        return new AnswerChange($definition['sort'], $definition['text'], $definition['canBeCommented']);
    }
}