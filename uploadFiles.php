<?php
chmod("./", 777);
chmod("./Documentos/", 777);
$dir_subida = './Documentos/';

if(isset($_GET['files']))
{
    $id = isset($_GET['id']) ? $_GET['id'] : "0";
    $table = isset($_GET['table']) ? $_GET['table'] : "temporal";

    $dir_subida .= "$table/";
    if(!file_exists($dir_subida))
    {
        mkdir($dir_subida, 0777);
    }

    $dir_subida .= "$id/";
    if(!file_exists($dir_subida))
    {
        mkdir($dir_subida, 0777);    
    }

    if($directory = opendir($dir_subida))
    {
        foreach($_FILES as $file)
        {
            move_uploaded_file($file['tmp_name'], $dir_subida .basename($file['name']));
        }
        closedir($directory);
    }
}
?>