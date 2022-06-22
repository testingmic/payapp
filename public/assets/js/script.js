var currentRequest;
$.submitoption = "submit";
$.interval = 90000;

var Notify = (message, rcode) => {
    Swal.fire({
        'icon': rcode,
        'text': message
    });
}

var _reload = (url) => {
    window.location.href = url;
}

var responseCode = (code) => {
    return code == 200 ? "success" : "error";
}

var _html_entities = (str) => {
    return String(str).replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function format_currency(total) {
    var neg = false;
    if (total < 0) {
        neg = true;
        total = Math.abs(total);
    }
    return (neg ? "-" : '') + parseFloat(total, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
}

var _ajax_cronjob = async () => {
    await $.post(`${baseURL}api/auth/ajax_cronjob`).then((d) => {
        if(d.data.additional !== undefined) { setTimeout(() => { _reload(d.data.additional.href); }, 1000); }
    }).fail((error) => {});
}

var _quick_save = () => {
    $(`[data-item="quicksave"]`).on("change", function() {
        let item = $(this);
        let item_id = item.attr("data-item_id"),
            column = item.attr("data-column"),
            table = item.attr("data-table"),
            value = item.val(),
            refresh = item.attr("data-require_refresh");
        $.post(`${baseURL}api/controller/quicksave`, {item_id, column, table, value}).then((res) => {
            if(res.code == 200) {
                if(refresh !== undefined) {
                    window.location.href = currentURL;
                }
            }
        }).fail((res)=> {});
    });
}

var _delete = (resource, resource_id, message = "") => {
    message = message.length ? message : "Are you sure you want to delete this Record?";
    Swal.fire({
        title: "Remove Record",
        text: message,
        icon: "warning",
        showCancelButton: !0,
        confirmButtonText: "Proceed!",
        cancelButtonText: "Cancel",
        confirmButtonClass: "btn btn-success w-xs me-2 mt-2",
        cancelButtonClass: "btn btn-danger w-xs mt-2",
        buttonsStyling: !1,
        showCloseButton: !0
    }).then((proceed) => {
        if (proceed.value) {
            $.post(`${baseURL}api/controller/_delete`, {resource, resource_id}).then((response) => {
                Notify(response.data.result, responseCode(response.code));
                if(response.code == 200) {
                    $(`tr[data-row_id="${resource_id}"]`).remove();
                }
            }).fail((error) => {
                Notify('Sorry! An error occured while processing the request.');
            });
        }
    });
}

var _logout = async () => {
    await $.post(`${baseURL}api/auth/logout`).then((response) => {
        Notify(response.data.result, responseCode(response.code));
        if(response.code == 200) { setTimeout(() => { _reload(`${baseURL}auth/login?logout`); }, 1000); }
    }).fail((error) => {
        Notify('Sorry! An error was encountered while processing the request.');
    });
}

var _auth_form = () => {
    $(`form[id="authForm"]`).on("submit", async function(evt) {
        evt.preventDefault();
        let data = $(`form[id="authForm"]`).serialize(),
            request_url = $(`form[id="authForm"]`).attr("action");

        await $.post(request_url, data).then((response) => {
            Notify(response.data.result, responseCode(response.code));
            if(response.code == 200) { setTimeout(() => { _reload(response.data.additional.href); }, 1000); }
        }).fail((error) => {
            Notify('Sorry! An error was encountered while processing the request.');
        });
    });
}