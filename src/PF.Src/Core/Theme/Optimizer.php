<?php

namespace Core\Theme;


class Optimizer
{

    protected $importedFiles = [];

    /**
     *
     */
    const IMPORT_REG = '/^\@import\s+["\']([^"\']+)["\'];/mi';

    /**
     *
     */
    const COMMENT_REG = '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/m';

    /**
     * @var array
     */
    protected $importPaths = [];

    /**
     * @return array
     */
    public function getImportPaths()
    {
        return $this->importPaths;
    }

    /**
     * @param array $importPaths
     */
    public function setImportPaths($importPaths)
    {
        $this->importPaths = $importPaths;
    }

    /**
     * @param $content
     *
     * @return string
     */
    public function optimize($content)
    {
//        $content = preg_replace(self::COMMENT_REG, ' ', $content);

        $result = preg_replace_callback(self::IMPORT_REG, function ($match) {
            return $this->parseImport($match[1]);
        }, $content);

        return $result;
    }

    /**
     * @param      $file_uri
     *
     * @return string
     */
    public function parseImport($file_uri)
    {
        $files = [
            $file_uri . '.less',
            //            $file_uri . '.css',
            $file_uri,
        ];

        $filename = null;
        $found = false;

        foreach ($this->importPaths as $path) {
            foreach ($files as $file) {
                if (is_file($filename = $path . '/' . $file)) {
                    $found = true;
                    break;
                }
            }
            if ($found) {
                break;
            }
        }


        if (!$found) {
            throw new \RuntimeException(sprintf('Unexpected @import "%s";', $file_uri));
        }


        if (!empty($this->importedFiles[$filename])) {
            return '';
        }

        if (!empty($_GET['debug'])) {
            echo '@import "', $filename . '";' . PHP_EOL;
        }

        $this->importedFiles[$filename] = true;

        $working_dir = dirname($filename);

        array_push($this->importPaths, $working_dir);

        // parse conent

        $content = file_get_contents($filename);
//        $content = preg_replace(self::COMMENT_REG, '', $content);

        $result = preg_replace_callback(self::IMPORT_REG, function ($item) {
            return $this->parseImport($item[1]);
        }, $content);

        array_pop($this->importPaths);

        return $result;
    }
}