<?php
global $wpdb;

if (!isset($wpdb)) {
    include_once '../../../../wp-config.php';
}

if (file_exists(dirname(__FILE__, 3) . '/common/autoload.php')) {
    include_once dirname(__FILE__, 3) . '/common/autoload.php';
}

function staffVerifyUser()
{
    global $payload;
    $payload = JwtToken::getBearerToken();
    return Staff::verifyUser($payload);
}

// echo "<pre>";
// print_r($_GET);
// die;
if (!empty($_GET['val'])) {

    if (staffVerifyUser()) {
        try {
            switch ($_GET['val']) {
                case 'getStudentApplications':

                    $sql = "SELECT app.*,u.id as stu_id, concat(u.f_name,'',u.l_name) as stu_name,agents.id as agent_id,
                    agents.name as agent_name,c.id as c_id,c.name as c_name,s.id as s_id,s.name as s_name FROM applications
                     as app JOIN users as u ON u.id=app.student_id JOIN school as s on s.id=app.school_id JOIN courses as c
                      ON c.id=app.course_id LEFT JOIN agents ON agents.id=app.agent_id";

                    $id = $payload->userId;

                    getApplications($wpdb, $sql, $id);

                    break;

                case 'getReviewApplications':
                    $id = $payload->userId;

                    $sql = "SELECT app.*,u.id as stu_id, concat(u.f_name,'',u.l_name) as stu_name,agents.id as agent_id,
                    agents.name as agent_name,c.id as c_id,c.name as c_name,s.id as s_id,s.name as s_name FROM applications
                     as app JOIN users as u ON u.id=app.student_id JOIN school as s on s.id=app.school_id JOIN courses as c
                      ON c.id=app.course_id LEFT JOIN agents ON agents.id=app.agent_id where is_reviewed='1' && review_by=" . $id;

                    getApplications($wpdb, $sql, $id);
                    break;

                default:
                    throw new Exception("No match Found");
                    break;
            }

        } catch (Exception $e) {
            $response = ['status' => Error_Code, 'message' => $e->getMessage()];
        }
    }
} else {
    $response = ['status' => Error_Code, 'message' => 'Unauthorized Access.Value is required'];
}

function getApplications($wpdb, $sql, $id)
{
    $start = $_GET['start'];
    $length = $_GET['length'];

    // set the limit of query...
    $limit = 'limit ' . $start . ',' . $length;

    // sort array...
    $sort_arr = ['app.id', 'agents.name', 's.name', 'c.name', 'u.f_name', '', 'app.created_at'];

    // order by...
    $order_by = 'order by ' . $sort_arr[$_GET['order'][0][column]] . ' ' . $_GET['order'][0][dir];

    // search array to search by column name...
    $srch_arr = ['app.id', 'agents.name', 's.name', 'c.name', 'u.f_name', 'u.l_name', 'app.created_at'];

    // if search value is not empty....
    if (!empty($_GET['search'][value])) {
        $where = ' where ';

        $srch_val = $_GET['search'][value];
        foreach ($srch_arr as $col_name) {
            $where .= $col_name . " like '%" . $srch_val . "%' or ";
        }
    }

    // repalcing the 'or' from last of the string...
    $where = substr_replace($where, '', -3);

    // query to get the total applications...
    $total_applications = $wpdb->get_results($sql . $where);

    $sql = $sql . ' ' . $where . ' ' . $order_by . ' ' . $limit;

    // echo $sql;die;
    // query to display the applications per page....
    $display_applications = $wpdb->get_results($sql);

    // if the records is not empty...
    if (!empty($display_applications)) {
        foreach ($display_applications as $key => $obj) {
            $record = [];
            $record[] = $obj->id;
            if (!empty($obj->agent_name)) {
                $record[] = $obj->agent_name;
            } else {
                $record[] = "No Agent";
            }
            $record[] = $obj->s_name;
            $record[] = $obj->c_name . "<br><a href='" . base_url . 'view-course?c_id=' . base64_encode($obj->c_id) . "' class='view_course'>View Course</a>";
            $record[] = $obj->stu_name . "<br><a href='" . base_url . 'student-detail?stu_id=' . base64_encode($obj->stu_id) . "' class='view_student'>View Student</a>";
            $intakes = json_decode($obj->intake, true);

            // get the month name from its id..
            $dateObj = DateTime::createFromFormat('!m', $intakes['month']);
            $monthName = $dateObj->format('F');

            $record[] = $monthName . "-" . $intakes['year'];
            $record[] = Date('d-m-Y', strtotime($obj->created_at));

            $pending = false;
            $approve = false;
            $decline = false;

            if ($obj->is_reviewed) {
                switch ($obj->status) {
                    case '0':
                        $pending = 'selected="selected"';
                        $status = "Pending";
                        break;

                    case '1':
                        $approve = 'selected="selected"';
                        $status = "Approved";
                        break;

                    case '2':
                        $decline = 'selected="selected"';
                        $status = "Declined";
                        break;
                }

                if ($obj->review_by == $id) {
                    // echo  $pending;
                    $record[] = "<select class='update_status' app_id=" . base64_encode($obj->id) . ">
                       <option value='0'" . $pending . " disabled>Pending</option>
                       <option value='1'" . $approve . ">Approve</option>
                       <option value='2'" . $decline . ">Decline</option>
                       </select>";
                    $record[] = "Reviewing by me. <br><a href='" . base_url . "application-detail?a_id=" . base64_encode($obj->id) . "'>View Application</a>";

                }
                // if application is not reviewed by me...
                else {
                    $staff_id = $obj->review_by;
                    $staff = $wpdb->get_results("select id,name from staff where id=" . $staff_id);

                    $staff_name = $staff[0]->name;
                    $record[] = $status;

                    $record[] = "Reviewing By " . $staff_name;
                }
            }

            if (!$obj->is_reviewed) {
                $record[] = "Pending";
                $record[] = "<input type='button' class='btn btn-primary mark_review' value='Mark Review' data_id='" . $obj->id . "'>";
            }
            $output['aaData'][] = $record;

        }

        $output['iTotalDisplayRecords'] = count($total_applications);
        $output['iTotalRecords'] = count($display_applications);

    } else {
        $output['aaData'] = [];
        $output['iTotalDisplayRecords'] = 0;
        $output['iTotalRecords'] = 0;
    }

    // return the json output...
    echo json_encode($output);
    exit;

}

echo json_encode($response);
