<?php

/**
 * A class to validate the user's inputs.
 *
 * @author Cong Danh Tran <c.d.tran2@ncl.ac.uk>
 * @copyright 2021 Newcastle University
 * @package Framework
 * @subpackage ModelExtend
 */

namespace ModelExtend;

use DateTime;

/**
 * User table stores info about users of the system
 */
trait Validator {

    /**
     * A function to ensure that the text field is exist and not an empty string
     *
     * @param string    $fieldName  field name to check empty string
     * @param object    $formData  form data
     * @param string    $errorMessage  error message if failed
     *
     * @throws \Framework\Exception\BadValue If text field  is not in the request or empty string, this could be thrown
     *
     * @return string
     */
    public static function checkText(string $fieldName, object $formData, string $errorMessage = "Empty field"): string {
        if (!$formData->exists($fieldName) || trim($formData->mustFetch($fieldName)) === '') {
            throw new \Framework\Exception\BadValue($errorMessage);
        }

        return trim($formData->mustFetch($fieldName));
    }

    /**
     * A function to ensure that the field is an integer
     *
     * @param string    $fieldName  field name to check empty string
     * @param object    $formData  form data
     * @param string    $errorMessage  error message if failed
     *
     * @throws \Framework\Exception\BadValue If field is not in the request or not an int, this could be thrown
     *
     * @return int
     */
    public static function checkInt(string $fieldName, object $formData, string $errorMessage = "This is not an integer"): int {
        $int = self::checkText($fieldName, $formData, $errorMessage);
        if (!is_numeric($int)) {
            throw new \Framework\Exception\BadValue($errorMessage);
        }

        return (int) $int;
    }

    /**
     * A function to ensure the user has access to the entity
     *
     * @param string    $userID  field name to check
     * @param array    $ownerIds  field name to check
     * @param string    $errorMessage  error message if failed
     *
     * @throws \Framework\Exception\BadValue If userid not in the ownIds array, this could be thrown
     *
     * @return void
     */
    public static function checkEntityAccess(string $userID, array $ownerIds, string $errorMessage = "Access denied"): void {
        if (!in_array($userID, $ownerIds)) {
            throw new \Framework\Exception\BadValue($errorMessage);
        }
    }

    /**
     * A function to ensure that the boolean value is exist and within range
     *
     * @param string    $fieldName  field name to check
     * @param string    $errorMessage  error message if failed
     * @param object    $formData  form data
     * @param object    $values  false,true value
     *
     * @throws \Framework\Exception\BadValue If text field  is not in the request or empty string, this could be thrown
     *
     * @return bool
     */
    public static function checkBool(string $fieldName, string $errorMessage, object $formData, array $values = [0, 1]): bool {
        if (!$formData->exists($fieldName) || trim($formData->mustFetch($fieldName)) === '' || !in_array(trim($formData->mustFetch($fieldName)), $values)) {
            throw new \Framework\Exception\BadValue($errorMessage);
        }
        return trim($formData->mustFetch($fieldName)) === $values[1];
    }

    /**
     * A function to ensure that the start date less than end date
     *
     * @param DateTime    $startDate  start date
     * @param DateTime    $endDate  end date
     * @param string      $errorMessage  error message if failed
     *
     * @throws \Framework\Exception\BadValue If text field  is not in the request or empty string, this could be thrown
     *
     * @return bool
     */
    public static function checkValidDateRange(DateTime $startDate, DateTime $endDate, string $errorMessage): bool {
        if ($endDate < $startDate) {
            throw new \Framework\Exception\BadValue($errorMessage);
        }
        return true;
    }

    /**
     * A function to ensure that the date field is exist, valid, not an empty string
     *
     * @param string    $fieldName  field name to check empty string
     * @param string    $errorMessage  error message if failed
     * @param object    $formData  form data
     *
     * @throws \Framework\Exception\BadValue If date field  is not in the request, empty string or failed validation, this could be thrown
     *
     * @return DateTime
     */
    public static function checkValidDate(string $fieldName, string $errorMessage, object $formData): DateTime {
        if (!$formData->exists($fieldName) || trim($formData->mustFetch($fieldName)) === '' || !self::validateDate($formData->mustFetch($fieldName))) {
            throw new \Framework\Exception\BadValue($errorMessage);
        }
        return self::stringToDate($formData->mustFetch($fieldName));
    }

    /**
     * A function to ensure that the date is valid
     *
     * @param string    $date  date string
     * @param string    $format  the date format. Default  Y-m-d H:i
     *
     * @return bool
     */
    public static function validateDate(string $date, $format = 'Y-m-d H:i'): bool {

        $d = self::stringToDate(trim($date), $format);
        return $d && $d->format($format) == $date;
    }

    /**
     * A function convert string to Date
     *
     * @param string    $date  date string
     * @param string    $format  the date format. Default  Y-m-d H:i
     *
     * @return DateTime
     */
    public static function stringToDate(string $date, $format = 'Y-m-d H:i'): DateTime {
        return DateTime::createFromFormat($format, $date);
    }

}

?>
