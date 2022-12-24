<?php

function slugify($text, string $divider = '-')
{
    $text = str_replace(' &', $sequence = uniqid('div'), $text);
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, $divider);
    $text = preg_replace('~-+~', $divider, $text);
    $text = strtolower($text);
    $text = str_replace($sequence, '-', $text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

$header = <<<MARKDOWN
# Awesome Livestream

A curated list of amazingly awesome streaming software, tools, resources and shiny things.

## Contributing and Collaborating

## Table of Contents

- Awesome Livestream
MARKDOWN;

$contents = [$header];

$awesome = json_decode(file_get_contents('awesome.json'), true);

usort($awesome['categories'], function ($a, $b) {
    return $a['name'] <=> $b['name'];
});
usort($awesome['projects'], function ($a, $b) {
    return $a['name'] <=> $b['name'];
});

foreach ($awesome['categories'] as $category) {
    $contents[] = sprintf("  - [%s](#%s)", $category['name'], slugify($category['name']));
}

foreach ($awesome['categories'] as $category) {
    $contents[] = '';
    $contents[] = sprintf("### %s\n", $category['name']);
    $contents[] = sprintf("*%s*\n", $category['description']);
    foreach ($awesome['projects'] as $project) {
        if (!in_array($category['id'], $project['categories'])) continue;

        $contents[] = sprintf(
            "* [%s](%s) - %s",
            $project['name'],
            $project['url'],
            $project['description']
        );
    }
}

file_put_contents('README.md', implode(PHP_EOL, $contents));

echo "Done!" . PHP_EOL;