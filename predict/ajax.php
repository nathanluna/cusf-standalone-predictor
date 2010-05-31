<?php
require_once("includes/functions.inc.php");

$action = $_GET['action'];

switch($action) {
case "getCSV":
    $uuid = $_GET['uuid'];
    $fh = fopen("preds/".$uuid."/flight_path.csv", "r") or die("No CSV for UUID");
    $data = array();
    while (!feof($fh)) {
        $line = trim(fgets($fh));
        array_push($data, $line);
    }
    $returned = json_encode($data);
    echo $returned;
    break;

case "JSONexists":
    $uuid = $_GET['uuid'];
    if(file_exists("preds/$uuid/progress.json")) {
        echo true;
    } else {
        echo false;
    }
    break;

case "submitForm":
    $software_available = array("gfs", "gfs_hd");

    $pred_model = array();

    if ( isset($_POST['submit'])) {
        // form was submitted, let's run a pred!
        // first, make a model from the form data
        if ( !$pred_model = createModel($_POST)) {
            echo false;
            break;
        }

        // verify the model
        if ( !verifyModel($pred_model, $software_available) ) {
            echo false;
            break;
        }

        // make a sha1 hash of the model for uuid
        $pred_model['uuid'] = makesha1hash($pred_model); 

        // now we have a populated model, run the predictor
        runPred($pred_model);
        echo true . "|" . $pred_model['uuid'] . "|" . $pred_model['timestamp'];
    } else {
        echo "The form submit function was called without any data";
        echo false;
    }
    break;

default:
    echo "Couldn't interpret 'action' variable";
    break;

}

?>
