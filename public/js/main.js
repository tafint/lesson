$( document ).ready(function() {
    
    function escape_html(text){
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };

        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    };

    function notification(content, type){
        noty({
            text: content,
            type: type,
            layout: 'topRight',
            timeout: 1000
        });
    };

	var date = 31;

	var year = new Date().getFullYear();

	var birthday = $("#birthday").val();

	var year_parse = birthday ? birthday.split("-")[0] : year;	var month_parse = birthday ? birthday.split("-")[1] : 1;

	var date_parse = birthday ? birthday.split("-")[2] : 1;

    var input_year = $("#form-year");
    var input_month = $("#form-month");
    var input_date = $("#form-date");

	for (var i = year; i >=1900; i--) {
		input_year.append("<option value='" + i + "'"+ ((year_parse == i) ? "selected" : "") + ">" + i + "</option>");
	};

	for (var i = 1; i <= 12; i++) {
		input_month.append("<option value='" + i + "'" + ((month_parse == i) ? "selected" : "") + ">" + i + "</option>");
	}

	for (var i = 1; i <= date; i++) {
		input_date.append("<option value='" + i + "'" + ((date_parse == i) ? "selected" : "") + ">" + i + "</option>");
	}
    
    $("#submit-register").on("click", function(){
        var c_date = input_date.val();
    	var c_month = input_month.val();
    	var c_year = input_year.val();

    	$("#birthday").val(c_year + "-" + ((c_month < 10) ? ("0" + c_month) : c_month) + "-" + ((c_date < 10) ? ("0" + c_date) : c_date));
    	$('#register-form').submit();
    });

    $("#submit-edit").on("click", function(){
        var c_date = input_date.val();
        var c_month = input_month.val();
        var c_year = input_year.val();

        $("#birthday").val(c_year + "-" + ((c_month < 10) ? ("0" + c_month) : c_month) + "-" + ((c_date < 10) ? ("0" + c_date) : c_date));
    	$("#edit-form").submit();
    });

    $('#form-month').on('change', function(){
    	var c_date = input_date.val();
        var c_month = input_month.val();
        var c_year = input_year.val();

    	if (c_month == 2) {
            // check leap year
    		if ((c_year % 4) == 0) {
    			date = 29;
    		} else {
    			date = 28;
    		}

            // compare current date and max date
    		if (c_date >= date) {
    			input_date.val(1);
    		}

    		input_date.html("");

    		for (var i = 1; i <= date; i++) {
				input_date.append("<option value='" + i + "'" + ((c_date == i) ? "selected" : "") + ">" + i + "</option>");
			}

    	} else if ((c_month == 2) || (c_month == 4) || (c_month == 6) || (c_month == 9) || (c_month == 11) || (c_month == 12)) {
    		date = 30;

    		if (c_date >= date) {
    			input_date.val(1);
    		}

    		input_date.html("");

    		for (var i = 1; i <= date; i++){
				input_date.append("<option value='" + i + "'" + ((c_date == i) ? "selected" : "") + ">" + i + "</option>");
			}

    	} else {
    		date = 31;

    		if (c_date >= date) {
    			input_date.val(1);
    		}

    		input_date.html("");

    		for (var i=1;i <=date; i++){
				input_date.append("<option value='" + i + "'" + ((c_date == i) ? "selected" : "") + ">" + i + "</option>");
			}
    	}
    });

    input_year.on("change", function(){
        var c_date = input_date.val();
    	var c_month = input_month.val();
    	var c_year = input_year.val();

    	if ( c_month == 2){
    		if ((c_date % 4 ) == 0) {
    			date = 29;
    		} else {
    			date = 28;
    		}

    		if (current_date > date) {
    			input_date.val(1);
    		}

    		input_date.html('');

    		for (var i = 1; i <= date; i++) {
				input_date.append("<option value='" + i + "'" + ((c_date == i) ? "selected" : "") + ">" + i + "</option>");
			}
    	} 
    });

    // check edit status in profile page
    if ( $("#edit-status").val() == 1) {
        $(".edit-hide").removeClass("hide");
        $(".edit-show").addClass("hide");
    };

    // switch edit model in profile page
    $("#change-button").on("click", function(){
    	$(".edit-hide").removeClass("hide");
    	$(".edit-show").addClass("hide");
    })

    // request capcha in register page
    $("#refresh-capcha").on("click", function(){
        $("#capcha-image").attr("src", $("#capcha-image").attr("src"));
    })

    // send new request friend
    $(".add-button").on("click", function(){
        var add_btn = $(this);
        var value = add_btn.attr("id-value");

        $.ajax({
            url: "/lesson/friend/add",
            type: "POST",
            data: {
                user_id_to: value,
            },
            success: function(result){

                if (result.error === false) {
                    add_btn.attr("disabled",true);
                } else {
                    notification(result.message, "error");
                }

            }
        })
    })

    // accept friend request in friend request page
    $(".accept-button").on("click", function(){
        var accept_btn = $(this);
        var value = $(this).attr("id-value");

        $.ajax({
            url: "/lesson/friend/handle",
            type: "POST",
            data: {
                id: value,
                type: 1
            },
            success: function(result){

                if (result.error === false) {
                    accept_btn.parent().parent().hide();
                } else {
                    notification(result.message, "error");
                }

            }
        })
    })

    // delete friend request in friend request page
    $(".delete-button").on('click', function(){
        var delete_btn = $(this);
        var value = delete_btn.attr("id-value");
        $.ajax({
            url: "/lesson/friend/handle",
            type: "POST",
            data: {
                id: value,
                type: 2
            },
            success: function(result){
                if (result.error === false) {
                    delete_btn.parent().parent().hide();
                } else {
                    notification(result.message, "error");
                }
            }
        })
    })

    var chat_box = $(".chat-box");
    var chat_form = $("#message-form");
    var chat_status = false;
    // auto scroll to bottom chatbox
    if (chat_box.length > 0 ) {
        chat_box.scrollTop(chat_box[0].scrollHeight);
    }


    // handle when submit chat form
    $("#message-form").on("submit", function(e){
        chat_status = true;
        e.preventDefault();
        var message = chat_form.find("[name=message-content]").val();
        var user_id_to = chat_form.find("[name=user-id-to]").val();
        var current_message = chat_form.find("[name=current-message]").val();

        if (message != "") {
            chat_form.find("[type=submit]").html("Sending...");
            chat_form.find("[type=submit]").prop("disabled", true);

            $.ajax({
                url: "/lesson/message/create",
                type: "POST",
                data: {
                    user_id_to: user_id_to,
                    message: message,
                    current_message: current_message
                },
                success: function(result){
                    
                    if (result.error === false) {

                        if (result.data) {
                            var current_user_id = chat_box.find("p").last().attr("user-id-value");
                            var mgs_class = "";
                            var author_name = "";

                            for (var i = 0; i < result.data.length; i++) {
                                current_user_id = chat_box.find("p").last().attr("user-id-value");

                                if (current_user_id == result.data[i].user_id) {
                                    mgs_class = "next-message";
                                    author_name = "";
                                } else {
                                    mgs_class = "";
                                    author_name = result.data[i].fullname + " : ";
                                }

                                chat_box.append("<p class='" + mgs_class + "' user-id-value='" + result.data[i].user_id + "'>" + author_name + result.data[i].message + "</p>");
                            }
                            chat_form.find("[name=current-message]").val(result.data[(result.data.length-1)].id);
                            chat_status = false;
                            chat_box.scrollTop(chat_box[0].scrollHeight);
                        }
                        chat_form.find("[name=message-content]").val("");
                        chat_form.find("[type=submit]").html("Send");
                        chat_form.find("[type=submit]").prop("disabled", false);
                    } else {
                        notification(result.message, "error");
                        setTimeout(function(){ location.reload(); }, 2000);
                    }
                }
            })
        } else {
            chat_form.find("[name=message-content]").focus();
        }
    });

    // handle when change group in management users page
    $(".change-group").change(function(){
        var change = $(this);
        var group_id = change.val();
        var user_id = change.attr("id-value");

        $.ajax({
            url: "/lesson/user/changegroup",
            type: "POST",
            data: {
                user_id: user_id,
                group_id: group_id
            },
            success: function(result){

                if (result.error === false) {
                    notification("Change group success", "success");
                } else {
                    notification(result.message, "error");
                }

            }
        })
    });

    // handle when press button delete in management users page
    $(".delete-user").on("click", function(){
        var delete_btn = $(this);
        var r = confirm("Are you sure?");

        if (r == true) {
           var user_id = delete_btn.attr("id-value");

            $.ajax({
                url: "/lesson/user/delete/" + user_id,
                type: "DELETE",
                success: function(result){

                    if (result.error === false) {
                       delete_btn.parent().parent().hide();
                       notification("Delete user success", "success");
                    } else {
                        notification(result.message, "error");
                    }

                }
            })
        } 
    });

    // view message in message page
    $(".view-message").on("click", function(){
        var view_btn = $(this);
        var message_modal = $("#message-modal");
        var id = view_btn.attr('id-value');

        message_modal.find(".modal-title").html($(this).attr("name-value"));
        message_modal.find(".modal-body p").html(escape_html(view_btn.attr('content-value')));
        message_modal.modal("show");
    });


    //set interval load message in chat box default 5 seconds
    var load_message = setInterval(function(){
        if (chat_box.length == 0 ){
            clearInterval(load_message);
        } else {
            var user_id_to = chat_form.find("[name=user-id-to]").val();
            var current_message = chat_form.find("[name=current-message]").val();

            if (chat_status == false) {
                $.ajax({
                    url: "/lesson/message/load",
                    type: "POST",
                    data: {
                        user_id: USER_ID,
                        user_id_to: user_id_to,
                        current_message: current_message
                    },
                    success: function(result){

                        if (result.error === false) {

                            if (result.data) {
                                var current_user_id = chat_box.find("p").last().attr("user-id-value");
                                var mgs_class = "";
                                var author_name = "";

                                for (var i = 0; i < result.data.length; i++) {
                                    current_user_id = chat_box.find("p").last().attr("user-id-value");

                                    if (current_user_id == result.data[i].user_id) {
                                        mgs_class = "next-message";
                                        author_name = "";
                                    } else {
                                        mgs_class = "";
                                        author_name = result.data[i].fullname + " : ";
                                    }

                                    chat_box.append("<p class='" + mgs_class + "' user-id-value='" + result.data[i].user_id + "'>" + author_name + result.data[i].message + "</p>");
                                }

                                chat_form.find("[name=current-message]").val(result.data[(result.data.length-1)].id);
                                chat_box.scrollTop(chat_box[0].scrollHeight);
                            }

                        } else {
                            clearInterval(load_message);
                        }
                    }
                })
            }
        }   
    }, 5000);

    // show conversation in user page
    $(".show-conversation").on("click", function(){
        var conversation_btn = $(this);
        var conversation_modal = $("#conversation-modal");
        var user_id = conversation_btn.attr("id-value");
        var user_id_to = chat_form.find("[name=user-id-to]").val();
        var fullname = conversation_btn.attr("fullname-value");

        $.ajax({
            url: "/lesson/message/load",
            type: "POST",
            data: {
                user_id: user_id,
                user_id_to: user_id_to,
                current_message: 0
            },
            success: function(result){

                if (result.error === false) {
                    var html = "";

                    if (result.data) { 
                        for (var i = 0; i < result.data.length; i++) {
                            html += "<p>" + result.data[i].fullname + " : " + result.data[i].message + "</p>";
                        }
                    }

                    conversation_modal.find(".modal-body").html(html);
                    conversation_modal.find(".modal-title").html("Conversation with " + fullname);
                    conversation_modal.modal("show");
                }
                else {
                    notification(result.message, "error");
                    setTimeout(function(){ location.reload(); }, 2000);

                }
            }
        })
    });

    var edit_modal = $("#edit-profile-modal");
    // handle when press button edit user
    $(".edit-user").on("click", function(){
        $("#show-message").html("");
       
        var user_id = $(this).attr("id-value");
         
        $.ajax({
            url: '/lesson/user/info/' + user_id,
            type: 'GET',
            success: function(result){

                if (result.error === false) {
                    var birthday = new Date(result.data.birthday);

                    edit_modal.find("[name=user-id]").val(user_id);
                    edit_modal.find("[name=fullname]").val(result.data.fullname);
                    edit_modal.find("[name=sex][value=" + result.data.sex + "]").prop("checked", true);
                    edit_modal.find("[name=form-year]").val(birthday.getFullYear());
                    edit_modal.find("[name=form-month]").val(birthday.getMonth()+1);
                    edit_modal.find("[name=form-date]").val(birthday.getDate());
                    edit_modal.find("[name=address]").val($("<div/>").html(result.data.address).text());
                    edit_modal.modal("show");
                } else {
                    notification(result.message, "error");
                }
            }
        })
    });

    //handle when press button save in edit profile modal
    $('#edit-profile-modal form').on('submit', function(e){
        e.preventDefault();
        var edit_form = $(this);
        $("#show-message").html("");
        var c_month = input_month.val();
        var c_date = input_date.val();
        var c_year = input_year.val();
        var fullname = edit_form.find("[name=fullname]").val();
        var sex = edit_form.find("[name=sex]:checked").val();
        var birthday = c_year + "-" + ((c_month<10) ? ("0" + c_month) : c_month) + "-" + ((c_date<10) ? ("0" + c_date) : c_date);
        var address = edit_form.find("[name=address]").val();
        var user_id = edit_form.find("[name=user-id]").val();
        
        $.ajax({
            url: "/lesson/user/update",
            type: "POST",
            data: {
                user_id: user_id,
                fullname: fullname,
                sex: sex,
                birthday: birthday,
                address: address
            },
            success: function(result){
                
                if (result.error === false) {
                    var manage_table = $("#management-table");

                    manage_table.find("tr[id-value="+user_id+"] td:nth(0)").html("<a href='/lesson/friend/view/" + user_id + "'>" + fullname + "</a>");
                    manage_table.find("tr[id-value="+user_id+"] td:nth(1)").html((sex == 1) ? "Male" : "Female");
                    manage_table.find("tr[id-value="+user_id+"] td:nth(2)").html(birthday);
                    manage_table.find("tr[id-value="+user_id+"] td:nth(3)").html(escape_html(address));
                    edit_modal.modal("hide");
                    notification("Update user success", "success");
                } else {
                    //$('#show-message').html('<p class="alert alert-warning">'+result.message+'</p>');
                    notification(result.message, 'error');
                }
                
            }
        })
    });

    var picture = $("#picture");
    // upload image
    $("#image-upload").on("change", function() {
        //console.log(this.files);
        picture.find(".progress").removeClass('hide');
        picture.find(".progress-bar").css('width', 0);
        picture.find(".progress-bar").html("");

        if (this.files[0] !== undefined) {
            var formData = new FormData($(".upload-block")[0]);

            $.ajax({
                url: '/lesson/image/upload',  //Server script to process data
                type: 'POST',
                xhr: function() {  // Custom XMLHttpRequest
                    var myXhr = $.ajaxSettings.xhr();
                    
                    if (myXhr.upload) { // Check if upload property exists
                        myXhr.upload.addEventListener("progress",progressHandlingFunction, false); // For handling the progress of the upload
                    }

                    return myXhr;
                },
                beforeSend: beforeSendHandler,
                success: completeHandler,
                error: errorHandler,
                data: formData,
                cache: false,
                contentType: false,
                processData: false
            });
        }
    });

    function progressHandlingFunction(e){
        if (e.lengthComputable) {
            picture.find(".progress-bar").css("width", Math.floor(100*(e.loaded/e.total)) + "%");
            picture.find(".progress-bar").html(Math.floor(100*(e.loaded/e.total)) + "%");
        }
    }

    function beforeSendHandler(e){
        console.log(e);
    }

    function completeHandler(e){
        if (e.error == false) {
            for (var i=0; i < e.msg_success.length; i++) {
                notification(e.msg_success[i], 'success');
            }

            for (var i=0; i < e.msg_error.length; i++) {
                notification(e.msg_error[i], 'error');
            }

            for (var i=0; i < e.images_data.length; i++) {
                var html = '<div class="m-b-10 col-md-3">'
                            +'<div class="picture-block picture-block-owner">'
                            +'<div class="picture-thumbnail fancybox" data-fancybox-group="galery1" id-value="' + e.images_data[i].id + '" source-image="/lesson/' + e.images_data[i].path + '"><img src="/lesson/' + e.images_data[i].thumbnail + '"></div>'
                            +'<ul class="list-group text-center">'
                            +'<li class="list-group-item" ><a class="delete-image-btn" id-value="' + e.images_data[i].id + '">Delete</a></li>'
                            +'<li class="list-group-item" ><a class="view-btn fancybox" data-fancybox-group="galery2" id-value="' + e.images_data[i].id + '">View <span>(0)</span></a></li>'
                            +'<li class="list-group-item" ><a class="like-btn ready-btn" id-value="' + e.images_data[i].id + '">Like <span>(0)</span></a><a class="unlike-btn ready-btn hide" id-value="' + e.images_data[i].id + '">Unlike <span>(0)</span></a></li>'
                            +'</ul>'
                            +'</div>'
                            +'</div>';
                $('#picture>.row>.col-md-3:nth-child(1)').after(html);
            }
        } else {
            notification(e.message, 'error');
        }
    }

    function errorHandler(e){
        console.log(e);
    }

    // open dynamic edit
    $(".open-edit").on("click", function(){
        $(this).parent().addClass('hide');
        var name_part = $(this).parent().attr('name-part');
        var form_select = $('.info-input[name-part='+name_part+']');

        if (name_part == 'birthday') {
            var current_birthday = $("#birthday input[name=birthday]").val();
            var birthday = new Date(current_birthday);
            input_year.val(birthday.getFullYear());
            input_month.val(birthday.getMonth()+1);
            input_date.val(birthday.getDate());
        }

        form_select.removeClass("hide");
        form_select.removeClass("has-error");
    })

    // submit dynamic edit
    $(".dynamic-edit-submit").on("click", function(e){
        e.preventDefault();
        var submit_button = $(this);
        var name_part = submit_button.parent().attr("name-part");
        var content = "";
        var flag = false;

        switch (name_part) {

            case "introduction":
                content = $("form[name-part=introduction] textarea").val();
                flag = true;
                break;

            case "fullname":
                content = $("[name-part=fullname] input").val();
                flag = true;
                break;

            case 'birthday':
                var c_month = input_month.val();
                var c_date = input_date.val();
                var c_year = input_year.val();
                content = c_year + '-' + ((c_month<10) ? ('0' + c_month) : c_month) + '-' + ((c_date<10) ? ('0'+c_date) : c_date);
                flag = true;
                break;

            case "sex":
                content = $("form[name-part=sex] [name=sex]:checked").val();
                flag = true;
                break;

            case "address":
                content = $("form[name-part=address] input").val();
                flag = true;
                break;

            case "email":
                content = $("form[name-part=email] input").val();
                flag = true;
                break;
            
            default: 
                break;
        }

        if (flag) {

            $.ajax({
                url: "/lesson/user/dynamicupdate",
                type: "POST",
                data: {
                    type: name_part,
                    content: content
                },
                success: function(result){

                    if (result.error === false) {
                        switch (name_part) {
                             case "introduction":
                                $('.info-output[name-part=introduction] span').html(result.data);
                                $("form[name-part=introduction] textarea").val($("<div/>").html(result.data).text());
                                break;

                            case "fullname":
                                $('.info-output[name-part=fullname] span').html(result.data);
                                $("form[name-part=fullname] input").val(escape_html(result.data));
                                break;

                            case 'birthday':
                                // input_date.val();
                                // input_month.val();
                                // input_year.val(result.data.split("-")[0]);
                                $("#birthday input[name=birthday]").val(result.data);
                                $('.info-output[name-part=birthday] span').html(result.data);
                                break;

                            case "sex":
                                $('.info-output[name-part=sex] span').html(result.data == 1 ? 'Male' : 'Female');
                                break;

                            case "address":
                                $('.info-output[name-part=address] span').html(result.data);
                                $("form[name-part=address] input").val($("<div/>").html(result.data).text());
                                break;

                            case "email":
                                $('.info-output[name-part=email] span').html(result.data);
                                break;
                            
                            default: 
                                break;
                        }

                        $('.info-input[name-part='+name_part+']').addClass('hide');
                        $('.info-output[name-part='+name_part+']').removeClass('hide');
                        notification("Update success", "success");
                        //location.reload();
                    } else {
                        notification(result.message, "error");
                    }

                }
            })
        }
    });

    //---------------------------lession 3------------------------------
    var markers=[];
    var latLng;
    var location_tab = $("#location");
    var confirm_location = $("#confirm-location");

    //google map
    if ($("#map").length>0){
        var lng_input = location_tab.find("[name=lng]");
        var lat_input = location_tab.find("[name=lat]");
        var lng_val = lng_input.val();
        var lat_val = lat_input.val();
        var lng =  (lng_val != "") ? lng_val : '106.7470984';
        var lat =  (lat_val != "") ? lat_val : '10.7884951';
        var center = new google.maps.LatLng(lat, lng);
        var myOptions = {
            zoom: 14,
            center: center,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("map"), myOptions);
        addMarker(center);
        google.maps.event.trigger(map, "resize"); 
        $("a[href='#location']").on("shown.bs.tab", function(e) {
            var center = map.getCenter();
            google.maps.event.trigger(map, "resize");
            map.setCenter(center);
        });
        var geocoder = new google.maps.Geocoder();
        map.addListener("click", function(e) {
            lng_input.val(e.latLng.lng());
            lat_input.val(e.latLng.lat());
            $.ajax({
                url: "https://maps.googleapis.com/maps/api/geocode/json?latlng="+ e.latLng.lat() + "," + e.latLng.lng(),
                type: "GET",
                success: function(result){
                    console.log(result);
                    if (result.results.length > 0) {
                        confirm_location.find("span.new-address").html(result.results[0]["formatted_address"]);
                    } else {
                        confirm_location.find("span.new-address").html("lat: " + e.latLng.lat() + ", long: " + e.latLng.lng());
                    }
                    
                    confirm_location.modal("show");
                    latLng=e.latLng;
                }
            })
            
        });
    }

    function addMarker(location) {
        var marker = new google.maps.Marker({
          position: location,
          map: map
        });
        markers.push(marker);
    }

    function setMapOnAll(map) {
        for (var i = 0; i < markers.length; i++) {
          markers[i].setMap(map);
        }
    }


    // submit change location
    $("#confirm-location .submit-location").on("click",function(){
        var lat = location_tab.find("[name=lat]").val();
        var lng = location_tab.find("[name=lng]").val();
        var user_id = confirm_location.find("[name=user-id]").val();

        $.ajax({
            url: "/lesson/user/update",
            type: "POST",
            data: {
                user_id: user_id,
                lng: lng,
                lat: lat
            },
            success: function(result){

                if (result.error === false){
                    setMapOnAll(null);
                    addMarker(latLng,map);
                    confirm_location.modal("hide");
                    notification("Change location success", "success");
                } else {
                    notification("Change location error", "error");
                }

            }
        })
    });

    // unfriend
    $(".unfriend-btn").on("click", function(){
        var unfriend_btn = $(this);
        var user_id = unfriend_btn.attr("id-value");
        var addfriend_btn = $(".addfriend-btn[id-value="+user_id+"]");

        $.ajax({
            url: "/lesson/friend/remove",
            type: "POST",
            data: {
                user_id: user_id,
            },
            success: function(result){

                if (result.error === false) {
                    unfriend_btn.addClass("hide");
                    addfriend_btn.removeClass("hide");
                } else {
                    notification(result.message, "error");
                }

            }
        })
    });

    // send new request friend
    $(".addfriend-btn").on("click", function(){
        var addfriend_btn = $(this);
        var user_id = addfriend_btn.attr('id-value');
        var request_btn = $(".request-status[id-value="+user_id+"]");

        $.ajax({
            url: "/lesson/friend/add",
            type: "POST",
            data: {
                user_id_to: user_id,
            },
            success: function(result){

                if (result.error === false) {
                   addfriend_btn.addClass("hide");
                   request_btn.removeClass("hide");
                } else {
                    notification(result.message, "error");
                }

            }
        })
    });

    // delete image
    $("#picture").delegate(".delete-image-btn", "click", function(){
        var del_btn = $(this);
        var image_id = del_btn.attr('id-value');
        var confirm_image = $("#confirm-image");
        confirm_image.find("form input[name=image-id]").val(image_id);
        confirm_image.modal("show");
    });

    // submit delete image
     $("#confirm-image .submit-image").on("click", function(e){
        e.preventDefault();
        var confirm_image = $("#confirm-image");
        var image_id = confirm_image.find("form input[name=image-id]").val();
        if (image_id > 0) {
             $.ajax({
                url: "/lesson/image/delete",
                type: "POST",
                data: {
                    image_id: image_id
                },
                success: function(result){

                    if (result.error === false) {
                        var image_thumbnail = $(".picture-block .picture-thumbnail[id-value=" + image_id + "]");
                        image_thumbnail.parent().parent().hide();
                        $("#image-album .picture-thumbnail[id-value=" +image_id+ "]").parent().hide();
                        notification("Delete image success", "success");
                    } else {
                        notification(result.message, "error");
                    }
                    confirm_image.modal("hide");
                }
            })
        } else {
            notification("Not exist image id", "error");
        }
     })

    $("#picture").delegate(".like-btn.ready-btn","click", function(){
        console.log($(this));
        var like_btn = $(this);
        like_btn.removeClass("ready-btn");
        var image_id = like_btn.attr('id-value');
        var unlike_btn =  $(".unlike-btn[id-value="+image_id+"]");
        console.log(image_id);

        $.ajax({
            url: "/lesson/image/like",
            type: "POST",
            data: {
                image_id: image_id
            },
            success: function(result){

                if (result.error === false) {
                    unlike_btn.find("span").html("("+result.like+")");
                    like_btn.addClass("ready-btn");
                    like_btn.addClass("hide");
                    unlike_btn.removeClass("hide");
                }

            }
        })
    });

    // unlike image 
    $("#picture").delegate(".unlike-btn.ready-btn","click", function(){
        var unlike_btn = $(this);
        $(this).removeClass("ready-btn");
        var image_id = unlike_btn.attr('id-value');
        var like_btn =  $(".like-btn[id-value="+image_id+"]");
       

        $.ajax({
            url: "/lesson/image/unlike",
            type: "POST",
            data: {
                image_id: image_id
            },
            success: function(result){

                if (result.error === false) {
                    like_btn.find("span").html("("+result.like+")");
                    unlike_btn.addClass("ready-btn");
                    unlike_btn.addClass("hide");
                    like_btn.removeClass("hide");
                } else {
                    notification(result.message, "error");
                }

            }
        })
    });

    // view image
    // $("#picture").delegate(".view-btn", "click", function(){
    //     var view_btn = $(this);
    //     var image_id = view_btn.attr('id-value');
    //     var href = $(".picture-thumbnail[id-value="+image_id+"]").attr("source-image");
        
    //     $.ajax({
    //         url: "/lesson/image/view",
    //         type: "POST",
    //         data: {
    //             image_id: image_id
    //         },
    //         success: function(result){

    //             if (result.error === false) {
    //                view_btn.find("span").html("("+result.view+")");
    //             }

    //         }
    //     })
    //     $.fancybox([{href:href}]);
    // });

    // $("#picture").delegate(".picture-block .picture-thumbnail", "click", function(){
    //     var picture = $(this);
    //     var image_id = picture.attr('id-value');
    //     var view_btn = $(".view-btn[id-value=" + image_id + "]");
    //     var href =picture.attr("source-image");
        
    //     $.ajax({
    //         url: "/lesson/image/view",
    //         type: "POST",
    //         data: {
    //             image_id: image_id
    //         },
    //         success: function(result){

    //             if (result.error === false) {
    //                view_btn.find("span").html("("+result.view+")");
    //             }

    //         }
    //     })
    //     $.fancybox([{href:href}]);
    // });

    // use fancybox to show album image
    $(".fancybox").fancybox({
        autoScale: true,
        autoSize    : true,
        type: 'image',
        padding: 0,
        closeClick: false,
        // other options
        beforeLoad: function () {
            var image_id = $(this.element).attr("id-value");
            var view_btn = $(".view-btn[id-value=" + image_id + "]");
            var url = $(".picture-thumbnail[id-value="+image_id+"]").attr("source-image");
            $.ajax({
                url: "/lesson/image/view",
                type: "POST",
                data: {
                    image_id: image_id
                },
                success: function(result){
                    if (result.error === false) {
                       view_btn.find("span").html("("+result.view+")");
                    }

                }
            });
            this.href = url;
        }
    }); 

    // favorite
    $(".addfavorite-btn").on("click", function(){
        var addfav_btn = $(this);
        var user_id = addfav_btn.attr("id-value");
        var unfav_btn = $('.unfavorite-btn[id-value='+user_id+']');

        $.ajax({
            url: "/lesson/favorite/add",
            type: "POST",
            data: {
                user_id: user_id,
            },
            success: function(result){

                if (result.error === false) {
                    unfav_btn.removeClass("hide");
                    addfav_btn.addClass("hide");
                } else {
                    notification(result.message, 'error');
                }

            }
        })
    });

    // unfavorite
    $(".unfavorite-btn").on("click", function(){
        var unfav_btn = $(this);
        var user_id = unfav_btn.attr("id-value");
        var addfav_btn = $('.addfavorite-btn[id-value='+user_id+']');

        $.ajax({
            url: "/lesson/favorite/remove",
            type: "POST",
            data: {
                user_id: user_id,
            },
            success : function(result){

                if (result.error === false) {
                   addfav_btn.removeClass("hide");
                   unfav_btn.addClass("hide");
                } else {
                    notification(result.message, "error");
                }

            }
        })
    });

    // follow
    $(".p-addfollow-btn").on("click", function(){
        var user_id = $(this).attr("id-value");

        $.ajax({
            url: "/lesson/follow/add",
            type: "POST",
            data: {
                user_id: user_id,
            },
            success: function(result){

                if (result.error === false) {
                   location.reload();
                } else {
                    notification(result.message, "error");
                }

            }
        })
    });

    // unfollow
    $(".p-unfollow-btn").on("click", function(){
        var user_id = $(this).attr("id-value");

        $.ajax({
            url: "/lesson/follow/remove",
            type: "POST",
            data: {
                user_id: user_id,
            },
            success: function(result){

                if (result.error === false) {
                   location.reload();
                } else {
                    notification(result.message, "error");
                }

            }
        })
    });
    
    // add favorite
    $(".p-addfavorite-btn").on("click", function(){
        var user_id = $(this).attr("id-value");
        $.ajax({
            url: "/lesson/favorite/add",
            type: "POST",
            data: {
                user_id: user_id,
            },
            success : function(result){

                if (result.error === false) {
                   location.reload();
                } else {
                    notification(result.message, "error");
                }

            }
        })
    });

    // unfavorite
    $(".p-unfavorite-btn").on("click", function(){
        var user_id = $(this).attr('id-value');

        $.ajax({
            url: "/lesson/favorite/remove",
            type: "POST",
            data: {
                user_id: user_id,
            },
            success: function(result){

                if (result.error === false) {
                   location.reload();
                } else {
                    notification(result.message, "error");
                }

            }
        })
    });

    $(".p-addfriend-btn").on("click", function(){
        var user_id = $(this).attr("id-value");

        $.ajax({
            url: "/lesson/friend/add",
            type: "POST",
            data: {
                user_id_to: user_id,
            },
            success: function(result){

                if (result.error === false) {
                   location.reload();
                } else {
                    notification(result.message, "error");
                }

            }
        })
    });

    // unfollow
    $(".p-unfriend-btn").on("click", function(){
        var user_id = $(this).attr("id-value");
        $.ajax({
            url: "/lesson/friend/remove",
            type: "POST",
            data: {
                user_id_to: user_id,
            },
            success : function(result){

                if (result.error === false) {
                   location.reload();
                } else {
                    notification(result.message, "error");
                }

            }
        })
    });

    // open change avatar 
    $("div.profile-avatar a").on("click",function(){
        $("#image-album").modal("show");
    });

    // open image album
    $("#image-album .picture-thumbnail").on("click", function(){
        var image_album =  $("#image-album");
        var current_image =  $(this);

        image_album.find(".picture-thumbnail").removeClass("picture-selected");
        $(this).addClass("picture-selected");
        image_album.find("input[name=image-value]").val(current_image.attr("id-value"));
    });

    // submit image
    $("#image-album .submit-avatar").on("click", function(){
        var image_album =  $("#image-album");
        var image_id = image_album.find("input[name=image-value]").val();
        var profile_avatar = $(".profile-avatar");
        if (image_id == 0) {
            notification("Please select image", "error");
        } else {
            $.ajax({
                url: "/lesson/user/dynamicupdate",
                type: "POST",
                data: {
                    type: "avatar",
                    content: image_id
                },
                success: function(result){

                    if (result.error === false) {
                        profile_avatar.find('img').attr("src", "/lesson/" + result.value);
                        notification("Update avatar success", "success");
                        image_album.modal("hide");
                    } else {
                        notification(result.message, "error");
                    }

                }
            })
        }
    });
});