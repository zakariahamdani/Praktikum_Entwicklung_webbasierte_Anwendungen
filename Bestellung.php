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
class Bestellung extends Page
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

    /**
     * Fetch all data that is necessary for later output.
     * Data is returned in an array e.g. as associative array.
     * @return array An array containing the requested data. 
     * This may be a normal array, an empty array or an associative array.
     */
    protected function getViewData(): array
    {
        // to do: fetch data for this view from the database
        // to do: return array containing data

        $pizzas = array();
        $SQLabfrage = "SELECT * FROM article";

        $Recordset = $this->_database->query($SQLabfrage);

        if (!$Recordset) {
            throw new Exception("Abfrage fehlgeschlagen: " . $this->database->error);
        }

        $record = $Recordset->fetch_assoc();

        $i = 0;
        while ($record) {
            $pizzas[$i] = array("id" => $record["article_id"], "name" => $record["name"], "picture" => $record["picture"], "price" => $record["price"]);
            $i++;
            $record = $Recordset->fetch_assoc();
        }

        return $pizzas;
    }

    protected function displayPizza($pizza): void
    {
        $pizzaId = $pizza["id"];
        $pizzaName = $pizza["name"];
        $pizzaPrice = $pizza["price"];
        $pizzaPicture = $pizza["picture"];

        echo <<<HTML
        <div class="pizza">
            <img class="pizzaPicture" src="$pizzaPicture" onclick="addPizza('$pizzaId', '$pizzaName', '$pizzaPrice')">
            <p class="name">$pizzaName</p>
            <p> - </p>
            <p class="price" id="$pizzaId">$pizzaPrice</p>
            <p>€</p>

        </div>
        
        
    HTML;
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
        $pizzas = $this->getViewData(); //NOSONAR ignore unused $data
        $this->generatePageHeader('Bestellung'); //to do: set optional parameters
        // to do: output view of this page
        echo <<<HTML
            <main>
            <h1>Bestellung</h1>
            <div class="divider"></div>
            <div class="bestellcontent">
            <div class="speiseKarte">
            <h2>Speisekarte</h2>
        HTML;
        foreach ($pizzas as $pizza) {
            $this->displayPizza($pizza);
        }

        $pizzas_number = sizeof($pizzas);

        echo <<<HTML
            </div>
            <div class="warenkorb">
                <h2>Warekorb</h2>
                <form class="warenkorbForm" accept-charset="UTF-8" id="basketFor" method="post" action="Bestellung.php">
                <select name="selected_pizzas[]" id="ordered_Pizzas" multiple></select>
                <button type="button" onclick="deleteAll()">Alle löschen</button>
                <button type="button" onclick="deleteSelection()">Auswahl löschen</button>
                <br/>
                <p>Preis: </p><p id="total">00.00</p><p>€</p>
                <br/>
                <input type="text" placeholder="Ihre Adresse" name="address" value="" id="address" onkeyup="checkConditions()"/>
                <br/>
                <button type="submit" id="submitButton" disabled onclick="selectPizzas()">Bestellen</button>
                </form>
            </div>
            </div>
        </main>
HTML;
        $this->generatePageFooter();
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

        if (count($_POST)) { //kamen Post Daten hier her?
            //var_dump($_POST);
            if (isset($_POST["selected_pizzas"]) and isset($_POST["address"])) {

                $address = $this->_database->real_escape_string($_POST["address"]);
                //SQL losschicken für Adresseingabe
                $sql_address = "INSERT INTO ordering (address) VALUES('$address')";
                $recordset = $this->_database->query($sql_address);

                //Fehlerabfrage
                if (!$recordset) {
                    throw new Exception("Fehler in Abfrage: " . $this->_database->error);
                }
                $ordering_id = $this->_database->insert_id;
                $_SESSION["BestellungID"] = $ordering_id;

                //ab hier pizzen in db schicken
                //var_dump($_POST["selected_pizzas"]);

                foreach ($_POST["selected_pizzas"] as $pizza) {
                    $sql_pizza = "INSERT INTO ordered_article(ordering_id, article_id, status) 
                                  VALUES ('$ordering_id','$pizza',0)";
                    $recordset = $this->_database->query($sql_pizza);
                    
                    if (!$recordset) {
                        throw new Exception("Fehler in Abfrage: " . $this->_database->error);
                    }
                }
            }


            $location = "Bestellung.php";

            // Weiterleitung nach PRG-Pattern
            header("Location: " . $location);
            die; //hier ausnahhmesweise OK! //Skript132
        }
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
            $page = new Bestellung();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
Bestellung::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >