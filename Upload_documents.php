<?php defined('BASEPATH') OR exit('No direct script access allowed');

/** 
 * Codeigniter Upload All Documents
 * 
 * @author: Jeevan Lal
 * @version: v0.0.5
 */
/*-----------------------
    How to Use 
------------------------

    $this->load->library('upload_documents');

    $output = $this->upload_documents->run([
        'field_name'    => 'image',
        'upload_path'   => './upload/images/',
        'allowed_types' => 'jpg|png|jpeg',
        // 'encrypt_name' => TRUE,
        // 'overwrite' => TRUE,
        // 'max_size' => 55,
        // 'max_filename' => '255',
        // 'max_width' => 18222,
        // 'min_width' => 19222,
        // 'max_height' => 1722,
        // 'min_height' => 1262,
        'thumbs' => [
            'small' => [ 'w' => 28, 'h'=> 28, 'path' => './upload/images/' ],
        ],
    ]);

    print_r($output);
    ---------------------------------------------------------
    Show Image

    $output = $this->show_image( base_url('upload/images/'), 'a45aea237b7540ba412d862aaf053b2d_.png', 'small');
    var_dump($output);

 */

class Upload_Documents
{
    protected $__upload_path = "./upload/images/";
    protected $__allowed_types = "jpg|png|jpeg";
    protected $__encrypt_name = TRUE;
    protected $__overwrite = FALSE;
    protected $__remove_spaces = TRUE;
    protected $__max_size = 50;
    protected $__max_filename = '255';
    protected $__max_width = FALSE;
    protected $__min_width = FALSE;
    protected $__max_height = FALSE;
    protected $__min_height = FALSE;

    protected $__thumbs_path = "./upload/images/thumb/";
    protected $__temp_image  = 'assets/_images/user.jpg'; // show image default path 
    
    public function __construct() {
        $this->CI =& get_instance();
    }

