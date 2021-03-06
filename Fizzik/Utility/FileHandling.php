<?php

namespace Fizzik\Utility;

class FileHandling {
    public static function generateTempFileIdentifier($seed) {
        return hash("sha256", "".$seed.time());
    }

    /*
     * Gives chmod permissions to the file at the given path
     */
    public static function ensurePermissions($file, $chmod = 0777) {
        chmod($file, $chmod);
    }

    public static function ensureDirectoryPermissionsRecursively($file, $chmod = 0777) {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($file));
        foreach($iterator as $item) {
            chmod($item, $chmod);
        }
    }

    public static function deleteAllFilesMatchingPattern($pattern) {
        array_map("unlink", glob($pattern));
    }

    /*
     * Will iteratively delete a file or directory and its contents at the resolved path
     */
    public static function deleteFileOrDirectoryAndItsContents($path) {
        if (is_dir($path) === true) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                if (in_array($file->getBasename(), array('.', '..')) !== true) {
                    if ($file->isDir() === true) {
                        rmdir($file->getPathName());
                    }
                    else if (($file->isFile() === true) || ($file->isLink() === true)) {
                        unlink($file->getPathname());
                    }
                }
            }
            return rmdir($path);
        }
        else if ((is_file($path) === true) || (is_link($path) === true)) {
            return unlink($path);
        }
        return false;
    }

    /*
     * Will recursively delete a directory and its contents at the resolved path
     */
    public static function deleteDirectoryAndItsContents($dir) {
        self::deleteDirectoryAndItsContentsInternal($dir, TRUE);
    }

    /*
     * Will recursively delete directory's contents at the resolved path, but not the directory itself
     */
    public static function deleteDirectoryContents($dir) {
        self::deleteDirectoryAndItsContentsInternal($dir, FALSE);
    }

    private static function deleteDirectoryAndItsContentsInternal($dir, $deletedir = FALSE) {
        foreach (glob("{$dir}/*") as $file) {
            if(is_dir($file)) {
                self::deleteDirectoryAndItsContentsInternal($file, TRUE);
            }
            else {
                unlink($file);
            }
        }
        if ($deletedir) rmdir($dir);
    }

    /*
     * Creates a copy of the file at originalpath, with the copy located at newpath
     * Uses cp
     */
    public static function copyFile($originalpath, $newpath) {
        $path1 = escapeshellarg($originalpath);
        $path2 = escapeshellarg($newpath);
        shell_exec("cp $path1 $path2");
    }

    /*
     * Ensures the existence of the directory, by creating it if it doesn't exist
     * at the given path, and/or isn't a valid directory
     *
     * Uses recursive mkdir
     */
    public static function ensureDirectory($path, $chmod = 0777) {
        if (!file_exists($path) || !is_dir($path)) {
            mkdir($path, $chmod, true);
            self::ensureDirectoryPermissionsRecursively($path, $chmod);
        }
    }

    public static function getFileExtension($filepath) {
        return pathinfo($filepath, PATHINFO_EXTENSION);
    }

    public static function isValidExtension($ext, $validexts) {
        return in_array($ext, $validexts);
    }

    public static function isValidMimeType($type, $validtypes) {
        return in_array($type, $validtypes);
    }

    public static function isValidSize($size, $maxSize) {
        return $size <= $maxSize;
    }

    public static function getBytesForKilobytes($kilobytes) {
        return $kilobytes * 1024;
    }

    public static function getBytesForMegabytes($megabytes) {
        return self::getBytesForKilobytes($megabytes * 1024);
    }

    public static function getBytesForGigabytes($gigabytes) {
        return self::getBytesForMegabytes($gigabytes * 1024);
    }

    public static function getBytesForTerabytes($terabytes) {
        return self::getBytesForGigabytes($terabytes * 1024);
    }
}