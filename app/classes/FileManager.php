<?php
/**
 * Created by PhpStorm.
 * User: Tudor
 * Date: 4/13/14
 * Time: 5:10 PM
 */

class FileManager {



   public function upload_files (array $fileArray,array $options){

       $error = 0;
       $destinationPath = public_path(). '/uploads/'. $options['id'] . '/';
       $path_array = array();
       //create new photo records and attach them to the event
       foreach($fileArray as $file){

           $filename = $file->getClientOriginalName();
           //$extension =$file->getClientOriginalExtension(); //if you need extension of the file
           $file = $file->move($destinationPath, $filename);
           $path_array[] = $destinationPath.$filename;
           if( is_null($file) ) {
               $error = 1;
               break; //stop the file uploads
           }
       }

       if($error){
           File::deleteDirectory($destinationPath);
           return array(
               "error" => 1
           );
       }

       return array(
           "error" => 0,
           "path_array" => $path_array
       );
   }
} 