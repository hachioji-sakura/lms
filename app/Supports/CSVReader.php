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
     * パス指定（URLでも可能）によるCSV読み取り
     *
     * 1行目を配列のキーとして情報を取得することができます
     *
     * @param string  $path
     * @return array
     */
    public function readWithKeyByPath(string $path): array
    {
        $csv = $this->readByUrl($path);
        if ((count($csv)) <= 1) {
            throw new LogicException(sprintf('CSVの取得に失敗しました。URL : %s', $path));
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
     * パス指定（URLでも可能）によるCSV読み取り
     *
     * @param  string  $path
     * @return array
     */
    public function readByUrl($path)
    {
        $str = file_get_contents($path);
        $is_win = strpos(PHP_OS, 'WIN') === 0;

        // Windowsの場合は Shift_JIS、Unix系は UTF-8で処理
        if ($is_win) {
            setlocale(LC_CTYPE, 'C');
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
                    return mb_convert_encoding($val, 'utf-8', 'UTF-8, sjis-win');
                }, $row)
                : $row;
        }
        fclose($fp);

        return $result;
    }
}
