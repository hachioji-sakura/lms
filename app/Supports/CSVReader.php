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
    
    /**
     * URL指定による外部サイトからのCSV読み取り
     *
     * @param string $url
     * @return array
     */
    public function readByUrl($url)
    {
        $str = file_get_contents($url);
        $is_win = strpos(PHP_OS, 'WIN') === 0;
        
        // Windowsの場合は Shift_JIS、Unix系は UTF-8で処理
        if ($is_win) {
            setlocale(LC_ALL, 'Japanese_Japan.932');
        } else {
            setlocale(LC_ALL, 'ja_JP.UTF-8');
            $str = mb_convert_encoding($str, 'utf-8', 'UTF-8, sjis-win');
        }
        
        $result = [];
        $fp = fopen('php://temp', 'r+');
        fwrite($fp, str_replace(["\r\n", "\r"], "\n", $str));
        rewind($fp);
        while ($row = fgetcsv($fp)) {
            // windows の場合はSJIS-win → UTF-8 変換
            $result[] = $is_win
                ? array_map(function ($val) {
                    return mb_convert_encoding($val, 'UTF-8', 'SJIS-win');
                }, $row)
                : $row;
        }
        fclose($fp);
        
        return $result;
    }
}
