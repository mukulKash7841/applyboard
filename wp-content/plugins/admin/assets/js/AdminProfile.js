$(document).ready(function () {

    getAdminProfile();
})

function getAdminProfile() {
    $.ajax({
        url: admin_server_url + "GetProfile.php",
        data: { val: "profile" },
        dataType: "json",
        beforeSend: function (request) {
            if (!appendToken(request)) {
                adminRedirectLogin();
            }
        },
        success: function (response) {

            if (verifyToken(response)) {

                if (response.status == 200) {
                    $("#profile_container").show();
                    $("#admin_email").val(response.data.email);
                    if (response.data.image != null) {
                        $("#image").attr("src", admin_assets_url + "images/" + response.data.image)
                    } else {
                        $("#image").attr("src", admin_assets_url + "images/default_image.png")
                    }
                    console.log(response.data);

                } else {
                    errorSwal(response)
                }
            } else {
                adminRedirectLogin();
            }
        },
        error: function (error) {
            var response = { 'status': 400, 'message': 'Internal Server Error' };
            errorSwal(response)
        }
    })
}

// when user clicks on change password link...
$("#change_password").click(function () {

    // to show the modal to enter old password...
    $("#password_modal").modal('show');
})

// when user clicks on password button after entering the password...
$("#check_password").click(function () {
    var password_val = $("#admin_password").val();
        // console.log(password_val);
    if(password_val!=""){
        verifyOldPassword(password_val);
    }
})

function verifyOldPassword(password_val) {

    $.ajax({
        url: admin_server_url + "GetProfile.php",
        type: "get",
        dataType: "json",
        data: { val: "oldPassword", password: password_val },
        beforeSend: function (request) {
            if (!appendToken(request)) {
                adminRedirectLogin();
            }
        },
        success: function (response) {

            if (verifyToken(response)) {
                if (response.status == 200) {
                    // console.log(response.data);
                    window.location.href = base_url + "change-password/?tok=" + response.data.token;

                } else {
                    errorSwal(response)
                }
            } else {
                adminRedirectLogin();
            }
        },
        error: function (response) {
            var response = { 'status': 400, 'message': 'Internal Server Error' };
            errorSwal(response)
        }
    })
}

$("#image_input").change(function () {
    if (this.files && this.files[0]) {

        // creating file reader object...
        var reader = new FileReader();

        reader.onload = function (e) {

            // setting the profile image...
            $('#image').attr('src', e.target.result);
        }

        reader.readAsDataURL(this.files[0]);
    }
})

$("#update_profile_form").submit(function (e) {
    e.preventDefault();

    var form = document.getElementById('update_profile_form');
    var data = new FormData(form);
    $.ajax({
        url: admin_server_url + "UpdateProfile.php",
        data: data,
        type: "post",
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function (request) {
            if (!appendToken(request)) {
                adminRedirectLogin();
            }
        },
        success: function (response) {
            if (verifyToken(response)) {
                sweetalert(response);

                if (response.status == 200) {
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                }
            } else {
                adminRedirectLogin();
            }
        },
        error: function (error) {
            var response = { 'status': 400, 'message': 'Internal Server Error' };
            errorSwal(response)
        }
    })
})
