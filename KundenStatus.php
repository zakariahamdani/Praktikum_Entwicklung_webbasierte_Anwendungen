<?php

declare(strict_types=1);
session_start();
// UTF-8 marker äöüÄÖÜß€
/**
 * Class PageTemplate for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7.4
 *
 * @file     PageTemplate.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 * @version  3.1
 */

// to do: change name 'PageTemplate' throughout this file
require_once './Page.php';

/**
 * This is a template for top level classes, which represent
 * a complete web page and which are called directly by the user.
 * Usually there will only be a single instance of such a class.
 * The name of the template is supposed
 * to be replaced by the name of the specific HTML page e.g. baker.
 * The order of methods might correspond to the order of thinking
 * during implementation.
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 */
class PageTemplate extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks

    /**
     * Instantiates members (to be defined above).
     * Calls the constructor of the parent i.e. page class.
     * So, the database connection is established.
     * @throws Exception
     */
    protected function __construct()
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
    }

    /**
     * Cleans up whatever is needed.
     * Calls the destructor of the parent i.e. page class.
     * So, the database connection is closed.
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    protected function statusFromIntToString($pizzaStatusAsInt)
    {
        if ($pizzaStatusAsInt == 0) {
            $pizzaStatusAsString = "bestellt";
        } else if ($pizzaStatusAsInt == 1) {
            $pizzaStatusAsString = "im Offen";
        } else if ($pizzaStatusAsInt == 3) {
            $pizzaStatusAsString = "fertig";
        } else if ($pizzaStatusAsInt == 4) {
            $pizzaStatusAsString = "unterwegs";
        } else if ($pizzaStatusAsInt == 5) {
            $pizzaStatusAsString = "geliefert";
        }
        return $pizzaStatusAsString;
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is returned in an array e.g. as associative array.
     * @return array An array containing the requested data. 
     * This may be a normal array, an empty array or an associative array.
     */
    protected function getViewData() //:array
    {
        // to do: fetch data for this view from the database
        // to do: return array containing data

        if (!isset($_SESSION["BestellungID"])) {
            $_data = array();
            return $_data;
        } else {
            $id = $_SESSION["BestellungID"];

            $pizzas = array(); //leeres Array für unseren Rückgabewert
            $sql = "SELECT article.name, ordered_article.status, ordered_article.ordered_article_id
                FROM article
                INNER JOIN ordered_article
                ON article.article_id = ordered_article.article_id
                where ordered_article.ordering_id = $id
                ORDER BY ordered_article.ordered_article_id";
            $recordset = $this->_database->query($sql); //recordset ist eine Kopie unserer Daten

            if (!$recordset) {
                throw new Exception("Fehler in Abfrage: " . $this->_database->error);
            }

            $record = $recordset->fetch_assoc();
            $i = 0;
            while ($record) {
                $pizzas[$i] = array("name" => $record["name"], "status" => $this->statusFromIntToString($record["status"]));
                $i++;
                $record = $recordset->fetch_assoc();
            }

            $recordset->free();

            //var_dump($data);
            return $pizzas;
        }
    }

    /**
     * First the required data is fetched and then the HTML is
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if available- the content of
     * all views contained is generated.
     * Finally, the footer is added.
     * @return void
     */
    protected function generateView(): void
    {
        $JSON_DATA = json_encode($this->getViewData());
        echo $JSON_DATA;
    }

    /**
     * Processes the data that comes via GET or POST.
     * If this page is supposed to do something with submitted
     * data do it here.
     * @return void
     */
    protected function processReceivedData(): void
    {
        parent::processReceivedData();
        // to do: call processReceivedData() for all members
    }

    /**
     * This main-function has the only purpose to create an instance
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
     * @return void
     */
    public static function main(): void
    {
        try {
            $page = new PageTemplate();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-Type: application/json; charset=UTF-8");
            //header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
PageTemplate::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >