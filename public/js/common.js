function numericFilter(txb) {
    txb.value = txb.value.replace(/[^0-9]/ig, "");
}

function wowMsg(str, subjtitle) {
    if (typeof (subjtitle) == "undefined")
        subjtitle = "Message Box";
    var unique_id = $.gritter.add({
        // (string | mandatory) the heading of the notification
        title: subjtitle,
        // (string | mandatory) the text inside the notification
        text: str,
        // (string | optional) the image to display on the left
        // image: './assets/img/avatar1.jpg',
        // (bool | optional) if you want it to fade out on its own or just sit there
        sticky: true,
        // (int | optional) the time you want it to be alive for before fading out
        time: '',
        // (string | optional) the class name you want to apply to that specific message
        class_name: 'my-sticky-class'
    });

    // You can have it return a unique id, this can be used to manually remove it later using
    setTimeout(function () {
        $.gritter.remove(unique_id, {
            fade: true,
            speed: 'slow'
        });
    }, 8000);
}

//  $("#manuModal").on("hide.bs.modal", function(event) {
//	$('#manuModal .modal-body').html('Loading....');
//        $('#manuModal h4.modal-title').html('Modal title');
//        $('#manuModal .modal-body').html('');
//        if($('#manuModal .modal-footer').length == 0){
//        $('#manuModal .modal-body').after('<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><button type="button" class="btn btn-primary">Save changes</button></div>');
//    }
//});

