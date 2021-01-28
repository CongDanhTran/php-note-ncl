<?php

/**
 * A model class for the RedBean object Note
 *
 * This is a Framework system class - do not edit!
 *
 * @author Cong Danh Tran <c.d.tran2@ncl.ac.uk>
 * @copyright 2021 Newcastle University
 * @package Framework
 * @subpackage SystemModel
 */

namespace Model;

use \Config\Framework as FW;
use \Support\Context;
use ModelExtend\Validator;
use DateTime;

/**
 * A class implementing a RedBean model for Note beans
 * @psalm-suppress UnusedClass
 */
class Note extends \RedBeanPHP\SimpleModel {

    /**
     * Return upload files belong to note method
     *
     * @return array
     */
    public function uploads(): array {
        return $this->bean->ownUpload;
    }

    /**
     * Return number of attachments
     *
     * @return int
     */
    public function noOfUploads(): int {
        return count($this->bean->ownUpload);
    }

    /**
     * Return formatted start date
     *
     * @return string 
     */
    public function startDate(string $format = 'Y-m-d H:i'): string {
        $d = new DateTime($this->bean->startDate);
        return strlen($this->bean->startDate) > 0 ? $d->format($format) : "";
    }

    /**
     * Return formatted end date
     *
     * @return string 
     */
    public function endDate(string $format = 'Y-m-d H:i'): string {
        $d = new DateTime($this->bean->endDate);
        return strlen($this->bean->endDate) > 0 ? $d->format($format) : "";
    }

    /**
     * Return project id
     *
     * @return string 
     */
    public function projectId(): string {
        return $this->bean->project_id;
    }

    /**
     * Return duration between start and end date
     *
     * @return string 
     */
    public function duration(): string {
        if (trim($this->bean->endDate) === '') {
            return '';
        }
        $endDate = new DateTime($this->bean->endDate);
        $startDate = new DateTime($this->bean->startDate);
        if ($endDate > $startDate) {
            $duration = $endDate->diff($startDate);
            $format = '%d ' . ($duration->d > 0 ? 'days' : 'day') . ' - %H:%I';
            return $duration->format($format);
        }

        return '';
    }

    /**
     * Handle form multiple upload files
     *
     * @param \Support\Context    $context  The context object
     * @param object    $object  the bean object
     *
     * @return void
     */
    private static function upload(Context $context, object $bean): void {
        $fdt = $context->formdata('file');
        if (!$fdt->exists("uploads")) {
            return;
        }

        foreach ($fdt->fileArray('uploads') as $file) {
            $upl = \R::dispense('upload');
            $upl->note = $bean;
            if (!$upl->savefile($context, $file, FALSE, $context->user(), 0)) { //
                throw new \Framework\Exception\BadValue('upload failed ' . $file['name'] . ' ' . $file['size'] . ' ' . $file['error']);
            }
        }
    }

    /**
     * Handle add new note form
     *
     * @param \Support\Context    $context  The context object
     *
     * @return \RedBeanPHP\OODBBean
     */
    public static function add(Context $context): \RedBeanPHP\OODBBean {

        $fdt = $context->formdata('post');

        $projectId = Validator::checkText("projectId", $fdt, 'Something wrong with the page, no project');
        $startDate = Validator::checkValidDate("startDate", 'Please select start date', $fdt);
        $note = Validator::checkText("note", $fdt, 'Please enter your note');

        $endDate = $fdt->fetch('endDate', null);

        if ($endDate != null) {
            $endDate = Validator::checkValidDate("endDate", 'Please select correct end date', $fdt);
            Validator::checkValidDateRange($startDate, $endDate, 'End Date must be after Start Date');
        }

        $project = \R::load(FW::PROJECT, $projectId);
        $bean = \R::dispense('note');
        $bean->note = $note;
        $bean->startDate = $startDate;
        $bean->endDate = $endDate;
        $bean->project = $project;

        self::upload($context, $bean);
        \R::store($bean);

        return $bean;
    }

    /**
     * Handle edit note ajax function
     *
     * @param \Support\Context    $context  The context object
     *
     * @return \RedBeanPHP\OODBBean
     */
    public function edit(Context $context): void {

        $fdt = $context->formdata('post');
        $startDate = Validator::checkValidDate("startDate", 'Please select start date', $fdt);
        $note = Validator::checkText("note", $fdt, 'Please enter your note');

        $endDate = $fdt->fetch('endDate', null);

        if ($endDate !== null) {
            $endDate = Validator::checkValidDate("endDate", 'Please select correct end date', $fdt);
            Validator::checkValidDateRange($startDate, $endDate, 'End Date must be after Start Date');
        }

        $bean = $this->bean;

        if (is_object($bean)) {
            if ($note != $bean->note) {
                $bean->note = $note;
            }
            if ($startDate != $bean->startDate) {
                $bean->startDate = $startDate;
            }
            if ($endDate != $bean->endDate) {
                $bean->endDate = $endDate;
            }
            if ($note != $bean->note) {
                $bean->note = $note;
            }

            \R::store($bean);
        }

        self::upload($context, $bean);
    }

}

?>