<?php

class Minify_Api_Core
{
   public function writeMinifySetting($data)
    {
        $filename = APPLICATION_PATH .'/temporary/yn_minify.php';
        $fp = fopen($filename, 'w');
        fwrite($fp, '<?php return ' . var_export($data, true) . ';?>');
        fclose($fp);
    }

    public function readMinifySetting()
    {
        if (is_readable(APPLICATION_PATH .'/temporary/yn_minify.php'))
        {
            $minifyConfig = (
            include APPLICATION_PATH .'/temporary/yn_minify.php');
                    
            return $minifyConfig;
        }
        return array();
    }
}
