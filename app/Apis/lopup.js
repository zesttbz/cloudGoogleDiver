var origin = document.location.origin,
    dropzone = $("#dropzone"),
    btnUpload = $("#submitUpload"),
    xhr_arr = [],
    allItems = [],
    myStart = "",
    click,
    uploadChange = function() {
        var e = $("#myfile").prop("files");
        checkItems(), addFiles(e), addItems(e);
    },
    checkItems = function() {
        xhr_arr.length || btnUpload.css("display", "inline-table"),
            allItems.length || dropzone.html("");
    },
    addFiles = function(e) {
        var _;
        for (
            dropzone.css("overflow", "auto"), checkItems(), x = 0;
            x < e.length;
            x += 1
        ) {
            var c = x + allItems.length,
                t = document.createElement("div");
            (t.className = "row box-items"), (t.id = "cancel-" + c);
            var d;
            d = e[x].name.length > 100 ? "..." : "";
            _ = "";
            _ += '<div class="col-md-12" style="padding-bottom:5px;">';
            _ +=
                '<p class="fileName"><span class="glyphicon glyphicon-cloud-upload"></span> ' +
                e[x].name.substr(0, 100) +
                d +
                "</p>";
            _ +=
                '<span id="cancel-button-' +
                c +
                '" style="float: right" onclick="myRemove(' +
                c +
                ')"><button class="btn btn-xs btn-danger">Remove</button></span>';
            (_ += "</div>"), (_ += '<div class="clearfix"></div>');
            (_ += '<div class="col-md-12" style="margin: 0;">'),
                (_ +=
                    '<span id="storage-' +
                    c +
                    '" class="storage">' +
                    _convertSize(e[x].size) +
                    "</span>"),
                (_ += '<div class="speed" id="speed-' + c + '"></div>'),
                (_ += '<div class="progress">'),
                (_ +=
                    '<div id="progress-bar-' +
                    c +
                    '" class="progress-bar progress-bar-striped active"></div>'),
                (_ += "</div>"),
                (_ += "</div>"),
                (t.innerHTML = _),
                dropzone.append(t);
            _ = "";
        }
    },
    addItems = function(e) {
        if (e.length > 0) for (x = 0; x < e.length; x += 1) allItems.push(e[x]);
    },
    submitUpload = function() {
        btnUpload.css("display", "none"),
            0 != allItems.length && uploadFile(allItems, 0);
    },
    myRemove = function(e) {
        console.log(e);
        if (
            ($("#cancel-" + e).remove(),
            allItems.splice(e, 1),
            "string" != typeof myStart && myStart == e)
        ) {
            for (i = 0; i < xhr_arr.length; i++) xhr_arr[i].abort();
        }
        for (t = e; t < allItems.length; t++) {
            $("#cancel-" + (t + 1)).attr("id", "cancel-" + t);
            $("#cancel-button-" + (t + 1)).attr(
                "onClick",
                "myRemove(" + t + ");"
            );
            $("#cancel-button-" + (t + 1)).attr("id", "cancel-button-" + t);
            $("#speed-" + (t + 1)).attr("id", "speed-" + t);
            $("#progress-bar-" + (t + 1)).attr("id", "progress-bar-" + t);
            $("#storage-" + (t + 1)).attr("id", "storage-" + t);
        }
        (xhr_arr.length = 0),
            0 == allItems.length && btnUpload.css("display", "none"),
            "number" == typeof myStart &&
                e + 1 <= allItems.length &&
                myStart == e &&
                uploadFile(allItems, e);
    };
myPause = function(e) {
    $("#pause-button-" + e).remove();
    $(
        '<span id="resume-button-' +
            e +
            '" style="float: right" onclick="myResume(' +
            e +
            ')"><button class="btn btn-xs btn-success">Resume</button></span>'
    ).insertBefore("#cancel-button-" + e);
    $("#progress-bar-" + e)
        .removeClass("progress-bar-striped active progress-bar-warning")
        .addClass("progress-bar-info");
};
myResume = function(e) {
    $("#resume-button-" + e).remove();
    $(
        '<span id="pause-button-' +
            e +
            '" style="float: right" onclick="myPause(' +
            e +
            ')"><button class="btn btn-xs btn-primary">Pause</button></span>'
    ).insertBefore("#cancel-button-" + e);

    // this.sendFile_()
};

dropzone.on("drop", function(e) {
    e.preventDefault(),
        (this.className = "dropzone"),
        dropzone.css("lineHeight", "inherit"),
        addFiles(e.originalEvent.dataTransfer.files),
        addItems(e.originalEvent.dataTransfer.files),
        checkItems();
}),
    dropzone.on("dragover", function() {
        return (this.className = "dropzone dragover"), !1;
    }),
    dropzone.on("dragleave", function() {
        return (this.className = "dropzone"), !1;
    });
