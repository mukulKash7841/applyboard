<?php

function reviewApplication()
{
    ?>
<table id="reviewed_application" border="2px solid black">
        <thead>
        <th>Application Id</th>
        <th>Agent Name</th>
        <th>School Name</th>
        <th>Course Name</th>
        <th>Student Name</th>
        <th>Intake</th>
        <th>Submitted On</th>
        <th>Status</th>
        <th>Action</th>
        </thead>
    </table>
    <script src="<?=staff_asset_url?>js/ReviewApplication.js"></script>
   <?php
}

add_shortcode('review_application', 'reviewApplication')
?>
