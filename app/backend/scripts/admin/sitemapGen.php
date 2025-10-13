<?php
function generate_sitemap($base_url = 'https://pixlshare.cc/', $directory = '.', $output_dir = '.', $sitemap_file = 'sitemap.xml')
{
  $sitemap = new SimpleXMLElement('<urlset/>');
  $sitemap->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

  // Recursive function to scan directories
  function scan_directory($directory, $base_url, $sitemap, $current_dir = '')
  {
    $files = scandir($directory);

    foreach ($files as $file) {
      if ($file !== '.' && $file !== '..') {
        $file_path = "$directory/$file";

        // Skip the backend directory and pageScript.php
        if (($file === 'backend' || $file === 'assets' && is_dir($file_path)) || $file === 'pageScript.php') {
          continue; // Skip this directory or file
        }

        if (is_dir($file_path)) {
          // Recurse into directories
          scan_directory($file_path, $base_url, $sitemap, "$current_dir/$file");
        } elseif (is_file($file_path)) {
          // Add .html and .php files to the sitemap
          $file_extension = pathinfo($file, PATHINFO_EXTENSION);
          if ($file_extension === 'html' || $file_extension === 'php') {
            $url = $sitemap->addChild('url');
            $loc = $base_url . ltrim($current_dir, '/') . '/' . $file;
            $url->addChild('loc', htmlspecialchars($loc));
            $url->addChild('lastmod', date('Y-m-d', filemtime($file_path)));
            $url->addChild('changefreq', 'monthly');
            $url->addChild('priority', '0.8');
          }
        }
      }
    }
  }

  // Start scanning the directory
  scan_directory($directory, $base_url, $sitemap);

  // Ensure output directory exists
  if (!is_dir($output_dir)) {
    mkdir($output_dir, 0777, true);
  }

  // Save the XML sitemap to the specified output directory
  $sitemap->asXML(rtrim($output_dir, '/') . '/' . $sitemap_file);
}

// Set the base URL, directory to scan, and output directory
$base_url = 'https://pixlshare.cc/';
$directory = '../../../'; // Directory to scan for HTML and PHP files
$output_dir = '../../../'; // Output directory where sitemap.xml will be saved

// Generate the sitemap
generate_sitemap($base_url, $directory, $output_dir);

// Ensure $sitemap_file is defined in the same scope
$sitemap_file = 'sitemap.xml';

echo "Sitemap has been generated and saved to $output_dir$sitemap_file.";