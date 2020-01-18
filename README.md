# CodeIgniter-Multiple-File-Upload-Library
CodeIgniter 3.x Library - Multiple File Upload and Validation

## Installation

Simply copy the `Upload_documents.php` file to your applications library directory.

## Required

* PHP Extension `gd` and `gmp`

## Use

## Fields

```php
$this->load->library('upload_documents');

$output = $this->upload_documents->run([
    'field_name'    => 'file_name', // Field Name
    'upload_path'   => './upload/images/', // Upload Path
    'allowed_types' => 'jpg|png|jpeg', // File Types
    // 'encrypt_name' => TRUE, // Change File Name
    // 'overwrite' => TRUE,
    // 'max_size' => 55,
    // 'max_filename' => '255',
    // 'max_width' => 18222,
    // 'min_width' => 19222,
    // 'max_height' => 1722,
    // 'min_height' => 1262,
    'thumbs' => [
        'small' => [ 'w' => 28, 'h'=> 28, 'path' => './upload/images/'],
        // 'key' => [ 'w' => wight, 'h'=> height, 'path' => 'upload path'],
    ],
]);
```

### Upload Only Single File

```php
// Load Library: Document Upload
$this->load->library('upload_documents');

$output = $this->upload_documents->run([
    'field_name'    => 'file_field_name', // Field Name
    'upload_path'   => './assets/image/', // Upload Path
    'allowed_types' => 'jpg|png|jpeg', // File Types
    'max_size' => 200, // Maximum File Size (KB)
    'thumbs' => [ // Image Thumbnail Size
        "x128" => [ 'w' => 128, 'h'=> 128, 'path' => './assets/image/thumb/'],
        "x192" => [ 'w' => 192, 'h'=> 192, 'path' => './assets/image/thumb/'],
        "x256" => [ 'w' => 256, 'h'=> 256, 'path' => './assets/image/thumb/'],
        "x512" => [ 'w' => 512, 'h'=> 512, 'path' => './assets/image/thumb/'],
    ],
]);

print_r($output); // $output['status'] = TRUE/FALSE
```

### Upload Multiple File One Time

Firstly check file validation then uploading start.

```php
// Load Library: Document Upload
$this->load->library('upload_documents');

// Upload Multiple Files
$output = $this->upload_documents->multi([
    'field_name'    => 'file_field_name', // Field Name
    'upload_path'   => './assets/image/', // Upload Path
    'allowed_types' => 'jpg|png|jpeg', // File Types
    'max_size' => 300, // Maximum File Size (KB)
    'thumbs' => [
        "x256" => [ 'w' => 256, 'h'=> 256, 'path' => './assets/image/thumb/'],
        "x512" => [ 'w' => 512, 'h'=> 512, 'path' => './assets/image/thumb/'],
    ],
]);
```

### Fetch Upload Images and Doc

```php
// Load Library: Document Upload
$this->load->library('upload_documents');

$output = $this->upload_documents->show_image(base_url('upload/images/'), 'file_name.png', 'key');
var_dump($output);

// AND

[
  // Original File
  "org"  => $this->upload_documents->show_image(base_url('/assets/building/'), $fileName),
  // Image Thumbnail's
  "x128"  => $this->upload_documents->show_image(base_url('/assets/building/thumb/'), $fileName, 'x128'),
  "x192"  => $this->upload_documents->show_image(base_url('/assets/building/thumb/'), $fileName, 'x192'),
  "x256"  => $this->upload_documents->show_image(base_url('/assets/building/thumb/'), $fileName, 'x256'),
  "x512"  => $this->upload_documents->show_image(base_url('/assets/building/thumb/'), $fileName, 'x512'),
]
```

## Reporting Issues ☢️

If you have a problem with this plugin or found any bug, please open an issue on [GitHub](https://github.com/ctechhindi/CodeIgniter-Multiple-File-Upload-Library/issues).

## Copyright and License ©️

Code copyright 2020 ctechhindi. Code released under the [MIT license](http://www.opensource.org/licenses/MIT)