var _convertSize = function(e) {
    return e > 1073741824
        ? (Math.round((100 * e) / 1073741824) / 100).toString() + " Gb"
        : e > 1048576
        ? (Math.round((100 * e) / 1048576) / 100).toString() + " Mb"
        : (Math.round((100 * e) / 1024) / 100).toString() + " Kb";
};
window.addEventListener("beforeunload", function(e) {
    if ("number" == typeof myStart) {
        var x = "Bạn muốn thoát khỏi trang?";
        return ((e || window.event).returnValue = x), x;
    }
});
var RetryHandler = function() {
    (this.interval = 1e3), (this.maxInterval = 6e4);
};
(RetryHandler.prototype.retry = function(e) {
    setTimeout(e, this.interval), (this.interval = this.nextInterval_());
}),
    (RetryHandler.prototype.reset = function() {
        this.interval = 1e3;
    }),
    (RetryHandler.prototype.nextInterval_ = function() {
        var e = 2 * this.interval + this.getRandomInt_(0, 1e3);
        return Math.min(e, this.maxInterval);
    }),
    (RetryHandler.prototype.getRandomInt_ = function(e, x) {
        return Math.floor(Math.random() * (x - e + 1) + e);
    });
var multiUpload = function(e) {
    var x = function() {};
    if (
        ((this.file = e.file),
        (this.contentType =
            e.contentType || this.file.type || "application/octet-stream"),
        (this.metadata = e.metadata || {
            name: this.file.name,
            mimeType: this.contentType
        }),
        (this.access = e.access),
        (this.onComplete = e.onComplete || x),
        (this.onProgress = e.onProgress || x),
        (this.onError = e.onError || x),
        (this.offset = e.offset || 0),
        (this.chunkSize = e.chunkSize || 0),
        (this.retryHandler = new RetryHandler()),
        (this.url = e.url),
        !this.url)
    ) {
        var _ = e.params || {};
        (_.uploadType = "resumable"),
            (this.url = this.buildUrl_(e.fileId, _, e.baseUrl));
    }
    //if(getCookie("parents")) this.metadata.parents = [getCookie("parents")];
    this.httpMethod = e.fileId ? "PUT" : "POST";
};
(multiUpload.prototype.upload = function() {
    var e = this;
    new _request({
        method: e.httpMethod,
        url: e.url,
        headers: {
            Authorization: "Bearer " + e.access,
            "Content-Type": "application/json",
            "X-Upload-Content-Length": e.file.size,
            "X-Upload-Content-Type": e.contentType
        },
        data: JSON.stringify(e.metadata),
        onComplete: function(x) {
            if (x.status < 400) {
                var _ = x.getResponseHeader("Location");
                (e.url = _), e.sendFile_();
            } else e.onUploadError_(x);
        },
        onError: function(x) {
            e.onUploadError_.bind(e);
        }
    });
}),
    (multiUpload.prototype.sendFile_ = function() {
        var e = this,
            x = e.file,
            _ = x.size,
            c = 0;
        (e.offset || e.chunkSize) &&
            (e.chunkSize &&
                ((c = e.offset + e.chunkSize), (_ = Math.min(c, e.file.size))),
            (x = x.slice(e.offset, _)));
        var t = new XMLHttpRequest();
        xhr_arr.push(t),
            t.open("PUT", this.url, !0),
            t.setRequestHeader("Content-Type", this.contentType),
            t.setRequestHeader(
                "Content-Range",
                "bytes " + this.offset + "-" + (_ - 1) + "/" + this.file.size
            ),
            t.setRequestHeader("X-Upload-Content-Type", this.file.type);
        var d = 0,
            n = 0;
        t.upload.addEventListener(
            "progress",
            function(e) {
                0 == n && (n = e.timeStamp);
                var x = e.loaded,
                    _ = e.total,
                    t = parseInt(((x + c) / _) * 100) || 0,
                    r = speedRate(n, e.timeStamp, d, x);
                $("#progress-bar-" + myStart).html(t + "%"),
                    $("#progress-bar-" + myStart).css("width", t + "%"),
                    $("#speed-" + myStart).html(r),
                    100 == t &&
                        $("#progress-bar-" + myStart)
                            .addClass("progress-bar-warning")
                            .html("Waiting...");
            },
            !1
        ),
            (t.onload = this.onContentUploadSuccess_.bind(this)),
            (t.onerror = this.onContentUploadError_.bind(this)),
            t.send(x);
    }),
    (multiUpload.prototype.resume_ = function() {
        var e = this;
        new _request({
            method: "PUT",
            url: e.url,
            headers: {
                "Content-Range": "bytes " + e.file.size,
                "X-Upload-Content-Type": e.file.type
            },
            beforeSend: function(x) {
                x.upload.addEventListener("progress", e.onProgress);
            },
            onComplete: function(x) {
                e.onContentUploadSuccess_.bind(e);
            },
            onError: function() {
                console.log("multiUpload.prototype.resume");
                e.onContentUploadError_.bind(e);
            }
        });
    }),
    (multiUpload.prototype.extractRange_ = function(e) {
        var x = e.getResponseHeader("Range");
        x && (this.offset = parseInt(x.match(/\d+/g).pop(), 10) + 1);
    }),
    (multiUpload.prototype.onContentUploadSuccess_ = function(e) {
        var x = this;
        if (200 == e.target.status || 201 == e.target.status) {
            var _ = JSON.parse(e.target.response),
                c =
                    "type=savefile&fileId=" +
                    _.id +
                    "&fileName=" +
                    _.name +
                    "&fileType=" +
                    _.mimeType +
                    "&fileSize=" +
                    this.file.size;
            new _request({
                method: "POST",
                url: "./upload",
                data: c,
                dataType: "json",
                onSuccess: function(e) {
                    if (1 == e.status) {
                        $("#progress-bar-" + myStart)
                            .removeClass(
                                "progress-bar-striped active progress-bar-warning"
                            )
                            .addClass("progress-bar-success")
                            .html("Complete");
                        if (e.msg && e.msg.length > 1)
                            $("#allLink")
                                .show()
                                .html($("#allLink").html() + e.msg + "\n");
                    } else {
                        $("#progress-bar-" + myStart)
                            .removeClass(
                                "progress-bar-striped active progress-bar-warning"
                            )
                            .addClass("progress-bar-danger")
                            .html(e.msg);
                    }
                    xhr_arr.length = 0;
                    x.onComplete();
                },
                onError: function(e) {
                    $("#progress-bar-" + myStart)
                        .removeClass(
                            "progress-bar-striped active progress-bar-warning"
                        )
                        .addClass("progress-bar-danger")
                        .html("Có lỗi xảy ra, vui lòng thử lại sau."),
                        (xhr_arr.length = 0),
                        x.onComplete();
                }
            });
        } else
            308 == e.target.status
                ? (this.extractRange_(e.target),
                  this.retryHandler.reset(),
                  this.sendFile_())
                : this.onContentUploadError_(e);
    }),
    (multiUpload.prototype.onContentUploadError_ = function(e) {
        console.log("onContentUploadError");
        e.target.status && e.target.status < 500
            ? this.onError(e.target.response)
            : this.retryHandler.retry(this.resume_.bind(this));
        sendError("onContentUploadError", e.target.response);
    }),
    (multiUpload.prototype.onUploadError_ = function(e) {
        console.log("onUploadError:\n" + e.target.response);
        sendError("onUploadError", e.target.response);
        this.onError(e.target.response);
    }),
    (multiUpload.prototype.buildQuery_ = function(e) {
        return (
            (e = e || {}),
            Object.keys(e)
                .map(function(x) {
                    return (
                        encodeURIComponent(x) + "=" + encodeURIComponent(e[x])
                    );
                })
                .join("&")
        );
    }),
    (multiUpload.prototype.buildUrl_ = function(e, x, _) {
        var c = _ || "https://www.googleapis.com/upload/drive/v3/files/";
        e && (c += e);
        var t = this.buildQuery_(x);
        return t && (c += "?" + t), c;
    });
