<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Finder\Finder;
use felpado as f;

$src = __DIR__ . '/../src';
$finder = Finder::create()->name('*.php')->name('*.xml')->in($src);

$fixUses = f\partial('preg_replace', '/^(use Akamon\\\\OAuth2\\\\Server)(?:.+);$/m', '');
$fixNamespace = f\partial('preg_replace', '/(namespace Akamon\\\\OAuth2\\\\Server)(.+);/', '$1;');
$fixFullyQualifiedClasses = f\partial('preg_replace', '/(\\\\?Akamon\\\\OAuth2\\\\Server)(?:\\\\(\w+))+(?!;)/m', '$1\\\\$2');

$fixTripleEof = function ($content) use (&$fixTripleEof) {
    $search = "/\n\n\n/m";
    $replace = f\partial('preg_replace', $search, "\n\n");

    return preg_match($search, $content) ? $fixTripleEof($replace($content)) : $content;
};

$fix = f\compose($fixTripleEof, $fixFullyQualifiedClasses, $fixUses, $fixNamespace);

$byePsr0 = function ($file) use ($fix) {
    $newContent = $fix(file_get_contents($file));
    file_put_contents($file, $newContent);
};

f\each($byePsr0, $finder);
