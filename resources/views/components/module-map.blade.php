@php
    // Relative ES module imports need their own content versions after a deployment.
    $moduleImports = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(public_path('js'), FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if ($file->getExtension() === 'js') {
            $path = str_replace(DIRECTORY_SEPARATOR, '/', substr($file->getPathname(), strlen(public_path()) + 1));
            $moduleImports[asset($path)] = asset_version($path);
        }
    }
@endphp
<script type="importmap">{!! json_encode(['imports' => $moduleImports], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES) !!}</script>
