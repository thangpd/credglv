<?php
/**
 * @copyright © 2019 by GLV 
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */



namespace credglv\helpers;


use credglv\core\interfaces\HelperInterface;
use credglv\core\RuntimeException;

class FileHelper implements HelperInterface
{

    /**
     * Scan files inside a directory
     * @param $base_dir
     * @return array
     */
    public  function scanDir($base_dir, $regex = false)
    {
        $files = array();
        $base_dir =  realpath($base_dir);
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base_dir)) as $filename)
        {
            /** @var \SplFileInfo $filename */
            // filter out "." and ".."
            if ($filename->isDir() || ($regex && !preg_match($regex, $filename->getRealPath()))) continue;
            $files[] = $filename->getRealPath();
        }
        return $files;
    }

    /**
     * @param $base_dir
     * @param bool $regex
     * @return array
     */
    public  function scanChildDir($base_dir, $regex = false) {
        $dirs =  array();
        $base_dir =  realpath($base_dir);
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base_dir),
            \RecursiveIteratorIterator::SELF_FIRST);

        foreach($iterator as $file) {
            if($file->isDir()) {
                $path = $file->getRealpath();
                if ($regex && !preg_match($regex, $path)) continue;
                if (!in_array($path, $dirs)){
                    $dirs[] = $path;
                }
            }
        }
        return $dirs;
    }

    /**
     * Remove a directory with all it's contents
     * @param $base_dir
     */
    public  function removeDir($base_dir)
    {
        try {
            $base_dir =  realpath($base_dir);
            $it = new \RecursiveDirectoryIterator($base_dir, \RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new \RecursiveIteratorIterator($it,
                \RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file) {
                if ($file->isDir()){
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            rmdir($base_dir);
        } catch (\Exception $e) {
            //Log the error
        }

    }

    /**
     * COPY all contents from source to dest directory
     * @param $source
     * @param $dest
     */
    public  function copyDir($source, $dest)
    {
        if ( ! is_dir($dest) ) {
            mkdir($dest, 0755);
        }
        foreach (
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    /**
     * @param array $namespace namespace to detect file paths
     * @param string $type Used to pre-filter files. It limited number of files
     *                     in order to don't waste system memory
     * @param bool $filter
     * @return array
     */
    public function findAllClass($namespace = ['credglv'], $type = 'Controller', $filter = false)
    {

        $baseDir = realpath(CREDGLV_PATH);
        $rootNamespace = 'credglv';
        $classes = [];
        $namespaces = [];
        if (!is_array($namespace)) {
            $namespaces = [$namespace];
        } else {
            $namespaces = $namespace;
        }
        foreach ($namespaces as $namespace) {
            $namespace = str_replace($rootNamespace, '', $namespace);
            $path = CREDGLV_PATH . str_replace('\\' , DIRECTORY_SEPARATOR , $namespace);


            if (is_dir($path)) {
                if (!empty($type)) {
                    $files = $this->scanDir($path, '/' . $type .'\.php$/');
                } else {
                    $files = $this->scanDir($path, '/\.php$/');
                }

                foreach ($files as $file) {
                    //$className = str_replace('.php', '', $file);
                    $class = $rootNamespace .
                        str_replace('/', "\\", str_replace($baseDir, '' , str_replace('.php', '', $file)));
                    if (class_exists($class)) {
                        if ($filter) {
                            $reflect = new \ReflectionClass($class);
                            if(!$reflect->implementsInterface($filter)){
                                continue;
                            }
                        }
                        $classes[] = $class;
                    }
                }
            }
        }

        return $classes;
    }
}