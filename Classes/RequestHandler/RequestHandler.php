<?php

require_once 'SyncRequestHandler.php';
require_once 'AjaxRequestHandler.php';
require_once 'Classes/SimplePDO/SimplePDO.php';

abstract class RequestHandler {
    protected $rawData = '';
    protected $filteredData = '';
    protected $firstDate = null; //Here we will store object of DateTime class
    protected $secondDate = null; //Here we will store object of DateTime class
    protected $clientIP = '';
    protected $startProcessingMicrotime = 0.0;
    protected $endProcessingMicrotime = 0.0;
    protected $diff = null; //Here we will store difference, by default as number of days, so here will be integer type

    abstract public function generateResponse(string $messageSubject, string $messageType);

    public static function getInstanse(string $requestType, string $rawData, string $clientIP, float $startProcessingMicrotime) {
        if ($requestType === 'async') {
            return new AjaxRequestHandler($rawData, $clientIP, $startProcessingMicrotime);
        } else {
            return new SyncRequestHandler($rawData, $clientIP, $startProcessingMicrotime);
        }
    }

    public function validateInputData() {
        $filteredData = preg_replace('/[^\d\/\-\.]/', '', $this->rawData); //Left only '.' '-' '/' and digits

        if (empty($this->rawData)) {
            $this->generateResponse('Data input field cannot be empty.', 'error');
        }

        if ($this->rawData !== $filteredData) {
            $this->generateResponse('Input contains not allowed symbols.', 'error');
        }

        $twoDates = explode('-', $filteredData);

        if (count($twoDates) !== 2) {
            $this->generateResponse('Number of date components should be only 2.', 'error');
        } else {

            //Check if month number <= 12:
            if ($this->checkMonthNumber($twoDates[0], '/', 1) === false ||
                $this->checkMonthNumber($twoDates[0], '.', 0) === false ||
                $this->checkMonthNumber($twoDates[1], '/', 1) === false ||
                $this->checkMonthNumber($twoDates[1], '.', 0) === false) {

                $this->generateResponse('Month number cannot be > 12!', 'error');
            }

            $this->firstDate = DateTime::createFromFormat('Y/m/d', $twoDates[0]) !== false ? DateTime::createFromFormat('Y/m/d', $twoDates[0]) : DateTime::createFromFormat('m.d.Y', $twoDates[0]);
            $this->secondDate = DateTime::createFromFormat('Y/m/d', $twoDates[1]) !== false ? DateTime::createFromFormat('Y/m/d', $twoDates[1]) : DateTime::createFromFormat('m.d.Y', $twoDates[1]);
        }

        if ($this->firstDate === false || $this->secondDate === false) {
            $this->generateResponse('Cannot create date object. Check syntax: YYYY/mm/dd OR mm.dd.YYYY', 'error');
        }

        $this->filteredData = $filteredData;

        //If we've got this place -> data passed validation.
        return;
    }

    public function calculateDifference(string $measurementUnit = 'days') {
        $difference = $this->secondDate->diff($this->firstDate);
        $this->diff = $difference->$measurementUnit;
    }

    public function saveToDB() {
        //If we have not calculated difference by unknown reason, but still got this place (nobody know how :)) -> just do nothing.
        if ($this->diff === null) {
            return;
        }

        $startDateTimestamp = $this->firstDate->getTimestamp();
        $endDateTimeStamp = $this->secondDate->getTimestamp();

        $this->endProcessingMicrotime = microtime(true);

        $timeSpent = $this->endProcessingMicrotime - $this->startProcessingMicrotime;

        //===================== TRY SAVE TO DB: ========================================================================
        $db = new simplePDO('fmscan.mysql.tools', 'fmscan_nptest', '-I4n0s)4Al', 'fmscan_nptest');
        $preparedQuery = 'INSERT INTO logs (ip_address, start_date_timestamp, end_date_timestamp, date_diff, time_spent) VALUES (?, ?, ?, ?, ?);';

        try {
            $stmt = $db->run($preparedQuery, [$this->clientIP, $startDateTimestamp, $endDateTimeStamp, $this->diff, $timeSpent]);
        } catch (PDOException $e) {
            //Sure, at real project we will not show user exception message :)
            $this->generateResponse('We caught exception: ' . $e->getMessage(), 'error');
        }
        //==============================================================================================================
    }

    public function getDiff() {
        return $this->diff;
    }

    protected function __construct(string $rawData, string $clientIP, float $startProcessingMicrotime) {
        $this->rawData = $rawData;
        $this->clientIP = $clientIP;
        $this->startProcessingMicrotime = $startProcessingMicrotime;
    }

    //Generally, DateTime object correctly handling case when number of month == 13.
    //In this case it just considered as 1st month of NEXT year.
    //But to not mislead the user, we will check month number and fail validation if it > 12.
    protected function checkMonthNumber(string $date, string $delimiter, int $monthPosition) {
        $dateParsed = explode($delimiter, $date);

        if (count($dateParsed) === 3 && intval($dateParsed[$monthPosition]) > 12) {
            return false;
        }
        elseif (count($dateParsed) !== 3) {
            return null;
        }
        else {
            return true;
        }
    }
}