<?php

declare(strict_types=1);

namespace App\UI\Editor;

use Nette;
use Tracy;
use Tracy\Debugger;
use Nette\Application\UI\Form;
use App\UI\_securedPresenter;

use App\Core\Models\Admin;
use Nette\DI\Attributes\Inject;


final class EditorPresenter extends _securedPresenter
{
    #[Inject]     public Admin      $model_admin;

    public function startup()
    {
        parent::startup();


        $this->setLayout(__DIR__ . '/@layout.latte');
    }


    public function renderDefault()
    {
        $latteFilePath = __DIR__ . '/../Salon/default.latte';

        $editablePages = $this->model_admin->getEditablePages();
        bdump($editablePages);
        $this->template->editablePages = $editablePages;

        //$this->processLatteFile($latteFilePath);
    }










    function processLatteFile($filePath)
    {
        $originalContent = file_get_contents($filePath);

        $dom = new \DOMDocument('1.0', 'UTF-8');

        libxml_use_internal_errors(true);

        $content = '<?xml encoding="UTF-8">' . $originalContent;
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);
        $element = $xpath->query("//*[@save-text]")->item(0);

        if (!$element) {
            throw new \RuntimeException("No element with 'save-text' attribute found in the file: $filePath");
        }

        $savedContent = $dom->saveHTML($element);
        $savedContent = trim(str_replace("\xEF\xBB\xBF", '', $savedContent));
        $savedContent = urldecode($savedContent);

        // Check if we need to process this content
        if ($this->isContentProcessed($savedContent)) {
            bdump('Content already fully processed, no changes needed.');
            return;
        }

        // Process the content to mark both text and images
        $updatedContent = $this->markElementsWithKeys($savedContent);

        // If no changes were made (which could happen if isContentProcessed missed something)
        if ($updatedContent === $savedContent) {
            bdump('No changes were made to the content.');
            return;
        }

        $updatedContent = (string)$updatedContent;
        $originalContent = (string)$originalContent;
        $savedContent = (string)$savedContent;

        bdump('Processing content with new markers.');
        $newContent = $this->replaceContentDivDOM($originalContent, $updatedContent);

        // Final fix for URL-encoded Nette template tags in image paths
        $newContent = $this->fixUrlEncodedNetteTags($newContent);

        file_put_contents($filePath, $newContent);

