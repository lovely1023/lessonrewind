<?
error_reporting(1);
ini_set('max_execution_time', 5000);
/*$zip = new ZipArchive;
if ($zip->open('/home/webdemo1/public_html/bridesplanner/bridesplanner.zip') === TRUE) {
	//echo "IN";die;
    $zip->extractTo('/home/webdemo1/public_html/bridesplanner/');
    $zip->close();
    echo 'Archive extracted to/home/webdemo1/public_html/helpinghands/bridesplanner.zip !';die;
} else {
    echo 'Failed to open the archive!';die;
}
die;*/
//$rootPath = '/var/www';

$the_folder = '/';
$zip_file_name = 'lessonrewind(online).zip';

class FlxZipArchive extends ZipArchive {
        /** Add a Dir with Files and Subdirs to the archive;;;;; @param string $location Real Location;;;;  @param string $name Name in Archive;;; @author Nicolas Heimann;;;; @access private  **/
    public function addDir($location, $name) {
        $this->addEmptyDir($name);
         $this->addDirDo($location, $name);
     } // EO addDir;

        /**  Add Files & Dirs to archive;;;; @param string $location Real Location;  @param string $name Name in Archive;;;;;; @author Nicolas Heimann * @access private   **/
    private function addDirDo($location, $name) {
        $name .= '/';         $location .= '/';
      // Read all Files in Dir
        $dir = opendir ($location);
        while ($file = readdir($dir))    {
            if ($file == '.' || $file == '..') continue;
          // Rekursiv, If dir: FlxZipArchive::addDir(), else ::File();
            $do = (filetype( $location . $file) == 'dir') ? 'addDir' : 'addFile';
            $this->$do($location . $file, $name . $file);
        }
    } 
}

$za = new FlxZipArchive;
$res = $za->open($zip_file_name, ZipArchive::CREATE);
if($res === TRUE)    {
    $za->addDir($the_folder, basename($the_folder)); $za->close();
}
else  { echo 'Could not create a zip archive';}


?>