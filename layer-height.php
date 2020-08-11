<?php
    //creating a file with layerheight
    $json = file_get_contents('php://input');
    $obj = json_decode($json);
    $layerheight = $obj->selected;
    $time = $obj->time;
    $file2path = "uploads/";
    $file2path .= $time;
    $file2path .= "layer-height.txt";
    $file2 = fopen($file2path, 'w');//creates new file
    fwrite($file2, $layerheight);
    fclose($file2);
    //$_FILES["mesh_export"]["error"] == UPLOAD_ERR_OK &&
?>