    /**
     *  Upload All Documents
     * -------------------------------
     * @param: array()
     */
    public function run(array $param)
    {
        // Image Field Name Validation
        if(!isset($param['field_name']) OR empty($param['field_name']) OR !is_string($param['field_name'])) {
            return [
                'status' => FALSE,
                'message' => "field name not valid."
            ];
        }

        // Upload Documents Path Validation
        if(isset($param['upload_path']) AND !is_string($param['upload_path'])) {
            return [
                'status' => FALSE,
                'message' => "document upload path not valid."
            ];
        } else {
            if(isset($param['upload_path']))
                $this->__upload_path = $param['upload_path'];

            if(!is_dir($this->__upload_path)) {
                return [
                    'status' => FALSE,
                    'message' => "document upload path not exists."
                ];
            }
        }

        // Document Allowed Types Validation
        if(isset($param['allowed_types']) AND !is_string($param['allowed_types'])) {
            return [
                'status' => FALSE,
                'message' => "document allowed types not valid."
            ];
        }else {
            if(isset($param['allowed_types']))
                $this->__allowed_types = $param['allowed_types'];
        }

        // Image Thumb Array Validation
        if(isset($param['thumbs']) AND is_string($param['thumbs'])) {
            return [
                'status' => FALSE,
                'message' => "thumb array not valid."
            ];
        }

        // Encrypt Name
        if(isset($param['encrypt_name']))
            $this->__encrypt_name = $param['encrypt_name'];

        // Overwrite Name
        if(isset($param['overwrite']))
            $this->__overwrite = $param['overwrite'];

        // Max Size Name
        if(isset($param['max_size']))
            $this->__max_size = $param['max_size'];

        // Max Filename
        if(isset($param['max_filename']))
            $this->__max_filename = $param['max_filename'];

        // Max Width
        if(isset($param['max_width']))
            $this->__max_width = $param['max_width'];

        // Min Width
        if(isset($param['min_width']))
            $this->__min_width = $param['min_width'];

        // Max Height
        if(isset($param['max_height']))
            $this->__max_height = $param['max_height'];

        // Min Height
        if(isset($param['min_height']))
            $this->__min_height = $param['min_height'];
            
        $_field_name  = $param['field_name'];
        $_thumbs      = (isset($param['thumbs']))? $param['thumbs']:"";
        $_file_data   = array();   // store file info once uploaded

        $config['upload_path']    = $this->__upload_path;
        $config['allowed_types']  = $this->__allowed_types;
        $config['encrypt_name']   = $this->__encrypt_name;
        $config['overwrite']      = $this->__overwrite;
        $config['remove_spaces']  = $this->__remove_spaces;
        $config['max_size']       = $this->__max_size;
        $config['max_filename']   = $this->__max_filename;
        $config['max_width']      = $this->__max_width;
        $config['min_width']      = $this->__min_width;
        $config['max_height']     = $this->__max_height;
        $config['min_height']     = $this->__min_height;

        $this->CI->load->library('upload', $config);
        $this->CI->upload->initialize($config);
        if (!$this->CI->upload->do_upload($_field_name)) {
            return [
                'status' => FALSE,
                'message' => $this->CI->upload->display_errors('', '')
            ];
        }else
        {
            $_file_data = $this->CI->upload->data();

            if(isset($_thumbs) AND is_array($_thumbs) AND !empty($_thumbs))
            {
                $orig_raw_name  = $_file_data['raw_name']; // original upload raw_name
                $orig_file_ext  = $_file_data['file_ext']; // original upload file extension

                // Image Resize
                foreach ($_thumbs as $thumbs_key => $thumbs_value) 
                {
                    if(isset($thumbs_value['path']) AND !empty($thumbs_value['path']) AND is_string($thumbs_value['path']))
                        $this->__thumbs_path = $thumbs_value['path'];

                    // Resize Image Name
                    $new_image = $this->__thumbs_path.$orig_raw_name.'_'.$thumbs_key.$orig_file_ext;

                    $config = array(
                        'source_image'   => $_file_data['full_path'],
                        'new_image'      => $new_image,
                        'maintain_ratio' => true,
                        'width'          => $thumbs_value['w'],
                        'height'         => $thumbs_value['h']
                    );

                    // load library
                    $this->CI->load->library('image_lib');
                    $this->CI->image_lib->initialize($config);
                    if (!$this->CI->image_lib->resize()) {
                        return $this->CI->image_lib->display_errors();
                    }
                    $this->CI->image_lib->clear();
                }
            }

            return [
                'status' => TRUE,
                // 'message' => '',
                'data' => [
                    'original_name' => $_file_data['file_name'],
                    'raw_name' => $_file_data['raw_name'],
                    'file_ext' => $_file_data['file_ext'],
                ],
            ];
        }
    }

