<?php

namespace App\Supports\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class CSVReader
 *
 * @package App\Supports\Facades
 */
class CSVReader extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \App\Supports\CSVReader::class;
    }
}
