<?php

class LogParser
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var mixed
     */
    private $file;

    /**
     * @var int
     */
    private $views;

    /**
     * @var array
     */
    private $urls;

    /**
     * @var integer
     */
    private $traffic;

    /**
     * @var array
     */
    private $code;

    /**
     * Регулярное вырожение для разбивания строки по проблелам
     * исключая пробелы в кавычках и скобках
     */
    const PATTERN = '/\[(.*?)\]|\((.*?)\)|\"(.*?)\"|\S+/';

    const SEARCH_GOOGLE = 'google';
    const SEARCH_BING = 'bing';
    const SEARCH_BAIDU = 'baidu';
    const SEARCH_YANDEX = 'yandex';

    const KEY_FIRST_VALUE = 0;
    const KEY_CODE = 7;
    const KEY_TRAFFIC = 8;
    const KEY_URL = 9;


    /**
     * @param $pathFile
     * @throws ErrorException
     */
    public function __construct($pathFile)
    {
        $this->validatePath($pathFile);
        $this->checkFileLog();
    }

    /**
     * @throws ErrorException
     */
    private function validatePath($pathFile)
    {
        if (empty($pathFile)) {
            throw new ErrorException('Puth not found');
        };

        $this->path = $pathFile;
    }

    private function checkFileLog()
    {
        if (!file_exists($this->path)) {
            throw new ErrorException('File not found');
        }
    }

    private function parser()
    {
        $this->openFile();
        $this->parsingStr();
        $this->closeFile();
    }

    private function openFile()
    {
        $this->file = fopen("access_log", 'r') or die("не удалось открыть файл");
    }

    private function closeFile()
    {
        fclose($this->file);
    }

    private function parsingStr()
    {
        while (!feof($this->file)) {
            $str = htmlentities(fgets($this->file));

            preg_match_all(self::PATTERN, $str, $matches, PREG_SET_ORDER);

            $this->views++;
            $this->code[] = (int)$matches[self::KEY_CODE][self::KEY_FIRST_VALUE];
            $this->urls[] = (string)$matches[self::KEY_URL][self::KEY_FIRST_VALUE];
            $this->traffic += (int)$matches[self::KEY_TRAFFIC][self::KEY_FIRST_VALUE];
        }
    }

    public function responseLog()
    {
        $this->parser();
        return $this->response();
    }

    private function response()
    {
        return json_encode(
            [
                'views'       => $this->views(),
                'urls'        => $this->uniqueUrls(),
                'traffic'     => $this->traffic(),
                'crawlers'    => $this->crawlers(),
                'statusCodes' => $this->statusCodes(),
            ]
        );
    }

    /**
     * @return int
     */
    private function views(): int
    {
        return $this->views;
    }

    /**
     * @return int
     */
    private function uniqueUrls(): int
    {
        return count(array_count_values($this->urls));
    }

    /**
     * @return int
     */
    private function traffic(): int
    {
        return $this->traffic;
    }

    /**
     * @return int[]
     */
    private function crawlers(): array
    {
        $googleCount = 0;
        $bingCount = 0;
        $baiduCount = 0;
        $yandexCount = 0;
        foreach ($this->urls as $url) {
            if (strpos($url, self::SEARCH_GOOGLE)) {
                $googleCount++;
            }

            if (strpos($url, self::SEARCH_BING)) {
                $bingCount++;
            }

            if (strpos($url, self::SEARCH_BAIDU)) {
                $baiduCount++;
            }

            if (strpos($url, self::SEARCH_YANDEX)) {
                $yandexCount++;
            }
        }

        return [
            self::SEARCH_GOOGLE => $googleCount,
            self::SEARCH_BING   => $bingCount,
            self::SEARCH_BAIDU  => $baiduCount,
            self::SEARCH_YANDEX => $yandexCount,
        ];
    }

    /**
     * @return array
     */
    private function statusCodes(): array
    {
        return array_count_values($this->code);
    }
}
