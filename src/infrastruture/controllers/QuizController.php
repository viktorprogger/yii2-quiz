<?php

declare(strict_types=1);

namespace app\modules\quiz\infrastruture\controllers;

use app\modules\quiz\domain\entities\AnswerChange;
use app\modules\quiz\domain\entities\QuestionChangeCustom;
use app\modules\quiz\domain\entities\QuestionChangeInterface;
use app\modules\quiz\domain\entities\QuestionChangeRated;
use app\modules\quiz\domain\entities\Quiz;
use app\modules\quiz\domain\entities\QuizChange;
use app\modules\quiz\domain\entities\QuizRepositoryInterface;
use DateTimeImmutable;
use InvalidArgumentException;
use JsonSerializable;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\HttpException;

final class QuizController extends Controller
{
    private QuizRepositoryInterface $quizRepository;

    public function __construct($id, $module, QuizRepositoryInterface $quizRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->quizRepository = $quizRepository;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors() ?? [];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        return $behaviors;
    }

    public function actionGetQuiz(): Quiz
    {
        return $this->quizRepository->getActiveForUser(Yii::$app->user->id);
    }

    /**
     * Quiz creation endpoint
     *
     * @param array $quiz The following JSON structure:
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
     *         sort: int, # required; the less is value, the higher this answer will be positioned
     *         canBeCommented: bool, # required; user can comment the answer when value is true
     *       }
     *     ],
     *   }
     * ]
     * ```
     *
     * @return Quiz
     *
     * @throws BadRequestHttpException
     */
    public function actionCreate(array $quiz): Quiz
    {
        return $this->quizRepository->create($this->createQuizFromArray($quiz));
    }

    private function createQuizFromArray(array $quiz): QuizChange
    {
        if (!isset($quiz['title']) || !is_string($quiz['title'])) {
            throw new BadRequestHttpException('Quiz title must be a string');
        }

        if (!isset($quiz['publishedFrom']) || !is_int($quiz['publishedFrom'])) {
            throw new BadRequestHttpException('Quiz publish start datetime must be a valid timestamp');
        }

        if (!isset($quiz['publishedTo']) || !is_int($quiz['publishedTo'])) {
            throw new BadRequestHttpException('Quiz publish end datetime must be a valid timestamp');
        }

        $questions = [];
        try {
            foreach ($quiz['questions'] as $index => $definition) {
                $questions[] = $this->createQuestionFromArray($definition);
            }
        } catch (InvalidArgumentException $exception) {
            throw new BadRequestHttpException("Question #$index structure is invalid", 0, $exception);
        }

        return new QuizChange(
           $quiz['title'],
           (new DateTimeImmutable())->setTimestamp($quiz['publishedFrom']),
           (new DateTimeImmutable())->setTimestamp($quiz['publishedTo']),
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