    /**
     * [Multiple]: Upload All Documents
     * -------------------------------
     * @param: array()
     */
    public function multi(array $param) {

        // File Name Validation
        if (empty($param['field_name']) OR !is_string($param['field_name'])) {
            return ['status' => FALSE, 'message' => "File field name not valid."];
        }

        // Upload File Path Validation
        if (isset($param['upload_path']) AND !is_string($param['upload_path'])) {
            return [ 'status' => FALSE, 'message' => "File upload path not valid."];
        } else {

            if (isset($param['upload_path']))
                $this->__upload_path = $param['upload_path'];

            if (!is_dir($this->__upload_path)) {
                return ['status' => FALSE, 'message' => "File upload path not exists."];
            }
        }

        // File Allowed Types Validation
        if (isset($param['allowed_types']) AND !is_string($param['allowed_types'])) {
            return ['status' => FALSE, 'message' => "File allowed types not valid."];
        } else {
            if(isset($param['allowed_types']))
                $this->__allowed_types = $param['allowed_types'];
        }

        // Image Thumb Array Validation
        if (isset($param['thumbs']) AND is_string($param['thumbs'])) {
            return ['status' => FALSE, 'message' => "File thumb array not valid."];
        }

        // Encrypt Name
        if(isset($param['encrypt_name']))
            $this->__encrypt_name = $param['encrypt_name'];

        // Overwrite Name
        if(isset($param['overwrite']))
            $this->__overwrite = $param['overwrite'];

        // Max Size Name
        if(isset($param['max_size']))
            $this->__max_size = $param['max_size'];

        // Max Filename
        if(isset($param['max_filename']))
            $this->__max_filename = $param['max_filename'];

        // Max Width
        if(isset($param['max_width']))
            $this->__max_width = $param['max_width'];

        // Min Width
        if(isset($param['min_width']))
            $this->__min_width = $param['min_width'];

        // Max Height
        if(isset($param['max_height']))
            $this->__max_height = $param['max_height'];

        // Min Height
        if(isset($param['min_height']))
            $this->__min_height = $param['min_height'];

        // Firstly: Check All File Validation
        $isErrors = $this->_check_validation($param['field_name']);
        if ($isErrors['total_errors'] !== 0 && !empty($isErrors['errors'])) {
            return [
                'status' => FALSE,
                'errors' => $isErrors['errors']
            ];
        }

        $fieldName = $param['field_name'];
        $_thumbs = (isset($param['thumbs']))? $param['thumbs']:"";
        $_file_data = array();   // store file info once uploaded
        $fileData = []; // Return File Data

        foreach ($_FILES[$fieldName]['name'] as $i => $value) {
            $fileName = $_FILES[$fieldName]['name'][$i];
            $fileSize = $_FILES[$fieldName]['size'][$i];
            $fileType = $_FILES[$fieldName]['type'][$i];

            $_FILES['file']['name'] = $_FILES[$fieldName]['name'][$i];
            $_FILES['file']['type'] = $_FILES[$fieldName]['type'][$i];
            $_FILES['file']['tmp_name'] = $_FILES[$fieldName]['tmp_name'][$i];
            $_FILES['file']['error'] = $_FILES[$fieldName]['error'][$i];
            $_FILES['file']['size'] = $_FILES[$fieldName]['size'][$i];

            $config['upload_path']    = $this->__upload_path;
            $config['allowed_types']  = $this->__allowed_types;
            $config['encrypt_name']   = $this->__encrypt_name;
            $config['overwrite']      = $this->__overwrite;
            $config['remove_spaces']  = $this->__remove_spaces;
            $config['max_size']       = $this->__max_size;
            $config['max_filename']   = $this->__max_filename;
            $config['max_width']      = $this->__max_width;
            $config['min_width']      = $this->__min_width;
            $config['max_height']     = $this->__max_height;
            $config['min_height']     = $this->__min_height;

            $this->CI->load->library('upload', $config);
            $this->CI->upload->initialize($config);
            if ($this->CI->upload->do_upload('file')) {
                
                $_file_data[$i] = $this->CI->upload->data();

                if(isset($_thumbs) AND is_array($_thumbs) AND !empty($_thumbs))
                {
                    $orig_raw_name  = $_file_data[$i]['raw_name']; // original upload raw_name
                    $orig_file_ext  = $_file_data[$i]['file_ext']; // original upload file extension

                    // Image Resize
                    foreach ($_thumbs as $thumbs_key => $thumbs_value) 
                    {
                        if(isset($thumbs_value['path']) AND !empty($thumbs_value['path']) AND is_string($thumbs_value['path']))
                            $this->__thumbs_path = $thumbs_value['path'];

                        // Resize Image Name
                        $new_image = $this->__thumbs_path.$orig_raw_name.'_'.$thumbs_key.$orig_file_ext;

                        $config = array(
                            'source_image'   => $_file_data[$i]['full_path'],
                            'new_image'      => $new_image,
                            'maintain_ratio' => true,
                            'width'          => $thumbs_value['w'],
                            'height'         => $thumbs_value['h']
                        );

                        // load library
                        $this->CI->load->library('image_lib');
                        $this->CI->image_lib->initialize($config);
                        if (!$this->CI->image_lib->resize()) {
                            return $this->CI->image_lib->display_errors();
                        }
                        $this->CI->image_lib->clear();
                    }
                }

                $fileData[] = [
                    'original_name' => $_file_data[$i]['file_name'],
                    'raw_name' => $_file_data[$i]['raw_name'],
                    'file_ext' => $_file_data[$i]['file_ext'],
                ];
            }
        }

        return [
            'status' => TRUE,
            'data' => $fileData
        ];
    }

