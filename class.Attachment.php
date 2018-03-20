<?php
/*
 File: Attachment.php

 This file contains the Attachment Class

 About:
 Author: Raul Perusquia <rulovico@gmail.com>

 Copyright (c) 2009, Inikoo

 Version 2.0
*/
include_once 'class.DB_Table.php';


class Attachment extends DB_Table {
    var $locations = false;
    var $compress = true;

    function Attachment($arg1 = false, $arg2 = false, $arg3 = false, $_db = false) {

        if (!$_db) {
            global $db;
            $this->db = $db;
        } else {
            $this->db = $_db;
        }


        $this->table_name    = 'Attachment';
        $this->ignore_fields = array('Attachment Key');

        if (preg_match('/^(new|create)$/i', $arg1) and is_array($arg2)) {
            $this->create($arg2);

            return;
        }

        if (preg_match('/find/i', $arg1)) {
            $this->find($arg2, $arg3);

            return;
        }
        if (is_numeric($arg1)) {
            $this->get_data('id', $arg1);

            return;
        }
        $this->get_data($arg1, $arg2);
    }

    function create($data, $options = '') {

        $this->data = $this->base_data();
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->data)) {
                $this->data[$key] = _trim($value);
            }
        }


        $filename = $data['file'];





        $this->data['Attachment Data'] = addslashes(
            fread(fopen($filename, "r"), filesize($filename))
        );


        $keys   = '(';
        $values = 'values(';
        foreach ($this->data as $key => $value) {

            $keys .= "`$key`,";

            if ($key == 'Attachment Data') {
                $values .= "'".$value."',";
            } else {
                $values .= prepare_mysql($value).",";
            }


        }

        $keys   = preg_replace('/,$/', ')', $keys);
        $values = preg_replace('/,$/', ')', $values);


        $sql = sprintf(
            "INSERT INTO `Attachment Dimension` %s %s", $keys, $values
        );


        if ($this->db->exec($sql)) {
            $this->id  = $this->db->lastInsertId();
            $this->new = true;
            $this->get_data('id', $this->id, true);

            $this->update_type();
            $this->create_thumbnail();
        } else {



            $error = $this->db->errorInfo();
            if (preg_match('/max_allowed_packet/i', $error[2])) {
                $this->msg
                    = "Got a packet bigger than 'max_allowed_packet' bytes ";
            } else {
                $this->msg = 'Unknown error';

            }
            $this->error = true;
        }

    }

    function get_data($key, $tag, $with_data = false) {

        if ($key == 'id') {
            $sql = sprintf(
                "SELECT * FROM `Attachment Dimension` WHERE `Attachment Key`=%d", $tag
            );

        } elseif ($key == 'bridge_key') {
            $sql = sprintf(
                "SELECT * FROM `Attachment Bridge` B LEFT JOIN  `Attachment Dimension` A ON (A.`Attachment Key`= B.`Attachment Key`) WHERE `Attachment Bridge Key`=%d", $tag
            );
        } else {
            return;
        }

        if ($this->data = $this->db->query($sql)->fetch()) {
            if (!$with_data) {
                unset($this->data['Attachment Data']);
            }
            $this->id = $this->data['Attachment Key'];
        }


    }


    function update_type() {
        $type = 'Other';
        if (preg_match('/^image/', $this->data['Attachment MIME Type'])) {
            $type = 'Image';
        } elseif (preg_match('/excel/', $this->data['Attachment MIME Type'])) {
            $type = 'Spreadsheet';
        } elseif (preg_match('/msword/', $this->data['Attachment MIME Type'])) {
            $type = 'Word';
        } elseif (preg_match('/pdf/', $this->data['Attachment MIME Type'])) {
            $type = 'PDF';
        } elseif (preg_match(
            '/(zip|rar)/', $this->data['Attachment MIME Type']
        )) {
            $type = 'Compressed';
        } elseif (preg_match('/(text)/', $this->data['Attachment MIME Type'])) {
            $type = 'Text';
        }

        $sql = sprintf(
            "UPDATE `Attachment Dimension` SET `Attachment Type`=%s WHERE `Attachment Key`=%d", prepare_mysql($type), $this->id
        );
        $this->db->exec($sql);
        $this->data['Attachment Type'] = $type;

    }

    function create_thumbnail() {
        include_once 'class.Image.php';
        if (preg_match('/application\/pdf/', $this->data['Attachment MIME Type'])) {
            $tmp_file = 'server_files/tmp/attch'.date('U').$this->data['Attachment File Checksum'];


            $tmp_file_name = $tmp_file.'.pdf';
            file_put_contents($tmp_file_name, $this->data['Attachment Data']);

            $im = new imagick($tmp_file_name.'[0]');


        } elseif (preg_match('/image\/(png|jpg|gif|jpeg)/', $this->data['Attachment MIME Type'])) {

            $tmp_file      = 'server_files/tmp/attch'.date('U').$this->data['Attachment File Checksum'];
            $tmp_file_name = $tmp_file;
            file_put_contents($tmp_file_name, $this->data['Attachment Data']);
            $im = new imagick($tmp_file_name);


        } else {
            return;
        }


        $im->setImageFormat('jpg');
        $im->thumbnailImage(500, 0);
        $im->writeImage($tmp_file.'.jpg');


        $image_data = array(
            'Image Width'         => 0,
            'Image Height'        => 0,
            'Image File Size'     => 0,
            'Image File Checksum' => '',
            'Image Filename'      => 'attachment_thumbnail',
            'Image File Format'   => '',
            'Image Data'          => '',
            'upload_data'         => array('tmp_name' => $tmp_file.'.jpg'),
            'editor'              => $this->editor
        );


        $image = new Image('find', $image_data, 'create');

        if (!$image->error) {


            $sql = sprintf(
                "DELETE FROM `Image Bridge` WHERE `Subject Type`=%s AND `Subject Key`=%d", prepare_mysql('Attachment Thumbnail'), $this->id

            );
           $this->db->exec($sql);
            $sql = sprintf(
                "INSERT INTO `Image Bridge` (`Subject Type`,`Subject Key`,`Image Key`,`Is Principal`,`Caption`) VALUES (%s,%d,%d,'Yes','')", prepare_mysql('Attachment Thumbnail'), $this->id,
                $image->id
            );
            $this->db->exec($sql);

            $sql = sprintf(
                "UPDATE `Attachment Dimension` SET `Attachment Thumbnail Image Key`=%d WHERE `Attachment Key`=%d", $image->id, $this->id
            );
            $this->db->exec($sql);
            $this->data['Attachment Thumbnail Image Key'] = $image->id;
        } else {


        }

        unlink($tmp_file_name);
        unlink($tmp_file.'.jpg');


    }

    function find($raw_data, $options) {

        if (isset($raw_data['editor'])) {
            foreach ($raw_data['editor'] as $key => $value) {

                if (array_key_exists($key, $this->editor)) {
                    $this->editor[$key] = $value;
                }

            }
        }


        $this->found = false;
        $create      = '';

        if (preg_match('/create/i', $options)) {
            $create = 'create';
        }



        if (isset($raw_data['file']) and $raw_data['file'] != '') {
            $file     = $raw_data['file'];
            $checksum = md5_file($file);


            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $file);
            finfo_close($finfo);
            if ($mime == 'unknown' and (isset($raw_data['Attachment MIME Type']) and $raw_data['Attachment MIME Type'] != '')) {
                $mime = "unknown (".$raw_data['Attachment MIME Type'].")";
            }
            $filesize  = filesize($file);
            $extension = $this->find_extension($file);

            $raw_data['Attachment MIME Type']     = $mime;
            $raw_data['Attachment File Checksum'] = $checksum;
            $raw_data['Attachment File Size']     = $filesize;


        }


        $data = $this->base_data();
        foreach ($raw_data as $key => $val) {
            $_key        = $key;
            $data[$_key] = $val;
        }

       // print_r($raw_data);
       // print_r($data);

        $sql = sprintf(
            "SELECT `Attachment Key` FROM `Attachment Dimension` WHERE `Attachment File Checksum`=%s", prepare_mysql($data['Attachment File Checksum'])
        );


        if ($result=$this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $this->found     = true;
                $this->found_key = $row['Attachment Key'];
        	}
        }else {
        	print_r($error_info=$this->db->errorInfo());
        	print "$sql\n";
        	exit;
        }


        //what to do if found
        if ($this->found) {
            $this->get_data('id', $this->found_key);
            $this->found = true;

            return;
        }


        if ($create) {

            $this->create($data, $options);

        }


    }

    function find_extension($filename) {
        $filename = strtolower($filename);
        $exts     = preg_split("/\.[a-z]$/i", $filename);
        $n        = count($exts) - 1;
        $exts     = $exts[$n];

        return $exts;
    }

    function get_abstract($original_name = '', $caption = '', $reference = false) {

        if (!$reference) {
            $reference_type = 'id';
            $reference_key  = $this->id;
        } else {
            $reference_type = 'bid';
            $reference_key  = $reference;
        }

        $mime = $this->mime_type_icon($this->data['Attachment MIME Type']);

        return sprintf(
            '%s <a href="file.php?%s=%d">%s</a> (%s) %s', $mime, $reference_type, $reference_key, $original_name

            , file_size($this->data['Attachment File Size']), $caption
        );
    }

    function mime_type_icon($mime_type) {
        if (preg_match('/^image/', $mime_type)) {
            return '<img src="art/icons/page_white_picture.png" alt="'.$mime_type.'" title="'.$mime_type.'" />';
        } elseif (preg_match('/excel/', $mime_type)) {
            return '<img src="art/icons/page_white_excel.png" alt="'.$mime_type.'" title="'.$mime_type.'"/>';
        } elseif (preg_match('/msword/', $mime_type)) {
            return '<img src="art/icons/page_white_word.png" alt="'.$mime_type.'" title="'.$mime_type.'"/>';
        } elseif (preg_match('/pdf/', $mime_type)) {
            return '<img src="art/icons/page_white_acrobat.png" alt="'.$mime_type.'" title="'.$mime_type.'"/>';
        } elseif (preg_match('/(zip|rar)/', $mime_type)) {
            return '<img src="art/icons/page_white_compressed.png" alt="'.$mime_type.'" title="'.$mime_type.'"/>';
        } elseif (preg_match('/(text)/', $mime_type)) {
            return '<img src="art/icons/page_white_text.png" alt="'.$mime_type.'" title="'.$mime_type.'"/>';
        } else {
            return $mime_type;
        }
    }

    function get_details() {
        return '';
    }

    function uncompress($srcName, $dstName) {
        $string = implode("", gzfile($srcName));
        $fp     = fopen($dstName, "w");
        fwrite($fp, $string, strlen($string));
        fclose($fp);
    }

    function compress($srcName, $dstName) {
        $fp   = fopen($srcName, "r");
        $data = fread($fp, filesize($srcName));
        fclose($fp);

        $zp = gzopen($dstName, "w9");
        gzwrite($zp, $data);
        gzclose($zp);
    }

    function delete($force = false) {
        $subjects     = $this->get_subjects();
        $num_subjects = count($subjects);
        if ($num_subjects == 0 or $force) {
            $sql = sprintf(
                "DELETE FROM `Attachment Dimension` WHERE `Attachment Key`=%d", $this->id
            );

            $this->db->exec($sql);
            $sql = sprintf(
                "DELETE FROM `Attachment Bridge` WHERE `Attachment Key`=%d", $this->id
            );

            $this->db->exec($sql);
        }
    }

    function get_subjects() {
        $subjects = array();
        $sql      = sprintf(
            'SELECT * FROM `Attachment Bridge` WHERE `Attachment Key`=%d', $this->id
        );


        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                $subjects[] = $row;
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        return $subjects;
    }

    function set_subject($subject) {
        $this->data['Subject'] = $subject;
    }

    function get_field_label($field) {

        switch ($field) {
            case 'Attachment Subject Type':
                $label = _('Content type');
                break;
            case 'Attachment Caption':
                $label = _('Short description');
                break;

            case 'Attachment Public':
                if ($this->get('Subject') == 'Staff') {
                    $label = _('Employee can see file');
                } else {
                    $label = _('Public');
                }
                break;
            case 'Attachment File':
                $label = _('File');
                break;
            case 'Attachment File Original Name':
                $label = _('File name');
                break;
            case 'Attachment File Size':
                $label = _('File size');
                break;
            case 'Attachment Preview':
                $label = _('Preview');
                break;
            default:
                $label = $field;
                break;
        }

        return $label;
    }

    function get($key, $data = false) {

        if (!$this->id) {
            return;
        }

        switch ($key) {


            case 'Preview':

                return sprintf('/attachment_preview.php?id=%d', $this->get('Attachment Bridge Key'));

            case 'Public':
                if ($this->data['Attachment Public'] == 'Yes') {
                    return _('Yes');
                } else {
                    return _('No');
                }

                break;
            case 'Public Info':

                if ($this->get('Subject') == 'Staff') {
                    if ($this->data['Attachment Public'] == 'Yes') {
                        $visibility = sprintf(
                            '<i title="%s" class="fa fa-eye"></i> %s', _('Public'), _('Employee can see file')
                        );
                    } else {
                        $visibility = sprintf(
                            '<span class="error" > <i title="%s" class="fa fa-eye-slash"></i> %s</span>', _('Private'), _('Top secret file')
                        );
                    }
                } else {
                    if ($this->data['Attachment Public'] == 'Yes') {
                        $visibility = sprintf('<i title="%s" class="fa fa-eye"></i> %s', _('Public'), _('Public'));
                    } else {
                        $visibility = sprintf('<i title="%s" class="fa fa-eye-slash"></i> %s', _('Private'), _('Private'));
                    }

                }

                return $visibility;

                break;


            case 'Subject Type':

                if(array_key_exists('Attachment Subject Type',$this->data)) {


                    switch ($this->data['Attachment Subject Type']) {
                        case 'Contract':
                            $type = _('Employment contract');
                            break;
                        case 'CV':
                            $type = _('Curriculum vitae');
                            break;
                        case 'Other':
                            $type = _('Other');
                            break;
                        case 'Invoice':
                            $type = _('Invoice');
                            break;
                        case 'PurchaseOrder':
                            $type = _('Purchase order');
                            break;
                        case 'Contact Card':
                            $type = _('Contact card');
                            break;
                        case 'Catalogue':
                            $type = _('Catalogue');
                            break;
                        case 'Image':
                            $type = _('Image');
                            break;
                        case 'MSDS':
                            $type = _('Material Safety Data Sheet (MSDS)');
                            break;
                        default:
                            $type = $this->data['Attachment Subject Type'].'*';
                            break;
                    }

                    return $type;
                }else{
                    return '';
                }
                break;
            case 'File Size':
                include_once 'utils/natural_language.php';

                return file_size($this->data['Attachment File Size']);
                break;
            case 'Type':
                switch ($this->data['Attachment Type']) {
                    case 'PDF':
                        $file_type = sprintf(
                            '<i title="%s" class="fa fa-fw fa-file-pdf"></i> %s', $this->data['Attachment MIME Type'], 'PDF'
                        );

                        break;
                    case 'Image':
                        $file_type = sprintf(
                            '<i title="%s" class="fa fa-fw fa-image"></i> %s', $this->data['Attachment MIME Type'], _('Image')
                        );
                        break;
                    case 'Compressed':
                        $file_type = sprintf(
                            '<i title="%s" class="fa fa-fw fa-file-archive"></i> %s', $this->data['Attachment MIME Type'], _('Compressed')
                        );
                        break;
                    case 'Spreadsheet':
                        $file_type = sprintf(
                            '<i title="%s" class="fa fa-fw fa-table"></i> %s', $this->data['Attachment MIME Type'], _('Spreadsheet')
                        );
                        break;
                    case 'Text':
                        $file_type = sprintf(
                            '<i title="%s" class="fal fa-file-alt fa-fw"></i> %s', $this->data['Attachment MIME Type'], _('Text')
                        );
                        break;
                    case 'Word':
                        $file_type = sprintf(
                            '<i title="%s" class="fa fa-fw fa-file-word"></i> %s', $this->data['Attachment MIME Type'], 'Word'
                        );
                        break;
                    default:
                        $file_type = sprintf(
                            '<i title="%s" class="fa fa-fw fa-file"></i> %s', $this->data['Attachment MIME Type'], _('Other')
                        );
                        break;
                }

                return $file_type;
                break;
            default:
                if (array_key_exists($key, $this->data)) {
                    return $this->data[$key];
                }

                if (array_key_exists('Attachment '.$key, $this->data)) {
                    return $this->data['Attachment '.$key];
                }
        }


        return '';
    }

    function update_field_switcher($field, $value, $options = '', $metadata = '') {
        if (is_string($value)) {
            $value = _trim($value);
        }


        switch ($field) {
            case 'Attachment Caption':
            case 'Attachment Subject Type':
            case 'Attachment Public':
                $this->update_table_field(
                    $field, $value, $options, 'Attachment Bridge', 'Attachment Bridge', $this->get('Attachment Bridge Key')
                );

                if ($field == 'Attachment Public') {
                    $this->other_fields_updated = array(
                        'Public_Info' => array(
                            'field'           => 'Public_Info',
                            'render'          => true,
                            'value'           => $this->get('Public_Info'),
                            'formatted_value' => $this->get('Public Info'),


                        )
                    );

                }

                break;
            default:
                $base_data = $this->base_data();
                if (array_key_exists($field, $base_data)) {
                    $this->update_field($field, $value, $options);
                }
        }
        $bridge_key = $this->get('Attachment Bridge Key');
        $this->reread();

        $this->get_subject_data($bridge_key);

    }

    function get_subject_data($bridge_key) {

        $sql = sprintf(
            "SELECT * FROM `Attachment Bridge` WHERE `Attachment Bridge Key`=%d AND `Attachment Key`=%d", $bridge_key, $this->id
        );


        if ($row = $this->db->query($sql)->fetch()) {

            foreach ($row as $key => $value) {
                $this->data[$key] = $value;
            }
        }
    }

    function save_to_file($file_path){

        $this->get_data('id', $this->id, $with_data = true);

        file_put_contents($file_path, $this->data['Attachment Data']);

    }

}


?>
