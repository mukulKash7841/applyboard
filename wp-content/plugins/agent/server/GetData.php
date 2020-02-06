<?php

global $wpdb;

if (!isset($wpdb)) {
    include_once '../../../../wp-config.php';
}

if (file_exists(dirname(__FILE__, 3) . '/common/autoload.php')) {
    include_once dirname(__FILE__, 3) . '/common/autoload.php';
}

function verifyUser()
{
    global $payload;
    $payload = JwtToken::getBearerToken();
    return Agent::verifyUser($payload);
}

if (!empty($_GET['val'])) {
    try {
        if (verifyUser()) {

            switch ($_GET['val']) {
                case 'getData':

                    // sql to get all the countries...
                    $country_sql = "select id,name from countries";
                    $country_data = $wpdb->get_results($country_sql);

                    // sql to get all the grades...
                    $grade_scheme_sql = "select id,grade_scheme from grade where status='1'";
                    $grade_data = $wpdb->get_results($grade_scheme_sql);

                    // sql to get all the exams...
                    $lang_sql = "select id,name from language";
                    $languages = $wpdb->get_results($lang_sql);

                    $category_sql = "select id,name from category";
                    $categories = $wpdb->get_results($category_sql);

                    $discipline_sql = "select id,name from type";
                    $discipline_data = $wpdb->get_results($discipline_sql);

                    // storing all data in an array...
                    $response = ['status' => Success_Code, 'cntry_data' => $country_data,
                        'grade' => $grade_data, 'languages' => $languages, 
                        'categories' => $categories,'disciplines'=>$discipline_data];

                    break;

                case 'getExamByLanguage':

                    if (empty($_GET['id'])) {
                        throw new Exception("Please select the language first");
                    }

                    $id = $_GET['id'];

                    // sql to get all the exams of specific language...
                    $exam_sql = "select id,name from exams where language_id=" . $id;
                    $exam_data = $wpdb->get_results($exam_sql);

                    // storing all data in an array...
                    $response = ['status' => Success_Code, 'message' => 'Exams fetched successfully', 'exam_data' => $exam_data];
                    break;

                case 'getGradeSchemeById':
                    if (empty($_GET['id'])) {
                        throw new Exception("Please select the language first");
                    }
                    $id = $_GET['id'];

                    // sql to get all the exams of specific language...
                    $grade_sql = "select id,grade_scheme from grade where id=" . $id;
                    $grade_data = $wpdb->get_results($grade_sql);

                    // storing all data in an array...
                    $response = ['status' => Success_Code, 'message' => 'Grade scheme fetched successfully',
                        'grade_data' => json_decode($grade_data[0]->grade_scheme)];

                    break;

                case 'getSchools':

                    if (empty($_GET['countries'])) {
                        throw new Exception("Please select the country first");
                    }
                    $countries = implode(",", $_GET['countries']);

                    // sql to get all the exams of specific language...
                    $school_sql = "select id,name from school where countries_id in (" . $countries . ")";
                    $school_data = $wpdb->get_results($school_sql);

                    $response = ['status' => Success_Code, 'message' => 'School Fetched Successfully',
                        'school_data' => $school_data];

                    break;

                case 'getCourses':

                    if (empty($_GET['categories'])) {
                        throw new Exception("Please select the category to view courses");
                    }

                    if (empty($_GET['school'])) {
                        throw new Exception("Please select the school to view courses");
                    }

                    $categories = implode(",", $_GET['categories']);

                    // $schools = implode(",", $_GET['school']);

                    // sql to get all the exams of specific language...
                    $course_sql = "select id,name from courses where category_id in (" . $categories . ")";
                    echo $sqldie;
                    $course_data = $wpdb->get_results($course_sql);

                    $response = ['status' => Success_Code, 'message' => 'Courses Fetched Successfully',
                        'course_data' => $course_data];

                    break;

                default:
                    throw new Exception("No match Found");
                    break;
            }
        }

    } catch (Exception $e) {
        $response = ['status' => Error_Code, 'message' => $e->getMessage()];
    }
} else {
    $response = ['status' => Error_Code, 'message' => 'Unauthorized Access.Value is Required'];
}

echo json_encode($response);