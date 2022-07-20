<?php
define("AJAX_ERROR", 128);
define("LANGUAGE_ERROR_RU", 256);
define("DEFAULT_FORMAT_ERROR", 8);
define("WEB_FORMAT_ERROR", 500);

namespace FileWork;

use App\Kernel\WorkData\Cam;

class FileWork
{
    /**
     * Default path to save file
     * 
     * @var string
     */

    protected $defaultPath = __DIR__;

    /**
     * Name file from save database 
     * 
     * @var string
     */

    protected $fileName;

    /**
     * Name file original client 
     * 
     * @var string
     */

    protected $originalName;

    /**
     * Format message
     * 
     * @var string
     */

    protected $format;

    protected $repo;

    public function __construct($file, $pathStoragePublic, $formatMessage = DEFAULT_FORMAT_ERROR, $stringCheckFormat = '', $noPublicRepo = false)
    {
        $this->format = $formatMessage;
        $this->save($file, $pathStoragePublic, $stringCheckFormat, $noPublicRepo);
    }

    /**
     * Checks the file format and returns an error
     * 
     * @param string $stringName
     * @param File $file
     */

    protected function checkFormatFile($stringName, $file)
    {
        $formatFile = str_replace('|', '/', $stringName);
        if (!$this->check($stringName, strtolower($file->getClientOriginalExtension()))) (new FormatMessage)->getResponse([
            'Invalid file extension. Required format: ' . $formatFile,
            'Неверное расширение файла. Необходимый формат: ' . $formatFile
        ], 400, $this->format);
    }

    /**
     * 
     * Сhecking in array
     * 
     * @param string $string 
     * Enumeration of values that have access, enumeration goes through "|". Example: one|two|three 
     * 
     * @param  string $current
     * Checked value
     * 
     * @return boolean true/false
     */

    function check($string, $current)
    {
        $mass = explode('|', $string);
        if (in_array($current, $mass)) return true;
        return false;
    }

    /**
     * Sets a unique name for the file, and also saves its original name
     * 
     * @param File $file
     */

    protected function setUniqueName($file)
    {
        $this->fileName = trim(preg_replace("/[^0-9a-z.]+/i", "", $this->translitName(time() . $file->getClientOriginalName())));
        $this->originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    }

    /**
     * Saves the file to the desired URL and gives an error if something went wrong
     * 
     * @param File $file
     * @param string $pathStoragePublic
     */

    protected function saveFile($file, $pathStoragePublic, $noPublic)
    {
        if ($noPublic) $pathPublic = '';
        else mb_substr($pathStoragePublic, 0, 1) == '/' ? $pathPublic = $this->defaultPath . '/app/public' : $this->defaultPath . '/app/public/';
        $resultMoveFile = $file->move($pathPublic . $pathStoragePublic, $this->fileName);
        if (empty($resultMoveFile)) (new FormatMessage)->getInternalError(false, $this->format);
        $this->repo = $pathPublic . $pathStoragePublic;
    }

    protected function save($file, $pathSave, $stringCheckFormat = '', $noPublic)
    {
        empty($stringCheckFormat) ?: $this->checkFormatFile($stringCheckFormat, $file);
        $this->setUniqueName($file);
        $this->saveFile($file, $pathSave, $noPublic);
    }

    protected function translitName($text)
    {
        $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', ' ', ',', '/', '(', ')', ';');
		$lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya', '', '.', '-', '', '', '');
		return str_replace($rus, $lat, $text);
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getOriginalName()
    {
        return $this->originalName;
    }

    public function getRepo()
    {
        return $this->repo;
    }
}
