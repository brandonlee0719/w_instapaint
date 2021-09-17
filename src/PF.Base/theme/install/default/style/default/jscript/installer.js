(function ($) {
    var BasePath = window.BasePath;
    var isUpgrade = $('#is-upgrade').length ? '&phpfox-upgrade=1' : '';

    var Step = function (params) {
        var name = params.name;
        this.name = name;
        this.requestTo = name;
        this.msg = params.msg;
        this.status = 'ready';
        this.isSub = false;
        this.main = name;
        this.hasSub = false;
        this.okLabel = false;
        this.data = {};
        this.showProgress = true;

        if (/\./.test(name)) {
            this.isSub = true;
            this.main = name.split('.')[0];
        }

        this.describe = function (msg) {
            console.log(msg + ': "' + this.name + '"', this.status);
            console.info(this.msg);
            installer.log(msg + ':' + this.name);
        };

        this.isRunning = function () {
            return this.status == 'running';
        };

        this.onDone = function () {
            this.describe("done");
        };

        this.getUrl = function () {
            return BasePath + '?step=' + this.requestTo + '&_ajax=1' + isUpgrade
        };

        this.onProcess = function () {
            // this.done();
            var that = this;

            if (this.okLabel == false) {
                if(this.showProgress){
                    installer.showProgress(this.msg);
                }
            } else {
                installer.shouldShowLoading(this.msg);
            }

            $.ajax({
                url: that.getUrl(),
                type: 'GET',
                data: that.data,
                timeout: 600e3,
                beforeSend: function(){installer.isSending = true},
                error: function (e) {
                    that.onError(e);
                },
                success: function (e) {
                    that.onSuccess(e);
                }
            }).always(function () {
                that.onAlways();
                installer.isSending = false;
            });
        };

        this.onAlways = function () {
            installer.shouldHideLoading();
            window.currentStep = this.name;
            window.setTimeout(function(){
                installer.isSending = false;
            },2000);
        };

        this.onError = function (e) {
            // document.open();
            // document.write(e.responseText);
            // document.close();
            $('#installer-content').html(e.responseText);
        };

        this.onSuccess = function (e) {
            if (typeof e.steps == 'object') {
                installer.addSteps(e.steps);
            }

            if (typeof e.message == 'string') {
                installer.log(e.message);
            }
            if (typeof(e.next) == 'string') {
                if (e.next == 'retry') {
                    this.retry();
                } else {
                    this.done();
                }
            }
            else if (typeof(e.content) == 'string') {
                if (e.content == 'done') {
                    this.done();
                } else {
                    $('#installer-content').html(e.content);
                    setTimeout(function () {
                        $("[autofocus]").focus();
                    }, 10);
                }
                if ($('.has-error').length) {
                    $('#btn_ok').text('Try Again!');
                }
            }
            else if (typeof(e.errors) == 'object') {
                $('#errors').html('<div class="alert alert-danger has-error">' + e.errors.join('<br/>') + '</div>').removeClass('hide');
            } else {
                $('#installer-content').html(e);
                // this.done();
            }
        };

        this.isDone = function () {
            return this.status == 'done';
        };

        this.isWaitingChildren = function () {
            return this.status == 'waiting';
        };

        this.isRunning = function () {
            return this.status == 'running';
        };

        this.retry = function () {
            if (this.name == 'first') {
                location.reload();
            } else {
                this.run();
            }

        };

        this.onContinue = function () {

            if (this.name == 'all_done') {
                window.location.reload();
            }

            if(installer.isLoading){
                return ;
            }

            // for requirement checking
            if ($('.check-requirements').length) {
                if ($('.has-error').length) {
                    this.retry();
                } else {
                    this.done();
                }
                return;
            }

            var form = $('form#js_form'),
                that = this;

            if (!form.length) {
                this.done();
            } else {
                installer.shouldShowLoading();
                $.ajax({
                    url: this.getUrl(),
                    type: form.prop('method'),
                    data: form.serialize(),
                    timeout: 600e3,
                    beforeSend: function(){installer.isSending = true},
                    error: function (e) {
                        that.onError(e);
                    },
                    success: function (e) {
                        that.onSuccess(e);
                    }
                }).always(function () {
                    that.onAlways();
                })
            }

        };

        this.continue = function () {
            this.describe("continue");
            this.onContinue();
        };

        this.done = function () {
            if (this.hasSub) {
                this.status = 'waiting';
                // done only all sub is done.
            } else {
                this.status = 'done';
                this.onDone();
            }

            if (this.isSub) {
                installer.shouldCompleteParentStep(this.main);
            }

            installer.shouldProcessNextStep(this);
        };

        this.forceDone = function () {
            this.status = 'done';
            this.onDone();
            // installer.shouldProcessNextStep(this);
        };

        this.run = function () {
            this.status = 'running';
            this.describe("onProcess");

            if(this.name == 'process'){
                installer.shouldConfirmReload();
            }else if(this.name == 'all_done'){
                installer.shouldRemoveConfirmReload();
            }

            if (!this.isSub) {
                var $li = $('li#step_' + this.name);
                if($li.length){
                    $li.addClass('active');
                    $li.prevAll().removeClass('active').addClass('done');
                }

                if (!this.okLabel) {
                    $('#btn_ok').addClass('hide');
                    if(this.showProgress){
                        installer.showProgress(this.msg);
                    }
                } else if ($('.has-error').length) {
                    $('#btn_ok').text('Try Again!').removeClass('hide');
                } else {
                    $('#btn_ok').text(this.okLabel).removeClass('hide');
                }
            }
            this.onProcess();

        };

        if (typeof params.onProcess == 'function') {
            this.onProcess = params.onProcess;
        }
        if (typeof params.onDone == 'function') {
            this.onDone = params.onDone;
        }
        if (typeof params.onContinue == 'function') {
            this.onContinue = params.onContinue;
        }
        if (typeof params.data != 'undefined') {
            this.data = params.data
        }

        if (typeof params.okLabel != 'undefined') {
            this.okLabel = params.okLabel;
        }
        if (typeof params.showProgress != 'undefined') {
            this.showProgress = params.showProgress;
        }

    };

    var Manager = function () {
        this.aSteps = {};
        this.container = $('#installer-content');
        this.logContainer = $('#log_area');
        this.isLoading = false;
        this.isSending  = false;
        this.init();
    };


    Manager.prototype.add = function (params) {
        var step = new Step(params);
        this.aSteps[step.name] = step;

        if(this.aSteps.hasOwnProperty(step.main)){
            if (step.isSub) this.get(step.main).hasSub = true;
        }else{
            console.error("There are no step "+ step.main);
        }
    };

    /**
     * @return {Step}
     */
    Manager.prototype.get = function (name) {
        return this.aSteps[name];
    };

    Manager.prototype.init = function () {
        this.aSteps = {};
        this.addSteps([
            {
                name: "first",
                msg: "System Requirements",
                okLabel: "Continue",
                onProcess: function () {
                }
            },
            {name: "requirement", msg: "Checking System Requirements", okLabel: "Continue"},
            {name: "load_general_steps", msg: "Load General Steps", okLabel: "", showProgress:false},
            {name: "key", msg: "License Key", okLabel: "Continue"}
        ]);
    };

    Manager.prototype.addSteps = function (array) {
        for (var i = 0; i < array.length; ++i) {
            this.add(array[i])
        }
    };

    Manager.prototype.createProgressBar = function () {
        var doneSteps = this.doneSteps(),
            totalSteps = this.totalSteps(),
            remain = doneSteps.toString() + '/' + totalSteps,
            width = Math.round(doneSteps / totalSteps * 100).toString() + '%'
        ;

        if ($('#progress_container').length) {
            $('#progress_bar').css({width: width}).text(remain);
        } else {
            this.container.html('<div id="progress_container">\n' +
                '                            <div id="progress_stage"></div>\n' +
                '                            <div id="progress_msg"></div>\n' +
                '                        </div>');

            $('<div class="progress">\n' +
                '  <div id="progress_bar" class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" \n' +
                '  aria-valuemin="0" aria-valuemax="100" style="width:' + width + '">\n' +
                remain +
                '  </div>\n' +
                '</div>').appendTo('#progress_stage');
        }
    };

    /**
     * Show progress bar with message.
     * s
     * @param {String} msg
     */
    Manager.prototype.showProgress = function (msg) {
        this.createProgressBar();
        $('#progress_msg').html(msg);
    };

    /**
     * @returns {number}
     */
    Manager.prototype.doneSteps = function () {
        var keys = this.keys();
        var doneCounter = 0;
        for (var i = 0; i < keys.length; ++i) {
            if (this.get(keys[i]).isDone()) {
                doneCounter++;
            }
        }

        return doneCounter;

    };

    /**
     * @return {Number}
     */
    Manager.prototype.totalSteps = function () {
        return this.keys().length;
    };

    /**
     * @return {Array}
     */
    Manager.prototype.keys = function () {
        return Object.keys(this.aSteps);
    };

    /**
     * @returns {Step}
     */
    Manager.prototype.current = function () {
        var keys = this.keys(), count = keys.length;
        for (var i = 0; i < count; ++i) {
            if (this.get(keys[i]).isRunning()) {
                return this.get(keys[i]);
            }
        }
        return false;
    };

    Manager.prototype.start = function () {
        if ($('.has-error').length) {
            $('.btn.btn-success').text('Try Again!');
        }
        this.get(this.keys()[0]).run();
    };

    Manager.prototype.filter = function (cb) {
        var result = [], keys = this.keys(), length = keys.length;

        for (var i = 0; i < length; ++i) {
            if (cb(keys[i], this.aSteps[keys[i]])) {
                result.push(keys[i]);
            }
        }
        return result;
    };

    Manager.prototype.shouldCompleteParentStep = function (main) {
        var remain = installer.filter(function (key, step) {
            return key.indexOf(main + ".") > -1 && !step.isDone()
        });
        if (remain.length == 0 && this.get(main).isWaitingChildren()) {
            this.get(main).forceDone();
        }
    };


    /**
     * @param {Step} currentStep
     * @return {Boolean}
     */
    Manager.prototype.shouldProcessNextStep = function (currentStep) {
        if (currentStep.hasSub || currentStep.isSub) {
            var main = currentStep.main,
                keys = this.filter(function (key, step) {
                    return key.indexOf(main + ".") > -1 && !step.isDone() && !step.isWaitingChildren()
                })
            ;
            if (keys.length) {
                this.get(keys[0]).run();
                return true;
            }
        }
        // get to next main step
        var mainKeys = this.filter(function (key, step) {
            return !step.isSub && !step.isDone() && !step.isWaitingChildren()
        });

        if (mainKeys.length) {
            this.get(mainKeys[0]).run();
            return true;
        }

        return false;
    };


    // return main step, sub-step.
    /**
     *
     * @returns {Step}
     */
    Manager.prototype.next = function () {
        // get keys it's not completed

        if (!this.current()) {
            console.log("There are no running step");
            return false;
        }

        var current = this.current(),
            test = current.main + ".",
            keys = this.filter(function (key, step) {
                return key.indexOf(test) > -1 && !step.isDone()
            })
        ;
        if (keys.length) {
            return this.get(keys[0]);
        }

        // get to next main step
        var mainKeys = this.filter(function (key, step) {
            return !step.isSub && !step.isDone()
        });

        if (mainKeys.length) {
            return this.get(mainKeys[0]);
        }
        return false;
    };

    Manager.prototype.fileSystemChanged = function (f, value) {
        var form = $(f);
        $('.method', form).addClass('hide');
        $('.method_' + value, form).removeClass('hide');
        setTimeout(function () {
            $("[autofocus]").focus();
        }, 1);
    };

    Manager.prototype.readPrivateFile = function () {
        var file = document.getElementById('fileprivate').files[0];
        var loaded = function (evt) {
            $('#fileprivate_content').val(evt.target.result);
        };
        if (file) {
            var reader;
            try {
                reader = new FileReader();
            } catch (e) {
                document.getElementById('output').innerHTML =
                    "Error: seems File API is not supported on your browser";
                return;
            }

            // Read file into memory as UTF-8
            reader.readAsText(file, "UTF-8");

            // Handle progress, success, and errors
            reader.onload = loaded;
        }
    };

    Manager.prototype.log = function (msg) {
        $('<div/>').html(msg).appendTo(this.logContainer);
    };

    Manager.prototype.continue = function () {
        if(this.isSending)
            return false;
        this.current().continue();
    };


    Manager.prototype.process = function () {
        installer.current().run();
    };

    Manager.prototype.shouldConfirmReload = function () {
        window.onbeforeunload = function () {
            return "Do not refresh or close this page before installation process completed.";
        };
    };

    Manager.prototype.shouldRemoveConfirmReload = function () {
        window.onbeforeunload = function () {
        };
    };

    Manager.prototype.toggleCheckAll = function (ele, sel) {
        var val = ele.checked;
        setTimeout(function () {
            $(sel).each(function (i, obj) {
                obj.checked = !!val;
            });
        }, 10);
    };

    Manager.prototype.showLoading = function () {
        if (this.isLoading) {
            $('#loading').removeClass('hide');
        } else {
            $('#loading').addClass('hide');
        }
    };

    Manager.prototype.shouldShowLoading = function () {
        this.isLoading = true;
        installer.showLoading();
    };

    Manager.prototype.shouldHideLoading = function () {
        this.isLoading = false;
        window.setTimeout(function () {
            installer.hideLoading();
        }, 1e3);

    };

    Manager.prototype.hideLoading = function () {
        this.isLoading = false;
        $('#loading').addClass('hide');
        $('#ok_btn').removeClass('disabled');
    };

    window.installer = new Manager();

    $(document).on('submit', '#js_form', function (evt) {
        evt.preventDefault();
        installer.continue();
        return false;
    });


})(jQuery);