        bdump('File updated with new markers for text and images.');
    }

    /**
     * Fixes URL-encoded Nette template tags in the final HTML output
     */
    private function fixUrlEncodedNetteTags($content)
    {
        // Fix URL-encoded curly braces in image src attributes with proper key matching
        $content = preg_replace('/%7B=(%22|\")(\d+)(%22|\")%7Ct%7D/', '{="$2"|t}', $content);
        $content = preg_replace('/%7B="(\d+)"\|t%7D/', '{="$1"|t}', $content);

        // Fix incomplete/broken image src patterns where the data-key exists but src is broken
        $content = preg_replace(
            '/src="{\\$baseUrl}\/assets\/images\/%7B=" data-key="(\d+)">/',
            'src="{$baseUrl}/assets/images/{="$1"|t}" data-key="$1">',
            $content
        );

        // Fix src attribute with missing t filter
        $content = preg_replace(
            '/src="{\\$baseUrl}\/assets\/images\/%7B=(\d+)" data-key="(\d+)">/',
            'src="{$baseUrl}/assets/images/{="$2"|t}" data-key="$2">',
            $content
        );

        // Fix general case of broken src with data-key
        $content = preg_replace(
            '/(src="{\\$baseUrl}\/assets\/images\/.*?)" data-key="(\d+)">/',
            'src="{$baseUrl}/assets/images/{="$2"|t}" data-key="$2">',
            $content
        );

        // Fix URL-encoded plink tags in href attributes
        $content = preg_replace('/href="%7Bplink%20([^%]+)%7D"/', 'href="{plink $1}"', $content);

        // Fix URL-encoded plink tags with more complex structures (handle spaces, colons, etc.)
        $content = preg_replace('/href="%7Bplink%20([^%}]+)(:)([^%}]+)%7D"/', 'href="{plink $1:$3}"', $content);

        // Replace various forms of baseUrl
        $content = str_replace('%7B$baseUrl%7D', '{$baseUrl}', $content);
        $content = str_replace('&#123;&#36;baseUrl&#125;', '{$baseUrl}', $content);

        // General replacement to fix incomplete Nette tags
        $content = preg_replace('/%7B=/', '{=', $content);

        // Also fix any potential HTML-encoded characters
        $content = str_replace('&amp;', '&', $content);

        return $content;
    }



    private function isContentProcessed($content)
    {
        // Check if ANY content is marked with data-key attributes
        $hasMarkedContent = preg_match('/{="\d+"\|t}/', $content) ||
            preg_match('/data-key="\d+"/', $content);

        // If we have no marked content at all, process everything
        if (!$hasMarkedContent) {
            return false;
        }

        // Create a DOM to check if there are any unmarked elements
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);

        $content = '<?xml encoding="UTF-8">' . $content;
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $wrapper = $xpath->query("//*[@save-text]")->item(0);

        // Check for unmarked images in the assets/images directory
        $images = $xpath->query(".//img[not(@data-key)]", $wrapper);
        foreach ($images as $image) {
            if ($image instanceof \DOMElement) {
                $src = $image->getAttribute('src');
                // Only consider images from assets/images directory
                if (
                    strpos($src, '{$baseUrl}/assets/images/') !== false ||
                    strpos($src, '/assets/images/') !== false
                ) {
                    return false; // Found an unmarked image in assets/images, process the content
                }
            }
        }

        // Check for text elements without data-key
        // Exclude empty elements and specific structural elements that shouldn't be processed
        $textContainers = $xpath->query(".//*[not(@data-key) and not(self::script) and not(self::style) and normalize-space(text())]", $wrapper);

        foreach ($textContainers as $container) {
            // Skip elements that should not be processed (dividers, indicators, etc.)
            if ($container instanceof \DOMElement && $container->nodeName === 'div' && $container->hasAttribute('class')) {
                $classes = $container->getAttribute('class');
                // Skip these specific elements
                if (
                    strpos($classes, 'divider') !== false ||
                    strpos($classes, 'carousel-indicators') !== false ||
                    strpos($classes, 'image-overlay') !== false
                ) {
                    continue;
                }
            }

            // Skip elements that only contain other marked elements but no direct text
            $hasDirectText = false;
            foreach ($container->childNodes as $child) {
                if ($child->nodeType === XML_TEXT_NODE && trim($child->nodeValue) !== '') {
                    $hasDirectText = true;
                    break;
                }
            }

            if ($hasDirectText) {
                return false; // Found an unmarked text element that should be processed
            }
        }

        // Everything is already marked
        return true;
    }

    function markElementsWithKeys($content)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        // Disable automatic entity conversion to prevent encoding issues
        $dom->substituteEntities = false;
        libxml_use_internal_errors(true);

        $content = '<?xml encoding="UTF-8">' . $content;
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $wrapper = $xpath->query("//*[@save-text]")->item(0);

        // Get the current key index for new elements
        $keyIndex = $this->model_admin->getEditorKeyIndex();

        // Track if we've made any changes
        $hasChanges = false;

        // First: Process all images without data-key attribute
        $images = $xpath->query(".//img[not(@data-key)]", $wrapper);
        foreach ($images as $image) {
            if ($image instanceof \DOMElement) {
                // Extract the image path from src attribute
                $src = $image->getAttribute('src');

                // Only process images from the assets/images directory
                if (
                    strpos($src, '{$baseUrl}/assets/images/') === false &&
                    strpos($src, '/assets/images/') === false
                ) {
                    continue; // Skip images not in assets/images directory
                }

                // Parse the path to extract the relative portion
                $relativePath = '';

                // Check if the src contains {$baseUrl} and extract what comes after
                if (strpos($src, '{$baseUrl}') !== false) {
                    $parts = explode('{$baseUrl}', $src);
                    if (isset($parts[1])) {
                        // Remove leading slash and /assets/images/ prefix to store just the relative path
                        $relativePath = trim($parts[1], '/');
                        if (strpos($relativePath, 'assets/images/') === 0) {
                            $relativePath = substr($relativePath, 14); // Remove 'assets/images/'
                        }
                    }
                } else {
                    // For absolute paths or other formats
                    $path = parse_url($src, PHP_URL_PATH);
                    if (strpos($path, '/assets/images/') === 0) {
                        $relativePath = substr($path, 15); // Remove '/assets/images/'
                    } else if (strpos($path, 'assets/images/') === 0) {
                        $relativePath = substr($path, 14); // Remove 'assets/images/'
                    } else {
                        // Not in the assets/images directory, skip
                        continue;
                    }
                }

                // Skip if we couldn't extract a valid relative path
                if (empty($relativePath)) {
                    continue;
                }

                // Save the image path in the database with is_image=1
                $imageSaveArray = [
                    'id' => $keyIndex,
                    'cs' => $relativePath,
                    'is_image' => 1
                ];
                $this->model_admin->saveEditorTexts($imageSaveArray);

                // Set the data-key attribute on the image
                $image->setAttribute('data-key', (string)$keyIndex);

                // Replace the src attribute with the Nette filter format
                // Use createTextNode to avoid issues with entity encoding
                $newSrc = '{$baseUrl}/assets/images/{="' . $keyIndex . '"|t}';
                $image->setAttribute('src', $newSrc);

                $keyIndex++;
                $hasChanges = true;
            }
        }

        // Second: Process all text-containing elements without data-key attribute
        // Use a better selector to find elements with text content but without data-key
        $textElements = $xpath->query(".//*[not(self::script) and not(self::style) and not(@data-key) and normalize-space(text())]", $wrapper);

        // Track processed elements to avoid duplicates
        $processedElements = [];

        foreach ($textElements as $element) {
            if (!($element instanceof \DOMElement)) {
                continue;
            }

            // Skip elements that already have data-key or save-text
            if ($element->hasAttribute('data-key') || $element->hasAttribute('save-text')) {
                continue;
            }

            // Skip if element is a divider or structural-only element (empty div)
            if ($element->nodeName === 'div' && $element->hasAttribute('class')) {
                $classes = $element->getAttribute('class');
                // Skip divider elements or containers that should be left alone
                if (
                    strpos($classes, 'divider') !== false ||
                    strpos($classes, 'carousel-indicators') !== false ||
                    strpos($classes, 'image-overlay') !== false
                ) {
                    continue;
                }
            }

            // Skip if we've already processed this element
            $elementHash = spl_object_hash($element);
            if (isset($processedElements[$elementHash])) {
                continue;
            }

            // Get direct text children
            $textContent = '';
            foreach ($element->childNodes as $child) {
                if ($child->nodeType === XML_TEXT_NODE) {
                    $text = trim($child->nodeValue);
                    if (!empty($text)) {
                        $textContent = $text;
                        break; // Use the first non-empty text node
                    }
                }
            }

            // Skip if there's no actual text content
            if (empty($textContent)) {
                continue;
            }

            // Save the text to database
            $textSaveArray = [
                'id' => $keyIndex,
                'cs' => $textContent,
                'is_image' => 0
            ];
            $this->model_admin->saveEditorTexts($textSaveArray);

            // Mark the element with data-key
            $element->setAttribute('data-key', (string)$keyIndex);

            // Replace all direct text nodes with the translation tag
            $translationNode = $dom->createTextNode("{=\"$keyIndex\"|t}");
            $replaced = false;

            foreach ($element->childNodes as $child) {
                if ($child->nodeType === XML_TEXT_NODE && trim($child->nodeValue) !== '') {
                    $element->replaceChild($translationNode, $child);
                    $replaced = true;
                    break; // Only replace the first text node
                }
            }

            // In case we couldn't replace (rare case), append it
            if (!$replaced) {
                $element->appendChild($translationNode);
            }

            $processedElements[$elementHash] = true;
            $keyIndex++;
            $hasChanges = true;
        }

        // Third: Process any remaining text nodes that may be direct children of elements with other children
        $remainingTextNodes = $xpath->query(".//*[not(@data-key)]/text()[normalize-space()]", $wrapper);
        foreach ($remainingTextNodes as $textNode) {
            $text = trim($textNode->nodeValue);
            if (empty($text)) {
                continue;
            }

            $parentNode = $textNode->parentNode;

            // Skip if parent already has data-key or is in script/style tags
            if (
                in_array(strtolower($parentNode->nodeName), ['script', 'style']) ||
                ($parentNode instanceof \DOMElement && ($parentNode->hasAttribute('data-key') || $parentNode->hasAttribute('save-text')))
            ) {
                continue;
            }

            // Skip if parent is a divider or structural-only element
            if ($parentNode instanceof \DOMElement && $parentNode->nodeName === 'div' && $parentNode->hasAttribute('class')) {
                $classes = $parentNode->getAttribute('class');
                if (
                    strpos($classes, 'divider') !== false ||
                    strpos($classes, 'carousel-indicators') !== false ||
                    strpos($classes, 'image-overlay') !== false
                ) {
                    continue;
                }
            }

            // Skip if we've already processed this parent
            $parentHash = spl_object_hash($parentNode);
            if (isset($processedElements[$parentHash])) {
                continue;
            }

            // Save the text
            $textSaveArray = [
                'id' => $keyIndex,
                'cs' => $text,
                'is_image' => 0
            ];
            $this->model_admin->saveEditorTexts($textSaveArray);

            // Mark the parent with data-key and replace the text node
            if ($parentNode instanceof \DOMElement) {
                $parentNode->setAttribute('data-key', (string)$keyIndex);
            }

            $translationNode = $dom->createTextNode("{=\"$keyIndex\"|t}");
            $textNode->parentNode->replaceChild($translationNode, $textNode);

            $processedElements[$parentHash] = true;
            $keyIndex++;
            $hasChanges = true;
        }

        if (!$hasChanges) {
            return $content; // Return original content if no changes were made
        }

        $updatedContent = $dom->saveHTML($wrapper);

        $updatedContent = trim(str_replace("\xEF\xBB\xBF", '', $updatedContent));
        $updatedContent = urldecode($updatedContent);

        return $updatedContent;
    }


    function replaceContentDivDOM($originalContent, $updatedContent)
    {
        $parts = [];

        preg_match('/^({block\s+content})/', $originalContent, $blockMatch);
        $parts['block'] = $blockMatch[1] ?? '';

        preg_match('/{include\s+[^}]+}/', $originalContent, $includeMatch);
        $parts['include'] = $includeMatch[0] ?? '';

        preg_match('/({[*].*?[*]})/s', $originalContent, $commentMatch);
        $parts['comment'] = $commentMatch[0] ?? '';

        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = true;
        $doc->formatOutput = false;
        // Don't automatically encode special characters
        $doc->substituteEntities = false;

        libxml_use_internal_errors(true);
        $doc->loadHTML(
            '<?xml encoding="UTF-8">' . $updatedContent,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $updatedDiv = $doc->saveHTML($doc->documentElement);

        $updatedDiv = str_replace([
            '<?xml encoding="UTF-8">',
            '<!DOCTYPE html>',
            '<html>',
            '</html>',
            '<body>',
            '</body>',
            "\xEF\xBB\xBF"
        ], '', $updatedDiv);

        // Fix any URL encoded Nette template tags in image src attributes
        $updatedDiv = preg_replace('/(%7B=|&#123;=)(%22|\")(\d+)(%22|\")(%7Ct%7D|&#124;t&#125;)/', '{="$3"|t}', $updatedDiv);
        $updatedDiv = preg_replace('/src="(%7B|\{)(\$baseUrl)(%7D|\})/', 'src="{$baseUrl}', $updatedDiv);

        // Fix URL-encoded plink tags in href attributes
        $updatedDiv = preg_replace('/href="%7Bplink%20([^%]+)%7D"/', 'href="{plink $1}"', $updatedDiv);
        $updatedDiv = preg_replace('/href="%7Bplink%20([^%}]+)(:)([^%}]+)%7D"/', 'href="{plink $1:$3}"', $updatedDiv);
        $updatedDiv = preg_replace('/href="&#123;plink\s+([^&#;]+)&#58;([^&#;]+)&#125;"/', 'href="{plink $1:$2}"', $updatedDiv);

        $result = implode("\n", array_filter([
            $parts['block'],
            $parts['include'],
            $updatedDiv,
            $parts['comment']
        ]));

        // Apply other necessary replacements
        $result = str_replace(['%7B%24baseUrl%7D', '&#123;&#36;baseUrl&#125;'], '{$baseUrl}', $result);
        $result = str_replace('&lt;', '<', $result);
        $result = str_replace('&amp;', '&', $result);

        return trim($result);
    }




    public function handleSaveEditorTexts()
    {
        $data = (array)$_POST;
        $files = (array)$_FILES;
        $updatedData = [];

        // Process regular text data
        foreach ($data as $key => $value) {
            // Skip file upload related keys
            if (is_string($key) && (strpos($key, 'file_') === 0 || strpos($key, 'has_file_') === 0)) {
                continue;
            }

            // For image-related keys, only include if explicitly sent
            // (which means they were modified)
            $row = $this->model_admin->database->table('translates')->get($key);
            if ($row && $row->is_image) {
                // This is an image being modified, so include it in the update
                $updatedData[$key] = $value;
            } else {
                // For regular text fields, always include
                $updatedData[$key] = $value;
            }
        }

        // Process file uploads
        foreach ($files as $fieldName => $fileInfo) {
            if (is_string($fieldName) && strpos($fieldName, 'file_') === 0) {
                $key = substr($fieldName, 5); // Remove 'file_' prefix to get the key

                if ($fileInfo['error'] === UPLOAD_ERR_OK) {
                    // Generate unique filename with random number
                    $randomNumber = mt_rand(10000, 99999);
                    $extension = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
                    $filename = 'image_' . $randomNumber . '.' . $extension;

                    // Ensure uploads directory exists
                    $uploadDir = dirname(dirname(dirname(__DIR__))) . '/www/assets/images/uploads';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    // Save the file
                    $filePath = $uploadDir . '/' . $filename;
                    move_uploaded_file($fileInfo['tmp_name'], $filePath);

                    // Store the relative path in the database - just the path after assets/images/
                    $updatedData[$key] = 'uploads/' . $filename;

                    // Check if this is an update to an existing image or a new one
                    $row = $this->model_admin->database->table('translates')->get($key);
                    if (!$row || !$row->is_image) {
                        // This is a new image or a conversion from text to image
                        // Make sure to set is_image flag
                        $this->model_admin->database->table('translates')
                            ->where('id', $key)
                            ->update(['is_image' => 1]);
                    }
                }
            }
        }

        $response = $this->model_admin->updateEditorTexts($updatedData);
        $this->sendJson($response);
    }

    /**
     * Special fix for plink tags in href attributes
     */
    private function fixPlinkTags($content)
    {
        // Fix URL-encoded plink tags in href attributes (various forms)
        $content = preg_replace('/href="%7Bplink%20([^%]+)%7D"/', 'href="{plink $1}"', $content);
        $content = preg_replace('/href="%7Bplink%20([^%}]+)(:)([^%}]+)%7D"/', 'href="{plink $1:$3}"', $content);
        $content = preg_replace('/href="&#123;plink\s+([^&#;]+)&#58;([^&#;]+)&#125;"/', 'href="{plink $1:$2}"', $content);

        // Additional complex cases
        $content = preg_replace('/%7Bplink\s+([^%:]+):([^%}]+)%7D/', '{plink $1:$2}', $content);
        $content = preg_replace('/%7Bplink\s+([^%}]+)%7D/', '{plink $1}', $content);

        return $content;
    }

    public function handleFixTemplates()
    {
        // Get list of template files to fix
        $editablePages = $this->model_admin->getEditablePages();
        $fixedCount = 0;

        foreach ($editablePages as $page) {
            // Determine template path based on page entry
            // This depends on your folder structure, adjust paths as needed
            $presenterName = $page->presenter_name ?: 'Home';
            $actionName = $page->action_name ?: 'default';

            $templatePath = __DIR__ . "/../$presenterName/$actionName.latte";

            if (file_exists($templatePath)) {
                // Read the file
                $content = file_get_contents($templatePath);

                // Apply the general fix
                $fixedContent = $this->fixUrlEncodedNetteTags($content);

                // Special handler for image src with mismatched data-key
                $fixedContent = $this->fixImageSrcWithDataKeys($fixedContent);

                // Special handler for plink tags
                $fixedContent = $this->fixPlinkTags($fixedContent);

                // Save the file only if changed
                if ($content !== $fixedContent) {
                    file_put_contents($templatePath, $fixedContent);
                    $fixedCount++;
                }
            }
        }

        // Special handling for the Home/default.latte file which seems to have issues
        $homePath = __DIR__ . "/../Home/default.latte";
        if (file_exists($homePath)) {
            $content = file_get_contents($homePath);
            $fixedContent = $this->fixHomeTemplate($content);
            if ($content !== $fixedContent) {
                file_put_contents($homePath, $fixedContent);
                $fixedCount++;
            }
        }

        $this->flashMessage("Fixed $fixedCount template files.");
        $this->redirect('this');
    }

    /**
     * Special fix for the Home template
     */
    private function fixHomeTemplate($content)
    {
        // Find all img tags with data-key attributes
        preg_match_all('/<img[^>]*data-key="(\d+)"[^>]*>/i', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $fullTag = $match[0];
            $dataKey = $match[1];

            // Check if this is an incorrectly formatted src
            if (
                strpos($fullTag, '{="' . $dataKey . '"|t}') === false &&
                strpos($fullTag, 'assets/images') !== false
            ) {

                // Create a fixed version with the matching key in the filter
                $fixedTag = preg_replace(
                    '/(src="{\\$baseUrl}\/assets\/images\/)([^"]*)" data-key="' . $dataKey . '"/i',
                    'src="{$baseUrl}/assets/images/{="' . $dataKey . '"|t}" data-key="' . $dataKey . '"',
                    $fullTag
                );

                // Replace in the content
                $content = str_replace($fullTag, $fixedTag, $content);
            }
        }

        // Fix URL-encoded plink tags in href attributes
        $content = preg_replace('/href="%7Bplink%20([^%]+)%7D"/', 'href="{plink $1}"', $content);
        $content = preg_replace('/href="%7Bplink%20([^%}]+)(:)([^%}]+)%7D"/', 'href="{plink $1:$3}"', $content);
        $content = preg_replace('/href="&#123;plink\s+([^&#;]+)&#58;([^&#;]+)&#125;"/', 'href="{plink $1:$2}"', $content);

        return $content;
    }

    /**
     * Fix image src attributes that have data-key
     */
    private function fixImageSrcWithDataKeys($content)
    {
        // Find all img tags with data-key attributes
        preg_match_all('/<img[^>]*data-key="(\d+)"[^>]*>/i', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $fullTag = $match[0];
            $dataKey = $match[1];

            // If the src doesn't have the proper Nette filter with matching data-key
            if (strpos($fullTag, '{="' . $dataKey . '"|t}') === false) {
                // Extract src value
                preg_match('/src="([^"]*)"/i', $fullTag, $srcMatch);

                if (isset($srcMatch[1]) && strpos($srcMatch[1], 'assets/images') !== false) {
                    // Create fixed tag with proper key
                    $fixedTag = preg_replace(
                        '/src="([^"]*)"/i',
                        'src="{$baseUrl}/assets/images/{="' . $dataKey . '"|t}"',
                        $fullTag
                    );

                    // Replace in the content
                    $content = str_replace($fullTag, $fixedTag, $content);
                }
            }
        }

        return $content;
    }
}
