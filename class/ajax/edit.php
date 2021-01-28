<?php

/**
 * Class to handle the Framework AJAX bean operation
 *
 * @author Cong Danh Tran <c.d.tran2@ncl.ac.uk>
 * @copyright 2021 Newcastle University
 */

namespace Ajax;

use \Config\Framework as FW;
use \Framework\Exception\BadOperation;

/**
 * Edit Operations on beans
 */
final class Edit extends \Framework\Ajax\Ajax {

    /**
     * @var array for permission control
     */
    private static $permissions = [
        FW::PROJECT => [TRUE, [], []],
    ];

    /**
     * @var string
     */
    private $class = '';

    /**
     * update multiple fields   /ajax/edit/KIND
     *
     * require id and fields to edit
     * 
     * @return void
     * @psalm-suppress UnusedMethod
     */
    private function post(string $bean, array $rest, bool $log): void {
        $this->checkAccess($this->context->user(), self::$permissions, $bean);
        if (!method_exists($this->class, 'add')) { // operation not supported
            throw new BadOperation('Cannot edit a ' . $bean);
        }
        [$id, $field] = $rest;
        $this->class::edit($this->context, $id);
        if ($log) {
            BeanLog::mklog($this->context, BeanLog::CREATE, $bean, $id, '*', NULL);
        }
        $this->context->web()->created($id); // 201 return code
    }

    /**
     * Carry out operations on beans
     *
     * @throws BadOperation
     * @throws \Framework\Exception\BadValue
     * @throws \Framework\Exception\Forbidden
     *
     * @return void
     */
    final public function handle(): void {
        [$bean, $rest] = $this->restCheck(1);
        $method = strtolower($this->context->web()->method());
        if (!method_exists(self::class, $method)) {
            throw new \Framework\Exception\BadOperation($method . ' is not supported');
        }
        /** @psalm-suppress UndefinedConstant */
        $this->class = REDBEAN_MODEL_PREFIX . $bean;
        /**
         * @psalm-suppress RedundantCondition
         * @psalm-suppress ArgumentTypeCoercion
         */
        if (method_exists($this->class, 'canAjaxBean')) {
            /** @psalm-suppress InvalidStringClass */
            $this->class::canAjaxBean($this->context, $method);
        }
        $this->{$method}($bean, $rest, $this->controller->log($bean));
    }

}

?>
