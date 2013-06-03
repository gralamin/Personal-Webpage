<div class="content">
    <div class="inner-content">
        <h1>Work</h1>
     <p> Below is a list of different projects that I have contributed to or created.
     </p>
<?php
     require_once("models".DIRECTORY_SEPARATOR."workItem.php");
     require_once("models".DIRECTORY_SEPARATOR."workGallery.php");
     require_once("settings.php");

     function createWorkItem($id) {
         $workItem = new WorkItem();
         $workGallery = new WorkGallery();
         $path = settings::path_from_root;
         $src = $workGallery->getItemThumb($id);
         $title = $workItem->getTitle($id);

         echo('<div class="outer-work-item">');
         if ($workItem->isNew($id)) {
             echo('<div class="new"></div>');
         }
         echo('<div class="work-item">');
         echo('    <div class="inner-work-item">');

         echo('        <a href="' . $path . '?p=article&id=' . $id . '">');

         echo('            <img src="' . $src . '" width="100%" title="' .
              $title . '" alt="' . $title . '"></img>');

         echo('        </a>');
         echo('    </div>');
         echo('    <h3>' . $title . '</h3>');
         echo('</div>');
         echo('</div>');
     }

$aWorkItem = new WorkItem();
$idList = $aWorkItem->getIdList();
foreach($idList as $id) {
    createWorkItem($id);
}
?>

        <div class="clear-both"></div>

    </div>
</div>
