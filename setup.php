<html>
<head>
<title>Gral_CMS Setup</title>
</head>
<body style='color: white; background-color: grey'>
     <div style='width: 800px; margin-left: auto; margin-right: auto; margin-top: 10px; background-color: black; padding: 10px 10px 10px 10px'>
<?php
require_once("colors.php");
require_once("settings.php");
require_once("models".DIRECTORY_SEPARATOR."author.php");
require_once("models".DIRECTORY_SEPARATOR."workGallery.php");
require_once("models".DIRECTORY_SEPARATOR."workItem.php");
require_once("models".DIRECTORY_SEPARATOR."workText.php");
require_once("models".DIRECTORY_SEPARATOR."main.php");

myPrint("Step 1: Please edit settings.php to have a user name, password, and database.", ColorEnum::BLUE);

myPrint("Step 2: Connecting to mysql.", ColorEnum::BLUE);

$con = new mysqli("localhost", $user_name, $password);

if (mysqli_connect_errno()) {
    myPrint("Failed to connect!", ColorEnum::RED);
    die(mysqli_connect_error());
}

myPrint("Connected Successfully!", ColorEnum::GREEN);

myPrint("Step 3: Checking if database created.", ColorEnum::BLUE);

$success = $con->select_db($database);
if (!$success) {
    myPrint("Warning: DB Not created. Attempting to create DB.", ColorEnum::YELLOW);
    if (!$con->query("CREATE DATABASE " . $database)) {
        myPrint("Could not create database " . $database, ColorEnum::RED);
        $con->close();
        die("( " . $con->errno . " ) " . $con->error);
    } else {
        $con->select_db($database);
        myPrint("DB created Successfully.", ColorEnum::GREEN);
    }
}
myPrint("DB Selected Successfully.", ColorEnum::GREEN);
$con->close();

myPrint("Step 4: Check if all tables exist, and create any table that does not.", ColorEnum::BLUE);
foreach ($ALL_MODEL_LIST as $modelName) {
    $model = new $modelName();
    checkTable($model);
}

myPrint("Step 5: Insert basic data.", ColorEnum::BLUE);
myPrint("Data to insert: author: Glen Nelson gralamin@gralamin.com", ColorEnum::PURPLE);
$myAuthor = new Author();
if ($myAuthor->createRow(array('first_name' => 'Glen',
                               'last_name' => 'Nelson',
                               'email' => 'gralamin@gralamin.com'))) {
    myPrint("Inserted Glen Nelson successfully", ColorEnum::GREEN);
} else {
    myPrint("Failed to insert Glen Nelson. Please check if the error is due to a duplicate", ColorEnum::YELLOW);
}

myPrint("Data to insert: workItem: 'Website Source code' https://github.com/gralamin/Personal-Webpage TODAY 1", ColorEnum::PURPLE);
// set the default timezone to use. Available since PHP 5.1
date_default_timezone_set('MST');
$myWorkItem = new WorkItem();
if ($myWorkItem->createRow(array('name' => 'Website Source Code',
                                 'repository_url' => 'https://github.com/gralamin/Personal-Webpage',
                                 'submission_date' => date("Y-m-d H:i:s"),
                                 'author_id' => 1))) {
    myPrint("Created work item successfully", ColorEnum::GREEN);
} else {
    myPrint("Failed to insert work item. Please check if the error is due to a duplicate", ColorEnum::YELLOW);
}

$myWorkText = new WorkText();
if ($myWorkText->createRow(array('work_id' => 1,
                                 'body' => 'article.txt'))) {
    myPrint("Created work text successfully", ColorEnum::GREEN);
} else {
    myPrint("Failed to insert work text. Please check if the error is due to a duplicate", ColorEnum::YELLOW);
}

myPrint("Attempting to display work text in a pre tag", ColorEnum::PURPLE);
$text = $myWorkText->retrieveText(1);
print("<pre>" . $text . "</pre>");

$myWorkImage = new WorkGallery();
if ($myWorkImage->createRow(array('work_id' => 1,
                                  'img' => 'images/personal-website.png'))) {
    myPrint("Created work image successfully", ColorEnum::GREEN);
} else {
    myPrint("Failed to insert work image. Please check if the error is due to a duplicate", ColorEnum::YELLOW);
}

myPrint("Attempting to display image with id 1:", ColorEnum::PURPLE);
print("<img src=\"/test2/images.php?id=1\">");

myPrint("Install Successful!", ColorEnum::GREEN);

function checkTable($tableModel) {
    $tableName = $tableModel->getName();
    myPrint("Checking if table \"" . $tableName . "\" exists", ColorEnum::PURPLE);
    if($tableModel->exists()) {
        myPrint($tableName . " exists!", ColorEnum::GREEN);
    } else {
        $printableSchema = "<br>" . str_replace("\n", "<br>", $tableModel->getSchema()) . "<br>";
        myPrint($tableName . " does not exist. Creating with schema \"" .
                $printableSchema . "\".", ColorEnum::YELLOW);
        if ($tableModel->create()) {
            myPrint($tableName . " Created!", ColorEnum::GREEN);
        }
    }
}

function myPrint($string, $color) {
    print_color($string, $color, False);
}


?>
</div>
</body>