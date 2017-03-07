(function($) { // TO-DO: // needs an internal events BUS and service DI
    var debug = false,
        verbose = false,
        dev = true;
        config = {},
        references = {},
        /*
        // service example
        "name1": {
            "action1": function() {
                debug && console.log("name1::action1 eecuting");
            },
            "action2": function(e) {
                // using config example
                // due to the way jQuery attaches events, "this" is overwritten and can not be used like in vanilla, but other services actions and config can be accessed
                debug && console.log("name1::action2 executing, with config: " + JSON.stringify(container["name1"]["config"]));
                e.preventDefault();
            },
            "config": {}
        }
        */
        container = {
            "uid": {
                "get": function(section) {
                    var id;

                    while(!id) {

                        id = (new Date()).getTime();

                        if (references.hasOwnProperty(section) && references[section].hasOwnProperty(id)) {

                            id = undefined;
                        }
                    }

                    return id;
                }
            },
            "interval": {
                "initInterval": function() {

                    $(container["interval"]["config"]["selectors"]["interval"]).each(function(i, e) {

                        $(e).datepicker("destroy");
                        $(e).datepicker(container["interval"]["config"]["datepicker"]);
                    });
                },
                "constrain": function(e) {
                    var conf, $this = $(e.target), $that, $thatMutator, $thatLimit, $dateOptions;

                    var limit = function(el, start, end) {
                        start = start || "";
                        end = end || "";

                        el.addClass("bubbling");
                        el.datepicker("setStartDate", start);
                        el.datepicker("setEndDate", end);
                        el.datepicker("update");
                        el.removeClass("bubbling");
                    }

                    if ($this.hasClass("bubbling")) {
                        // event bubbling can be avoided by attaching event listener to upper containers, such as $(document).on("event", "selector" + "selector not bubbling", action)
                        return;
                    }

                    $this.addClass("bubbling");

                    conf = container["interval"]["config"];
                    $that = $(conf["selectors"]["interval"]).not("[id=" + $this.attr("id") + "]").first();

                    if ($this.val()) {

                        $thatMutator = $this.attr("data-limiter");

                        $thatLimit = moment($this.val(), conf["dateformat"])[$thatMutator](conf["length"], conf["unit"]).format(conf["dateformat"]);

                        if ($thatMutator === "add") {
                            $dateOptions = {"startDate": $this.val(), "endDate": $thatLimit};
                        } else {
                            $dateOptions = {"startDate": $thatLimit, "endDate": $this.val()};
                        }

                    } else {
                        $dateOptions = {"startDate": undefined, "endDate": undefined};
                    }

                    limit($that, $dateOptions["startDate"], $dateOptions["endDate"]);

                    $this.removeClass("bubbling");
                },
                "config": {
                    "selectors": {
                        "interval": ".interval"
                    },
                    "datepicker": {
                        format: "mm/dd/yyyy", todayHighlight: true, todayBtn: true, clearBtn: true, autoclose : true
                    },
                    "dateformat": "MM/DD/YYYY"
                }
            },
            "form": {
                "initForm": function() {

                },
                "ajaxSubmit": function(e) {

                    e.preventDefault();

                    container["messages"]["clearElements"]();

                    if (!container["form"]["validateDeviceStats"]()) {

                        return false;
                    }

                    var $payload = $(this).serialize(),
                        $url = $(this).attr("action"),
                        $method = $(this).attr("method").toLowerCase(),
                        cb;

                    if (container["form"]["config"].hasOwnProperty("ajaxHandler")) {

                        cb = container["form"][container["form"]["config"]["ajaxHandler"]];
                    }

                    if (container["requests"].hasOwnProperty($method)) {

                        container["requests"][$method]($url, $payload, cb);
                    } else {

                        debug && console.log("container[form]::ajaxSubmit invalid request method: " + $method);
                    }

                },
                "callLineChart": function(d) {

                    if (typeof(d) === "object" && d.hasOwnProperty("status") && d.hasOwnProperty("data")) {

                        if (d["status"]) {

                            d = container["form"]["normalizeLineChartData"](d["data"]);

                            container["charts"]["drawDevinceLineChart"](d);
                        } else {

                            if (d["data"].hasOwnProperty("form")) {

                                container["messages"]["showElements"](d["data"]["form"]);
                            } else {
                                var msg = (typeof(d["data"]) === "object") ? JSON.stringify(d["data"]) : d["data"];

                                container["messages"]["showError"](msg);
                            }
                        }
                    } else {
                        container["messages"]["showError"](container["requests"]["config"]["errors"]["format"]);
                    }
                },
                "normalizeLineChartData": function(data) {
                    var template = $.extend(true, {}, container["charts"]["config"]["deviceLineChart"]["options"]),
                        normalizedData = {
                            "xAxis": template["xAxis"],
                            "series": template["series"]
                        };

                    for (key in data) {
                        normalizedData["xAxis"]["categories"].push(data[key][0]);
                        normalizedData["series"][0]["data"].push(data[key][1]);
                        normalizedData["series"][1]["data"].push(data[key][2]);
                    }

                    return normalizedData;
                },
                "validateDeviceStats": function() {
                    var valid = true,
                        validationErrors = {};

                    $("input.interval").each(function() {
                        if (!$(this).val()) {

                            $(this).datepicker("show");

                            validationErrors[$(this).attr("id")] = {"key": container["form"]["config"]["errors"]["date"]};
                            valid = false;
                        }
                    });

                    if (!$("#device").val()) {

                        validationErrors[$("#device").attr("id")] = {"key": container["form"]["config"]["errors"]["device"]};
                        valid = false;
                    }

                    if (!valid) {

                        container["messages"]["showElements"](validationErrors);
                    }

                    return valid;
                },
                "config": {
                    "errors": {
                        "date": "Please select a date",
                        "device": "Please select a device"
                    }
                }
            },
            "charts": {
                "initDeviceLineChart": function() {
                    var options = $.extend(true, {}, container["charts"]["config"]["deviceLineChart"]["options"]);

                    options["chart"]["renderTo"] = container["charts"]["config"]["deviceLineChart"]["selector"];

                    chart = new Highcharts.Chart(options);
                },
                "drawDevinceLineChart": function(dt) {
                    var chart = $("#" + container["charts"]["config"]["deviceLineChart"]["selector"]).highcharts();

                    chart.showLoading();
                    chart["xAxis"][0].setCategories(dt["xAxis"]["categories"]);

                    while (chart.series.length) {
                        chart["series"][0].remove();
                    }

                    for (key in dt["series"]) {
                        chart.addSeries(dt["series"][key]);
                    }

                    chart.redraw();
                    chart.hideLoading();
                },
                "config": {
                    "deviceLineChart": {
                        "options": {
                            "chart": {"type": "line", "renderTo": "line-chart" /* to change */ },
                            "title": {"text": "Device Stats" },
                            "xAxis": {"categories": []},
                            "yAxis": {"title": {"text": "Count"}},
                            "plotOptions": {"line": {"dataLabels": {"enabled": true}, "enableMouseTracking": false}},
                            "series": [{"name": "Errors", "color": "red", "data": []}, {"name": "Traffic", "color": "green", "data": []}]
                        },
                        "selector": "line-chart" // no # in this selector
                    }
                }
            },
            "requests": {
                "post": function(url, payload, cb, success, fail) {

                    success = success || container["requests"]["handleSuccess"];
                    fail = fail || container["requests"]["handleError"];

                    $
                    .post(url, payload, function(data) {
                        success(data, cb);
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        // errorThrown is moved on 1st position, to serve any error
                        fail(errorThrown, jqXHR, textStatus, url, "POST", payload);
                    });
                },
                "get": function() {
                    // TO-DO: implement it when required
                },
                "handleSuccess": function(data, cb) {
                    var obj;

                    if (typeof(cb) !== "function") {

                        return;
                    }

                    if (typeof(data) === "object" || !data) {

                        return cb(data);
                    }

                    try {
                        obj = JSON.parse(data);
                    } catch (err) {

                        container["requests"]["handleError"](container["requests"]["config"]["errors"]["parse"].replace("###error###", JSON.stringify(err)));
                    }

                    cb(obj);
                },
                "handleError": function(errorThrown, jqXHR, textStatus, url, method, payload) {

                    debug && console.log("container[requests]::handleError > " + errorThrown);
                    // TO-DO: maybe POST the error data to backend for logging, when required
                },
                "config": {
                    "errors": {
                        "parse": "An error occured while parsing data: ###error###",
                        "format": "Invalid data format"
                    }
                }
            },
            "messages": {
                "showError": function(message) {
                    showNotification(message, 'danger');
                },
                "showSuccess": function(message) {
                    showNotification(message, 'success');
                },
                "showElements": function(messages) {

                    var errorsArray,
                        showElementErrors = function(eId, eArr) {

                            var el = $("#" + eId),
                                eTxt = '<p class="help-block error-message">' + eArr.join('<br/>') + '</p>';

                            el.parents("div.form-group").addClass("has-error");
                            el.after(eTxt);
                        };

                    for (inputId in messages) {

                        errorsArray = [];

                        for (errorKey in messages[inputId]) {
                            errorsArray.push(messages[inputId][errorKey]);
                        }

                        showElementErrors(inputId, errorsArray);
                    }
                },
                "clearElements": function() {
                    $("div.form-group.has-error").removeClass("has-error");
                    $("p.help-block.error-message").remove();
                }
            }
        },
        make = function() {
            /*
            // using structure example, this goes in PHP app view, in a javascript block, anywhere, will be used on document load event
            var using = {
                "service1": {
                    "init": "handler/action",
                    "hooks": {
                        "hookname1": {
                            "s": "#/.query",
                            "e": "event",
                            "h": "handler/action"
                        }
                    },
                    "config": {
                        "key1": "value",
                        "key2": "value"
                    }
                }
            };
            */

            debug && console.log("anonymous::make executing");

            if (typeof(window["using"]) !== "object") {

                return;
            }

            using = window["using"];

            for (service in window["using"]) {

                debug && console.log("anonymous::make handleing service " + service + (verbose && "'s request: " + JSON.stringify(window["using"][service])));

                if (container.hasOwnProperty(service)) {

                    if (window["using"][service].hasOwnProperty("config")) {

                        // customizing config
                        $.extend(true, container[service]["config"], window["using"][service]["config"]);

                        debug && verbose && console.log("anonymous::make config for service " + service + ": " + JSON.stringify(container[service]["config"]));
                    }

                    if (window["using"][service].hasOwnProperty("hooks")) {

                        // setting event listeners
                        for (hook in window["using"][service]["hooks"]) {

                            if (window["using"][service]["hooks"][hook].hasOwnProperty("s") &&
                                window["using"][service]["hooks"][hook].hasOwnProperty("e") &&
                                window["using"][service]["hooks"][hook].hasOwnProperty("h")) {

                                debug && console.log("anonymous::make setting hook " + hook);
                                $(window["using"][service]["hooks"][hook]["s"]).on(window["using"][service]["hooks"][hook]["e"], container[service][window["using"][service]["hooks"][hook]["h"]]);
                                debug && console.log("anonymous::make done with hook");
                            } else {

                                (debug || verbose) && console.log("anonymous::make service " + service + "'s hook" + hook + " has invalid config!");
                            }
                        }
                    }

                    if (window["using"][service].hasOwnProperty("init") && container[service].hasOwnProperty(window["using"][service]["init"])) {

                        // calling init action
                        container[service][window["using"][service]["init"]]();
                    }

                    debug && verbose && console.log("anonymous::make finished handleing service " + service);

                } else {

                    (debug || verbose) && console.log("anonymous::make service " + service + " not found!");
                }
            }
        };

        $(document).ready(make);
})(jQuery);