$('.switch-status.change-request').on('switchChange.bootstrapSwitch', function (e, state) {
    var _this = $(this);
    var _id = _this.data("id");
    var url = _this.data("url");
    var field = _this.data("field");
    var table = _this.closest("section.content").data("table");
    if (e.target.checked == true) {
        var changedval = 1;
    } else {
        var changedval = 0;
    }
    var defaultMsg = 'Do you really want to process this action !';
    $.confirm({
        title: 'Alert',
        content: defaultMsg,
        icon: 'fa fa-exclamation-circle',
        animation: 'scale',
        closeAnimation: 'scale',
        opacity: 0.5,
        theme: 'supervan',
        buttons: {
            'confirm': {
                text: 'Yes',
                btnClass: 'btn-blue',
                action: function () {
                    if (url == undefined) {
                        url = baseurl + 'admin/' + table + '/change-flag/';
                    }
                    if (field == undefined) {
                        field = 'status';
                    }
                    $.ajax({
                        url: url,
                        type: 'post',
                        data: { id: _id, field: field, status: changedval },
                        dataType: 'json',
                        headers: {
                            "accept": "application/json",
                        },
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', CLIENT_TOKEN);
                        },
                        complete: function () { },
                        success: function (record) {
                            wowMsg(record.message);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                }
            },
            cancelAction: {
                text: 'Cancel',
                action: function () {
                    if (changedval == 1) {
                        _this.bootstrapSwitch('state', false, 'skip');
                    } else {
                        _this.bootstrapSwitch('state', true, 'skip');
                    }
                }
            }
        }
    });

});

$(document).on('click', '.confirmDeleteBtn', function () {
    var _this = $(this);
    var _id = _this.data("id");
    var url = _this.data("url");
    var message = _this.data("message");
    var title = _this.data("title");
    var action = _this.data("action");

    if (message == 'undefined') {
        message = 'Are you sure want to delete this record?';
    } else {
        message = message;
    }
    if (action == 'undefined') {
        actionB = 'DELETE';
    } else {
        actionB = action;
    }

    $.confirm({
        title: 'Alert',
        content: message,
        icon: 'fa fa-exclamation-circle',
        animation: 'scale',
        closeAnimation: 'scale',
        opacity: 0.5,
        theme: 'supervan',
        buttons: {
            'confirm': {
                text: 'Yes',
                btnClass: 'btn-blue',
                action: function () {
                    $.ajax({
                        url: url,
                        type: actionB,
                        dataType: 'json',
                        headers: {
                            "accept": "application/json",
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function (xhr) {
                            $('.loader').removeClass('hide');
                        },
                        success: function (record) {
                            $('.loader').addClass('hide');
                            if (record.status == true) {
                                $.alert({
                                    columnClass: 'medium',
                                    title: 'Success',
                                    icon: 'fa fa-check',
                                    type: 'green',
                                    content: record.message,
                                    buttons: {
                                        Ok: function () {
                                            //location.reload();
                                            if (_this.hasClass("noreload")) {
                                                $(".table-row-" + record.data.id).remove();
                                            } else {
                                                location.reload();
                                            }
                                        }
                                    }
                                });
                                if (_this.hasClass("reload")) {
                                    location.reload();
                                } else {
                                    $(".row-" + record.data.id).remove();
                                }
                            } else {
                                $.alert({
                                    columnClass: 'medium',
                                    title: 'Error',
                                    icon: 'fa fa-warning',
                                    type: 'red',
                                    content: record.message,
                                });
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                }
            },
            cancelAction: {
                text: 'Cancel',
            }
        }
    });

});

function confirmDelete(elem, title) {
    var title = title;
    var message = $(elem).data("message");
    if (message != undefined) {
        title = message;
    } else {
        title = 'Are you sure want to delete ' + title + '?';
    }
    $.confirm({
        title: 'Alert',
        content: title,
        icon: 'fa fa-exclamation-circle',
        animation: 'scale',
        closeAnimation: 'scale',
        opacity: 0.5,
        theme: 'supervan',
        buttons: {
            'confirm': {
                text: 'Yes',
                btnClass: 'btn-blue',
                action: function () {
                    var action = $(elem).prev('form').attr('action');
                    if (action.indexOf('delete') > -1) {
                        $(elem).prev('form').submit();
                    }
                }
            },
            cancelAction: {
                text: 'Cancel'
            }
        }
    });
}

$(".recrodData").click(function (event) {
    event.preventDefault();
    var urlData = jQuery(this).attr("data-url");
    var self = this;
    $.ajax({
        url: urlData,
        dataType: "html",
        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (record) {
            location.href = jQuery(self).attr("href");
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });

});

$("#localityStateId").on('change', function (event) {
    event.preventDefault();
    var self = this;
    var stateId = jQuery(self).val();
    var cityStateUrl = jQuery('#cityStateUrl').val();
    var urlData = cityStateUrl + "/" + stateId;
    $.ajax({
        url: urlData,
        dataType: "html",
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (record) {
            if (record != 'NO') {
                jQuery('#localityCityId').html(record);
            } else {
                alert('Cities not available.');
            }
            // location.href = jQuery(self).attr("href");
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });

});

function confirmRemove(elem, title) {
    $.confirm({
        title: 'Alert',
        content: 'Are you sure want to delete ' + title + '?',
        icon: 'fa fa-exclamation-circle',
        animation: 'scale',
        closeAnimation: 'scale',
        opacity: 0.5,
        theme: 'supervan',
        buttons: {
            'confirm': {
                text: 'Yes',
                btnClass: 'btn-blue',
                action: function () {
                    if (elem.length > 0) {
                        elem.remove();
                    }
                }
            },
            cancelAction: {
                text: 'Cancel'
            }
        }
    });
}


$(document).on('click', '.confirmDeleteAjax', function (event) {
    event.preventDefault();
    var _this = $(this);
    var title = _this.data('title');
    $.confirm({
        title: 'Alert',
        content: 'Are you sure want to delete ' + title + '?',
        icon: 'fa fa-exclamation-circle',
        animation: 'scale',
        closeAnimation: 'scale',
        opacity: 0.5,
        theme: 'supervan',
        buttons: {
            'confirm': {
                text: 'Yes',
                btnClass: 'btn-blue',
                action: function () {
                    $.ajax({
                        url: _this.attr('href'),
                        type: 'DELETE',
                        dataType: "json",
                        headers: {
                            "accept": "application/json",
                        },
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', CLIENT_TOKEN);
                        },
                        success: function (response) {
                            if (response.status == true) {
                                _this.closest('.deletedRow').remove();
                            }
                            wowMsg(response.message);
                        },
                        complete: function () { },
                    });
                }
            },
            cancelAction: {
                text: 'Cancel'
            }
        }
    });
});

$("#quickStartForm").submit(function (event) {
    event.preventDefault();
    l = Ladda.create(document.querySelector('.l-button'));
    l.start();
    $(".print-error-msg").find("ul").html('');
    $(".print-error-msg").css('display', 'none');
    var form = $(this);
    $.ajax({
        url: form.attr("action"),
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function (responce) {
            if (responce.status === true) {
                form[0].reset();
                $.alert({
                    title: 'Success!',
                    icon: 'fa fa-info',
                    content: responce.errors,
                    type: 'green',
                    theme: 'light',
                    buttons: {
                        Okay: function () {
                            window.location.reload(true);
                        }
                    }
                });
            } else {
                $(".print-error-msg").find("ul").html('');
                $(".print-error-msg").css('display', 'block');
                $(".print-error-msg").find("ul").append(printErrorMsg(responce.errors));
            }
            l.stop();

        },
        error: function (data) {
            var errors = data.responseJSON;
            //console.log(errors);
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display', 'block');
            $(".print-error-msg").find("ul").append(printErrorMsg(errors));
            l.stop();
        }
    });
});

function printErrorMsg(msg) {
    var html = '';
    $.each(msg, function (key, value) {
        if (typeof value == 'object') {
            $.each(value, function (key2, value2) {
                html += '<li>' + value2 + '</li>';
            });
        } else {
            html += '<li>' + value + '</li>';
        }
    });
    return html;
}
function ajaxerror(errors) {
    var alertErrs = '';
    console.log(errors);
    if (errors != undefined) {
        $.each(errors, function (index, value) {
            if (value[0] != undefined) {
                alertErrs += value[0];
            }
        });
    }
    return alertErrs;
}
function removeURLParameter(url, parameter) {
    //prefer to use l.search if you have a location/link object
    var urlparts = url.split('?');
    if (urlparts.length >= 2) {

        var prefix = encodeURIComponent(parameter) + '=';
        var pars = urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i = pars.length; i-- > 0;) {
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                pars.splice(i, 1);
            }
        }

        return urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
    }
    return url;
}


function formErrorsflash(errors, formId) {
    var html = '';
    $.each(errors, function (key, value) {
        if (typeof value == 'object') {
            $.each(value, function (key2, value2) {
                var element = $("#" + formId + " input[name='" + key + "'], #" + formId + " textarea[name='" + key + "'], #" + formId + " select[name='" + key + "']");
                if (element.length == 0) {
                    var id = key.replace('.', '_').replace('.', '_');
                    element = $("#" + id);
                }
                if (element.length == 0) {
                    element = $("#" + formId + " input[name^='" + key + "'], #" + formId + " select[name^='" + key + "']");
                }
                console.log(element);
                if (element.attr('type') == "radio") {
                    element.closest("div").addClass('is-invalid');
                } else if (element.attr('type') == "checkbox") {
                    element.closest("div.checkbox-row").addClass('is-invalid');
                } else {
                    element.addClass('is-invalid');
                }
                var erText = value2.replace('.', ' ');
                element.closest(".form-group").find("span.invalid-feedback").html(erText);
            });
        } else {
            var element = $("#" + formId + " input[name='" + key + "'], # " + formId + " textarea[name='" + key + "']");
            if (element.attr('type') == "radio") {
                element.closest("div").addClass('is-invalid');
            } else {
                element.addClass('is-invalid');
            }
            var erText = value.replace('.', '_');
            element.closest(".form-group").find("span.invalid-feedback").html(value);
        }
    });
}
$('#impRules li').on('click', function (e) {
    e.preventDefault();
    var _this = $(this);
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(_this.find('small').text().trim()).select();
    document.execCommand("copy");
    $temp.remove();
    _this.append("<div class='copiedText'>Copied</div>");
    setTimeout(() => _this.find(".copiedText").remove(), 1000);
})
function DDMMYYYY(value, event) {
    let newValue = value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');

    const dayOrMonth = (index) => index % 2 === 1 && index < 4;

    // on delete key.  
    if (!event.data) {
        return value;
    }
    return newValue.split('').map((v, i) => dayOrMonth(i) ? v + '/' : v).join('');;
}