var mediaUpload = function(e, x) {
        var _ = new multiUpload({
            file: e[x],
            access: getCookie("access"),
            onComplete: function() {
                return x + 1 < e.length
                    ? uploadFile(e, x + 1)
                    : allItems.splice(0, allItems.length);
            }
        });
        (myStart = x), _.upload();
    },
    uploadFile = function(e, x) {
        if (void 0 != typeof e[x]) {
            console.log(e[x]);
            if (e[x].size > 10737418240)
                return (
                    $("#progress-bar-" + x + ", #cancel-button-" + x).remove(),
                    $("#storage-" + x).html(
                        '<strong style="color:red">Error: File lớn hơn 10gb</strong>'
                    ),
                    x + 1 < e.length
                        ? uploadFile(e, x + 1)
                        : allItems.splice(0, allItems.length)
                );
            var re = /(?:\.([^.]+))?$/;
            var ext = re.exec(e[x].name);
            ext = ext && ext[1] ? ext[1].toLowerCase() : false;
            if (
                ext &&
                typeof disallow_exts !== "undefined" &&
                disallow_exts.includes(ext)
            )
                return (
                    $("#progress-bar-" + x + ", #cancel-button-" + x).remove(),
                    $("#storage-" + x).html(
                        '<strong style="color:red">Error: Định dạng file ' +
                            ext +
                            " không cho phép</strong>"
                    ),
                    x + 1 < e.length
                        ? uploadFile(e, x + 1)
                        : allItems.splice(0, allItems.length)
                );

            if (e[x].size < 1)
                return (
                    $("#progress-bar-" + x + ", #cancel-button-" + x).remove(),
                    $("#storage-" + x).html(
                        '<strong style="color:red">Error: Tệp tin trống</strong>'
                    ),
                    x + 1 < e.length
                        ? uploadFile(e, x + 1)
                        : allItems.splice(0, allItems.length)
                );
            _checkUpload(e, x);
        }
    },
    _checkUpload = function(e, x) {
        var params =
            "type=checkfileupload&fileName=" +
            encodeURIComponent(e[x].name) +
            "&fileType=" +
            encodeURIComponent(e[x].type) +
            "&fileSize=" +
            e[x].size;
        (xhr = new XMLHttpRequest()), xhr.open("POST", "./upload", !0);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.setRequestHeader(
            "Content-type",
            "application/x-www-form-urlencoded"
        );
        xhr.send(params);
        xhr.onreadystatechange = function() {
            if (4 == xhr.readyState && 200 == xhr.status) {
                var _ = JSON.parse(xhr.responseText);
                if (_.status)
                    if (getCookie("access")) {
                        if (_.msg && _.msg.length > 1)
                            $("#allLink")
                                .show()
                                .html($("#allLink").html() + _.msg + "\n");
                        mediaUpload(e, x);
                    } else {
                        var c;
                        _checkUpload(e, x),
                            (c = setInterval(function() {
                                getCookie("access") &&
                                    (clearInterval(c), mediaUpload(e, x));
                            }, 100));
                    }
                else
                    (document.cookie =
                        "access=;expires=Thu, 01 Jan 1970 00:00:00 GMT"),
                        alert(_.msg);
            }
        };
    },
    getCookie = function(e) {
        for (
            var x = e + "=", _ = document.cookie.split(";"), c = 0;
            c < _.length;
            c++
        ) {
            for (var t = _[c]; " " == t.charAt(0); ) t = t.substring(1);
            if (0 == t.indexOf(x)) return t.substring(x.length, t.length);
        }
        return "";
    },
    speedRate = function(e, x, _, c) {
        var t = x - e,
            d = 0;
        if (0 != t) {
            var n = (c - _) / t;
            return (
                (d =
                    navigator.userAgent.toLowerCase().indexOf("firefox") > -1
                        ? parseInt(1e3 * n * 1e3)
                        : parseInt(1e3 * n)),
                _convertSize(d) + "/s"
            );
        }
        return (
            (d =
                navigator.userAgent.toLowerCase().indexOf("firefox") > -1
                    ? parseInt(1e3 * c)
                    : parseInt(c)),
            _convertSize(d) + "/s"
        );
    },
    _request = function(e) {
        var x = function() {};
        (this.beforeSend = e.beforeSend || x),
            (this.onComplete = e.onComplete || x),
            (this.onSuccess = e.onSuccess || x),
            (this.onError = e.onError || x),
            (this.method = e.method),
            (this.url = e.url || ""),
            (this.headers = e.headers || ""),
            (this.dataType = e.dataType || ""),
            (this.data = e.data || ""),
            this._send();
    };
_request.prototype._send = function() {
    var e = this;
    $.ajax({
        type: e.method,
        url: e.url,
        headers: e.headers,
        dataType: e.dataType,
        data: e.data,
        contentType: e.contentType,
        success: function(x) {
            e.onSuccess(x);
        },
        complete: function(x) {
            e.onComplete(x);
        },
        error: function(x) {
            e.onError(x);
        }
    });
};
sendError = function(type, data) {
    $("#progress-bar-" + myStart)
        .removeClass("progress-bar-striped active progress-bar-warning")
        .addClass("progress-bar-danger")
        .html("Có lỗi xảy ra.");
    //$("#cancel-button-" + myStart).attr({'onclick':'myResume('+myStart+')'}).html('<button class="btn btn-xs btn-default">Resume</button>');
    new _request({
        method: "POST",
        url: "./upload",
        data: { type: "report", error: type, data: data },
        dataType: "json",
        onSuccess: function(e) {}
    });
};
