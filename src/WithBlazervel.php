<?php

namespace Blazervel\Blazervel;

trait WithBlazervel
{
    public function getRootView(): string
    {
        return 'blazervel::app';
    }
}