<?php

/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>

 Copyright (c) 2014, Inikoo
 Created: 9 January 2016 at 12:46:23 GMT+8, Kuala Lumpur, Malaysia

 Version 2.0
*/
use Elasticsearch\ClientBuilder;


class Data_Sets extends DB_Table {


    function __construct($arg1 = false, $arg2 = false, $arg3 = false) {
        global $db;

        $this->db            = $db;
        $this->table_name    = 'Data Sets';
        $this->ignore_fields = array('Data Sets Key');

        if (is_numeric($arg1)) {
            $this->get_data('id', $arg1);

            return;
        }
        if (preg_match('/^find/i', $arg1)) {

            $this->find($arg2, $arg3);

            return;
        }

        if (preg_match('/^(create|new)/i', $arg1)) {
            $this->create($arg2);

            return;
        }

        $this->get_data($arg1, $arg2);

        return;

    }


    function get_data($tipo, $tag) {


        if ($tipo == 'id') {
            $sql = sprintf(
                "SELECT * FROM `Data Sets Dimension` WHERE `Data Sets Key`=%d", $tag
            );
        } elseif ($tipo == 'code') {
            $sql = sprintf(
                "SELECT * FROM `Data Sets Dimension` WHERE `Data Sets Code`=%s", prepare_mysql($tag)
            );
        } else {
            return;
        }

        if ($this->data = $this->db->query($sql)->fetch()) {

            $this->id = $this->data['Data Sets Key'];

        }

    }

    function find($raw_data, $options) {


        if (isset($raw_data['editor'])) {
            foreach ($raw_data['editor'] as $key => $value) {

                if (array_key_exists($key, $this->editor)) {
                    $this->editor[$key] = $value;
                }

            }
        }


        $create = '';
        $update = '';
        if (preg_match('/create/i', $options)) {
            $create = 'create';
        }
        if (preg_match('/update/i', $options)) {
            $update = 'update';
        }


        $data = $this->base_data();

        foreach ($raw_data as $key => $value) {
            if (array_key_exists($key, $data)) {
                $data[$key] = _trim($value);
            }
        }


        $sql = sprintf(
            "SELECT `Data Sets Key` FROM `Data Sets Dimension` WHERE `Data Sets Code`=%s ", prepare_mysql($data['Data Sets Code'])


        );

        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $this->found     = true;
                $this->found_key = $row['Data Sets Key'];
                $this->get_data('id', $this->found_key);
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }

        if ($create and !$this->found) {
            $this->create($raw_data);
        }

    }

    function create($data) {


        $this->duplicated = false;
        $this->new        = false;

        $this->editor = $data['editor'];
        unset($data['editor']);
        $this->data = $data;

        $keys   = '';
        $values = '';


        foreach ($this->data as $key => $value) {
            $keys .= ",`".$key."`";

            $values .= ','.prepare_mysql($value, false);
        }
        $values = preg_replace('/^,/', '', $values);
        $keys   = preg_replace('/^,/', '', $keys);

        $sql = "insert into `Data Sets Dimension` ($keys) values ($values)";

        if ($this->db->exec($sql)) {

            $this->id  = $this->db->lastInsertId();
            $this->new = true;
            $this->get_data('id', $this->id);
            $this->update_stats();

        } else {
            $this->error = true;
            $error_info  = $this->db->errorInfo();
            if ($error_info[0] == 23000) {
                $this->duplicated = true;
                $this->msg        = _('Data Sets already exists');
            } else {
                $this->msg = 'Can not create timeseries. '.$error_info[2];
            }
        }
    }

    function update_stats() {


        global $dns_db;


        $tables = '';

        if ($this->data['Data Sets Code'] == 'Images') {
            $tables = '"Image Dimension"';
            $sql    = sprintf(
                'SELECT count(*) AS num  FROM `Image Dimension`', $this->id
            );
            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {
                    $num = $row['num'];

                } else {
                    $num = 0;

                }

                $this->update(
                    array(
                        'Data Sets Number Sets'  => 1,
                        'Data Sets Number Items' => $num
                    ), 'no_history'
                );


            } else {
                print_r($error_info = $this->db->errorInfo());
                exit;
            }


        }
        if ($this->data['Data Sets Code'] == 'Materials') {
            $tables = '"Material Dimension"';
            $sql    = sprintf(
                'SELECT count(*) AS num  FROM `Material Dimension`', $this->id
            );
            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {
                    $num = $row['num'];

                } else {
                    $num = 0;

                }

                $this->update(
                    array(
                        'Data Sets Number Sets'  => 1,
                        'Data Sets Number Items' => $num
                    ), 'no_history'
                );


            } else {
                print_r($error_info = $this->db->errorInfo());
                exit;
            }


        } elseif ($this->data['Data Sets Code'] == 'Attachments') {
            $tables = '"Attachment Bridge","Attachment Bridge History Bridge","Attachment Dimension"';
            $sql    = sprintf(
                'SELECT count(*) AS num  FROM `Attachment Dimension`', $this->id
            );
            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {
                    $num = $row['num'];

                } else {
                    $num = 0;

                }
                $this->update(
                    array(
                        'Data Sets Number Sets'  => 1,
                        'Data Sets Number Items' => $num
                    ), 'no_history'
                );

            } else {
                print_r($error_info = $this->db->errorInfo());
                exit;
            }
        } elseif ($this->data['Data Sets Code'] == 'Timeseries') {
            $tables = '"Timeseries Dimension","Timeseries Record Dimension"';

            $sql = sprintf(
                'SELECT count(*) AS num  FROM `Timeseries Record Dimension`', $this->id
            );
            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {
                    $num = $row['num'];

                } else {
                    $num = 0;

                }
                $this->update(
                    array('Data Sets Number Items' => $num), 'no_history'
                );

            } else {
                print_r($error_info = $this->db->errorInfo());
                exit;
            }

            $sql = sprintf(
                'SELECT count(*) AS num  FROM `Timeseries Dimension`', $this->id
            );
            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {
                    $num = $row['num'];

                } else {
                    $num = 0;

                }
                $this->update(
                    array('Data Sets Number Sets' => $num), 'no_history'
                );

            } else {
                print_r($error_info = $this->db->errorInfo());
                exit;
            }


        } elseif ($this->data['Data Sets Code'] == 'Uploads') {
            $tables = '"Upload Dimension","Upload File Dimension","Upload Record Dimension"';
            $sql    = sprintf(
                'SELECT count(*) AS num  FROM `Upload Dimension`', $this->id
            );
            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {
                    $num = $row['num'];

                } else {
                    $num = 0;

                }
                $this->update(
                    array(
                        'Data Sets Number Sets'  => 1,
                        'Data Sets Number Items' => $num
                    ), 'no_history'
                );

            } else {
                print_r($error_info = $this->db->errorInfo());
                exit;
            }
        }


        if ($tables != '') {
            $sql = sprintf(
                'SELECT sum(((data_length + index_length))) data_size FROM information_schema.TABLES WHERE table_schema = %s AND table_name IN (%s)', prepare_mysql($dns_db), $tables


            );

            //print "$sql\n";

            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {
                    $size = $row['data_size'];

                } else {
                    $size = 0;

                }

                $this->update(array('Data Sets Size' => $size), 'no_history');


            } else {
                print_r($error_info = $this->db->errorInfo());
                print "$sql\n";
                exit;
            }


        }
    }

    function update_field_switcher($field, $value, $options = '', $metadata = '') {
        if (is_string($value)) {
            $value = _trim($value);
        }


        switch ($field) {


            default:
                $base_data = $this->base_data();
                if (array_key_exists($field, $base_data)) {
                    $this->update_field($field, $value, $options);
                }
        }
        $this->reread();

    }

    function get($key = '') {

        global $account;


        switch ($key) {

            default:
                if (isset($this->data[$key])) {
                    return $this->data[$key];
                }

                if (array_key_exists('Data Sets '.$key, $this->data)) {
                    return $this->data['Data Sets '.$key];
                }

                return false;

        }


    }

    function get_field_label($field) {

        switch ($field) {
            case 'Data Sets Type':
                $label = _('Type');
                break;
            default:
                $label = $field;
        }

        return $label;

    }


}
