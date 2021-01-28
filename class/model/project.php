<?php

/**
 * A model class for the RedBean object project
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

/**
 * A class implementing a RedBean model for project beans
 * @psalm-suppress UnusedClass
 */
class Project extends \RedBeanPHP\SimpleModel {

    /**
     * Return note array
     *
     * @return array
     */
    public function notes(): array {
        return $this->bean->ownNote;
    }

    /**
     * Return number of notes
     *
     * @return int
     */
    public function noOfNotes(): int {
        return count($this->bean->ownNote);
    }

    /**
     * handle add a new project via post form
     *
     * @param Context    $context  The context object
     *
     * @return \RedBeanPHP\OODBBean
     */
    public static function add(Context $context): \RedBeanPHP\OODBBean {
        $fdt = $context->formdata('post');
        $name = Validator::checkText("name", $fdt, 'Please enter project name');

        self::checkDuplicateName($name, $context->user()->getID());

        $bean = \R::dispense(FW::PROJECT);
        $bean->name = $name;
        $bean->description = $fdt->fetch('description', '');
        $bean->user = $context->user();
        \R::store($bean);
        return $bean;
    }

    /**
     * Handle project edit ajax call
     *
     * @param \Support\Context    $context  The context object
     *
     * @return array
     */
    public static function edit(Context $context) {
        $fdt = $context->formdata('post');
        $projectId = Validator::checkInt("id", $fdt, 'Something wrong with the page, no project existed');
        $bean = $context->load(FW::PROJECT, $projectId);

        Validator::checkEntityAccess($context->user()->getID(), [$bean->user_id], "User doesn't have access to this entity");

        if (is_object($bean)) {
            $name = Validator::checkText("name", $fdt, 'Please enter project name');
            $description = $fdt->fetch('description', '');

            self::checkDuplicateName($name, $context->user()->getID(), $bean->getID());

            if ($name != $bean->name) {
                $bean->name = $name;
            }
            if ($description != $bean->description) {
                $bean->description = $description;
            }
            \R::store($bean);
        }
    }

    /**
     * Check for duplicate name
     *
     * @param string    $name   name of project
     * @param string    $userID project owner id
     * @param string    $id  project id if not created
     * 
     * @throws \Framework\Exception\BadValue throw if duplicate name found for same owner
     *
     * @return void
     */
    private static function checkDuplicateName(string $name, string $userID, string $id = "-1"): void {
        if ($id != "-1") {
            $existedProject = \R::find(FW::PROJECT, 'name = ? and user_id = ? and id != ?', [$name, $userID, $id]);
        } else {
            $existedProject = \R::find(FW::PROJECT, 'name = ? and user_id = ?', [$name, $userID]);
        }
        if (count($existedProject) > 0) {
            throw new \Framework\Exception\BadValue("Duplicate name used, please enter another name");
        }
    }

}

?>