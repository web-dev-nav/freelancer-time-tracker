@if(!empty($name))
{{ $name }}

@endif
{{ trim(html_entity_decode(strip_tags(preg_replace('/<br\s*\/?>|<\/p>/i', "\n", (string) ($body_html ?: $body ?? ''))), ENT_QUOTES | ENT_HTML5, 'UTF-8')) }}
