<?php

namespace App\Supports;

use LogicException;

/**
 * Class CSVReader
 *
 * @package App\Supports\Facades
 */
class CSVReader
{
    /**
     * URL指定による外部サイトからのCSV読み取り
     *
     * @param string $url
     * @return array
     */
    public function readByUrl(string $url): array
    {
        $results = file_get_contents($url);
        $results = mb_convert_encoding($results, 'utf-8', 'UTF-8, sjis-win');
        $temp = tmpfile();
        $csv = [];
        
        fwrite($temp, $results);
        rewind($temp);
        
        while ($results = fgetcsv($temp)) {
            $csv[] = $results;
        }
        fclose($temp);
        
        return $csv;
    }
    
    /**
     * URL指定による外部サイトからのCSV読み取り
     *
     * 1行目を配列のキーとして情報を取得することができます
     *
     * @param string $url
     * @return array
     */
    public function readWithKeyByUrl(string $url): array
    {
        $csv = $this->readByUrl($url);
        if ((count($csv)) <= 1) {
            throw new LogicException(sprintf('CSVの取得に失敗しました。URL : %s', $url));
        }
        
        $header_column_names = [];
        $results = [];
        foreach ($csv as $row_index => $row_data) {
            if ($row_index === 0) {
                $header_column_names = $csv[0];
                continue;
            }
            
            $attributes = [];
            foreach ($row_data as $column_index => $cell) {
                $attributes[$header_column_names[$column_index]] = $cell;
            }
            
            $results[] = $attributes;
        }
        
        return $results;
    }
}