    /**
     * Show Image
     * ------------------
     * Return image full path, but image not exist return default path 
     * @param: image path
     * @param: image url
     * @param: image type
     * 
     * @return: url
     */
    public function show_image($image_path, $image_url, $image_type = null)
    {
        $_image_name = "";
        $_image_ext = "";
        $_image_type = "";

        if (empty($image_url))
            return base_url($this->__temp_image);

        $_file_data = pathinfo($image_url);

        if (is_array($_file_data)) {
            $_image_name = $_file_data['filename'];
            $_image_ext = $_file_data['extension'];
        }

        if (!empty($image_type) AND is_string($image_type))
            $_image_type = '_'.$image_type;

        
        $full_img_path = $image_path.$_image_name.$_image_type.'.'.$_image_ext;

        $file_headers = get_headers($full_img_path);
       
        // return image full path if image exists
        if (preg_match("|200|", $file_headers[0])) {
            return $full_img_path;
        } else {
            return base_url($this->__temp_image);
        }
    }

    /**
     * Show Image Path
     */
    public function show_image_path($folderPath, $fileName, $imageThumbName = null)
    {
        $_imageName = "";
        $_imageExt = "";
        $_imageThumb = "";

        $_fileData = pathinfo($fileName);

        if (is_array($_fileData)) {
            $_imageName = $_fileData['filename'];
            $_imageExt = $_fileData['extension'];
        }

        if (!empty($imageThumbName) AND is_string($imageThumbName))
            $_imageThumb = '_'.$imageThumbName;

        return $folderPath.$_imageName.$_imageThumb.'.'.$_imageExt;
    }

    /**
     * Check File Validation before upload file
     * -----------------------------------------------
     * @param {String} fileName
     * @return {Array}
     */
    private function _check_validation($fieldName) {
        $returnData = [];
        $returnData['total_errors'] = 0;
        if (!empty($_FILES[$fieldName]) && !empty($_FILES[$fieldName]['name'])) {
            foreach ($_FILES[$fieldName]['name'] as $i => $value) {
                $fileName = $_FILES[$fieldName]['name'][$i];
                $fileSize = $_FILES[$fieldName]['size'][$i];
                $fileType = $_FILES[$fieldName]['type'][$i];

                if (!empty($fileName) && !empty($fileSize)) {

                    $_FILES['file']['name'] = $_FILES[$fieldName]['name'][$i];
                    $_FILES['file']['type'] = $_FILES[$fieldName]['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES[$fieldName]['tmp_name'][$i];
                    $_FILES['file']['error'] = $_FILES[$fieldName]['error'][$i];
                    $_FILES['file']['size'] = $_FILES[$fieldName]['size'][$i];

                    $config['upload_path']    = $this->__upload_path;
                    $config['allowed_types']  = $this->__allowed_types;
                    $config['encrypt_name']   = $this->__encrypt_name;
                    $config['overwrite']      = $this->__overwrite;
                    $config['remove_spaces']  = $this->__remove_spaces;
                    $config['max_size']       = $this->__max_size;
                    $config['max_filename']   = $this->__max_filename;
                    $config['max_width']      = $this->__max_width;
                    $config['min_width']      = $this->__min_width;
                    $config['max_height']     = $this->__max_height;
                    $config['min_height']     = $this->__min_height;

                    $this->CI->load->library('upload', $config);
                    $this->CI->upload->initialize($config);
                    if (!$this->CI->upload->do_upload('file')) {
                        
                        $returnData['errors'][] = [
                            'file' => $fileName,
                            'size' => $fileSize,
                            'type' => $fileType,
                            'error' => $this->CI->upload->display_errors('', '')
                        ];
                        ++$returnData['total_errors'];

                    } else {

                        $_file_data = $this->CI->upload->data();
                        @unlink($_file_data['full_path']);
                    }
                } else {

                    $returnData['errors'][] = [
                        'file' => $fileName,
                        'size' => $fileSize,
                        'type' => $fileType,
                        'error' => "The file you are attempting to upload is smaller than the permitted size."
                    ];
                    ++$returnData['total_errors'];
                }
            }
        } else {
            $returnData['errors'][] = [
                'error' => "Invalid File Name"
            ];
            ++$returnData['total_errors'];
        }
        return $returnData;
    }
}
