<?php

/**
 * @param int|null $object_id
 * @return string
 */
function getCallingFunctionName(?int $object_id): string
{
    $caller = debug_backtrace();
    if (count($caller) < 3) return '*invalid backtrace*';
    $caller = $caller[2];

    $class = isset($caller['class']) ? $caller['class'] : '*UNKNOWN*';
    $function = isset($caller['function']) ? $caller['function'] : '*unknown*';

    return sprintf('%s[id:%s]::%s()', $class, $object_id ?? '', $function);
}
