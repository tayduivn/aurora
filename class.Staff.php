<?php
/*
 File: Staff.php

 This file contains the Staff Class

 About:
 Author: Raul Perusquia <rulovico@gmail.com>

 Copyright (c) 2009, Inikoo

 Version 2.0
*/


require_once 'class.User.php';
require_once 'trait.AttachmentSubject.php';
require_once 'trait.ImageSubject.php';

class Staff extends DB_Table {
    use AttachmentSubject;
    use ImageSubject;

    function __construct($arg1 = false, $arg2 = false, $arg3 = false) {
        global $db;
        $this->db = $db;


        $this->table_name    = 'Staff';
        $this->ignore_fields = array('Staff Key');
        $this->system_user   = false;

        if (is_numeric($arg1)) {
            $this->get_data('id', $arg1);

            return;
        }
        if (preg_match('/^find/i', $arg1)) {

            $this->find($arg2, $arg3);

            return;
        }

        if (preg_match('/create|new/i', $arg1) and is_array($arg2)) {

            $this->find($arg2, 'create');

            return;
        }
        $this->get_data($arg1, $arg2);


    }


    function get_data($key, $id) {
        if ($key == 'alias') {
            $sql = sprintf(
                "SELECT * FROM `Staff Dimension` WHERE `Staff Alias`=%s", prepare_mysql($id)
            );
        } elseif ($key == 'staff_id') {
            $sql = sprintf(
                "SELECT * FROM  `Staff Dimension`  WHERE `Staff ID`=%s", prepare_mysql($id)
            );
        } elseif ($key == 'id') {
            $sql = sprintf(
                "SELECT * FROM `Staff Dimension` WHERE `Staff Key`=%d", $id
            );
        } elseif ($key == 'deleted') {
            $this->get_deleted_data($id);

            return;
        } else {
            return;
        }


        if ($this->data = $this->db->query($sql)->fetch()) {
            $this->id = $this->data['Staff Key'];

        }

    }


    function get_deleted_data($tag) {

        $this->deleted = true;
        $sql           = sprintf(
            "SELECT * FROM `Staff Deleted Dimension` WHERE `Staff Deleted Key`=%d", $tag
        );

        if ($this->data = $this->db->query($sql)->fetch()) {
            $this->id     = $this->data['Staff Deleted Key'];
            $deleted_data = json_decode(
                gzuncompress($this->data['Staff Deleted Metadata']), true
            );
            foreach ($deleted_data['data'] as $key => $value) {
                $this->data[$key] = $value;
            }
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
            "SELECT `Staff Key` FROM `Staff Dimension` WHERE `Staff Alias`=%s", prepare_mysql($data['Staff Alias'])
        );


        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $this->found     = true;
                $this->found_key = $row['Staff Key'];
                $this->get_data('id', $this->found_key);
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql";
            exit;
        }


