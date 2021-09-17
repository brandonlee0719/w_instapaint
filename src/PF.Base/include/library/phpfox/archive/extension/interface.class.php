<?php


interface Phpfox_Archive_Extension_Interface
{
    /**
     * Test if the server has support for working with tar.gz files
     *
     * @return bool True if support is there | False if no support
     */
    public function test();

    /**
     * Compress data into the archive
     *
     * @param string $sFile File or full path to folder we should compress
     *
     * @param string $sFolder
     *
     * @return mixed Return bool false if we where unable to create the archive
     *               | Return the Archive name if we were able to create it.
     */
    public function compress($sFile, $sFolder);

    /**
     * Extracts data from the archive
     *
     * @param string $sFile     Full path to the archive
     * @param string $sLocation Final location of where to place the extracted
     *                          content
     */
    public function extract($sFile, $sLocation);
}