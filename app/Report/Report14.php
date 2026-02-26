<?php
/**
 * @author Denis Khodakovskii <denis.khodakovskiy@gmail.com>
 */

declare(strict_types=1);

namespace App\Report;

use This\ORM\DQL\Expr;
use This\ORM\ORMInterface;
use This\ORM\Query\Select;
use This\Output\CLI\CLIMarkupRenderer;
use This\Output\CLI\CLIOutput;

final readonly class Report14
{
    public function __construct(
        private ORMInterface $orm,
    ) {
    }

    public function __invoke(): void
    {
        $output = new CLIOutput(new CLIMarkupRenderer());

        $query = Select::from(ExamUserStatusSchema::class, 'eus')
            ->select('eus.user_id', 'eus.exam_id', 'eus.attempt', 'eus.end_dt', 'e.type', Expr::expression(
                'SUM(CASE WHEN eut.is_correct = 1 THEN t.points ELSE 0 END) AS points'
            ))
            ->innerJoin(ExamSchema::class, 'e', Expr::equal('e.exam_id', Expr::columnRef('eus.exam_id')))
            ->leftJoin(ExamUserTaskSchema::class, 'eut', Expr::and(
                Expr::equal('eut.exam_id', Expr::columnRef('eus.exam_id')),
                Expr::equal('eut.user_id', Expr::columnRef('eus.user_id')),
            ))
            ->leftJoin(TaskSchema::class, 't', Expr::equal('t.task_id', Expr::columnRef('eut.task_id')))
            ->where(Expr::equal('e.course_id', 14))
            ->limit(10)
        ;

        $examIds = $this->orm->query(
            Select::from(ExamSchema::class)
                ->select('exam_id')
                ->where(Expr::equal('course_id', 14))
            )
            ->column()
        ;

        $userTasks = [];
        $step = 0;
        $lastId = 0;

        do {
            $start = microtime(true);
            $output->info(sprintf('Fetching batch #%d', $step));

            $rows = $this->orm->query(
                Select::from(ExamUserTaskSchema::class)
                    ->select('id', 'is_correct')
                    ->where(
                        Expr::and(
                            Expr::equal('exam_id', 20),
                            Expr::between('user_id', $lastId, $lastId + 1000)
                        )
                    )
                    ->orderBy(['id' => 'ASC'])
                )
                ->execute()
            ;

            if (!$rows) {
                break;
            }

            $lastId += 1000;

            $output->info(sprintf('Fetched %d rows in %d s', count($rows), (int) ceil(microtime(true) - $start)));

            $step++;
        } while ($rows);
    }
}
