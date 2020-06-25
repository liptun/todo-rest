<?php

function d($data = null): void
{
    var_dump($data);
}

function dd($data = null): void
{
    d($data);
    die;
}
