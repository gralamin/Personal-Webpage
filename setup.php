<html>
<head>
<title>Gral_CMS Setup</title>
</head>
<body style='color: white; background-color: grey'>
     <div style='width: 800px; margin-left: auto; margin-right: auto; margin-top: 10px; background-color: black; padding: 10px 10px 10px 10px'>
<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
     echo("Requiring colors<br>");
require_once("colors.php");
     echo("Requiring settings<br>");
require_once("settings.php");
     echo("Requiring renderer<br>");
require_once("renderer.php");
     echo("Requiring authors<br>");
require_once("models".DIRECTORY_SEPARATOR."author.php");
     echo("Requiring workGallery<br>");
require_once("models".DIRECTORY_SEPARATOR."workGallery.php");
     echo("Requiring image<br>");
require_once("models".DIRECTORY_SEPARATOR."image.php");
     echo("Requiring workItem<br>");
require_once("models".DIRECTORY_SEPARATOR."workItem.php");
     echo("Requiring workText<br>");
require_once("models".DIRECTORY_SEPARATOR."workText.php");
     echo("Requiring main<br>");
require_once("models".DIRECTORY_SEPARATOR."main.php");
     echo("Done-requiring<br>");

myPrint("Step 1: Please edit settings.php to have a user name, password, " .
        "and database.", ColorEnum::BLUE);

myPrint("Step 2: Connecting to mysql.", ColorEnum::BLUE);

$con = new mysqli("localhost", Settings::user_name, Settings::password);

if (mysqli_connect_errno()) {
    myPrint("Failed to connect!", ColorEnum::RED);
    die(mysqli_connect_error());
}

myPrint("Connected Successfully!", ColorEnum::GREEN);

myPrint("Step 3: Checking if database created.", ColorEnum::BLUE);

$success = $con->select_db(Settings::database);
if (!$success) {
    myPrint("Warning: DB Not created. Attempting to create DB.",
            ColorEnum::YELLOW);
    if (!$con->query("CREATE DATABASE " . Settings::database)) {
        myPrint("Could not create database " . Settings::database,
                ColorEnum::RED);
        $con->close();
        die("( " . $con->errno . " ) " . $con->error);
    } else {
        $success = $con->select_db(Settings::database);
        if ($success) {
            myPrint("DB created Successfully.", ColorEnum::GREEN);
        } else {
            throw new Exception("Could not select db just created");
        }
    }
}
myPrint("DB Selected Successfully.", ColorEnum::GREEN);
$con->close();

myPrint("Step 4: Check if all tables exist, and create any table that does " .
        "not.", ColorEnum::BLUE);
foreach ($ALL_MODEL_LIST as $modelName) {
    $model = new $modelName();
    checkTable($model);
}

myPrint("Step 5: Insert basic data.", ColorEnum::BLUE);
myPrint("Data to insert: author: Glen Nelson gralamin@gralamin.com",
        ColorEnum::PURPLE);
$myAuthor = new Author();
if ($myAuthor->createRow(array('first_name' => 'Glen',
                               'last_name' => 'Nelson',
                               'email' => 'gralamin@gralamin.com'))) {
    myPrint("Inserted Glen Nelson successfully", ColorEnum::GREEN);
} else {
    myPrint("Failed to insert Glen Nelson. Please check if the error is due " .
            "to a duplicate", ColorEnum::YELLOW);
}

$myRepo = "https://github.com/gralamin/Personal-Webpage";
myPrint("Data to insert: workItem: 'Website Source code' " .
        $myRepo . "TODAY 1",
        ColorEnum::PURPLE);
// set the default timezone to use. Available since PHP 5.1
date_default_timezone_set('MST');
$myWorkItem = new WorkItem();
if ($myWorkItem->createRow(array('name' => 'Website Source Code',
                                 'repository_url' => $myRepo,
                                 'submission_date' => date("Y-m-d H:i:s"),
                                 'author_id' => 1))) {
    myPrint("Created work item successfully", ColorEnum::GREEN);
} else {
    myPrint("Failed to insert work item. Please check if the error is due" .
    "to a duplicate", ColorEnum::YELLOW);
}

$myWorkText = new WorkText();
if ($myWorkText->createRow(array('work_id' => 1,
                                 'body' => 'article.txt'))) {
    myPrint("Created work text successfully", ColorEnum::GREEN);
} else {
    myPrint("Failed to insert work text. Please check if the error is due " .
    "to a duplicate", ColorEnum::YELLOW);
}

myPrint("Attempting to display work text in a pre tag", ColorEnum::PURPLE);
print("<div class='article'>");
renderArticle(1, FALSE);
print("</div>");
print("<div class='article-src'>");
renderArticle(1, TRUE);
print("</div>");

myPrint("Attempting to upload Image", ColorEnum::PURPLE);

$myWorkImage = new Image();
if ($myWorkImage->createRow(array('img' => 'images/personal-website.png'))) {
    myPrint("Created work image successfully", ColorEnum::GREEN);
} else {
    myPrint("Failed to insert work image. Please check if the error is due " .
            "to a duplicate, and ensure the file is less then " .
            Settings::max_file_size . " bytes in size", ColorEnum::YELLOW);
}

myPrint("Associating Image with work item 1", ColorEnum::PURPLE);
$myWorkGallery = new WorkGallery();
if ($myWorkGallery->createRow(array('work_id' => 1,
                                  'image_id' => 1,
                                  'caption' => "v1.0 of the website"
))) {
    myPrint("Associated Work Image successfully", ColorEnum::GREEN);
} else {
    myPrint("Failed to Associate Work Image", ColorEnum::RED);
}

myPrint("Attempting to display image with id 1:", ColorEnum::PURPLE);
print("<img src=\"" . Settings::path_from_root .  "images.php?id=1\">");

myPrint("Attempting to display thumbnail for image with id 1 and height of " .
        "width of 100px:", ColorEnum::PURPLE);
print("<img src=\"" . Settings::path_from_root .
       "thumbnail.php?id=1&width=100\">");

myPrint("Install Successful!", ColorEnum::GREEN);

function checkTable($tableModel) {
    $tableName = $tableModel->getName();
    myPrint("Checking if table \"" . $tableName . "\" exists",
                ColorEnum::PURPLE);
    if($tableModel->exists()) {
        myPrint($tableName . " exists!", ColorEnum::GREEN);
    } else {
        $printableSchema = "<br>" .
            str_replace("\n", "<br>", $tableModel->getSchema()) . "<br>";
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
