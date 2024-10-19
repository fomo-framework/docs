<?php

ob_start();

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/assets/includes/header.html';

$markdown = file_get_contents(__DIR__ . '/docs.md');

// Convert the markdown to HTML
$Parsedown = new Parsedown();
$html = $Parsedown->text($markdown);

// find all <h2> tags and add an id attribute, class, and necessary tags with the text content
$headings = [];
$last_h1_tag = '';
$html = preg_replace_callback('/<(h1|h2)>(.*?)<\/(h1|h2)>/', function($matches) use (&$headings, &$last_h1_tag) {
	$raw_text = $matches[2];
	// make the id seo friendly
	$id = strtolower(str_replace(' ', '-', $matches[2]));
	$id = preg_replace('/[^a-z0-9-]/', '', $id);
	$tag = $matches[1];
	// h1 gets treated differently than h2
	if($tag === 'h1') {
		$headings[$id] = [ 'text' => $raw_text, 'children' => [] ];
		$last_h1_tag = $id;
	} else {
		$headings[$last_h1_tag]['children'][] = [ 'text' => $raw_text, 'id' => $id ];
	}
	return "</div></section><section class=\"doc-section\"><{$matches[1]} class=\"section-title\" id=\"$id\">{$matches[2]}</{$matches[1]}><div class=\"section-block\">";
}, $html);

// add a class="table table-bordered" to all tables
$html = str_replace('<table>', '<table class="table table-bordered">', $html);

echo $html;

// This will add the $headings array to the sidebar to be foreached
require __DIR__ . '/assets/includes/sidebar.php';

require __DIR__ . '/assets/includes/footer.html';

file_put_contents(__DIR__ . '/index.html', ob_get_clean());