        if ($create and !$this->found) {


            $this->create($raw_data);

        }


    }

    function create($data) {


        $account = new Account();

        include_once 'class.Timesheet.php';
        require_once 'utils/date_functions.php';


        $this->data = $this->base_data();
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->data)) {
                $this->data[$key] = _trim($value);
            }
        }
        $this->editor = $data['editor'];

        if ($this->data['Staff Type'] == '') {
            $this->data['Staff Type'] = 'Employee';
        }

        if ($this->data['Staff Currently Working'] == '') {
            $this->data['Staff Currently Working'] = 'Yes';
        }

        if ($this->data['Staff Valid From'] == '') {
            $this->data['Staff Valid From'] = gmdate('Y-m-d H:i:s');
        }

        if ($this->data['Staff PIN'] == '') {
            $this->data['Staff PIN'] = '1234';
        }


        $keys   = '';
        $values = '';
        foreach ($this->data as $key => $value) {
            $keys .= ",`".$key."`";
            if ($key == 'Staff Valid To' or $key == 'Staff Birthday' or $key == 'Staff Working Hours Per Week' or $key == 'Staff Warehouse Key') {
                $values .= ','.prepare_mysql($value, true);

            } else {
                $values .= ','.prepare_mysql($value, false);
            }
        }
        $values = preg_replace('/^,/', '', $values);
        $keys   = preg_replace('/^,/', '', $keys);

        $sql = "insert into `Staff Dimension` ($keys) values ($values)";

        if ($this->db->exec($sql)) {
            $this->id = $this->db->lastInsertId();
            $this->get_data('id', $this->id);


            $from = date('Y-m-d');
            $to   = date(
                'Y-m-d', strtotime(
                           date('Y', strtotime('now + 1 year')).'-'.$account->get(
                               'Account HR Start Year'
                           )
                       )
            );

            $dates = date_range($from, $to);
            foreach ($dates as $date) {
                $timesheet = $this->create_timesheet(
                    strtotime($date.' 00:00:00'), ''
                );
            }


            if (!$this->data['Staff ID']) {
                $sql = sprintf(
                    "UPDATE `Staff Dimension` SET `Staff ID`=%d WHERE `Staff Key`=%d", $this->id, $this->id
                );
                $this->db->exec($sql);
            }


            if ($this->get('Staff Type') == 'Contractor') {
                $abstract = sprintf(
                    _('Contractor %s created'), sprintf(
                                                  '<span class="button" onClick="change_view(\'contractor/%d\')">%s</span>', $this->id, $this->get('Alias')
                                              )
                );
            } else {
                $abstract = sprintf(
                    _('Employee %s created'), sprintf(
                                                '<span class="button" onClick="change_view(\'employee/%d\')">%s</span>', $this->id, $this->get('Alias')
                                            )
                );

            }


            $history_data = array(
                'History Abstract' => $abstract,
                'History Details'  => '',
                'Action'           => 'created'
            );


            $history_key = $this->add_subject_history(
                $history_data, true, 'No', 'Changes', $this->get_object_name(), $this->id
            );

            $sql = sprintf(
                'INSERT INTO `HR History Bridge`  (`HR Key`,`History Key`,`Type`) VALUES (1,%d,"Changes")  ', $history_key
            );
            $this->db->exec($sql);

            $this->new = true;


            if (array_key_exists('Staff User Active', $data)) {
                $user_data = array();
                foreach ($data as $key => $value) {
                    if (preg_match('/^Staff User /', $key)) {
                        $key             = preg_replace('/^Staff /', '', $key);
                        $user_data[$key] = $value;

                    }
                }

                $user_data['editor'] = $this->editor;


                $staff_user = $this->create_user($user_data);
                //print_r($this->user);
                if ($this->create_user_error) {
                    $this->extra_msg = '<span class="warning"><i class="fa fa-exclamation-triangle"></i> '._("System user couldn't be created").' ('.$this->create_user_msg.')</span>';
                    return;
                }
                $staff_user->editor = $data['editor'];


            }

        } else {
            $this->error = true;
            $this->msg   = 'Error inserting staff record';

            print $sql;

        }


    }

    function create_timesheet($date = '', $options = '') {

        include_once 'class.Timesheet.php';
        include_once 'class.Timesheet_Record.php';
        if ($date == '') {
            $date = date();
        }

        //$start = microtime(true);
        //exit;
        $working_hours = json_decode($this->data['Staff Working Hours'], true);


        if (!$working_hours) {

            $timesheet_data = array(
                'Timesheet Date'      => date("Y-m-d", $date),
                'Timesheet Staff Key' => $this->id,
                'editor'              => $this->editor
            );
            $timesheet      = new Timesheet('find', $timesheet_data, 'create');
            $this->update(
                array('Timesheet Type' => 'NoFixedWorkingHours'), 'no_history'
            );


            return $timesheet;
        }
        $day_of_the_week = date('N', $date);

        if (isset($working_hours['data'][$day_of_the_week])) {

            $day_data       = $working_hours['data'][$day_of_the_week];
            $timesheet_data = array(
                'Timesheet Date'      => date("Y-m-d", $date),
                'Timesheet Staff Key' => $this->id,
                'editor'              => $this->editor
            );


            $timesheet = new Timesheet('find', $timesheet_data, 'create');


            if ($options == 'force') {

                $sql = sprintf(
                    "DELETE FROM `Timesheet Record Dimension`  WHERE `Timesheet Record Type` IN ('WorkingHoursMark','OvertimeMark','BreakMark') AND `Timesheet Record Timesheet Key`=%d  ", $timesheet->id
                );

                $this->db->exec($sql);
            }


            if ($timesheet->get('Timesheet Working Hours Records') >= 2 and $options == '') {
                $timesheet->update_number_records('WorkingHoursMark');
                $timesheet->update_type();


                return $timesheet;
            }

            $timesheet->remove_records('WorkingHoursMark');


            $record_data = array(
                'Timesheet Record Timesheet Key' => $timesheet->id,
                'Timesheet Record Type'          => 'WorkingHoursMark',
                'Timesheet Record Staff Key'     => $this->id,
                'Timesheet Record Date'          => date('Y-m-d', $date).' '.$day_data['s'].':00',
                'Timesheet Record Source'        => 'System',
                'editor'                         => $this->editor

            );

            $timesheet_record                     = new Timesheet_Record(
                'new', $record_data
            );
            $record_data['Timesheet Record Type'] = 'WorkingHoursMark';

            $record_data['Timesheet Record Date'] = date('Y-m-d', $date).' '.$day_data['e'].':00';
            $timesheet_record                     = new Timesheet_Record(
                'new', $record_data
            );

            foreach ($day_data['b'] as $break) {
                $record_data['Timesheet Record Type'] = 'BreakMark';
                $record_data['Timesheet Record Date'] = date('Y-m-d', $date).' '.$break['s'].':00';
                $timesheet_record                     = new Timesheet_Record(
                    'new', $record_data
                );
                $record_data['Timesheet Record Date'] = date('Y-m-d', $date).' '.$break['e'].':00';
                $timesheet_record                     = new Timesheet_Record(
                    'new', $record_data
                );
            }

        } else {
            $timesheet_data = array(
                'Timesheet Date'      => date("Y-m-d", $date),
                'Timesheet Staff Key' => $this->id,
                'editor'              => $this->editor
            );
            $timesheet      = new Timesheet('find', $timesheet_data, 'create');
        }

        $timesheet->update_number_records('BreakMark');

        $timesheet->update_number_records('WorkingHoursMark');
        $timesheet->update_type();
        $timesheet->process_mark_records_action_type();

        //$time_elapsed_secs = 1000*(microtime(true) - $start);
        //print "\n<br>$time_elapsed_secs\n";

        return $timesheet;
    }

    function get($key) {


        if (!$this->id) {
            if ($key == 'Staff Type') {
                if (isset($this->data[$key])) {
                    return $this->data[$key];
                } else {
                    return 'Employee';
                }

            } else {
                return;
            }
        }

        switch ($key) {
            case 'Salary':

                $salary = '';

                global $account;
                $salary_data = json_decode($this->data['Staff Salary'], true);
                if (!$salary_data) {
                    return '';
                }

                if (isset($salary_data['data']['amount'])) {
                    $salary_amount = money(
                        $salary_data['data']['amount'], $account->get('Account Currency')
                    );

                } else {
                    $salary_amount = '('.money(
                            $salary_data['data']['amount_weekdays'], $account->get('Account Currency')
                        ).' '._('Mon-Fri').', ';
                    $salary_amount .= money(
                            $salary_data['data']['amount_saturday'], $account->get('Account Currency')
                        ).' '._('Sat').', ';
                    $salary_amount .= money(
                            $salary_data['data']['amount_sunday'], $account->get('Account Currency')
                        ).' '._('Sun').') ';
                    $compress      = false;
                }
                if ($salary_data['data']['type'] == 'prorata_hour') {


                    if ($salary_data['data']['frequency'] == 'monthly') {

                        if (isset($salary_data['data']['amount'])) {
                            $average_year_amount = $salary_data['data']['amount'] * $this->data['Staff Working Hours Per Week'] * 52.1429;
                        } else {
                            $week_hours_day_breakdown = json_decode(
                                $this->data['Staff Working Hours Per Week Metadata'], true
                            );
                            $average_year_amount      = ($salary_data['data']['amount_weekdays'] * $week_hours_day_breakdown['Weekdays'] + $salary_data['data']['amount_saturday'] * $week_hours_day_breakdown['Saturday'] + $salary_data['data']['amount_sunday']
                                    * $week_hours_day_breakdown['Sunday']) * 52.1429;

                        }

                        $salary = sprintf(
                            _(
                                '%s/hour (pro rata) paid every %s day of the month (%s per year)'
                            ), $salary_amount, get_ordinal_suffix($salary_data['data']['payday']), money(
                                $average_year_amount, $account->get('Account Currency')
                            )
                        );

                    } elseif ($salary_data['data']['frequency'] == 'weekly') {
                        $day_names = array(
                            1 => _('Monday'),
                            2 => _('Tuesday'),
                            3 => _('Wednesday'),
                            4 => _('Thursday'),
                            5 => _('Friday'),
                            6 => _('Satday'),
                            7 => _('Sunday')
                        );


                        if (isset($salary_data['data']['amount'])) {
                            $average_year_amount = $salary_data['data']['amount'] * $this->data['Staff Working Hours Per Week'] * 52.1429;
                        } else {
                            $week_hours_day_breakdown = json_decode(
                                $this->data['Staff Working Hours Per Week Metadata'], true
                            );
                            $average_year_amount      = ($salary_data['data']['amount_weekdays'] * $week_hours_day_breakdown['Weekdays'] + $salary_data['data']['amount_saturday'] * $week_hours_day_breakdown['Saturday'] + $salary_data['data']['amount_sunday']
                                    * $week_hours_day_breakdown['Sunday']) * 52.1429;

                        }


                        $salary = sprintf(
                            _(
                                '%s/hour (pro rata) paid every %s (&#8776;%s per year)'
                            ), $salary_amount, $day_names[$salary_data['data']['payday']], money(
                                $average_year_amount, $account->get('Account Currency')
                            )
                        );

                    }
                } elseif ($salary_data['data']['type'] == 'fixed_month') {


                    if ($salary_data['data']['frequency'] == 'monthly') {

                        if ($this->data['Staff Working Hours Per Week'] == 0) {
                            return sprintf(
                                _('%s paid every %s day of the month'), $salary_amount, get_ordinal_suffix(
                                                                          $salary_data['data']['payday']
                                                                      )
                            );
                        }

                        $average_hour_amount = $salary_data['data']['amount'] / ($this->data['Staff Working Hours Per Week'] * 4.348125);

                        $salary = sprintf(
                            _(
                                '%s paid every %s day of the month (%s per year, ~%s per hour)'
                            ), $salary_amount, get_ordinal_suffix($salary_data['data']['payday']), money(
                                $salary_data['data']['amount'] * 12, $account->get('Account Currency')
                            ), money(
                                $average_hour_amount, $account->get('Account Currency')
                            )
                        );

                    } elseif ($salary_data['data']['frequency'] == 'weekly') {
                        $day_names = array(
                            1 => _('Monday'),
                            2 => _('Tuesday'),
                            3 => _('Wednesday'),
                            4 => _('Thursday'),
                            5 => _('Friday'),
                            6 => _('Satday'),
                            7 => _('Sunday')
                        );

                        $salary = sprintf(
                            _('%s paid every %s'), $salary_amount, $day_names[$salary_data['data']['payday']]
                        );

                    }
                } elseif ($salary_data['data']['type'] == 'fixed_week') {
                    $day_names = array(
                        1 => _('Monday'),
                        2 => _('Tuesday'),
                        3 => _('Wednesday'),
                        4 => _('Thursday'),
                        5 => _('Friday'),
                        6 => _('Satday'),
                        7 => _('Sunday')
                    );


                    $salary = sprintf(
                        _('%s paid every %s (&#8776;%s per year)'), $salary_amount, $day_names[$salary_data['data']['payday']], money(
                                                                      $salary_data['data']['amount'] * 52.1429, $account->get('Account Currency')
                                                                  )
                    );


                }


                //print $salary;exit;
                return $salary;

                //{"metadata":{},"data":{"frequency":"monthly","payday":"23","type":"prorata_hour","amount":"2323"}}
                break;
            case('Working Hours'):
                include_once 'utils/natural_language.php';


                $day_names = array(
                    1 => _('Mon'),
                    2 => _('Tue'),
                    3 => _('Wed'),
                    4 => _('Thu'),
                    5 => _('Fri'),
                    6 => _('Sat'),
                    7 => _('Sun')
                );

                $formatted_working_hours = '';


                $working_hours = json_decode(
                    $this->data['Staff Working Hours'], true
                );
                if (!$working_hours) {
                    return '';
                }

                //  print_r($working_hours);

                if ((isset($working_hours['data'][1]) and isset($working_hours['data'][2]) and isset($working_hours['data'][3]) and isset($working_hours['data'][4]) and isset($working_hours['data'][5])) and ($working_hours['data'][1] == $working_hours['data'][2]
                        and $working_hours['data'][1] == $working_hours['data'][3] and $working_hours['data'][1] == $working_hours['data'][4] and $working_hours['data'][1] == $working_hours['data'][5])

                ) {

                    $start = date(
                        'H:i', strtotime('2000-01-01 '.$working_hours['data'][1]['s'])
                    );
                    $end   = date(
                        'H:i', strtotime('2000-01-01 '.$working_hours['data'][1]['e'])
                    );

                    $breaks = $this->get_breaks($working_hours['data'][1]['b']);

                    $formatted_working_hours = _('Mon-Fri').' '.$start.'-'.$end.$breaks.', ';


                    if (isset($working_hours['data'][6]) and isset($working_hours['data'][7]) and $working_hours['data'][6] = $working_hours['data'][7]) {

                        $start = date(
                            'H:i', strtotime(
                                     '2000-01-01 '.$working_hours['data'][6]['s']
                                 )
                        );
                        $end   = date(
                            'H:i', strtotime(
                                     '2000-01-01 '.$working_hours['data'][6]['e']
                                 )
                        );

                        $breaks = $this->get_breaks(
                            $working_hours['data'][6]['b']
                        );

                        $formatted_working_hours .= _('Sat-Sun').' '.$start.'-'.$end.$breaks.', ';


                    } else {


                        foreach (
                            $working_hours['data'] as $day_key => $day_working_hours
                        ) {
                            if ($day_key >= 6) {
                                $start  = date(
                                    'H:i', strtotime(
                                             '2000-01-01 '.$day_working_hours['s']
                                         )
                                );
                                $end    = date(
                                    'H:i', strtotime(
                                             '2000-01-01 '.$day_working_hours['e']
                                         )
                                );
                                $breaks = $this->get_breaks(
                                    $day_working_hours['b']
                                );

                                $formatted_working_hours .= $day_names[$day_key].' '.$start.'-'.$end.$breaks.', ';
                            }
                        }
                    }

                } else {


                    foreach (
                        $working_hours['data'] as $day_key => $day_working_hours
                    ) {
                        $start  = date(
                            'H:i', strtotime('2000-01-01 '.$day_working_hours['s'])
                        );
                        $end    = date(
                            'H:i', strtotime('2000-01-01 '.$day_working_hours['e'])
                        );
                        $breaks = $this->get_breaks($day_working_hours['b']);

                        $formatted_working_hours .= $day_names[$day_key].' '.$start.'-'.$end.$breaks.', ';

                    }

                }


                $formatted_working_hours = preg_replace(
                    '/, $/', '', $formatted_working_hours
                );
                //print $formatted_working_hours;exit;

                $formatted_working_hours .= '; '.sprintf(
                        _('%s hrs/w'), number($this->data['Staff Working Hours Per Week'])
                    );

                return $formatted_working_hours;

            case('Address'):
                return nl2br($this->data['Staff Address']);
                break;
            case('Staff User Password'):
            case('Staff PIN'):
                return '';
                break;

            case('PIN'):
                return '****';
                break;
            case('User Password'):
            case('Password'):
                return '******';
                break;

            case('Telephone'):
                return $this->data['Staff Telephone Formatted'];
                break;
            case('Email'):
                return $this->data['Staff Email'] != '' ? sprintf(
                    '<a href="mailto:%s" target="_top">%s</a>', $this->data['Staff Email'], $this->data['Staff Email']
                ) : '';
                break;

            case 'Staff User Groups':
            case 'Staff User Stores':
            case 'Staff User Websites':

            case 'Staff User Warehouses':
            case 'Staff User Productions':
                $field = preg_replace('/^Staff /', '', $key);

                if (!is_object($this->system_user)) {
                    $this->get_user();
                }
                if (is_object($this->system_user)) {
                    return $this->system_user->get($field);
                }
                break;
            case 'User Groups':
            case 'User Stores':
            case 'User Websites':

            case 'User Warehouses':
            case 'User Productions':
                $field = preg_replace('/^User /', '', $key);
                if (!is_object($this->system_user)) {
                    $this->get_user();
                }
                if (is_object($this->system_user)) {
                    return $this->system_user->get($field);
                }
                break;

            case('User Active'):
                if (array_key_exists('Staff User Active', $this->data)) {
                    switch ($this->data['Staff User Active']) {
                        case('Yes'):
                            $formatted_value = _('Yes');
                            break;
                        case('No'):
                            $formatted_value = _('No');
                            break;

                        default:
                            $formatted_value = $this->data['Staff User Active'];
                    }

                    return $formatted_value;

                } else {
                    return _('No');
                }

                break;


            case('Staff PIN'):
                return '';
                break;

            case('Staff Position'):

                $positions = '';
                $sql       = sprintf(
                    'SELECT GROUP_CONCAT(`Role Code`) AS positions  FROM `Staff Role Bridge` WHERE  `Staff Key`=%d ', $this->id
                );

                if ($row = $this->db->query($sql)->fetch()) {
                    $positions = $row['positions'];
                }

                return $positions;

                break;
            case('Position'):
                $positions = '';
                return $positions;
                /*
                include 'conf/roles.php';

                $sql = sprintf(
                    'SELECT `Role Code` FROM `Staff Role Bridge` WHERE  `Staff Key`=%d ', $this->id
                );

                if ($result = $this->db->query($sql)) {
                    foreach ($result as $row) {


                        $positions .= $roles[$row['Role Code']]['title'].', ';
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    exit;
                }
                $positions = preg_replace('/, $/', '', $positions);

                return $positions;
                break;
            case('Staff Supervisor'):
                return $this->get_supervisors();
                break;
            case('Supervisor'):
                return $this->get_formatted_supervisors();
                */
                break;

            case ('Valid From'):
            case ('Valid To'):
            case ('Birthday'):


                return ($this->data['Staff '.$key] == '' or $this->data['Staff '.$key] == '0000-00-00 00:00:00')
                    ? ''
                    : strftime(
                        "%x", strtotime($this->data['Staff '.$key])
                    );

                break;
            case ('Deleted Date'):


                return $this->data['Staff '.$key] == ''
                    ? ''
                    : strftime(
                        "%a %e %b %Y %H:%M %Z", strtotime($this->data['Staff '.$key])
                    );

                break;

            case('Currently Working'):

                switch ($this->data['Staff Currently Working']) {
                    case('Yes'):
                        $formatted_value = _('Yes');
                        break;
                    case('No'):
                        $formatted_value = _('No');
                        break;

                    default:
                        $formatted_value = $this->data['Staff Currently Working'];
                }

                return $formatted_value;

                break;
            case('Type'):
                switch ($this->data['Staff Type']) {
                    case('Employee'):
                        $type = _('Employee');
                        break;
                    case('Volunteer'):
                        $type = _('Volunteer');
                        break;
                    case('Contractor'):
                        $type = _('Contractor');
                        break;
                    case('TemporalWorker'):
                        $type = _('Temporal Worker');
                        break;
                    case('WorkExperience'):
                        $type = _('Work Experience');
                        break;

                    default:
                        $type = $this->data['Staff Type'];
                }

                return $type;
                break;
            case 'Staff Clocking Data':
                $sql = sprintf(
                    "SELECT ( CASE WHEN `Timesheet Clocking Records` = 0 THEN 'Off' WHEN (`Timesheet Clocking Records` %% 2) = 0 THEN 'Out' ELSE 'In' END) AS `Clocking Status`, (SELECT max(`Timesheet Record Date`) FROM `Timesheet Record Dimension` WHERE `Timesheet Record Timesheet Key`=TD.`Timesheet Key` ) AS `Last Clocking`,  `Timesheet Clocking Records` , `Timesheet Ignored Clocking Records` , TD.`Timesheet Key` FROM `Timesheet Dimension` AS TD WHERE `Timesheet Staff Key`=%d AND  date(`Timesheet Date`)=%s",
                    $this->id, prepare_mysql(gmdate('Y-m-d'))
                );


                if ($result = $this->db->query($sql)) {
                    if ($row = $result->fetch()) {


                        return $row;
                    } else {
                        return array(
                            'Clocking Status'                    => 'Off',
                            'Last Clocking'                      => '',
                            'Timesheet Clocking Records'         => 0,
                            'Timesheet Ignored Clocking Records' => 0,
                            'Timesheet Key'                      => ''
                        );
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    exit;
                }

                break;

            case 'Clocking Data':
                $data = $this->get('Staff Clocking Data');


                if ($data['Timesheet Clocking Records'] == 0) {
                    return _('No clockings today');
                } else {
                    if ($data['Clocking Status'] == 'Off') {
                        $clocked_out_time = ($data['Last Clocking'] == ''
                            ? ''
                            : ' <span class="discreet">('.strftime(
                                "%H:%M", strtotime($data['Last Clocking'].' +0:00')
                            ).')</span>');

                        return '<span class="highlight">'._('Clocked out').'</span> '.$clocked_out_time;
                    } else {
                        return '<span class="highlight success">'._(
                                'Clocked in'
                            ).'</span> ';
                    }
                }

                break;

            default:
                if (array_key_exists($key, $this->data)) {
                    return $this->data[$key];
                }

                if (array_key_exists('Staff '.$key, $this->data)) {
                    return $this->data['Staff '.$key];
                }

        }


    }

    function get_breaks($data) {
        // print_r($data);

        $formatted_breaks = ' (<i class="fa fa-fw fa-utensils"></i> ';
        if (count($data) == 0) {
            return '';
        }
        foreach ($data as $break_data) {

            $break_duration = seconds_to_string(
                abs(
                    strtotime('2000-01-01 '.$break_data['s']) - strtotime(
                        '2000-01-01 '.$break_data['e']
                    )
                ), false, true
            );


            $formatted_breaks .= $break_duration.' @'.$break_data['s'].', ';
        }
        $formatted_breaks = preg_replace('/, $/', '', $formatted_breaks);

        return $formatted_breaks.')';
    }

    function get_user() {


        $sql = sprintf(
            'SELECT `User Key` FROM `User Dimension` WHERE `User Type`=%s AND `User Parent Key`=%d ', prepare_mysql(($this->get('Staff Type') == 'Contractor' ? 'Contractor' : 'Staff')), $this->id
        );

        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $this->system_user = get_object('User', $row['User Key']);
                $this->system_user->read_stores();
                $this->system_user->get_groups();

                return $this->system_user;
            } else {
                return false;
            }
        }


    }

    function get_supervisors() {
        $supervisors = '';
        $sql         = sprintf(
            'SELECT GROUP_CONCAT(B.`Supervisor Key`) AS supervisors  FROM `Staff Supervisor Bridge` B WHERE  `Staff Key`=%d ', $this->id
        );

        if ($row = $this->db->query($sql)->fetch()) {
            $supervisors = $row['supervisors'];
        }

        return $supervisors;
    }

    function get_formatted_supervisors() {

        $supervisors = '';
        $sql         = sprintf(
            'SELECT GROUP_CONCAT(`Staff Alias`  ORDER BY `Staff Alias` SEPARATOR ", ") AS supervisors   FROM  `Staff Supervisor Bridge` B LEFT JOIN `Staff Dimension` S ON (B.`Supervisor Key`=S.`Staff Key`)  WHERE  B.`Staff Key`=%d ', $this->id
        );
        if ($row = $this->db->query($sql)->fetch()) {

            $supervisors = $row['supervisors'];
        }

        $supervisors = preg_replace('/, $/', '', $supervisors);

        return $supervisors;
    }

    function create_user($data) {

        if (isset($this->data['Staff User Key']) and $this->data['Staff User Key']) {
            $this->create_user_error = true;
            $this->create_user_msg   = _('Employee is already a system user');
            $this->user              = false;

            return false;
        }


        //    $data['editor'] = $this->editor;

        if (!array_key_exists('User Handle', $data) or $data['User Handle'] == '') {
            $this->create_user_error = true;
            $this->create_user_msg   = _('User login must be provided');
            $this->user              = false;

            return false;

        }


        if (!array_key_exists('User Password', $data) or $data['User Password'] == '') {
            include_once 'utils/password_functions.php';
            $data['User Password'] = hash('sha256', generatePassword(8, 3));
        }

        if ($this->get('Staff Type') == 'Contractor') {
            $data['User Type'] = 'Contractor';
        } else {
            $data['User Type'] = 'Staff';

        }


        $data['User Parent Key'] = $this->id;
        $data['User Alias']      = $this->get('Name');
        $data['editor']          = $this->editor;
        $user                    = new User('find', $data, 'create');


        $this->get_user_data();
        $this->create_user_error = $user->error;
        $this->create_user_msg   = $user->msg;
        $this->user              = $user;

        return $user;


    }

    function get_user_data() {


        if ($this->get('Staff Type') == 'Contractor') {
            $staff_type = 'Contractor';
        } else {
            $staff_type = 'Staff';
        }

        $sql = sprintf(
            'SELECT * FROM `User Dimension` WHERE `User Type`=%s AND `User Parent Key`=%d ', prepare_mysql($staff_type), $this->id
        );

        if ($row = $this->db->query($sql)->fetch()) {

            foreach ($row as $key => $value) {
                $this->data['Staff '.$key] = $value;
            }
        }


    }




    function create_timesheet_record($data) {

        $data['Timesheet Record Staff Key'] = $this->id;
        $this->timesheet_record             = new Timesheet_Record(
            'new', $data
        );

        $this->create_timesheet_record_error      = $this->timesheet_record->error;
        $this->create_timesheet_record_duplicated = $this->timesheet_record->duplicated;
        $this->create_timesheet_record_msg        = $this->timesheet_record->msg;

        if ($this->timesheet_record->new) {

            $timesheet_data = array(
                'Timesheet Date'      => date(
                    "Y-m-d", strtotime(
                               $this->timesheet_record->data['Timesheet Record Date'].' +0:00'
                           )
                ),
                'Timesheet Staff Key' => $this->id,
                'editor'              => $this->editor
            );
            $timesheet      = new Timesheet('find', $timesheet_data, 'create');

            $this->timesheet_record->update(
                array('Timesheet Record Timesheet Key' => $timesheet->id)
            );


            $timesheet->update_number_clocking_records();
            $timesheet->process_clocking_records_action_type();
            $timesheet->update_clocked_time();
            $timesheet->update_working_time();
            $timesheet->update_unpaid_overtime();


        }

    }

    function update_field_switcher($field, $value, $options = '', $metadata = '') {

        global $account;

        if (is_string($value)) {
            $value = _trim($value);
        }


        switch ($field) {

            case('Staff Telephone'):
                if ($value == '') {
                    $formatted_value = '';
                } else {
                    list(
                        $value, $formatted_value
                        ) = $this->get_formatted_number($value);
                }
                $this->update_field($field, $value, 'no_history');
                $this->update_field(
                    'Staff Telephone Formatted', $formatted_value, $options
                );

                break;
            case 'Staff Valid From':
                require_once 'utils/date_functions.php';


                if ($this->get('Staff Valid To') != '' and strtotime($value) > strtotime($this->get('Staff Valid To'))) {
                    $this->error = true;
                    $this->msg   = _("Working from must be before the end of employment ");

                    return;

                }


                $this->update_field($field, $value, $options);
                $from = date('Y-m-d', strtotime($this->get('Staff Valid From')));
                $to   = date('Y-m-d', strtotime(date('Y', strtotime('now + 1 year')).'-'.$account->get('Account HR Start Year')));

                if ($from and $to) {


                    $dates = date_range($from, $to);
                    foreach ($dates as $date) {
                        $timesheet = $this->create_timesheet(
                            strtotime($date.' 00:00:00'), ''
                        );
                        $timesheet->update_number_clocking_records();
                        $timesheet->process_clocking_records_action_type();
                        if ($timesheet->get('Timesheet Clocking Records') > 0) {


                            $timesheet->update_clocked_time();
                            $timesheet->update_working_time();
                            $timesheet->update_unpaid_overtime();
                        }


                    }


                }


                break;

            case 'Staff Valid To':
                require_once 'utils/date_functions.php';


                if (strtotime($this->get('Staff Valid From')) > strtotime($value)) {
                    $this->error = true;
                    $this->msg   = _("Working from must be before the end of employment ");

                    return;

                }


                $this->update_field($field, $value, $options);
                $from = date('Y-m-d', strtotime($this->get('Staff Valid From')));
                $to   = date('Y-m-d', strtotime(date('Y', strtotime('now + 1 year')).'-'.$account->get('Account HR Start Year')));

                if ($from and $to) {


                    $dates = date_range($from, $to);
                    foreach ($dates as $date) {
                        $timesheet = $this->create_timesheet(
                            strtotime($date.' 00:00:00'), ''
                        );
                        $timesheet->update_number_clocking_records();
                        $timesheet->process_clocking_records_action_type();
                        if ($timesheet->get('Timesheet Clocking Records') > 0) {


                            $timesheet->update_clocked_time();
                            $timesheet->update_working_time();
                            $timesheet->update_unpaid_overtime();
                        }


                    }


                }


                break;

            case('Staff Working Hours'):
                require_once 'utils/date_functions.php';

                $this->update_field($field, $value, $options);

                list($working_hours_per_week, $working_hours_per_week_metadata) = $this->get_working_hours_per_week($this->data['Staff Working Hours']);

                $this->update_field('Staff Working Hours Per Week', $working_hours_per_week, 'no_history');
                $this->update_field('Staff Working Hours Per Week Metadata', json_encode($working_hours_per_week_metadata), 'no_history');


                $to = date('Y-m-d', strtotime(date('Y', strtotime('now + 1 year')).'-'.$account->get('Account HR Start Year')));

                $from = date('Y-m-d');

                if ($from and $to) {


                    $dates = date_range($from, $to);
                    foreach ($dates as $date) {
                        $timesheet = $this->create_timesheet(strtotime($date.' 00:00:00'), 'force');
                        $timesheet->update_number_clocking_records();
                        $timesheet->process_clocking_records_action_type();
                        if ($timesheet->get('Timesheet Clocking Records') > 0) {


                            $timesheet->update_clocked_time();
                            $timesheet->update_working_time();
                            $timesheet->update_unpaid_overtime();
                        }


                    }


                }


                $this->other_fields_updated = array(
                    'Staff_Salary' => array(
                        'field'           => 'Staff_Salary',
                        'render'          => true,
                        'value'           => $this->get('Staff Salary'),
                        'formatted_value' => $this->get('Salary'),
                    )
                );


                break;
            case('Staff PIN'):
                $this->update_pin($value);
                break;
            case('Staff Currently Working'):
                $this->update_is_working($value, $options);
                break;
            case('Staff Name'):
                $this->update_name($value);
                break;

            case('Staff Supervisor'):
                $this->update_supervisors($value);
                break;
            case('Staff User Handle'):
            case('Staff User Password'):
            case('Staff User Active'):
            case('Staff User Groups'):
            case('Staff User Stores'):
            case('Staff User Websites'):
            case('Staff User Warehouses'):
            case('Staff User Productions'):

                $this->get_user();


                if (is_object($this->system_user)) {
                    $this->system_user->editor = $this->editor;
                    $user_field                = preg_replace('/^Staff /', '', $field);

                    $this->system_user->update(array($user_field => $value), $options);
                    $this->error   = $this->system_user->error;
                    $this->msg     = $this->system_user->msg;
                    $this->updated = $this->system_user->updated;

                    $this->get_user_data();


                    $this->other_fields_updated = array(
                        'Staff_User_Stores'      => array(
                            'field'  => 'Staff_User_Stores',
                            'render' => $this->system_user->has_scope('Stores')
                        ),
                        'Staff_User_Warehouses'  => array(
                            'field'  => 'Staff_User_Warehouses',
                            'render' => $this->system_user->has_scope('Warehouses')
                        ),
                        'Staff_User_Websites'    => array(
                            'field'  => 'Staff_User_Websites',
                            'render' => $this->system_user->has_scope('Websites')
                        ),
                        'Staff_User_Productions' => array(
                            'field'  => 'Staff_User_Productions',
                            'render' => $this->system_user->has_scope('Productions')
                        ),


                    );


                }

                break;

            default:
                $base_data = $this->base_data();
                if (array_key_exists($field, $base_data)) {
                    $this->update_field($field, $value, $options);
                }
        }
        $this->reread();
        $this->get_user_data();
    }

    function get_formatted_number($value) {
        include_once 'utils/get_phoneUtil.php';
        $phoneUtil = get_phoneUtil();
        try {

            $account = new Account($this->db);
            $country = $account->get('Account Country 2 Alpha Code');

            $proto_number    = $phoneUtil->parse($value, $country);
            $formatted_value = $phoneUtil->format(
                $proto_number, \libphonenumber\PhoneNumberFormat::INTERNATIONAL
            );

            $value = $phoneUtil->format(
                $proto_number, \libphonenumber\PhoneNumberFormat::E164
            );


        } catch (\libphonenumber\NumberParseException $e) {

        }


        return array(
            $value,
            $formatted_value
        );

    }

    function get_working_hours_per_week($data) {

        $working_hours = json_decode($data, true);
        if (!$working_hours) {
            return '';
        }
        $diff     = 0;
        $metadata = array(
            'Weekdays' => 0,
            'Saturday' => 0,
            'Sunday'   => 0
        );
        foreach ($working_hours['data'] as $day_key => $day_data) {
            //$start=date('H:i', strtotime('2000-01-01 '.$day_data['s']));
            //$end=date('H:i', strtotime('2000-01-01 '.$day_data['e']));
            $day_hours  = strtotime('2000-01-01 '.$day_data['e']) - strtotime(
                    '2000-01-01 '.$day_data['s']
                );
            $break_diff = 0;
            foreach ($day_data['b'] as $break_data) {


                $break_diff += strtotime('2000-01-01 '.$break_data['e']) - strtotime('2000-01-01 '.$break_data['s']);

            }
            $day_hours = $day_hours - $break_diff;

            if ($day_key == 6) {
                $metadata['Saturday'] = $day_hours / 3600;
            } elseif ($day_key == 7) {
                $metadata['Sunday'] = $day_hours / 3600;

            } else {
                $metadata['Weekdays'] = $metadata['Weekdays'] + ($day_hours / 3600);

            }

            $diff += $day_hours;

        }
        $diff = $diff / 3600;

        return array(
            $diff,
            $metadata
        );

    }

    function update_pin($value) {

        $value = password_hash($value, PASSWORD_DEFAULT);

        $this->update_field('Staff PIN', $value, 'nohistory');
        $this->add_changelog_record(
            'Staff PIN', '****', '****', '', $this->table_name, $this->id
        );
        $system_user         = new User($this->data['Staff User Key']);
        $system_user->editor = $this->editor;

        if ($system_user->id) {
            $system_user->add_changelog_record(
                'User PIN', '****', '****', '', $system_user->table_name, $system_user->id
            );
        }

    }

    function update_is_working($value, $options = '') {

        global $account;
        include_once 'class.Timesheet.php';
        require_once 'utils/date_functions.php';


        if ($value == 'No') {
            $this->update_field('Staff Currently Working', $value, $options);
            $this->update_field('Staff Valid To', gmdate('Y-m-d H:i:s'), 'no_history');


            $delete_from = date(
                'Y-m-d', strtotime($this->get('Staff Valid To').' + 1 day')
            );
            $sql         = sprintf(
                "DELETE FROM `Timesheet Record Dimension` WHERE `Timesheet Record Staff Key`=%d AND `Timesheet Record Date`>=%s AND `Timesheet Record Type`!='ClockingRecord'  ", $this->id, prepare_mysql($delete_from)
            );
            $this->db->exec($sql);


            $sql = sprintf(
                "SELECT `Timesheet Key` FROM `Timesheet Dimension` WHERE `Timesheet Staff Key`=%d AND `Timesheet Date`>=%s   ", $this->id, prepare_mysql($delete_from)
            );

            print $sql;

            if ($result3 = $this->db->query($sql)) {

                foreach ($result3 as $data) {
                    $timesheet = new Timesheet($data['Timesheet Key']);

                    //print $timesheet->get('Timesheet Date').' '.$timesheet->get('Timesheet Staff Key')."\n";

                    $sql = sprintf(
                        "SELECT count(*) AS num  FROM `Timesheet Record Dimension` WHERE `Timesheet Record Timesheet Key`=%d    ", $timesheet->id
                    );

                    //print "$sql\n";
                    if ($result2 = $this->db->query($sql)) {

                        if ($row2 = $result2->fetch()) {
                            if ($row2['num'] > 0) {
                                $timesheet->update_number_clocking_records();
                                $timesheet->process_clocking_records_action_type();
                                $timesheet->update_clocked_time();
                                $timesheet->update_working_time();
                                $timesheet->update_unpaid_overtime();

                            } else {

                                $sql = sprintf(
                                    "DELETE FROM `Timesheet Dimension` WHERE `Timesheet Key`=%d   ", $timesheet->id
                                );
                                $this->db->exec($sql);
                                //print "$sql\n";

                            }
                        }
                    } else {
                        print_r($error_info = $this->db->errorInfo());
                        exit;

                    }


                }

            } else {
                print_r($error_info = $this->db->errorInfo());
                exit;
            }


        } else {


            if ($this->get('Staff Official ID') != '') {
                $sql = sprintf(
                    "SELECT `Staff Key` ,`Staff Alias`  FROM `Staff Dimension` WHERE`Staff Currently Working`='Yes' AND `Staff Official ID`=%s", prepare_mysql($this->get('Staff Official ID'))
                );

                if ($result = $this->db->query($sql)) {
                    if ($row = $result->fetch()) {
                        $this->error = true;
                        $this->msg   = sprintf(
                            _('%s has same %s'), '<span class="link" onClick="change_view(\'employee/'.$row['Staff Key'].'\')">'.$row['Staff Alias'].'</span>', $this->get_field_label('Staff Official ID')
                        );

                        return;
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    exit;
                }
            }

            if ($this->get('Staff Email') != '') {
                $sql = sprintf(
                    "SELECT `Staff Key` ,`Staff Alias`  FROM `Staff Dimension` WHERE`Staff Currently Working`='Yes' AND `Staff Email`=%s", prepare_mysql($this->get('Staff Email'))
                );

                if ($result = $this->db->query($sql)) {
                    if ($row = $result->fetch()) {
                        $this->error = true;
                        $this->msg   = sprintf(
                            _('%s has same %s'), '<span class="link" onClick="change_view(\'employee/'.$row['Staff Key'].'\')">'.$row['Staff Alias'].'</span>', $this->get_field_label('Staff Email')
                        );

                        return;
                    }
                } else {
                    print_r($error_info = $this->db->errorInfo());
                    exit;
                }
            }


            $sql = sprintf(
                "SELECT `Staff Key` ,`Staff Alias`  FROM `Staff Dimension` WHERE`Staff Currently Working`='Yes' AND `Staff Alias`=%s", prepare_mysql($this->get('Staff Alias'))
            );

            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {
                    $this->error = true;
                    $this->msg   = sprintf(
                        _('%s has same %s'), '<span class="link" onClick="change_view(\'employee/'.$row['Staff Key'].'\')">'.$row['Staff Alias'].'</span>', $this->get_field_label('Staff Alias')
                    );

                    return;
                }
            } else {
                print_r($error_info = $this->db->errorInfo());
                exit;
            }

            $sql = sprintf(
                "SELECT `Staff Key` ,`Staff Alias`  FROM `Staff Dimension` WHERE`Staff Currently Working`='Yes' AND `Staff ID`=%s", prepare_mysql($this->get('Staff ID'))
            );

            if ($result = $this->db->query($sql)) {
                if ($row = $result->fetch()) {
                    $this->error = true;
                    $this->msg   = sprintf(
                        _('%s has same %s'), '<span class="link" onClick="change_view(\'employee/'.$row['Staff Key'].'\')">'.$row['Staff Alias'].'</span>', $this->get_field_label('Staff ID')
                    );

                    return;
                }
            } else {
                print_r($error_info = $this->db->errorInfo());
                exit;
            }


            $this->update_field('Staff Currently Working', $value, $options);


            $this->update_field('Staff Valid To', '', 'no_history');

            $from = date('Y-m-d');
            $to   = date(
                'Y-m-d', strtotime(
                           date('Y', strtotime('now + 1 year')).'-'.$account->get(
                               'Account HR Start Year'
                           )
                       )
            );

            $dates = date_range($from, $to);
            foreach ($dates as $date) {
                $timesheet = $this->create_timesheet(
                    strtotime($date.' 00:00:00'), ''
                );
            }


        }

        $this->other_fields_updated = array(
            'Staff_Valid_To' => array(
                'field'           => 'Staff_Valid_To',
                'render'          => ($this->get('Staff Currently Working') == 'Yes' ? false : true),
                'value'           => $this->get('Staff Valid To'),
                'formatted_value' => $this->get('Valid To'),


            )
        );

    }

    function get_field_label($field) {


        switch ($field) {

            case 'Staff Key':
                $label = _('id');
                break;
            case 'Staff ID':
                if ($this->data['Staff Type'] == 'Contractor') {
                    $label = _('reference');
                } else {
                    $label = _('payroll Id');
                }
                break;
            case 'Staff Alias':
                $label = _('code');
                break;
            case 'Staff Name':
                $label = _('name');
                break;
            case 'Staff Birthday':
                $label = _('date of birth');
                break;
            case 'Staff Email':
                $label = _('email');
                break;
            case 'Staff Telephone':
            case 'Staff Telephone Formatted':
                $label = _('contact number');
                break;
            case 'Staff Address':
                $label = _('address');
                break;
            case 'Staff Official ID':
                $account = new Account();
                $label   = $account->get('National Employment Code Label') == ''
                    ? _('Official Id')
                    : $account->get(
                        'National Employment Code Label'
                    );
                break;
            case 'Staff Next of Kind':
                $label = _('next of kin');
                break;

            case 'Staff Type':
                $label = _('type');
                break;
            case 'Staff Currently Working':
                $label = _('currently working');
                break;
            case 'Staff Valid From':
                $label = _('working from');
                break;
            case 'Staff Valid To':

                if ($this->data['Staff Type'] == 'Contractor') {
                    $label = _('end of contract');
                } else {
                    $label = _('end of employment');
                }

                break;
            case 'Staff Position':
                $label = _('Job position');
                break;
            case 'Staff Job Title':

                if ($this->data['Staff Type'] == 'Contractor') {
                    $label = _('assignment title');
                } else {
                    $label = _('job title');
                }
                break;
            case 'Staff Supervisor':
                if ($this->data['Staff Type'] == 'Contractor') {
                    $label = _('point of contact');
                } else {
                    $label = _('supervisor');
                }
                break;
            case 'Staff Working Hours':
                if ($this->data['Staff Type'] == 'Contractor') {
                    $label = _('insite working hours');
                } else {
                    $label = _('working hours');
                }
                break;
            case 'Staff Salary':
                if ($this->data['Staff Type'] == 'Contractor') {
                    $label = _('cost');
                } else {
                    $label = _('salary');
                }
                break;

            case 'Staff User Active':
            case 'User Active':
                $label = _('system user active');
                break;
            case 'Staff User Handle':
            case 'User Handle':
                $label = _('system user login');
                break;

            case 'Staff User Password':
            case 'User Password':
                $label = _('system user password');
                break;

            case 'Staff User PIN':
                $label = _('System user PIN');
                break;
            case 'User Groups':
                $label = _('System user groups');
                break;
            case 'User Stores':
                $label = _('Authorized stores');
                break;
            case 'User Websites':
                $label = _('Authorized websites');
                break;
            case 'User Warehouses':
                $label = _('Authorized warehouses');
                break;
            case 'User Productions':
                $label = _('Authorized manufactures');
                break;


            default:
                $label = $field;

        }

        return $label;

    }

    function update_name($value, $options = '') {

        if ($value == '') {
            $this->error = true;
            $this->msg   = 'invalid value';

            return;
        }

        $this->get_user_data();
        $system_user = new User($this->get('Staff User Key'));
        if ($system_user->id) {

            $system_user->update(array('User Alias' => $value), $options);
        }


        $this->update_field('Staff Name', $value);

    }

    function update_supervisors($values, $options = '') {

        $old_value = $this->get('Supervisor');


        $supervisors = array();
        $sql         = sprintf('SELECT `Staff Key` FROM `Staff Dimension`  ');
        foreach ($this->db->query($sql) as $row) {
            $supervisors[$row['Staff Key']] = false;
        }

        foreach (preg_split('/,/', $values) as $selected_supervisor) {
            if (is_numeric($selected_supervisor) and array_key_exists(
                    $selected_supervisor, $supervisors
                )) {
                $supervisors[$selected_supervisor] = true;
            }
        }


        foreach ($supervisors as $key => $value) {
            if ($value) {
                $this->add_supervisor($key);
            } else {
                $this->remove_supervisor($key);
            }
        }

        $new_value = $this->get('Supervisor');
        $this->add_changelog_record(
            'Staff Supervisor', $old_value, $new_value, $options, $this->table_name, $this->id
        );


    }

    function add_supervisor($value) {
        $updated = false;
        $sql     = sprintf(
            "INSERT INTO `Staff Supervisor Bridge` (`Supervisor Key`, `Staff Key`) VALUES (%d, %d)   ON DUPLICATE KEY UPDATE  `Supervisor Key`= %d", $value, $this->id, $value
        );
        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $this->updated = true;
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql";
            exit;
        }
    }

    function remove_supervisor($supervisor_key) {

        $sql = sprintf(
            "DELETE FROM  `Staff Supervisor Bridge` WHERE `Supervisor Key`=%d AND `Staff Key`=%d", $supervisor_key, $this->id
        );
        if ($result = $this->db->query($sql)) {
            if ($row = $result->fetch()) {
                $this->updated = true;
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print "$sql";
            exit;
        }
    }

    function get_name() {
        return $this->data['Staff Name'];
    }

    function delete() {

        include_once 'class.Attachment.php';
        include_once 'class.Image.php';


        $timesheet_records = array();
        $sql               = sprintf(
            "SELECT `Timesheet Record Date`,`Timesheet Record Source` FROM `Timesheet Record Dimension`   WHERE  `Timesheet Record Source`!='System' AND  `Timesheet Record Staff Key`=%d  ", $this->id
        );

        $clockings = array();
        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                $timesheet_records[] = array(
                    $row['Timesheet Record Source'],
                    $row['Timesheet Record Date']
                );
            }

        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }


        $this->get_user_data();

        $user = new User($this->get('Staff User Key'));
        if ($user->id) {
            $user->delete();
        }


        $data     = array(
            'data'              => $this->data,
            'timesheet_records' => $timesheet_records
        );
        $metadata = json_encode($data);


        $sql = sprintf(
            "INSERT INTO `Staff Deleted Dimension`  (`Staff Deleted Key`,`Staff Deleted Type`,`Staff Deleted ID`,`Staff Deleted Alias`,`Staff Deleted Name`,`Staff Deleted Date`,`Staff Deleted Metadata`) VALUES (%d,%s,%s,%s,%s,%s,%s) ", $this->id, prepare_mysql(
            ($this->get('Staff Type') == 'Contractor' ? 'Contractor' : 'Employee')
        ), prepare_mysql($this->get('Staff ID'), true), prepare_mysql($this->get('Staff Alias'), true), prepare_mysql($this->get('Staff Name'), true), prepare_mysql(gmdate('Y-m-d H:i:s')), prepare_mysql(gzcompress($metadata, 9))

        );

        //print $sql;


        $stmt = $this->db->prepare($sql);
        $stmt->execute();


        $this->get_user_data();

        $user = new User($this->get('Staff User Key'));
        if ($user->id) {
            $user->delete();
        }


        $sql = sprintf(
            "DELETE FROM `Timesheet Record Dimension` WHERE `Timesheet Record Staff Key`=%d  ", $this->id
        );
        $this->db->exec($sql);


        $sql = sprintf(
            "DELETE FROM `Timesheet Dimension` WHERE `Timesheet Staff Key`=%d  ", $this->id
        );
        $this->db->exec($sql);

        $sql = sprintf(
            "DELETE FROM `Staff Role Bridge` WHERE `Staff Key`=%d  ", $this->id
        );
        $this->db->exec($sql);

        $sql = sprintf(
            "DELETE FROM `Staff Supervisor Bridge` WHERE `Staff Key`=%d  ", $this->id
        );
        $this->db->exec($sql);

        $sql = sprintf(
            "SELECT `Attachment Bridge Key`,`Attachment Key` FROM `Attachment Bridge` WHERE `Subject`='Staff' AND `Subject Key`=%d", $this->id
        );
        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                $sql = sprintf(
                    "DELETE FROM `Attachment Bridge` WHERE `Attachment Bridge Key`=%d", $row['Attachment Bridge Key']
                );
                $this->db->exec($sql);
                $attachment = new Attachment($row['Attachment Key']);
                $attachment->delete();
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            exit;
        }

        $sql = sprintf(
            "SELECT `Image Subject Key`,`Image Subject Image Key` FROM `Image Subject Bridge` WHERE `Image Subject Object`='Staff' AND `Image Subject Object Key`=%d", $this->id
        );
        if ($result = $this->db->query($sql)) {
            foreach ($result as $row) {
                $sql = sprintf(
                    "DELETE FROM `Image Subject Bridge` WHERE `Image Subject Key`=%d", $row['Image Subject Key']
                );
                $this->db->exec($sql);
                $image = get_object('Image', $row['Image Subject Image Key']);
                $image->delete();
            }
        } else {
            print_r($error_info = $this->db->errorInfo());
            print $sql;
            exit;
        }


        $sql = sprintf(
            "DELETE FROM `Staff Dimension` WHERE `Staff Key`=%d  ", $this->id
        );
        $this->db->exec($sql);


        if ($this->get('Staff Type') == 'Contractor') {
            $abstract = sprintf(
                _('Contractor %s deleted'), sprintf(
                                              '<span class="button" onClick="change_view(\'contractor/%d\')">%s</span>', $this->id, $this->get('Alias')
                                          )
            );
        } else {
            $abstract = sprintf(
                _('Employee %s deleted'), sprintf(
                                            '<span class="button" onClick="change_view(\'employee/%d\')">%s</span>', $this->id, $this->get('Alias')
                                        )
            );

        }


        $history_data = array(
            'History Abstract' => $abstract,
            'History Details'  => '',
            'Action'           => 'deleted'
        );
        $history_key  = $this->add_subject_history($history_data, true, 'No', 'Changes', $this->get_object_name(), $this->id);

        $sql = sprintf(
            'INSERT INTO `HR History Bridge`  (`HR Key`,`History Key`,`Type`) VALUES (1,%d,"Changes")  ', $history_key
        );
        $this->db->exec($sql);


        $this->deleted = true;


    }

    function terminate_employment() {


        if ($this->get('Staff Currently Working') == 'No') {
            $this->error = true;
            $this->msg   = _('Employee is no longer working');

            return;

        }


        $this->update(
            array(

                'Staff Currently Working' => 'No',
                'Staff User Active'       => 'No',
                'Staff Valid To'          => gmdate('Y-m-d H:i:s')

            )

        );


    }

}


?>
