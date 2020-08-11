<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($_SERVER['REQUEST_METHOD']=='POST'){
    ob_flush();
    if ($_FILES["mesh_export"]["error"] == UPLOAD_ERR_OK) {
        print_r($_FILES);
        $data = file_get_contents($_FILES["mesh_export"]["tmp_name"]);
        $datetime = $_FILES["mesh_export"]["name"];
        $support = $datetime[0];
        $filepath = "uploads/";
        $filepath .= $datetime;
        $filepath .= ".stl";
        $file = fopen($filepath, 'w');//creates new file
        fwrite($file, $data);
        fclose($file);

        $layerheightpath = "uploads/";
        $layerheightpath .= $datetime;
        $layerheightpath .= "layer-height.txt";
        $layerheight = fread(fopen($layerheightpath, 'r'), filesize("$layerheightpath"));

        $command = 'slic3r --layer-height ';
        $command .= $layerheight;
        if ($support == "2") {
            $command .= ' --support-material --support-material-threshold 55 --support-material-spacing 2 uploads/';
        } else {
            $command .= ' uploads/';
        }
        $command .= $datetime;
        $command .= '.stl --output gcodes/';
        $command .= $datetime;
        $command .= '.gcode';
        $command_output = shell_exec($command);

        $command2 = './timeCalc.py gcodes/';
        $command2 .= $datetime;
        $command2 .= '.gcode > gcodes/output.txt';
        $command_output2 = shell_exec($command2);
    } else {
        switch ($_FILES["mesh_export"]["error"]) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        echo $message;
    }
}

if ($_SERVER['REQUEST_METHOD']=='GET'){
    $gcode_time = file_get_contents('gcodes/output.txt', FALSE, NULL, -9, 8);
    if (!isset($answer)) $answer = new stdClass();
    $answer->time = $gcode_time;
    $answer_JSON = json_encode($answer);
    echo $answer_JSON;

    unlink("gcodes/output.txt");
}
?>