<?php
/**
 * @author Denis Khodakovskii <denis.khodakovskiy@gmail.com>
 */

declare(strict_types=1);

namespace This\ORM\DQL;

use This\ORM\DQL\AST\AggregateExpression;
use This\ORM\DQL\AST\AlwaysFalseExpression;
use This\ORM\DQL\AST\AlwaysTrueExpression;
use This\ORM\DQL\AST\BetweenExpression;
use This\ORM\DQL\AST\ColumnRefExpression;
use This\ORM\DQL\AST\ComparisonExpression;
use This\ORM\DQL\AST\ComparisonOperatorEnum;
use This\ORM\DQL\AST\ExpressionNode;
use This\ORM\DQL\AST\InExpression;
use This\ORM\DQL\AST\LogicalExpression;
use This\ORM\DQL\AST\LogicalTypeEnum;
use This\ORM\DQL\AST\NotExpression;
use This\ORM\DQL\AST\NullCheckExpression;
use This\ORM\DQL\AST\RawExpression;

final class Expr
{
    public static function equal(string $field, mixed $value): ExpressionNode
    {
        return new ComparisonExpression(field: $field, operator: ComparisonOperatorEnum::EQUAL, value: $value);
    }

    public static function notEqual(string $field, mixed $value): ExpressionNode
    {
        return new ComparisonExpression(field: $field, operator: ComparisonOperatorEnum::NOT_EQUAL, value: $value);
    }

    public static function greaterThan(string $field, mixed $value): ExpressionNode
    {
        return new ComparisonExpression(field: $field, operator: ComparisonOperatorEnum::GRATER_THAN, value: $value);
    }

    public static function greaterThanOrEqual(string $field, mixed $value): ExpressionNode
    {
        return new ComparisonExpression(field: $field, operator: ComparisonOperatorEnum::GRATER_THAN_OR_EQUAL, value: $value);
    }

    public static function lessThan(string $field, mixed $value): ExpressionNode
    {
        return new ComparisonExpression(field: $field, operator: ComparisonOperatorEnum::LESS_THAN, value: $value);
    }

    public static function lessThanOrEqual(string $field, mixed $value): ExpressionNode
    {
        return new ComparisonExpression(field: $field, operator: ComparisonOperatorEnum::LESS_THAN_OR_EQUAL, value: $value);
    }

    public static function like(string $field, mixed $value): ExpressionNode
    {
        return new ComparisonExpression(field: $field, operator: ComparisonOperatorEnum::LIKE, value: $value);
    }

    public static function notLike(string $field, mixed $value): ExpressionNode
    {
        return new ComparisonExpression(field: $field, operator: ComparisonOperatorEnum::NOT_LIKE, value: $value);
    }

    public static function in(string $field, array $values): ExpressionNode
    {
        if ($values === []) {
            return new AlwaysFalseExpression();
        }

        return new InExpression(field: $field, values: $values);
    }

    public static function notIn(string $field, array $values): ExpressionNode
    {
        return new InExpression(field: $field, values: $values, negated: true);
    }

    public static function between(string $field, mixed $min, mixed $max): ExpressionNode
    {
        return new BetweenExpression(field: $field, min: $min, max: $max);
    }

    public static function notBetween(string $field, mixed $min, mixed $max): ExpressionNode
    {
        return new BetweenExpression(field: $field, min: $min, max: $max, negated: true);
    }

    public static function isNull(string $field): ExpressionNode
    {
        return new NullCheckExpression(field: $field);
    }

    public static function isNotNull(string $field): ExpressionNode
    {
        return new NullCheckExpression(field: $field, negated: true);
    }

    public static function and(...$expressions): ExpressionNode
    {
        return new LogicalExpression(type: LogicalTypeEnum::AND, expressions: $expressions);
    }

    public static function or(...$expressions): ExpressionNode
    {
        return new LogicalExpression(type: LogicalTypeEnum::OR, expressions: $expressions);
    }

    public static function not(ExpressionNode $expression): ExpressionNode
    {
        return new NotExpression(expression: $expression);
    }

    public static function equals(array $conditions): ExpressionNode
    {
        if ($conditions === []) {
            return new AlwaysTrueExpression();
        }

        $expressions = [];

        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                throw new \InvalidArgumentException(
                    'Expression::equals() does not support array values. Use Expression::in()'
                );
            }

            if ($value === null) {
                throw new \InvalidArgumentException(
                    'Expression::equals() does not support NULL. Use Expression::isNull()'
                );
            }

            $expressions[] = self::equal(field: $field, value: $value);
        }

        return new LogicalExpression(
            type: LogicalTypeEnum::AND,
            expressions: $expressions,
        );
    }

    public static function expression(string $expression): ExpressionNode
    {
        return new RawExpression(expression: $expression);
    }

    public static function columnRef(string $expression): ExpressionNode
    {
        return new ColumnRefExpression(columnReference: $expression);
    }

    public static function sum(string $field, ?string $alias = null): AggregateExpression
    {
        return new AggregateExpression('SUM', $field, $alias);
    }

    public static function avg(string $field, ?string $alias = null): AggregateExpression
    {
        return new AggregateExpression('AVG', $field, $alias);
    }

    public static function count(string $field, ?string $alias = null): AggregateExpression
    {
        return new AggregateExpression('COUNT', $field, $alias);
    }

    public static function min(string $field, ?string $alias = null): AggregateExpression
    {
        return new AggregateExpression('MIN', $field, $alias);
    }

    public static function max(string $field, ?string $alias = null): AggregateExpression
    {
        return new AggregateExpression('MAX', $field, $alias);
    }

    public static function on(string $field, string $refField): ExpressionNode
    {
        return self::equal($field, self::columnRef($refField));
    }
}
