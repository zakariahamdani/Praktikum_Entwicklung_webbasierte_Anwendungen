<?php

declare(strict_types=1);
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
        $data = array(); //leeres Array für unseren Rückgabewert
        $sql = "SELECT article.name, ordered_article.status, ordered_article.ordered_article_id
                FROM article
                INNER JOIN ordered_article
                ON article.article_id = ordered_article.article_id
                where ordered_article.status <= 3
                ORDER BY ordered_article.ordered_article_id";
        $recordset = $this->_database->query($sql); //recordset ist eine Kopie unserer Daten

        if (!$recordset) {
            throw new Exception("Fehler in Abfrage: " . $this->_database->error);
        }

        $data = $recordset->fetch_all(1);
        $recordset->free();

        //var_dump($data);
        return $data;
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
        $data = $this->getViewData();
        $this->generatePageHeader('Bäcker'); //to do: set optional parameters
        // to do: output view of this page

        echo <<<HTML
        <main>
        <h1>Bestellte Pizzen</h1>
        <div class="divider"></div>
        <form accept-charset="UTF-8" id="baeckerForm" method="post" action="baecker.php">

                <section id ="Pizzaliste">
                <table>
                <caption>Status der Pizza</caption>
                <tr>
                    <th>Pizza</th>
                    <th>bestellt</th>
                    <th>im Ofen</th>
                    <th>fertig</th>
                </tr>
HTML;

        foreach ($data as $key => $value) {
            //var_dump($value);

            $pizzaName = $value["name"];
            $pizzaQueue = $value["ordered_article_id"];

            $checked_bestellt = "";
            $checked_imOfen = "";
            $checked_fertig = "";

            //je nach dem wie der Status der jeweiligen Pizza ist, setze das richtige radiobutton auf checked
            if ($value["status"] == 0)
                $checked_bestellt = "checked";
            else if ($value["status"] == 1)
                $checked_imOfen = "checked";
            else if ($value["status"] >= 3)
                $checked_fertig = "checked";

            echo <<< HTML
                <tr>
                    <td><p>$pizzaName</p></td>
                    <td><input  type="radio" onclick="document.forms['baeckerForm'].submit();" name=$pizzaQueue value="0" $checked_bestellt></td>             
                    <td><input type="radio" onclick="document.forms['baeckerForm'].submit();" name=$pizzaQueue value="1" $checked_imOfen></td>
                    <td><input type="radio" onclick="document.forms['baeckerForm'].submit();" name=$pizzaQueue value="3" $checked_fertig></td>
                </tr>
HTML;
        }
        echo <<<HTML
                </table>

                </section>
                
        </form>
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
        // to do: call processReceivedData() for all members

        if (count($_POST)) { //kamen Post Daten hier her?

            if (isset($_POST)) {
                foreach ($_POST as $key => $value) { //assoziatives array
                    //var_dump($key);
                    $sql_status_update = "UPDATE `ordered_article` 
                                          SET `status`='$value'
                                          WHERE `ordered_article_id`='$key'";
                    //hier forsetzen. Möchte den status auf backer seite submitten und in die db eintragen

                    $recordset = $this->_database->query($sql_status_update); //recordset ist eine Kopie unserer Daten

                    if (!$recordset) {
                        throw new Exception("Fehler in Abfrage: " . $this->_database->error);
                    }
                }
            }

            $location = "baecker.php";
            // Weiterleitung nach PRG-Pattern
            header("HTTP/1.1 303 See Other");
            header("Location: " . $location);
            die; //hier ausnahmesweise OK! //Skript 132
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
            $page = new PageTemplate();
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
PageTemplate::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >